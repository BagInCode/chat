<?php

session_start();

if(!isset($_SESSION['user_data']))
{
    header('location:index.php');
}

$success_message = "";
$error = "";

require ("database/ChatTable.php");

$chat_list_object = new ChatTable;

if(isset($_POST['create_chat']))
{
    $chat_name = $_POST['chat_name'];
    unset($_POST['chat_name']);

    $chat_list_object->setChatName($chat_name);

    if(!$chat_list_object->createChat())
    {
        $error = "Cant create chat";
    }else
    {
        $chat_id = $chat_list_object->getIdByName();
        $user_id = "NaN";

        foreach ($_SESSION['user_data'] as $key => $value)
        {
            $user_id = $value['id'];
        }

        if($user_id === "NaN")
        {
            $error = "there is sth wrong";
        }else
        {
            require_once ("database/ChatToUserTable.php");

            $chat_to_user_object = new ChatToUserTable;

            $chat_to_user_object->setChatId($chat_id);
            $chat_to_user_object->setUserId($user_id);

            if(!$chat_to_user_object->addPerson())
            {
                $error = "Cant connect you to new chat";
            }else
            {
                $success_message = "We have done it!";
                header("location:chatroom.php?chat_id='.$chat_id.'");
            }
        }
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

    <title>Create chat | PHP Chat App with WebSockets and MySQL</title>

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
        <div class="col col-md-4-mt-5">
            <?php
            if($error != '')
            {
                echo '
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            '.$error.'
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        ';
            }

            if($success_message != "")
            {
                echo '
                        <div class="alert alert-success">
                        '.$success_message.'
                        </div>
                        ';

                header('location:index.php');
            }
            ?>
            <div class="card">
                <div class="card-header">
                    <a href="chatlist.php" class="btn btn-primary mt-2 mb-2">Back</a>
                    <h3 style="display: inline-block">Create chat</h3>
                </div>
                <div class="card-body">

                    <form method="post" id="create_chat_form">

                        <div class="form-group">
                            <label>Enter Chat Name</label>
                            <input type="text" name="chat_name" id="chat_name" class="form-control" data-parsley-patter="/^[a-zA-Z\s]+$" required>
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" name="create_chat" class="btn btn-success" value="Create">
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
        $('#create_chat_form').parsley();
    });
</script>

</body>

</html>
