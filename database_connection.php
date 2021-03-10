<?php

class Database_connection
{
    function connect()
    {
        $connect = new PDO("mysql:host=127.0.0.1; dbname=chat", "user", "user_password");

        return $connect;
    }
}
