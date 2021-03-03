<?php

class Database_connection
{
    function connect()
    {
        $connect = new PDO("mysql:host=localhost; dbname=chat", "root", "rootG_20marder2");

        return $connect;
    }
}
