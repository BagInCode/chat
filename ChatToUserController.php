<?php

require ("database/ChatToUserTable.php");

if(isset($_POST['action']))
{
    if($_POST['action'] == 'create')
    {
        $chat_to_user_object = new ChatToUserTable;

        $chat_to_user_object->setChatId($_POST['chat_id']);
        $chat_to_user_object->setUserId($_POST['user_id']);

        if(!$chat_to_user_object->addPerson())
        {
            echo json_encode(['status' => 2, 'error_message' => 'Can`t connect you to chat']);
        }else
        {
            echo json_encode(['status' => 1, 'chat_id' => $_POST['chat_id']]);
        }
    }

    unset($_POST['action']);
}

?>