<?php


class ChatUser
{
    private $user_id;
    private $user_name;
    private $user_email;
    private $user_password;
    private $user_profile;
    private $user_status;
    private $user_created_on;
    private $user_verification_code;
    private $user_login_status;
    public $connect;

    public function __construct()
    {
        require_once ('database_connection.php');

        $database_object = new Database_connection;

        $this->connect=$database_object->connect();
    }

    function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
    function setUserName($user_name)
    {
        $this->user_name = $user_name;
    }
    function setUserEmail($user_email)
    {
        $this->user_email=$user_email;
    }
    function setUserPassword($user_password)
    {
        $this->user_password = $user_password;
    }
    function setUserProfile($user_profile)
    {
        $this->user_profile=$user_profile;
    }
    function setUserStatus($user_status)
    {
        $this->user_status=$user_status;
    }
    function setUserCreatedOn($user_created_on)
    {
        $this->user_created_on = $user_created_on;
    }
    function setUserVerificationCode($user_verification_code)
    {
        $this->user_verification_code = $user_verification_code;
    }
    function setUserLoginStatus($user_login_status)
    {
        $this->user_login_status=$user_login_status;
    }

    function getUserId()
    {
        return $this->user_id;
    }
    function getUserName()
    {
        return $this->user_name;
    }
    function getUserEmail()
    {
        return $this->user_email;
    }
    function getUserPassword()
    {
        return $this->user_password;
    }
    function getUserProfile()
    {
        return $this->user_profile;
    }
    function getUserStatus()
    {
        return $this->user_status;
    }
    function getUserCreatedOn()
    {
        return $this->user_created_on;
    }
    function getUserVerificationCode()
    {
        return $this->user_verification_code;
    }
    function getUserLoginStatus()
    {
        return $this->user_login_status;
    }

    function make_avatar($character)
    {
        $path = "images/".time().".png";
        $image = imagecreate(200, 200);

        $red = rand(0, 255);
        $green = rand(0, 255);
        $blue = rand(0, 255);

        imagecolorallocate($image, $red, $green, $blue);
        $textcolor = imagecolorallocate($image, 255, 255, 255);

        $font = dirname(__FILE__).'/font/RobotoSlab-VariableFont_wght.ttf';

        imagettftext($image, 100, 0, 55, 150, $textcolor, $font, $character);
        imagepng($image, $path);
        imagedestroy($image);
        return $path;
    }

    function get_user_data_by_email()
    {
        $query = "
        SELECT * from chat_user_table Where user_email = :user_email
        ";

        $statement = $this->connect->prepare($query);

        $statement->bindParam(':user_email', $this->user_email);

        try
        {
            if ($statement->execute()) {
                $user_data = $statement->fetch(PDO::FETCH_ASSOC);
            } else {
                $user_data = array();
            }
        }catch (Exception $error)
        {
            echo $error->getMessage();
        }

        return $user_data;
    }

    function get_user_data_by_id()
    {
        $query = "
        SELECT * from chat_user_table Where user_id = :user_id
        ";

        $statement = $this->connect->prepare($query);

        $statement->bindParam(':user_id', $this->user_id);

        try
        {
            if ($statement->execute()) {
                $user_data = $statement->fetch(PDO::FETCH_ASSOC);
            } else {
                $user_data = array();
            }
        }catch (Exception $error)
        {
            echo $error->getMessage();
        }

        return $user_data;
    }

    function hash_password($user_password)
    {
        $key = 257;
        $step = 1;
        $result = 0;
        $mod = 1000000000+7;

        for($i = 0; $i < strlen($user_password); $i++)
        {
            $result = ($result + $step * ord($user_password[$i])) % $mod;
            $step = ($step * $key) % $mod;
        }

        return $result;
    }

    function save_data()
    {
        $this->user_password = $this->hash_password($this->user_password);

        $query = "
        INSERT INTO chat_user_table (user_name, user_email, user_password, user_profile, user_status, user_created_on, user_verification_code)
        VALUES (:user_name, :user_email, :user_password, :user_profile, :user_status, :user_created_on, :user_verification_code)
        ";

        $statement = $this->connect->prepare($query);

        $statement->bindParam(':user_name', $this->user_name);
        $statement->bindParam(':user_email', $this->user_email);
        $statement->bindParam(':user_password', $this->user_password);
        $statement->bindParam(':user_profile', $this->user_profile);
        $statement->bindParam(':user_status', $this->user_status);
        $statement->bindParam(':user_created_on', $this->user_created_on);
        $statement->bindParam(':user_verification_code', $this->user_verification_code);

        if($statement->execute())
        {
            return true;
        }else
        {
            return false;
        }
    }

    function is_valid_email_verification_code()
    {
        $query = "
        SELECT * FROM chat_user_table
        WHERE user_verification_code = :user_verification_code
        ";

        $statment = $this->connect->prepare($query);
        $statment->bindParam(':user_verification_code', $this->user_verification_code);
        $statment->execute();

        if($statment->rowCount() > 0)
        {
            return true;
        }else
        {
            return false;
        }
    }

    function enable_user_account()
    {
        $query = "
            UPDATE chat_user_table
            SET user_status = :user_status, user_login_status = :user_login_status
            WHERE user_verification_code = :user_verification_code
            ";

        $Logout = "Logout";

        $statement = $this->connect->prepare($query);

        $statement->bindParam(':user_status', $this->user_status);
        $statement->bindParam(':user_verification_code', $this->user_verification_code);
        $statement->bindParam(':user_login_status', $Logout);

        if($statement->execute())
        {
            return true;
        }else
        {
            return false;
        }
    }

    function update_user_login_data()
    {
        $query = "
        UPDATE chat_user_table
        SET user_login_status = :user_login_status
        WHERE user_id = :user_id
        ";

        $statement = $this->connect->prepare($query);

        $statement->bindParam(':user_login_status', $this->user_login_status);
        $statement->bindParam(':user_id', $this->user_id);

        if($statement->execute())
        {
            return true;
        }else
        {
            return false;
        }
    }

    function upload_image($user_profile)
    {
        $extension = explode('.', $user_profile['name']);
        $new_name = rand().'.'.$extension[1];
        $destination = 'images/'.$new_name;
        move_uploaded_file($user_profile['tmp_name'], $destination);
        return $destination;
    }

    function update_data()
    {
        $query = "
        UPDATE chat_user_table
        SET user_name = :user_name,
            user_email = :user_email,
            user_password = :user_password,
            user_profile = :user_profile
        WHERE user_id = :user_id
        ";

        $this->user_password = $this->hash_password($this->user_password);

        $statement = $this->connect->prepare($query);

        $statement->bindParam(':user_name', $this->user_name);
        $statement->bindParam(':user_email', $this->user_email);
        $statement->bindParam(':user_password', $this->user_password);
        $statement->bindParam(':user_profile', $this->user_profile);
        $statement->bindParam(':user_id', $this->user_id);

        if($statement->execute())
        {
            return true;
        }else
        {
            return false;
        }
    }

    function get_users_chats_by_id()
    {
        $query = "SELECT DISTINCT * FROM chat_table
        WHERE EXISTS (SELECT * FROM chat_to_user_table
                     WHERE chat_to_user_table.chat_id = chat_table.chat_id
                       AND chat_to_user_table.user_id = :user_id)";

        $statement = $this->connect->prepare($query);

        $statement->bindParam(':user_id', $this->user_id);

        $result = array();

        try
        {
            if($statement->execute())
            {
                $result[0] = $statement->rowCount();

                for($i = 0; $i < $statement->rowCount(); $i++)
                {
                    $data = $statement->fetch(PDO::FETCH_ASSOC);

                    $result[$i+1][1] = $data['chat_name'];
                    $result[$i+1][0] = $data['chat_id'];
                }
            }else
            {
                $data = array();
            }
        }catch(Exception $error)
        {
            echo $error->getMessage();
        }

        return $result;
    }

    function getIdByEmail()
    {
        $query = "SELECT user_id 
                  FROM chat_user_table
                  WHERE chat_user_table.user_email=:user_email";

        $user_email = $this->getUserEmail();

        $statement=$this->connect->prepare($query);
        $statement->bindParam(':user_email', $user_email);

        $data = array();

        try
        {
            if($statement->execute())
            {
                if($statement->rowCount() > 0)
                {
                    $data = $statement->fetch(PDO::FETCH_ASSOC);
                    $data = $data['user_id'];
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

        return $data;
    }
}