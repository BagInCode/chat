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

    <title>Rgister | PHP Chat App with WebSockets and MySQL</title>

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


</head>
<body>

<div class="container">
    <br/>
    <br/>
    <h1 class="text-center">PHP Chat App with WebSockets and MySQL</h1>

    <div class="row justify-content-md-center">
        <div class="col-md-4">
            <?php
                if(isset($_SESSION['success_message']))
                {
                    echo '
                    <div class="alert alert-success">
                    '.$_SESSION['success_message'].'
                    </div>
                    ';

                    unset($_SESSION['success_message']);
                }
            ?>
        </div>
    </div>
</div>
</body>

</html>