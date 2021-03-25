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
                    <div class="card-header" id="card-header">
                        <a href="chatlist.php" class="btn btn-primary mt-2 mb-2">Back</a>

                    </div>
                    <div class="card-body" id="message_area">
                        <input type="hidden" name="count_message" id="count_message" value="">
                        <input type="hidden" name="load_more" id="load_more" value=""/>
                    </div>
                </div>

                <form method="post" id="chat_form">
                    <div class="input-group mb-3">
                        <textarea class="form-control" id="chat_message" name="chat_message" placeholder="Type Message Here" data-parsley-maxlength="512" data-parsley-pattern="/^[\w\s\.!?,:;-]+$/" required></textarea>
                        <div class="input-group-append">
                            <button type="submit" name="send" id="send" class="btn btn-primary">Send</button>
                        </div>
                        <input type="hidden" name="id_for_edit" id="id_for_edit" value="0">
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
                    <img src="" width="150" class="img-fluid rounded-circle img-thumbnail" id="user_img_here"/>
                    <h3 class="mt-2" id="user_name_here"></h3>
                    <a href="profile.php" class="btn btn-secondary mt-2 mb-2">Edit</a>
                    <input type="button" class="btn btn-primary mt-2 mb-2" name="logout" id="logout" value="Logout"/>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function()
    {
        document.getElementById('message_area').scrollTop = document.getElementById('message_area').scrollHeight;
        var conn = new WebSocket('ws://localhost:8080');
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        var user_id = '<?php foreach ($_SESSION['user_data'] as $key => $value ) { echo $value['id']; }?>';
        var chat_id = '<?php echo $chat_id; ?>';
        var this_user_name = '';

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

        $.ajax({
            url: "ChatUserController.php",
            method: "GET",
            data: {
                user_id: user_id,
                action: "let me public user data!"
            },
            async: false,
            success: function(data)
            {
                var response = JSON.parse(data);

                if(response.status == 1)
                {
                    this_user_name = response.user_name;
                    $('#user_name_here').append(response.user_name);
                    document.getElementById('user_img_here').setAttribute('src', response.user_profile);
                }
            }
        })

        $.ajax({
            url: "MessageController.php",
            method: "GET",
            data: {
                chat_id: chat_id,
                message_id: '0',
                action: 'load_message'
            },
            success: function(data)
            {
                var response = JSON.parse(data);

                if(response.status == 1)
                {
                    var html_data = "";

                    for(i = 1; i <= response.result[0]['rowCount']; i++)
                    {
                        var from = "";
                        var background = "";
                        var row = "";
                        var msgID = response.result[i]['id'];

                        if(response.result[i]['user_id'] == user_id)
                        {
                            from = "Me";
                            row = "row justify-content-start";
                            background = 'text-dark alert-light';
                        }else
                        {
                            from = "Unknow...";

                            $.ajax({
                                url: "ChatUserController.php",
                                method: "GET",
                                data:{
                                    user_id: response.result[i]['user_id'],
                                    message_id: msgID,
                                    action: 'let me public user data!'
                                },
                                async: false,
                                success: function(data)
                                {
                                    var input = JSON.parse(data);

                                    if(input.status == 1)
                                    {
                                        from = input.user_name;
                                    }
                                }
                            })

                            row = 'row justify-content-end';
                            background = 'alert-success';
                        }

                        html_add = "<div class='"+row+"' id='block_message_"+msgID+"'>"+
                                    "<div class='col-sm-10'>"+
                                    "<div class='shadow-sm alert "+background+"' id='message_"+msgID+"'>"+
                                    "<b>"+from+"</b> - <span id='message_text_"+msgID+"'>"+response.result[i]['text']+"</span><br/>"+
                                    "<div class='text-right'>"+
                                    "<small>"+
                                    "<i>"+response.result[i]['created_on']+"</i>"+
                                    "</small>"+
                                    "</div>";

                    if(from == "Me")
                    {
                        html_add += "<input type='button' class='btn btn-secondary' name='edit_message_"+msgID+"' id='edit_message_"+msgID+"' onclick='editMessage(this.id)' value='Edit'>"+
                                    "<input type='button' class='btn btn-secondary' name='delete_message_"+msgID+"' id='delete_message_"+msgID+"' onclick='deleteMessage(this.id)' value='Delete'>";
                    }

                        html_add+="</div>"+
                                "</div>"+
                                "</div>";

                        html_data = html_add + html_data;
                    }

                    $('#message_area').append(html_data);
                    document.getElementById('count_message').setAttribute('value', response.result[0]['rowCount'].toString());

                    if(response.result[0]['rowCount'] < 10)
                    {
                        document.getElementById('load_more').setAttribute('value', "0");
                    }else
                    {
                        document.getElementById('load_more').setAttribute('value', "1");
                    }
                }

                document.getElementById('message_area').scrollTop = document.getElementById('message_area').scrollHeight;
            }
        })

        $.ajax({
            url: "ChatController.php",
            method: "GET",
            data:{
                chat_id: chat_id,
                action: "get chat name"
            },
            success: function(data)
            {
                var response = JSON.parse(data)

                if(response.status == 1)
                {
                    var html = '<a href="addperson.php?chat_id='+chat_id+'" class="btn btn-primary mt-2 mb-2">Add Person</a>'+
                                '<h3 class="text-right" style="display: inline-block"> '+response.chat_name+'</h3>';

                    $('#card-header').append(html);
                }
            }
        })

        conn.onmessage = function(e)
        {

            var data = JSON.parse(e.data);

            if(data.status == 1)
            {
                data = data.data;

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
                    "<b>"+data.from+" - </b><span id='message_text_"+data.message_id+"'>"+data.message+"</span><br/>"+
                    "<div class='text-right'>"+
                    "<small>"+
                    "<i>"+data.created_on+"</i>"+
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
            }else if(data.status == 0)
            {
                document.getElementById("message_text_"+data.data['message_id']).innerText = data.data['message'];
            }
        };

        $('#message_area').scroll(function()
        {
            var SH = document.getElementById('message_area').scrollHeight;

            if(document.getElementById('message_area').scrollTop == 0 &&
                $('#load_more').val() == '1')
            {

                var cnt_msg = parseInt($('#count_message').val(), 10);

                $.ajax({
                    url:"MessageController.php",
                    method: "GET",
                    data: {
                        chat_id: chat_id,
                        message_id: cnt_msg,
                        action: 'load_message'
                    },
                    success:function(data)
                    {
                        var response = JSON.parse(data);

                        if(response.status == 1)
                        {
                            var html_data = document.getElementById('message_area').innerHTML;

                            for(let i = 1; i <= response.result[0]['rowCount']; i++)
                            {
                                var row_class = "";
                                var background_class = "";
                                var from = "";
                                var message_id = response.result[i]['id'];
                                var msg = response.result[i]['text'];
                                var dt = response.result[i]['created_on'];

                                if(response.result[i]['user_id'] == user_id)
                                {
                                    from = "Me";
                                    row_class = "row justify-content-start";
                                    background_class = 'text-dark alert-light';
                                }else
                                {
                                    from = "Unknow...";

                                    $.ajax({
                                        url: "ChatUserController.php",
                                        method: "GET",
                                        data:{
                                            user_id: response.result[i]['user_id'],
                                            action: 'let me public user data!'
                                        },
                                        async: false,
                                        success: function(data)
                                        {
                                            var input = JSON.parse(data);

                                            if(input.status == 1)
                                            {
                                                from = input.user_name;
                                            }
                                        }
                                    })

                                    row_class = 'row justify-content-end';
                                    background_class = 'alert-success';
                                }

                                var html_add = "<div class='"+row_class+"' id='block_message_"+message_id+"'>"+
                                    "<div class='col-sm-10'>"+
                                    "<div class='shadow-sm alert "+background_class+"' id='message_"+message_id+"'>"+
                                    "<b>"+from+" - </b><span id='message_text_"+message_id+"'>"+msg+"</span><br/>"+
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
                var message_id = $('#id_for_edit').val();
                document.getElementById('id_for_edit').value = '0';

                var user_id = $('#login_user_id').val();

                var message = $('#chat_message').val();

                var chat_id = $('#chat_id').val();

                var user_name = this_user_name;

                if(message_id == 0)
                {
                    var data = {user_id: user_id, message: message, chat_id: chat_id, user_name: user_name, action: "save"};

                    $.ajax({
                        url: "MessageController.php",
                        method: "POST",
                        data: data,
                        success: function(data)
                        {
                            conn.send(data);
                        }
                    })
                }else
                {
                    var data = {message_id: message_id, user_id: user_id, message: message, chat_id: chat_id, user_name: user_name, action: "edit"};

                    $.ajax({
                        url: "MessageController.php",
                        method: "POST",
                        data: data,
                        success: function(data)
                        {
                            conn.send(data);
                        }
                    })
                }
            }
        })

        $('#logout').click(function ()
        {
            $.ajax({
                url: "ChatUserController.php",
                method: "POST",
                data: {user_id: user_id, action: 'leave'},
                success: function (data) {
                    var response = JSON.parse(data);

                    if (response.status == 1) {
                        location = 'index.php';
                        conn.close();
                    }
                }
            })

        })
    });

    function editMessage(id)
    {
        var ID = "";
        var i;

        for(i = 13; i < (id.length); i++)
        {
            ID = ID+id[i];
        }

        document.getElementById("id_for_edit").setAttribute('value', ID);
        document.getElementById("chat_message").value = document.getElementById('message_text_'+ID).innerText;
    }

    function deleteMessage(id)
    {
        var ID = '';
        var i;

        for(i = 15; i < (id.length); i++)
        {
            ID = ID+id[i];
        }

        ID = parseInt(ID, 10);

        $.ajax({
            url: "MessageController.php",
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