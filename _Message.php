<?php


class _Message
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
    function getCreatedOn()
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

    function saveMessage()
    {
        $created_on = $this->getCreatedOn();
        $text = $this->getText();
        $chat_id = $this->getChatId();
        $user_id = $this->getUserId();

        $query = "INSERT INTO message_table(text, created_on, chat_id, user_id) 
                                     VALUES(:text, :created_on, :chat_id, :user_id)";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':text', $text);
        $statement->bindParam(':created_on', $created_on);
        $statement->bindParam(':chat_id', $chat_id);
        $statement->bindParam(':user_id', $user_id);

        $result = false;

        try
        {
            if($statement->execute())
            {
                $result = true;
            }else
            {
                $result = false;
            }
        }catch (Exception $error)
        {
            $result = false;

            $error->getMessage();
        }

        return $result;
    }
}