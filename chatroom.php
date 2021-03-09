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

require ("database/ChatTable.php");
$chat_table_object = new ChatTable;

$chat_table_object->setChatId($chat_id);

if(!$chat_table_object->exist_chat())
{
    header('location:chatlist.php');
}

$chatList_object = new ChatTable;
$chatList_object->setChatId($chat_id);

$chat_name = $chatList_object->get_name_by_id();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width-device-width, initial-scale-1, shrink-to-fit-no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Chatroom | PHP Chat App with WebSockets and MySQL</title>

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
                <div class="card">
                    <div class="card-header">
                        <a href="chatlist.php" class="btn btn-primary mt-2 mb-2">Back</a>
                        <?php
                        echo '<a href="addperson.php?chat_id='.$chat_id.'" class="btn btn-primary mt-2 mb-2">Add Person</a>';
                        echo '<h3 class="text-right" style="display: inline-block"> '.$chat_name['name'].'</h3>';
                        ?>
                    </div>
                    <div class="card-body" id="message_area">

                    </div>
                </div>

                <form method="post" id="chat_form">
                    <div class="input-group mb-3">
                        <textarea class="form-control" id="chat_message" name="chat_message" placeholder="Type Message Here" data-parsley-maxlength="512" data-parsley-pattern="/^[\w\s\.!?,:;-]+$/" required></textarea>
                        <div class="input-group-append">
                            <button type="submit" name="send" id="send" class="btn btn-primary">Send</button>
                        </div>
                    </div>
                    <div id="validation_error">

                    </div>
                </form>
            </div>
            <div class="col-lg-4">
                <?php

                $login_user_id = '';

                foreach($_SESSION['user_data'] as $key => $value)
                {
                    $login_user_id = $value['id'];
                    ?>
                    <input type="hidden" name="login_user_id" id="login_user_id" value="<?php echo $login_user_id; ?>"/>
                    <p style="display: none" id="testText">testText</p>
                    <div class="mt-3 mb-3 text-center">
                        <img src="<?php echo $value['profile'];?>" width="150" class="img-fluid rounded-circle img-thumbnail"/>
                        <h3 class="mt-2"><?php echo $value['name'];?></h3>
                        <a href="profile.php" class="btn btn-secondary mt-2 mb-2">Edit</a>

                        <input type="button" class="btn btn-primary mt-2 mb-2" name="logout" id="logout" value="Logout"/>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function()
    {
        var conn = new WebSocket('ws://localhost:8080');
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {
            console.log(e.data);

            var data = JSON.parse(e.data);

            var row_class = '';

            var background_class = '';

            if(data.from == 'Me')
            {
                row_class = "row justify-content-start";
                background_class = 'text-dark alert-light';
            }else
            {
                row_class = 'row justify-content-end';
                background_class = 'alert-success';
            }

            var html_data = "<div class='"+row_class+"'>"+
                                "<div class='col-sm-10'>"+
                                    "<div class='shadow-sm alert "+background_class+"'>"+
                                        "<b>"+data.from+" - </b>"+data.msg+"<br/>"+
                                        "<div class='text-right'>"+
                                            "<small>"+
                                                "<i>"+data.dt+"</i>"+
                                            "</small>"+
                                        "</div>"+
                                    "</div>"+
                                "</div>"+
                            "</div>";

            $('#message_area').append(html_data);
            $('#chat_message').val('');
        };

        $('#chat_form').parsley();

        $('#chat_form').on('submit', function(event){

            event.preventDefault();

            if($('#chat_form').parsley().isValid())
            {
                var user_id = $('#login_user_id').val();

                var message = $('#chat_message').val();

                var data = { userId : user_id, msg : message };

                conn.send(JSON.stringify(data));
            }
        })

        $('#logout').click(function(){

            user_id = $('#login_user_id').val();

            $.ajax({
                url:"action.php",
                method:"POST",
                data:{user_id:user_id, action:'leave'},
                success:function(data)
                {
                    var response = JSON.parse(data);

                    if(response.status == 1)
                    {
                        location = 'index.php';
                        conn.close();
                    }
                }
            })

        });
    });
</script>
</body>
</html>