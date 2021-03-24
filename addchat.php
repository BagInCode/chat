<?php

session_start();

if(!isset($_SESSION['user_data']))
{
    header('location:index.php');
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
        var user_id = '<?php foreach ($_SESSION['user_data'] as $key => $value) { echo $value['id']; }?>';

        $('#create_chat_form').parsley();
        $('#create_chat_form').on('submit', function()
        {
            var chat_name = $('#chat_name').val();

            $.ajax({
                url: "ChatController.php",
                method: "POST",
                data:{
                    chat_name: chat_name,
                    action: 'create'
                },
                success: function(data)
                {
                    console.log("first ajax:\n"+data);

                    var response = JSON.parse(data);

                    if(response.status == 1)
                    {
                        $.ajax({
                            url: "ChatToUserController.php",
                            method: "POST",
                            data:{
                                chat_id: response.chat_id,
                                user_id: user_id,
                                action: 'create'
                            },
                            success: function(data)
                            {
                                console.log("second ajax:\n"+data);

                                let response = JSON.parse(data);

                                if(response.status == 1)
                                {
                                    window.location.href = "http://localhost:63342/Chat/chatroom.php?chat_id="+response.chat_id;
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

</body>

</html>
