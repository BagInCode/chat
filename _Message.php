<?php


class _Message
{
    private $id;
    private $created_on;
    private $text;
    private $chat_id;
    private $user_id;
    public $connect;

    public function __construct()
    {
        require_once ('database_connection.php');

        $database_object = new Database_connection;

        $this->connect=$database_object->connect();
    }

    function setId($id)
    {
        $this->id = $id;
    }
    function setCreatedOn($created_on)
    {
        $this->created_on = $created_on;
    }
    function setText($text)
    {
        $this->text = $text;
    }
    function setChatId($chat_id)
    {
        $this->chat_id = $chat_id;
    }
    function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
    
    function getId()
    {
        return $this->id;
    }
    function getCreatedOn()
    {
        return $this->created_on;
    }
    function getText()
    {
        return $this->text;
    }
    function getChatId()
    {
        return $this->chat_id;
    }
    function getUserId()
    {
        return $this->user_id;
    }

    function saveMessage()
    {
        $created_on = $this->getCreatedOn();
        $text = $this->getText();
        $chat_id = $this->getChatId();
        $user_id = $this->getUserId();

        $query = "INSERT INTO message_table(text, created_on, chat_id, user_id) 
                                     VALUES(:text, :created_on, :chat_id, :user_id)";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':text', $text);
        $statement->bindParam(':created_on', $created_on);
        $statement->bindParam(':chat_id', $chat_id);
        $statement->bindParam(':user_id', $user_id);

        $result = false;

        try
        {
            if($statement->execute())
            {
                $result = true;
            }else
            {
                $result = false;
            }
        }catch (Exception $error)
        {
            $result = false;

            $error->getMessage();
        }

        return $result;
    }

    function loadMessage()
    {
        $last_message_id = $this->getId();
        $count_message = 10;
        $chat_id = $this->getChatId();

        $query = "SELECT * 
                    FROM message_table 
                    WHERE message_table.chat_id=:chat_id 
                    ORDER BY message_table.id DESC
					LIMIT ".$last_message_id.", ".$count_message.";";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':chat_id', $chat_id);

        $result = array();

        try
        {
            if($statement->execute())
            {
                $result[0]['rowCount'] = $statement->rowCount();

                for($i = 0; $i < $statement->rowCount(); $i++)
                {
                    $data = $statement->fetch(PDO::FETCH_ASSOC);

                    $result[$i+1]['id'] = $data['id'];
                    $result[$i+1]['user_id'] = $data['user_id'];
                    $result[$i+1]['created_on'] = $data['created_on'];
                    $result[$i+1]['text'] = $data['text'];
                }
            }
        }catch(Exception $error)
        {
            die($error->getMessage());
        }

        return $result;
    }
}