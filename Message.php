<?php


class Message
{
    private $id;
    private $created_on;
    private $text;
    private $chat_id;
    private $user_id;
    public $connect;

    public function __construct()
    {
        require_once ('database_connection.php');

        $database_object = new Database_connection;

        $this->connect=$database_object->connect();
    }

    function setId($id)
    {
        $this->id = $id;
    }
    function setCreatedOn($created_on)
    {
        $this->created_on = $created_on;
    }
    function setText($text)
    {
        $this->text = $text;
    }
    function setChatId($chat_id)
    {
        $this->chat_id = $chat_id;
    }
    function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
    
    function getId()
    {
        return $this->id;
    }
    function getCreatedOd()
    {
        return $this->created_on;
    }
    function getText()
    {
        return $this->text;
    }
    function getChatId()
    {
        return $this->chat_id;
    }
    function getUserId()
    {
        return $this->user_id;
    }
}