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

if(!$chat_table_object->existChat())
{
    header('location:chatlist.php');
}

$chatList_object = new ChatTable;
$chatList_object->setChatId($chat_id);

$chat_name = $chatList_object->getNameById();

require ("database/chatUser.php");

$user_object = new ChatUser();

$user_id = '';

foreach($_SESSION['user_data'] as $key => $value)
{
    $user_id = $value['id'];
}

$user_object->setUserId($user_id);
$data = $user_object->get_user_data_by_id();

$user_name = $data['user_name'];
$user_profile = $data['user_profile'];

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
                <div class="card" id="try_scroll_here">
                    <div class="card-header">
                        <a href="chatlist.php" class="btn btn-primary mt-2 mb-2">Back</a>
                        <?php
                        echo '<a href="addperson.php?chat_id='.$chat_id.'" class="btn btn-primary mt-2 mb-2">Add Person</a>';
                        echo '<h3 class="text-right" style="display: inline-block"> '.$chat_name['chat_name'].'</h3>';
                        ?>
                    </div>
                    <div class="card-body" id="message_area">

                        <?php
                            require ("database/_Message.php");

                            $message_object = new _Message();

                            $message_object->setId(0);
                            $message_object->setChatId($chat_id);
                            $result = $message_object->loadMessage();

                            $html_data = "";

                            for($i = 1; $i <= $result[0]['rowCount']; $i++)
                            {
                                $from = "";
                                $background = "";
                                $row = "";
                                $msgID = $result[$i]['id'];

                                if($result[$i]['user_id'] == $user_id)
                                {
                                    $from = "Me";
                                    $row = "row justify-content-start";
                                    $background = 'text-dark alert-light';
                                }else
                                {
                                    $user_object = new ChatUser;
                                    $user_object->setUserId($result[$i]['user_id']);

                                    $data = $user_object->get_user_data_by_id();

                                    $from = $data['user_name'];
                                    $row = 'row justify-content-end';
                                    $background = 'alert-success';
                                }

                                $html_add = "<div class='".$row."' id='block_message_".$msgID."'>
                                                <div class='col-sm-10'>
                                                    <div class='shadow-sm alert ".$background."' id='message_".$msgID."'>
                                                        <b>".$from." - </b>".$result[$i]['text']."<br/>
                                                        <div class='text-right'>
                                                            <small>
                                                                 <i>".$result[$i]['created_on']."</i>
                                                            </small>
                                                        </div>";

                                if($from == "Me")
                                {
                                    $html_add.="
                                                    <input type='button' class='btn btn-secondary' name='edit_message_".$msgID."' id='edit_message_".$msgID."' onclick='editMessage(this.id)' value='Edit'>
                                                    <input type='button' class='btn btn-secondary' name='delete_message_".$msgID."' id='delete_message_".$msgID."' onclick='deleteMessage(this.id)' value='Delete'>";
                                }

                                $html_add.="
                                                  </div>
                                                </div>
                                               </div>";

                                $html_data = $html_add.$html_data;
                            }

                            echo $html_data;

                        ?>
                        <input type="hidden" name="count_message" id="count_message" value="<?php echo $result[0]['rowCount'] ?>">
                        <input type="hidden" name="load_more" id="load_more" value="<?php if($result[0]['rowCount'] < 10){echo 0;}else{echo 1;}?>"/>
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
                <input type="hidden" name="login_user_id" id="login_user_id" value="<?php echo $user_id; ?>"/>
                <input type="hidden" name="chat_id" id="chat_id" value="<?php echo $chat_id; ?>"/>
                <p style="display: none" id="testText">testText</p>
                <div class="mt-3 mb-3 text-center">
                    <img src="<?php echo $user_profile;?>" width="150" class="img-fluid rounded-circle img-thumbnail"/>
                    <h3 class="mt-2"><?php echo $user_name;?></h3>
                    <a href="profile.php" class="btn btn-secondary mt-2 mb-2">Edit</a>

                    <input type="button" class="btn btn-primary mt-2 mb-2" name="logout" id="logout" value="Logout"/>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function()
    {
        var scrollLoadDelt = document.getElementById('message_area').scrollHeight / parseInt($('#count_message').val(), 10);
        document.getElementById('message_area').scrollTop = document.getElementById('message_area').scrollHeight;
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

            var scrollDown = false;

            if(document.getElementById('message_area').scrollTop >=
                document.getElementById('message_area').scrollHeight -
                document.getElementById('message_area').clientHeight - 10)
            {
                scrollDown = true;
            }

            var html_data = "<div class='"+row_class+"' id='block_message_"+data.message_id+"'>"+
                                "<div class='col-sm-10'>"+
                                    "<div class='shadow-sm alert "+background_class+"' id='message_"+data.message_id+"'>"+
                                        "<b>"+data.from+" - </b>"+data.msg+"<br/>"+
                                        "<div class='text-right'>"+
                                            "<small>"+
                                                "<i>"+data.dt+"</i>"+
                                            "</small>"+
                                        "</div>";

            if(data.from == 'Me')
            {
                html_data += "<input type='button' class='btn btn-secondary' name='edit_message_"+data.message_id+"' id='edit_message_"+data.message_id+"' onclick='editMessage(this.id)' value='Edit'>"+
                             "<input type='button' class='btn btn-secondary' name='delete_message_"+data.message_id+"' id='delete_message_"+data.message_id+"' onclick='deleteMessage(this.id)' value='Delete'>";
            }

            html_data += "</div>"+
                         "</div>"+
                         "</div>";

            $('#message_area').append(html_data);
            $('#chat_message').val('');

            if(scrollDown)
            {
                document.getElementById('message_area').scrollTop = document.getElementById('message_area').scrollHeight;
            }

            if(data.cntMsgDelt > 0)
            {
                var cnt_msg = parseInt($('#count_message').val(), 10);
                cnt_msg += $data.cntMsgDelt;
                document.getElementById('count_message').setAttribute('value', cnt_msg.toString());
            }
        };

        $('#message_area').scroll(function()
        {
            var SH = document.getElementById('message_area').scrollHeight;

            if(document.getElementById('message_area').scrollTop == 0 &&
                $('#load_more').val() == '1')
            {
                var cnt_msg = parseInt($('#count_message').val(), 10);
                var chat_id = $('#chat_id').val();

                $.ajax({
                    url:"action.php",
                    method: "POST",
                    data: {
                        chat_id: chat_id,
                        cnt_msg: cnt_msg,
                        action: "load"
                    },
                    success:function(data)
                    {
                        var response = JSON.parse(data);

                        if(response.status == 1)
                        {
                            var html_data = document.getElementById('message_area').innerHTML;

                            var user_id = $('#login_user_id').val();

                            for(let i = 1; i <= response.result[0]['rowCount']; i++)
                            {
                                row_class = "";
                                background_claa = "";
                                from = "";
                                message_id = response.result[i]['id'];
                                msg = response.result[i]['text'];
                                dt = response.result[i]['created_on'];

                                if(response.result[i]['user_id'] == user_id)
                                {
                                    from = "Me";
                                    row_class = "row justify-content-start";
                                    background_class = 'text-dark alert-light';
                                }else
                                {
                                    from = response.result[i]['user_name'];
                                    row_class = 'row justify-content-end';
                                    background_class = 'alert-success';
                                }

                                var html_add = "<div class='"+row_class+"' id='block_message_"+message_id+"'>"+
                                    "<div class='col-sm-10'>"+
                                    "<div class='shadow-sm alert "+background_class+"' id='message_"+message_id+"'>"+
                                    "<b>"+from+" - </b>"+msg+"<br/>"+
                                    "<div class='text-right'>"+
                                    "<small>"+
                                    "<i>"+dt+"</i>"+
                                    "</small>"+
                                    "</div>";

                                if(from == 'Me')
                                {
                                    html_add += "<input type='button' class='btn btn-secondary' name='edit_message_"+message_id+"' id='edit_message_"+message_id+"' onclick='editMessage(this.id)' value='Edit'>"+
                                                "<input type='button' class='btn btn-secondary' name='delete_message_"+message_id+"' id='delete_message_"+message_id+"' onclick='deleteMessage(this.id)' value='Delete'>";
                                }

                                html_add += "</div>"+
                                            "</div>"+
                                            "</div>";

                                html_data = html_add + html_data;
                            }

                            cnt_msg += response.result[0]['rowCount'];

                            if(response.result[0]['rowCount'] < 10)
                            {
                                document.getElementById('load_more').setAttribute('value', '0');
                            }

                            document.getElementById('message_area').innerHTML = html_data;

                            var SHnew = document.getElementById('message_area').scrollHeight;

                            document.getElementById('message_area').scrollTop = SHnew - SH;
                            document.getElementById('count_message').setAttribute('value', cnt_msg.toString());
                        }
                    }
                })
            }
        })

        $('#chat_form').parsley();

        $('#chat_form').on('submit', function(event){

            event.preventDefault();

            if($('#chat_form').parsley().isValid())
            {
                var user_id = $('#login_user_id').val();

                var message = $('#chat_message').val();

                var chat_id = $('#chat_id').val();

                var data = { userId : user_id, msg : message, chatId : chat_id };

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

    function editMessage(id)
    {
        alert("edit message id: "+id);
    }

    function deleteMessage(id)
    {
        var ID = '';
        var i

        for(i = 15; i < (id.length); i++)
        {
            ID = ID+id[i];
        }

        ID = parseInt(ID, 10);

        $.ajax({
            url: "action.php",
            method: "POST",
            data: {
                id: ID,
                action: 'delete'
            },
            success:function(data)
            {
                var response = JSON.parse(data);

                if(response.status == 1)
                {
                    $('#block_message_'+ID).hide();

                    var cnt_msg = parseInt($('#count_message').val(), 10);
                    cnt_msg -= 1;
                    document.getElementById('count_message').setAttribute('value', cnt_msg.toString());
                }
            }
        })
    }
</script>
</body>
</html>