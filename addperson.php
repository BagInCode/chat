<?php

session_start();

if(!isset($_SESSION['user_data']))
{
    header('location:index.php');
}

if(!isset($_GET['chat_id']))
{
    header('location:chatlist.php');
}

$chat_id = $_GET['chat_id'];
unset($_GET['chat_id']);

require_once ("database/ChatTable.php");
$chat_table_object = new ChatTable;

$chat_table_object->setChatId($chat_id);

if(!$chat_table_object->existChat())
{
    header('location:chatlist.php');
}

$error = "";

if(isset($_POST['add']))
{
    require_once("database/chatUser.php");

    $user_object = new ChatUser;

    $user_object->setUserEmail($_POST['user_email']);

    $user_id = 0;

    if(($user_id = $user_object->getIdByEmail()) !== false)
    {
        require_once ("database/ChatToUserTable.php");

        $chat_to_user_object = new ChatToUserTable;

        $chat_to_user_object->setUserId($user_id);
        $chat_to_user_object->setChatId($chat_id);

        if($chat_to_user_object->checkForSecondaryAdding())
        {
            if ($chat_to_user_object->addPerson())
            {
                header('location:chatroom.php?chat_id=' . $chat_id);
            }else
            {
                $error = "There is sth wrong...";
            }
        }else
        {
            $error = "This person already in this chat";
        }
    }else
    {
        $error = "There is no person with this email in this app...";
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

    <title>Add Person | PHP Chat App with WebSockets and MySQL</title>

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
            <div class="card-header">Add</div>
            <div class="card-body">
                <form method="post" id="searching_form">
                    <div class="form-group">
                        <label>Enter Email Address</label>
                        <input type="text" name="user_email" id="user_email" class="form-control" data-parsley-type="email" required/>
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" name="add" id="add" class="btn btn-primaty" value="Add"/>
                        <?php
                            echo '<a href="chatroom.php?chat_id='.$chat_id.'" class="btn btn-secondary mt-2 mb-2">Back</a>'
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

<script>

    $(document).ready(function()
    {
        $('#searching_form').parsley();
    });

</script>
</html>


