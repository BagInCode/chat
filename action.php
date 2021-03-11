<?php

//action.php

session_start();

if(isset($_POST['action']))
{
    if($_POST['action'] == 'leave')
    {
        require('database/chatUser.php');

        $user_object = new ChatUser;

        $user_object->setUserId($_POST['user_id']);

        $user_object->setUserLoginStatus('Logout');

        if ($user_object->update_user_login_data()) {
            unset($_SESSION['user_data']);

            session_destroy();

            echo json_encode(['status' => 1]);
        }
    }else if($_POST['action'] == 'load')
    {
        require ("database/_Message.php");
        require ("database/chatUser.php");

        $message_object = new _Message;
        $message_object->setChatId($_POST['chat_id']);
        $message_object->setId($_POST['cnt_msg']);

        $result = $message_object->loadMessage();

        for($i = 1; $i <= $result[0]['rowCount']; $i++)
        {
            $user_object = new ChatUser;
            $user_object->setUserId($result[$i]['user_id']);

            $data = $user_object->get_user_data_by_id();
            $result[$i]['user_name'] = $data['user_name'];
        }

        echo json_encode(['status' => 1, 'result' => $result]);
    }
}

?>
