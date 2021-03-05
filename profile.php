<?php

session_start();

if(!isset($_SESSION['user_data']))
{
    header('location:index.php');
}

require_once ('database/ChatUser.php');

$user_object = new ChatUser;

$user_id = '';

foreach ($_SESSION['user_data'] as $key => $value)
{
    $user_id = $value['id'];
}

$user_object->setUserId($user_id);

$user_data = $user_object->get_user_data_by_id();

$message = "";

if(isset($_POST['edit']))
{
    $user_profile = $_POST['hidden_user_profile'];

    if($_FILES['user_profile']['name'] != '')
    {
        $user_profile = $user_object->upload_image($_FILES['user_profile']);
        $_SESSION['user_data'][$user_id]['profile'] = $user_profile;
    }

    $user_object->setUserName($_POST['user_name']);
    $user_object->setUserEmail($_POST['user_email']);
    $user_object->setUserPassword($_POST['user_password']);
    $user_object->setUserProfile($user_profile);
    $user_object->setUserId($user_id);

    if($user_object->update_data())
    {
        $message = '<div class="alert alert-success">Profile Details Updated</div>';
        $user_data['user_name'] = $user_object->getUserName();
        $user_data['user_email'] = $user_object->getUserEmail();
        $user_data['user_profile'] = $user_object->getUserProfile();
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

    <title>Profile | PHP Chat App with WebSockets and MySQL</title>

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
        <br/>
        <br/>
        <?php
            echo $message;
        ?>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-5">Profile</div>
                    <div class="col-md-5 text-right">
                        <a href="chatroom.php" class="btn btn-warning btn-sm">Go to Chat</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="post" id="profile_form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="user_name" id="user_name" class="form-control" data-parsley-pattern="/^[a-zA-Z\s]+$/" required value="<?php echo $user_data['user_name'];?>"/>
                    </div>
                    <div class="from-group">
                        <label>Email</label>
                        <input type="email" name="user_email" id="user_email" class="form-control" data-parsley-type="email" required readonly value="<?php echo $user_data['user_email']?>"/>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="user_password" id="user_password" class="form-control" data-parsley-minlength="8" data-parsley-maxlength="16" required/>
                    </div>
                    <div class="form-group">
                        <label>Profile</label><br/>
                        <input type="file" name="user_profile" id="user_profile"/><br/>
                        <img src="<?php echo $user_data['user_profile']; ?>" class="img-fluid img-thumbnail mt-3" width="100"/>
                        <input type="hidden" name="hidden_user_profile" value="<?php echo $user_data['user_profile'];?>"/>
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" name="edit" class="btn btn-primary" value="Edit"/>
                    </div>
                </form>
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
