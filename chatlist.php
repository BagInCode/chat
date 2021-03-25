<?php

session_start();

if(!isset($_SESSION['user_data']))
{
    header('location:index.php');
}

$user_id = '';

foreach($_SESSION['user_data'] as $key => $value)
{
    $user_id = $value['id'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width-device-width, initial-scale-1, shrink-to-fit-no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Chatroom List | PHP Chat App with WebSockets and MySQL</title>

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

    <style type="text/css">
        html,
        body{
            height: 100%;
            width: 100%;
            margin: 0;
        }
        #wrapper{
            display: flex;
            flex-flow: column;
            height: 100%;
        }
        #remaining{
            flex-grow: 1;
        }
        #message{
            height:200px;
            background: whitesmoke;
            overflow: auto;
        }
        #chat-room-frm{
            margin-top: 10px;
        }
        #user_list{
            height: 650px;
            overflow-y: auto;
        }
        #message_area{
            height: 500px;
            overflow-y: auto;
            background-color: #e6e6e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <br/>
        <h1 class="text-center">PHP Chat App with WebSockets and MySQL</h1>
        <br/>
        <div class="row">
            <div class="col-lg-8">
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
                        <a href="addchat.php" class="btn btn-primary mt-2 mb-2">Create New Chat</a>
                        <h3 style="display: inline-block">Chat Room List</h3>
                    </div>
                    <div class="card-body" id="chat_list_area">

                    </div>
                </div>

            </div>
            <div class="col-lg-4">
                <input type="hidden" name="login_user_id" id="login_user_id" value=""/>
                <p style="display: none" id="testText">testText</p>
                <div class="mt-3 mb-3 text-center">
                    <img src="" width="150" class="img-fluid rounded-circle img-thumbnail" id="user_img_here"/>
                    <h3 class="mt-2" id="user_name_here"></h3>
                    <a href="profile.php" class="btn btn-secondary mt-2 mb-2">Edit</a>

                    <input type="button" class="btn btn-primary mt-2 mb-2" name="logout" id="logout" value="Logout"/>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    $(document).ready(function()
    {
        var user_id = '<?php foreach ($_SESSION['user_data'] as $key => $value ) { echo $value['id']; }?>';

        $.ajax({
            url: "ChatUserController.php",
            method: "GET",
            data: {
                user_id: user_id,
                action: "load_chats"
            },
            success: function(data)
            {
                var response = JSON.parse(data);

                if(response.status == 1)
                {
                    var html_data = "";

                    for(i = 1; i <= response.result[0]; i++)
                    {
                        html_data = html_data +
                                    '<div class="text-center">'+
                                        '<a href="chatroom.php?chat_id='+response.result[i][0]+'" class="text-center btn btn-primary mt-2 mb-2"><b>'+response.result[i][1]+'</b></a>'+
                                    '</div>'+
                                    '</br>';
                    }

                    $('#chat_list_area').append(html_data);
                }else
                {
                    $('#error_message_box').show();
                    $('#error_message').append(responce.error_message);
                }
            }
        })

        $.ajax({
            url: "ChatUserController.php",
            method: "GET",
            data: {
                user_id: user_id,
                action: "let me public user data!"
            },
            success: function(data)
            {
                var response = JSON.parse(data);

                if(response.status == 1)
                {
                    $('#user_name_here').append(response.user_name);
                    document.getElementById('user_img_here').setAttribute('src', response.user_profile);
                }
            }
        })

        $('#logout').click(function ()
        {
            $.ajax({
                url: "ChatUserController.php",
                method: "POST",
                data: {user_id: user_id, action: 'leave'},
                success: function (data)
                {

                    var response = JSON.parse(data);

                    if (response.status == 1) {
                        location = 'index.php';
                        conn.close();
                    }
                }
            })

        })
    });
</script>
</html>
