<?php

/**
 * Model Users - Takes care of database and user integration
 * @author Raphael Nachbar
 */

class UsersModel {

    private $conn;

    private $users_table = 'Users';
    private $credentials_table = 'Credentials';
    private $tokens_table = 'Tokens';
    private $drink_counter_table = 'DrinkCounter';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Create new user and drink counter
     * @param object $data
     * @return array
     */
    public function create(object $data) {
        try {
            # Insert User
            $user = $this->conn->prepare("INSERT INTO $this->users_table (Name) VALUES (:name)");
            $user->execute([':name' => $data->name]);

            $user_id = $this->conn->lastInsertId();

            # Insert Credential
            $credential = $this->conn->prepare("INSERT INTO $this->credentials_table (UsersId, Email, Password) VALUES (:userid, :email, :password)");
            $credential->execute([':userid' => $user_id, ':email' => $data->email, ':password' => md5($data->password)]);

            $token = md5(uniqid(rand(), true));

            # Insert Token
            $insert_token = $this->conn->prepare("INSERT INTO $this->tokens_table (UsersId, Token) VALUES (:userid, :token)");
            $insert_token->execute([':userid' => $user_id, ':token' => $token]);

            # Insert Drink Counter
            $drink_counter = $this->conn->prepare("INSERT INTO $this->drink_counter_table (UsersId, Counter, ML) VALUES (:userid, :counter, :ml)");
            $drink_counter->execute([':userid' => $user_id, ':counter' => 0, ':ml' => 0]);

            return [
                'success' => true,
                'message' => 'User created.'
            ];
        } catch(PDOException $e)   {
            return [
                'success' => false,
                'message' => $sql . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Search and return users
     * If receive ID, only return the user of that ID, if exist
     * @param int $id
     * @return array
     */
    public function read(Int $id = null) {
        $where = '';
        $execute = [];

        if ($id != null) :
            $where = "WHERE $this->users_table.Id = :id";
            $execute = [':id' => $id];
        endif;

        $query = $this->conn->prepare(
            "SELECT $this->users_table.Id as iduser, $this->users_table.Name as name, $this->credentials_table.Email as email, $this->drink_counter_table.Counter as drink_counter, $this->drink_counter_table.ML as drink_ml 
            FROM $this->users_table 
            LEFT JOIN $this->credentials_table ON ($this->credentials_table.UsersId = $this->users_table.Id) 
            LEFT JOIN $this->drink_counter_table ON ($this->drink_counter_table.UsersId = $this->users_table.Id) 
            $where", 
            array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)
        );

        $query->execute($execute);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update user data
     * @param object $data
     * @return array
     */
    public function update(object $data, int $id) {
        try {
            $message = '';

            if (isset($data->email)) :
                # Validates if email exists and does not belong to the current user
                $email = $this->conn->prepare("SELECT UsersId FROM $this->credentials_table WHERE Email = :email", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $email->execute([':email' => $data->email]);

                $result = $email->fetch(PDO::FETCH_ASSOC);

                if (!$result) :
                    $updateEmail = $this->conn->prepare("UPDATE $this->credentials_table SET Email = :email WHERE UsersId = :userid");
                    $updateEmail->execute([':email' => $data->email, ':userid' => $id]);

                    $message .= 'Updated email/';
                else :
                    $message .= 'E-mall has not been updated since the new email already exists/';
                endif;
            endif;

            if (isset($data->name)) :
                $updateEmail = $this->conn->prepare("UPDATE $this->users_table SET Name = :name WHERE Id = :id");
                $updateEmail->execute([':name' => $data->name, ':id' => $id]);

                $message .= 'Updated name OK/';
            endif;

            if (isset($data->password)) :
                $updateEmail = $this->conn->prepare("UPDATE $this->credentials_table SET Password = :password WHERE UsersId = :userid");
                $updateEmail->execute([':password' => md5($data->password), ':userid' => $id]);

                $message .= 'Password name OK.';
            endif;
  
            return [
                'success' => true,
                'message' => $message
            ];
        } catch(PDOException $e)   {
            return [
                'success' => false,
                'message' => $sql . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete user by ID
     * @param int $id
     * @return array
     */
    public function delete(Int $id) {
        try {
            $delete_token = $this->conn->prepare("DELETE FROM $this->tokens_table WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $delete_token->execute([':userid' => $id]);

            $delete_credential = $this->conn->prepare("DELETE FROM $this->credentials_table WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $delete_credential->execute([':userid' => $id]);

            $delete_counter = $this->conn->prepare("DELETE FROM $this->drink_counter_table WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $delete_counter->execute([':userid' => $id]);

            $delete_user = $this->conn->prepare("DELETE FROM $this->users_table WHERE Id = :id", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $delete_user->execute([':id' => $id]);
  
            return [
                'success' => true,
                'message' => 'Deleted user.'
            ];
        } catch(PDOException $e)   {
            return [
                'success' => false,
                'message' => $sql . ': ' . $e->getMessage()
            ];
        }        
    }

    /**
     * 
     * @param object $data
     * @return array
     */
    public function addDrink(object $data, int $user_id) {
        try {
            $drink = $this->conn->prepare("SELECT Counter, ML FROM $this->drink_counter_table WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $drink->execute([':userid' => $user_id]);

            $drink_result = $drink->fetch(PDO::FETCH_ASSOC);

            # Update Drink Counter
            $counter = 1;
            $counter += intval($drink_result['Counter']);

            $drink_counter = $this->conn->prepare("UPDATE $this->drink_counter_table SET Counter = :counter WHERE UsersId = :userid");
            $drink_counter->execute([':counter' => $counter, ':userid' => $user_id]);

            if (isset($data->drink_ml)) :
                $ml = intval($data->drink_ml);
                $ml += intval($drink_result['ML']);

                $drink_ml = $this->conn->prepare("UPDATE $this->drink_counter_table SET Ml = :ml WHERE UsersId = :userid");
                $drink_ml->execute([':ml' => $ml, ':userid' => $user_id]);
            endif;

            $drink = $this->conn->prepare(
                "SELECT Counter, ML, $this->users_table.Id, $this->users_table.Name
                FROM $this->drink_counter_table 
                LEFT JOIN $this->users_table ON ($this->users_table.Id = $this->drink_counter_table.UsersId) 
                WHERE UsersId = :userid", 
                array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)
            );

            $drink->execute([':userid' => $user_id]);
            $result = $drink->fetch(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'message' => 'Drink Counter Add.',
                'data' => [
                    'iduser' => $result['Id'],
                    // 'email' => $result['Email'],
                    'name' => $result['Name'],
                    'drink_counter' => $result['Counter'],
                    'drink_ml' => $result['ML']
                ]
            ];
        } catch(PDOException $e)   {
            return [
                'success' => false,
                'message' => $sql . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Search and return users by email
     * @param string $email
     * @return int
     */
    public function find(string $email) {
        $credential = $this->conn->prepare("SELECT * FROM $this->credentials_table WHERE Email = :email", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $credential->execute([':email' => $email]);

        return $credential->rowCount();
    }

    /**
     * Search and return users by id
     * @param string $email
     * @return int
     */
    public function findById(int $id) {
        $credential = $this->conn->prepare("SELECT * FROM $this->users_table WHERE Id = :id", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $credential->execute([':id' => $id]);

        return $credential->rowCount();
    }

}
