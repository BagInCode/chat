<?php


class ChatToUserTable
{
    private $chat_id;
    private $user_id;
    public $connect;


    public function __construct()
    {
        require_once ('database_connection.php');

        $database_object = new Database_connection;

        $this->connect=$database_object->connect();
    }

    function setChatId($chat_id)
    {
        $this->chat_id = $chat_id;
    }
    function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    function getChatId()
    {
        return $this->chat_id;
    }
    function getUserId()
    {
        return $this->user_id;
    }

    function checkForSecondaryAdding()
    {
        $query = "SELECT id
                  FROM chat_to_user_table
                  WHERE chat_to_user_table.chat_id=:chat_id 
                    AND chat_to_user_table.user_id=:user_id";

        $user_id = $this->getUserId();
        $chat_id = $this->getChatId();

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':user_id', $user_id);
        $statement->bindParam(':chat_id', $chat_id);

        try
        {
            if($statement->execute())
            {
                if($statement->rowCount() === 0)
                {
                    return true;
                }else
                {
                    return false;
                }
            }else
            {
                return false;
            }

        }catch(Exception $error)
        {
            $error->getMessage();
        }

        return false;
    }

    function addPerson()
    {
        $query = "
        INSERT INTO chat_to_user_table (user_id, chat_id)
        VALUES (:user_id, :chat_id)
        ";

        $user_id = $this->getUserId();
        $chat_id = $this->getChatId();

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':chat_id', $chat_id);
        $statement->bindParam(':user_id', $user_id);

        try
        {
            if($statement->execute())
            {
                return true;
            }else
            {
                return false;
            }
        }catch(Exception $error)
        {
            $error->getMessage();
        }
    }

}