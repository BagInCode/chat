<?php


$error = '';

session_start();

if(isset($_SESSION['user_data']))
{
    header('location:chatroom.php');
}

if(isset($_POST['login']))
{
    require_once ('database/chatUser.php');

    $user_object = new ChatUser;

    $user_object->setUserEmail($_POST['user_email']);

    $user_data = $user_object->get_user_data_by_email();

    if(is_array($user_data) && count($user_data) > 0)
    {
        if($user_data['user_status'] == 'Enable')
        {
            if($user_data['user_password'] == $user_object->hash_password($_POST['user_password']))
            {
                $user_object->setUserId($user_data['user_id']);
                $user_object->setUserLoginStatus('Login');

                if($user_object->update_user_login_data())
                {
                    $_SESSION['user_data'][$user_data['user_id']] = [
                            'id' => $user_data['user_id'],
                            'name' => $user_data['user_name'],
                            'profile' => $user_data['user_profile']
                    ];

                    header('location:chatroom.php');
                }
            }else
            {
                $error = "Wrong Password";
            }
        }else
        {
            $error = "Verify your Email";
        }
    }else
    {
        $error = "Wron Email Address";
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width-device-width, initial-scale-1, shrink-to-fit-no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login | PHP Chat App with WebSockets and MySQL</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <!--
    <link href="vendor-front/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    -->
    <link rel="stylesheet" type="text/css" href="parsley/src/parsley.css">

    <!-- Bootstrap core JavaScript-->
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

    <script src="parsley/dist/parsley.min.js"></script>


</head>
<body>

<div class="container">
    <br/>
    <br/>
    <h1 class="text-center">PHP Chat App with WebSockets and MySQL</h1>

    <div class="row justify-content-md-center">
        <div class="col-md-4">
            <?php
                if(isset($_SESSION['success_message']))
                {
                    echo '
                    <div class="alert alert-success">
                    '.$_SESSION['success_message'].'
                    </div>
                    ';

                    unset($_SESSION['success_message']);
                }

                if($error != '')
                {
                    echo '
                    <div class="alert alert-danger">
                    '.$error.'
                    </div>
                    ';
                }
            ?>
            <div class="card">
                <div class="card-header">Login</div>
                <div class="card-body">
                    <form method="post" id="login_form">
                        <div class="form-group">
                            <label>Enter Your Email Address</label>
                            <input type="text" name="user_email" id="user_email" class="form-control" data-parsley-type="email" required/>
                        </div>

                        <div class="form-group">
                            <label>Enter Your Password</label>
                            <input type="password" name="user_password" id="user_password" class="form-control" data-parsley-minlength="8" data-parsley-maxlength="16" required>
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" name="login" id="login" class="btn btn-primaty" value="Login"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function()
    {
        $('#login_form').parsley();
    });

</script>
</body>

</html>