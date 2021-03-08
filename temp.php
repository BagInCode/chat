<?php
/*
$stringValue = "some string";

$key = 257;
$step = 0;

for($i = 0; $i < strlen($stringValue); $i++)
{
    echo ord($stringValue[$i]).'<br>';
}

unset($_SESSION['user_data']);
*/

$connect = new PDO("mysql:host=localhost; dbname=chat", "root", "rootG_20marder2");

if(! $connect ) {
    die('Could not connect:');
}

echo 'Connected successfully';
$query = "";

/*
$query = 'CREATE TABLE chat_table(
    chat_id int(11) PRIMARY KEY AUTO_INCREMENT,
    chat_name varchar(250) NOT NULL,
    user_email varchar(250) NOT NULL,
    user_password varchar(100) NOT NULL,
    user_profile varchar(100) NOT NULL,
    user_status ENUM(\'Disabled\', \'Enable\') NOT NULL,
    user_created_on DATETIME NOT NULL,
    user_verification_code varchar(100) NOT NULL,
    user_login_status ENUM(\'Logout\', \'Login\')
);';
*/
/*
$query = 'CREATE TABLE chat_table(
    chat_id int(11) PRIMARY KEY AUTO_INCREMENT,
    chat_name varchar(250) NOT NULL
);';
*/
/*
$query = 'CREATE TABLE chat_to_user_table(
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    chat_id int(11) NOT NULL REFERENCES chat_table(chat_id),
    user_id int(11) NOT NULL REFERENCES chat_user_table(user_id)
);';

    SELECT DISTINCT * FROM chat_table
        WHERE EXIST (SELECT * FROM chat_to_user_table
                     WHERE chat_to_user_table.chat_id = chat_table.chat_id
                       AND chat_to_user_table.user_id = :user_id)


*/

$query = 'CREATE TABLE message_table(
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    created_on datetime NOT NULL,
    text varchar(1000) NOT NULL,
    chat_id int(11) NOT NULL,
    user_id int(11) NOT NULL
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