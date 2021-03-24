<?php

session_start();

if(!isset($_SESSION['user_data']))
{
    header('location:index.php');
}

$user_id = '';
$user_name = '';
$user_profile = '';

foreach ($_SESSION['user_data'] as $key => $value)
{
    $user_id = $value['id'];
    $user_name = $value['name'];
    $user_profile = $value['profile'];
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
        <div style="display: none" id="error_message_box">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div id="error_message"></div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <div style="display: none" id="success_message_box">
            <div class="alert alert-success">
                <div id="success_message"></div>
            </div>
        </div>
        <br/>
        <br/>
        <h1 class="text-center">PHP Chat App with WebSockets and MySQL</h1>
        <br/>
        <br/>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-5">Profile</div>
                    <div class="col-md-5 text-right">
                        <a href="chatlist.php" class="btn btn-warning btn-sm">Go to Chat List</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="post" id="profile_form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="user_name" id="user_name" class="form-control" data-parsley-pattern="/^[a-zA-Z\s]+$/" required value="<?php echo $user_name;?>"/>
                    </div>
                    <div class="from-group">
                        <label>Email</label>
                        <input type="email" name="user_email" id="user_email" class="form-control" data-parsley-type="email" required readonly value=""/>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="user_password" id="user_password" class="form-control" data-parsley-minlength="8" data-parsley-maxlength="16" required/>
                    </div>
                    <div class="form-group">
                        <label>Profile</label><br/>
                        <input type="file" name="user_profile" id="user_profile"/><br/>
                        <img src="<?php echo $user_profile; ?>" class="img-fluid img-thumbnail mt-3" width="100"/>
                        <input type="hidden" id="hidden_user_profile" name="hidden_user_profile" value="<?php echo $user_profile;?>"/>
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
        var user_id = '<?php echo $user_id; ?>';

        $.ajax({
            url: "ChatUserController.php",
            method: "GET",
            data: {
                user_id: user_id,
                action: 'getEmailById'
            },
            success: function(data)
            {
                var response = JSON.parse(data);

                if(response.status == 1)
                {
                    $('#user_email').val(response.user_email);
                }else
                {
                    $('#error_message_box').show();
                    $('#error_message').append(response.error_message);
                }
            }
        })

        $('#profile_form').parsley();

        $('#profile_form').on('submit', function()
        {

            var temp = false;
            var formD = new FormData(this);
            formD.append('action', 'change');
            formD.append('user_id', user_id);

            $.ajax({
                url: "ChatUserController.php",
                method: "POST",
                data: formD,
                processData: false,
                contentType: false,
                success: function(data)
                {
                    var response = JSON.parse(data);

                    if(response.status == 1)
                    {
                        temp = true;
                    }else
                    {
                        $('#error_message_box').show();
                        $('#error_message').append(response.error_message);
                    }
                }
            })

            if(!temp)
            {
                return false;
            }
        })
    });

</script>
</body>
</html>
