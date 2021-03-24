<?php

require ('database/_Message.php');
$message_object = new _Message();

if(isset($_POST['action']))
{
    if($_POST['action'] == 'delete')
    {
        $message_object->setId($_POST['id']);

        if($message_object->deleteMessage())
        {
            echo json_encode(['status' => 1]);
        }else
        {
            echo json_encode(['status' => 0]);
        }
    }else if($_POST['action'] == 'save')
    {
        $message_object->setUserId($_POST['user_id']);
        $message_object->setChatId($_POST['chat_id']);
        $message_object->setText($_POST['message']);
        $message_object->setCreatedOn(date("Y-m-d h:i:s"));

        if($message_object->saveMessage())
        {
            $message_id = $message_object->getMessageId();
            $user_id = $_POST['user_id'];
            $chat_id = $_POST['chat_id'];
            $message = $_POST['message'];
            $created_on = $message_object->getCreatedOn();
            $user_name = $_POST['user_name'];

            echo json_encode(['status' => 1,
                                'user_id' => $user_id,
                                'chat_id' => $chat_id,
                                'message' => $message,
                                'created_on' => $created_on,
                                'message_id' => $message_id,
                                'user_name' => $user_name,
                                'action' => "save"]);
        }else
        {
            echo json_encode(['status' => 2]);
        }
    }else if($_POST['action'] == 'edit')
    {
        $message_object->setId($_POST['message_id']);
        $message_object->setText($_POST['message']);

        if($message_object->editMessage())
        {
            $message_id = $_POST['message_id'];
            $user_id = $_POST['user_id'];
            $chat_id = $_POST['chat_id'];
            $message = $_POST['message'];
            $user_name = $_POST['user_name'];

            echo json_encode(['status' => 1,
                'user_id' => $user_id,
                'chat_id' => $chat_id,
                'message' => $message,
                'message_id' => $message_id,
                'user_name' => $user_name,
                'action' => "edit"]);
        }
    }

    unset($_POST['action']);
}

if(isset($_GET['action']))
{
    if($_GET['action'] == 'load_message')
    {
        $message_object->setid($_GET['message_id']);
        $message_object->setChatId($_GET['chat_id']);

        $result = $message_object->loadMessage();

        echo json_encode(['status' => 1, 'result' => $result]);
    }

    unset($_GET['action']);
}