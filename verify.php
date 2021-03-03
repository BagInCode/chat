<?php

$error = '';

session_start();

if(isset($_GET['code']))
{
    require_once ('database/chatUser.php');

    $user_object = new ChatUser;

    $user_object->setUserVerificationCode($_GET['code']);

    if($user_object->is_valid_email_verification_code())
    {
        $user_object->setUserStatus('Enable');

        if($user_object->enable_user_account())
        {
            $_SESSION['success_message'] = 'Your email verify';

            header('location:index.php');
        }else
        {
            $error = 'Sth went wrong';
        }
    }else
    {
        $error = 'Sth went wrong';
    }
}
?>