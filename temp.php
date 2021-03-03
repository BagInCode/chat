<?php

$connect = new PDO("mysql:host=localhost; dbname=chat", "root", "rootG_20marder2");

if(! $connect ) {
    die('Could not connect:');
}

echo 'Connected successfully';

$query = 'CREATE TABLE chat_user_table(
    user_id int(11) PRIMARY KEY AUTO_INCREMENT,
    user_name varchar(250) NOT NULL,
    user_email varchar(250) NOT NULL,
    user_password varchar(100) NOT NULL,
    user_profile varchar(100) NOT NULL,
    user_status ENUM(\'Disabled\', \'Enable\') NOT NULL,
    user_created_on DATETIME NOT NULL,
    user_verification_code varchar(100) NOT NULL,
    user_login_status ENUM(\'Logout\', \'Login\')
);';
$statment = $connect->prepare($query);

if($statment->execute())
{
    echo "Table chat_user_table created successfully\n";
}else
{
    die('Could not create Table');
}
?>