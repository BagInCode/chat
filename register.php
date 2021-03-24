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

    <title>Register | PHP Chat App with WebSockets and MySQL</title>

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
                    <div class="card-header">Register</div>
                    <div class="card-body">

                        <form method="post" id="register_form">

                            <div class="form-group">
                                <label>Enter Your Name</label>
                                <input type="text" name="user_name" id="user_name" class="form-control" data-parsley-patter="/^[a-zA-Z\s]+$" required>
                            </div>

                            <div class="form-group">
                                <label>Enter Your Email</label>
                                <input type="text" name="user_email" id="user_email" class="form-control" data-parsley-type="email" required>
                            </div>

                            <div class="form-group">
                                <label>Enter Your Password</label>
                                <input type="password" name="user_password" id="user_password" class="form-control" data-parsley-minlength="8" data-parsley-maxlength="16" required>
                            </div>

                            <div class="form-group text-center">
                                <input type="submit" name="register" id="register" class="btn btn-success" value="Register">
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
        $('#register_form').parsley();

        $('#register_form').on('submit', function(event)
        {
            if($('#register_form').parsley().isValid())
            {
                var user_name = $('#user_name').val();
                var user_email = $('#user_email').val();
                var user_password = $('#user_password').val();

                $.ajax({
                    url: "ChatUserController.php",
                    method: "POST",
                    data: {
                        user_name: user_name,
                        user_email: user_email,
                        user_password: user_password,
                        action: "register"
                    },
                    success: function(data)
                    {
                        var response = JSON.parse(data);

                        if(response.status == 1)
                        {
                            $('#success_message_box').show();
                            $('#success_message').append(response.success_message);
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
    })

    function redirect()
    {
        var element = document.getElementById('user_verification_code');

        if(typeof(element) != 'undefined' && element != null)
        {
            var code = $('#user_verification_code').val();

            $.ajax({
                url: "ChatUserController.php",
                method: "POST",
                data: {
                    code: code,
                    action: 'verify'
                },
                success: function(data)
                {
                    var response = JSON.parse(data);

                    if(response.status == 2)
                    {
                        $('#error_message').append(response.error_message);
                    }else
                    {
                        window.location.href = "http://localhost:63342/Chat/index.php";
                    }
                }
            })
        }else
        {
            document.getElementById('error_message').value = "";
            document.getElementById('error_message_box').display = "none";
        }
    }
</script>

</body>

</html>