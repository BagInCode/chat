<?php

session_start();

if(!isset($_SESSION['user_data']))
{
    header('location:index.php');
}

require_once ("database/chatUser.php");

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
                <div class="card">
                    <div class="card-header">
                        <a href="addchat.php" class="btn btn-primary mt-2 mb-2">Create New Chat</a>
                        <h3 style="display: inline-block">Chat Room List</h3>
                    </div>
                    <div class="card-body" id="chat_list_area">
                        <?php
                            $user_object = new ChatUser;
                            $user_id = '';
                            foreach($_SESSION['user_data'] as $key => $value)
                            {
                                $user_id = $value['id'];

                                $user_object->setUserId($user_id);

                                $result = $user_object->get_users_chats_by_id();

                                for($i = 1; $i <= $result[0]; $i++)
                                {
                                    echo '<div class="text-center">
                                            <a href="chatroom.php?chat_id='.$result[$i][0].'" class="text-center btn btn-primary mt-2 mb-2"><b>'.$result[$i][1].'</b></a>
                                          </div>
                                          </br>';
                                }
                            }
                        ?>
                    </div>
                </div>

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
</body>

<script>
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
</script>
</html>
