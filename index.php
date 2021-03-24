<?php

session_start();

if(isset($_SESSION['user_data']))
{
    header('location:chatlist.php');
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
                            <input type="submit" name="login" id="login" class="btn btn-primary" value="Login"/>
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

        $('#login_form').on('submit', function(event)
        {
            event.preventDefault();

            if($('#login_form').parsley().isValid())
            {
                var user_email = $('#user_email').val();
                var user_password = $('#user_password').val();
                var session_id = '<?php echo session_id(); ?>';

                $.ajax({
                    url: "ChatUserController.php",
                    method: "GET",
                    data: {
                        user_email: user_email,
                        user_password: user_password,
                        session_id: session_id,
                        action: "login"
                    },
                    success: function(data)
                    {
                        var response = JSON.parse(data);

                        if(response.status == 1)
                        {
                            window.location.href = "http://localhost:63342/Chat/chatlist.php";
                        }else
                        {
                            $('#error_message_box').show();
                            $('#error_message').append(response.error_message);
                        }
                    }
                })
            }

            return false;
        })
    });

</script>
</body>

</html>