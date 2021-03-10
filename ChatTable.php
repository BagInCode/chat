<?php


class ChatTable
{
    private $chat_id;
    private $chat_name;
    public $connect;

    public function __construct()
    {
        require_once ('database_connection.php');

        $database_object = new Database_connection;

        $this->connect=$database_object->connect();
    }

    function setChatId($chat_id)
    {
        $this->chat_id=$chat_id;
    }
    function setChatName($chatName)
    {
        $this->chat_name = $chatName;
    }

    function getChatId()
    {
        return $this->chat_id;
    }
    function getChatName()
    {
        return $this->chat_name;
    }

    function getNameById()
    {
        $chat_id = $this->getChatId();

        $query="SELECT chat_name FROM chat_table
                WHERE chat_table.chat_id=:chat_id";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':chat_id', $chat_id);

        $data = array();

        try
        {
            if($statement->execute())
            {
                $data = $statement->fetch(PDO::FETCH_ASSOC);
            }
        }catch(Exception $error)
        {
            echo $error->getMessage();
        }

        return $data;
    }
    function getIdByName()
    {
        $query = "SELECT MAX(chat_id) FROM chat_table WHERE chat_table.chat_name=:chat_name";

        $chat_name = $this->getChatName();

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':chat_name', $chat_name);

        $data = array();

        try
        {
            if($statement->execute())
            {
                $data = $statement->fetch(PDO::FETCH_ASSOC);

                $data = $data['MAX(chat_id)'];
            }else
            {
                return false;
            }
        }catch(Exception $error)
        {
            $error->getMessage();
        }

        return $data;
    }
    function existChat()
    {
        $query = "SELECT * 
                  FROM chat_table 
                  WHERE chat_table.chat_id=:chat_id;";

        $chat_id = $this->getChatId();

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':chat_id', $chat_id);

        try
        {
            if($statement->execute())
            {
                if($statement->rowCount() > 0)
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
    }
    function createChat()
    {
        $query = "
        INSERT INTO chat_table (chat_name)
        VALUES (:chat_name)";

        $chat_name = $this->getChatName();

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':chat_name', $chat_name);

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