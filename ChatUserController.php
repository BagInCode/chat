<?php

require("database/chatUser.php");

if(isset($_POST['action']))
{
    if($_POST['action'] == 'register')
    {
        $user_object = new ChatUser;

        $user_object->setUserName($_POST['user_name']);
        $user_object->setUserEmail($_POST['user_email']);
        $user_object->setUserPassword($_POST['user_password']);
        $user_object->setUserProfile($user_object->make_avatar(strtoupper($_POST['user_name'][0])));
        $user_object->setUserStatus('Disabled');
        $user_object->setUserCreatedOn(date('Y-n-d H:i:s'));
        $user_object->setUserVerificationCode(md5(uniqid()));

        $user_data = $user_object->get_user_data_by_email();

        if(is_array($user_data) && count($user_data) > 0)
        {
            echo json_encode(['status' => 3, 'error_message' => 'This Email already registered']);
        }else
        {
            if ($user_object->save_data())
            {
                $to = '<' . $user_object->getUserEmail() . '>';
                $subject = 'Registration verification for Chat App Demo';
                $message = '
            <p>Thank you for registration for Chat App Demo.</p>
                <p>This is a verification email, please click the link to verify your email adress.</p>
                <p><a href="http://localhost:81/tutorial/chat_application/verify.php?code=' . $user_object->getUserVerificationCode() . '">Click to verify</a></p>
                <p>Thank you...</p>
                ';
                $headers = "Content-type: text/html; charset=windows-1251 \r\n";
                $headers .= "From: От кого письмо <serg1331@inbox.ru>\r\n";

                if (!mail($to, $subject, $message, $headers))
                {
                    echo json_encode(['status' => 2, 'error_message' => '<p>Can`t send verification mail</p><p>But follow <button class="btn btn-primary" onclick="redirect()">link</button> to verify email</p><input id="user_verification_code" type="hidden" value="'.$user_object->getUserVerificationCode().'">']);
                } else
                {
                    echo json_encode(['status' => 1, 'success_message' => 'Verification Email sent to ' . $user_object->getUserEmail() . ', so before login verify your email']);
                }
            } else
            {
                echo json_encode(['status' => 4, 'error_message' => 'Sth went wrong']);
            }
        }
    }else if($_POST['action'] == 'leave')
    {
        session_start();
        $user_object = new ChatUser;

        $user_object->setUserId($_POST['user_id']);

        $user_object->setUserLoginStatus('Logout');

        if ($user_object->update_user_login_data()) {
            unset($_SESSION['user_data']);

            session_destroy();

            echo json_encode(['status' => 1]);
        }
    }else
    if($_POST['action'] == 'verify')
    {
        $code = $_POST['code'];

        $user_object = new ChatUser();
        $user_object->setUserVerificationCode($code);

        if($user_object->is_valid_email_verification_code())
        {
            $user_object->setUserStatus('Enable');

            if($user_object->enable_user_account())
            {
                echo json_encode(['status' => 1]);
            }else
            {
                echo json_encode(['status' => 2, 'error_message' => 'Sth Went Wrong']);
            }
        }else
        {
            echo json_encode(['status' => 2, 'error_message' => 'Sth Went Wrong']);
        }
    }else if($_POST['action'] == 'change')
    {
        session_start();

        $user_object = new ChatUser();
        $user_profile = $_POST['hidden_user_profile'];

        if($_FILES['user_profile']['name'] != '')
        {
            $user_profile = $user_object->upload_image($_FILES['user_profile']);
            $_SESSION['user_data'][$_POST['user_id']]['profile'] = $user_profile;
        }

        $user_object->setUserName($_POST['user_name']);
        $user_object->setUserEmail($_POST['user_email']);
        $user_object->setUserPassword($_POST['user_password']);
        $user_object->setUserProfile($user_profile);
        $user_object->setUserId($_POST['user_id']);

        if($user_object->update_data())
        {
            $user_data['user_id'] = $user_object->getUserId();
            $user_data['user_name'] = $user_object->getUserName();
            $user_data['user_email'] = $user_object->getUserEmail();
            $user_data['user_profile'] = $user_object->getUserProfile();


            $_SESSION['user_data'][$user_data['user_id']] = [
                'id' => $user_data['user_id'],
                'name' => $user_data['user_name'],
                'profile' => $user_data['user_profile']
            ];

            echo json_encode(['status' => 1]);
        }else
        {
            echo json_encode(['status' => 2, 'error_message' => 'Cant update your profile']);
        }
    }

    unset($_POST['action']);
}

if(isset($_GET['action']))
{
    if($_GET['action'] == 'login')
    {
        session_id($_GET['session_id']);
        session_start();

        $user_object = new ChatUser();
        $user_object->setUserEmail($_GET['user_email']);

        $user_data = $user_object->get_user_data_by_email();

        if(is_array($user_data) && count($user_data) > 0)
        {
            if($user_data['user_status'] == 'Enable')
            {
                if($user_data['user_password'] == $user_object->hash_password($_GET['user_password']))
                {
                    $user_object->setUserId($user_data['user_id']);
                    $user_object->setUserLoginStatus('Login');


                    $_SESSION['user_data'][$user_data['user_id']] = [
                        'id' => $user_data['user_id'],
                        'name' => $user_data['user_name'],
                        'profile' => $user_data['user_profile']
                    ];

                    if($user_object->update_user_login_data())
                    {
                        echo json_encode(['status' => 1]);
                    }else
                    {
                        echo json_encode(['status' => 2, 'error_message' => 'Sth Wrong']);
                    }
                }else
                {
                    echo json_encode(['status' => 2, 'error_message' => 'Wrong password']);
                }
            }else
            {
                echo json_encode(['status' => 2, 'error_message' => 'Verify Your Email']);
            }
        }else
        {
            echo json_encode(['status' => 2, 'error_message' => 'Wrong Email']);
        }
    }else if($_GET['action'] == 'load_chats')
    {
        $user_object = new ChatUser();
        $user_object->setUserId($_GET['user_id']);

        $data = $user_object->get_users_chats_by_id();

        echo json_encode(['status' => 1, 'result' => $data]);
    }else if($_GET['action'] == 'exist_by_email')
    {
        $user_object = new ChatUser();
        $user_object->setUserEmail($_GET['user_email']);

        $user_data = $user_object->get_user_data_by_email();

        if(is_array($user_data) && count($user_data) > 0)
        {
            echo json_encode(['status' => 1, 'user_id' => $user_data['user_id']]);
        }else
        {
            echo json_encode(['status' => 2, 'error_message' => 'cant find this user in DB']);
        }
    }else if($_GET['action'] == 'getEmailById')
    {
        $user_object = new ChatUser();
        $user_object -> setUserId($_GET['user_id']);

        $user_data = $user_object->get_user_data_by_id();
        $user_email = $user_data['user_email'];

        echo json_encode(['status' => 1, 'user_email' => $user_data['user_email']]);
    }else if($_GET['action'] == 'let me public user data!')
    {
        $user_object = new ChatUser();
        $user_object->setUserId($_GET['user_id']);

        $user_data = $user_object->get_user_data_by_id();

        if(!isset($_GET['message_id']))
        {
            echo json_encode(['status' => 1, 'user_name' => $user_data['user_name'], 'user_profile' => $user_data['user_profile']]);
        }else
        {
            echo json_encode(['status' => 1, 'user_name' => $user_data['user_name'], 'user_profile' => $user_data['user_profile'], 'message_id' => $_GET['message_id']]);
        }
    }

    unset($_GET['action']);
}

?>
