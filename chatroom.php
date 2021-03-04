<?php

session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width-device-width, initial-scale-1, shrink-to-fit-no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Chatroom | PHP Chat App with WebSockets and MySQL</title>

    <!-- Bootstrap core CSS
    <link href="vendor-front/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="vendor-front/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    -->
    <link rel="stylesheet" type="text/css" href="parsley/src/parsley.css">



    <!-- Bootstrap core JavaScript
    <script src="vendor-front/jquery/jquery.min.js"></script>
    <script src="vendor-front/bootstrap/js/bootstrap.bundle.min.js"></script>
    -->
    <script type="text/javascript" src="parsley/dist/parsley.min.js"></script>

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
            height: 650px;
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
            </div>
            <div class="col-lg-4">
                <?php
                if(!isset($_SESSION['user_data']))
                {
                    die('Sth went wrong');
                }

                foreach($_SESSION['user_data'] as $key => $value)
                {?>
                    <div class="mt-3 mb-3 text-center">
                        <img src="<?php echo $value['profile'];?>" width="150" class="img-fluid rounded-circle img-thumbnail"/>
                        <h3 class="mt-2"><?php echo $value['name'];?></h3>
                        <a href="profile.php" class="btn btn-secondary mt-2 mb-2">Edit</a>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>

<script type="text/javascript">
    $(document).ready(function()
    {
       $('#register_form').parsley();
    });
</script>