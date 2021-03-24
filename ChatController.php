<?php

require ('database/ChatTable.php');

if(isset($_POST['action']))
{
    if($_POST['action'] == 'create')
    {
        session_start();

        $chat_list_object = new ChatTable();
        $chat_list_object->setChatName($_POST['chat_name']);

        if(!$chat_list_object->createChat())
        {
            echo json_encode(['status' => 2, 'error_message' => 'Can`t create chat...']);
        }else
        {
            $chat_id = $chat_list_object->getIdByName();

            echo json_encode(['status' => 1, 'chat_id' => $chat_id]);

        }
    }

    unset($_POST['action']);
}

if(isset($_GET['action']))
{
    if($_GET['action'] == 'exist?')
    {
        $chat_table_object = new ChatTable;

        $chat_table_object->setChatId($_GET['chat_id']);

        if(!$chat_table_object->existChat())
        {
            echo json_encode(['status' => 2]);
        }else
        {
            echo json_encode(['status' => 1]);
        }
    }else if ($_GET['action'] == 'get chat name')
    {
        $chat_table_object = new ChatTable;
        $chat_table_object->setChatId($_GET['chat_id']);

        $data = $chat_table_object->getNameById();

        echo json_encode(['status' => 1, 'chat_name' => $data['chat_name']]);
    }

    unset($_GET['action']);
}

?>
