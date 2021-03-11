<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require dirname(__DIR__) . "/database/chatUser.php";
require dirname(__DIR__) . "/database/_Message.php";

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $data = json_decode($msg, true);

        if($data['message_id'] == 0)
        {
            $user_object = new \ChatUser();
            $message_object = new \_Message();

            $message_object->setUserId($data['userId']);
            $message_object->setChatId($data['chatId']);
            $message_object->setText($data['msg']);
            $message_object->setCreatedOn(date("Y-m-d h:i:s"));

            $message_object->saveMessage();

            $msgID = $message_object->getMessageId();
            if($msgID === false)
            {
                $data['message_id'] = "false";
            }else
            {
                $data['message_id'] = $msgID['id'];
            }

            $user_object->setUserId($data['userId']);
            $user_data = $user_object->get_user_data_by_id();

            $user_name = $user_data['user_name'];
            $data['dt'] = date("Y-m-d h:i:s");

            foreach ($this->clients as $client) {
                /*if ($from !== $client) {
                    // The sender is not the receiver, send to each client connected
                    $client->send($msg);
                }*/

                if($from == $client)
                {
                    $data['from'] = 'Me';
                }else
                {
                    $data['from'] = $user_name;
                }

                $client->send(json_encode(['status' => 1, 'data' => $data]));
            }
        }else
        {
            $message_object = new \_Message();
            $message_object->setId($data['message_id']);
            $message_object->setText($data['msg']);

            $user_object = new \ChatUser();
            $user_object->setUserId($data['userId']);
            $user_data = $user_object->get_user_data_by_id();
            $user_name = $user_data['user_name'];

            if($message_object->editMessage())
            {
                foreach ($this->clients as $client) {
                    /*if ($from !== $client) {
                        // The sender is not the receiver, send to each client connected
                        $client->send($msg);
                    }*/

                    if($from == $client)
                    {
                        $data['from'] = 'Me';
                    }else
                    {
                        $data['from'] = $user_name;
                    }

                    $client->send(json_encode(['status' => 0, 'data' => $data]));
                }
            }else
            {
                foreach ($this->clients as $client) {
                    /*if ($from !== $client) {
                        // The sender is not the receiver, send to each client connected
                        $client->send($msg);
                    }*/

                    if($from == $client)
                    {
                        $data['from'] = 'Me';
                    }else
                    {
                        $data['from'] = $user_name;
                    }

                    $client->send(json_encode(['status' => 2, 'data' => $data]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}