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
        var chat_id = '<?php echo $chat_id; ?>';

        $.ajax({
            url: "ChatController.php",
            method: "GET",
            data: {
                chat_id: chat_id,
                action: "exist?"
            },
            success: function(data)
            {
                var response = JSON.parse(data);

                if(response.status == 1)
                {

                }else
                {
                    window.location.href = "http://localhost:63342/Chat/chatlist.php";
                }
            }
        })

        $('#searching_form').parsley();
        $('#searching_form').on('submit', function()
        {
            var user_email = $('#user_email').val();

            $.ajax({
                url: "ChatUserController.php",
                method: "GET",
                data: {
                    user_email: user_email,
                    action: "exist_by_email"
                },
                success: function(data)
                {
                    var response = JSON.parse(data);

                    if(response.status == 1)
                    {
                        $.ajax({
                            url: "ChatToUserController.php",
                            method: "POST",
                            data:{
                                chat_id: chat_id,
                                user_id: response.user_id,
                                action: 'create'
                            },
                            success: function(data)
                            {
                                let response = JSON.parse(data);

                                if(response.status == 1)
                                {
                                    window.location.href = "http://localhost:63342/Chat/chatroom.php?chat_id="+chat_id;
                                }else
                                {
                                    $('#error_message_box').show();
                                    $('#error_message').append(response.error_message);
                                }
                            }
                        })
                    }else
                    {
                        $('#error_message_box').show();
                        $('#error_message').append(response.error_message);
                    }
                }
            })

            return false;
        })
    });

</script>
</html>


