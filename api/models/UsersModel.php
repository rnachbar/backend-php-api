<?php

/**
 * Model Users - Takes care of database and user integration
 * @author Raphael Nachbar
 */

class UsersModel {

    private $conn;

    # Declaring tables
    private $users_table = 'Users';
    private $credentials_table = 'Credentials';
    private $tokens_table = 'Tokens';
    private $drink_counter_table = 'DrinkCounter';
    private $historic_table = 'Historic';

    public function __construct($conn) {
        # Connection to the database
        $this->conn = $conn;
    }

    /**
     * Create new user and drink counter
     * @param object $data
     * @return array
     */
    public function create(object $data) {
        try {
            # Insert user
            $user = $this->conn->prepare("INSERT INTO $this->users_table (Name) VALUES (:name)", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $user->execute([':name' => $data->name]);

            $user_id = $this->conn->lastInsertId();

            # Insert credential to users
            $credential = $this->conn->prepare("INSERT INTO $this->credentials_table (UsersId, Email, Password) VALUES (:userid, :email, :password)", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $credential->execute([':userid' => $user_id, ':email' => $data->email, ':password' => md5($data->password)]);

            # Insert token to user
            $token = md5(uniqid(rand(), true));

            $insert_token = $this->conn->prepare("INSERT INTO $this->tokens_table (UsersId, Token) VALUES (:userid, :token)", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $insert_token->execute([':userid' => $user_id, ':token' => $token]);

            # Create Drink Counter do user, with values equal zero
            $drink_counter = $this->conn->prepare("INSERT INTO $this->drink_counter_table (UsersId, Counter, ML) VALUES (:userid, :counter, :ml)", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
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
     * Search and return user
     * If receive ID, only return the user of that ID, if exist
     * Otherwise, returns all users
     * @param int $id
     * @return array
     */
    public function read(Int $id = null) {
        try {
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
        } catch(PDOException $e)   {
            return $e->getMessage();
        }  
    }

    /**
     * Update user data
     * @param object $data
     * @param int $id
     * @return array
     */
    public function update(object $data, int $id) {
        try {
            $message = '';

            if (isset($data->email)) :
                # Validates if email exists before update
                $email = $this->conn->prepare("SELECT UsersId FROM $this->credentials_table WHERE Email = :email", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $email->execute([':email' => $data->email]);

                $result = $email->fetch(PDO::FETCH_ASSOC);

                if (!$result) :
                    # If email does not exist, update the email field
                    $update_email = $this->conn->prepare("UPDATE $this->credentials_table SET Email = :email WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $update_email->execute([':email' => $data->email, ':userid' => $id]);

                    $message .= 'Updated email/';
                else :
                    # If email exists, do not update the email field
                    $message .= 'E-mall has not been updated since the new email already exists/';
                endif;
            endif;

            if (isset($data->name)) :
                $update_name = $this->conn->prepare("UPDATE $this->users_table SET Name = :name, UpdatedAt = :updatedat WHERE Id = :id", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $update_name->execute([':name' => $data->name, ':id' => $id, ':updatedat' => date('Y-m-d H:i:s')]);

                $message .= 'Updated name OK/';
            endif;

            if (isset($data->password)) :
                $update_password = $this->conn->prepare("UPDATE $this->credentials_table SET Password = :password WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $update_password->execute([':password' => md5($data->password), ':userid' => $id]);

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
            # Delete Token
            $delete_token = $this->conn->prepare("DELETE FROM $this->tokens_table WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $delete_token->execute([':userid' => $id]);

            # Delete Credential
            $delete_credential = $this->conn->prepare("DELETE FROM $this->credentials_table WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $delete_credential->execute([':userid' => $id]);

            # Delete Counter Historic
            $delete_counter = $this->conn->prepare("DELETE FROM $this->historic_table WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $delete_counter->execute([':userid' => $id]);

            # Delete Counter
            $delete_counter = $this->conn->prepare("DELETE FROM $this->drink_counter_table WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $delete_counter->execute([':userid' => $id]);

            # Delete User
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
     * Update counter
     * @param object $data
     * @param int user_id
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

            $drink_counter = $this->conn->prepare("UPDATE $this->drink_counter_table SET Counter = :counter, UpdatedAt = :updatedat WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $drink_counter->execute([':counter' => $counter, ':userid' => $user_id, ':updatedat' => date('Y-m-d H:i:s')]);

            # Update Drink ML, if exist in the body
            if (isset($data->drink_ml)) :
                $ml = intval($data->drink_ml);
                $ml += intval($drink_result['ML']);

                $drink_ml = $this->conn->prepare("UPDATE $this->drink_counter_table SET Ml = :ml, UpdatedAt = :updatedat WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $drink_ml->execute([':ml' => $ml, ':userid' => $user_id, ':updatedat' => date('Y-m-d H:i:s')]);

                # Insert historic of ML
                $historic = $this->conn->prepare("INSERT INTO $this->historic_table (UsersId, ML) VALUES (:userid, :ml)", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $historic->execute([':userid' => $user_id, ':ml' => intval($data->drink_ml)]);
            endif;

            # Returns updated data
            $drink = $this->conn->prepare(
                "SELECT Counter, ML, $this->users_table.Id, $this->users_table.Name
                FROM $this->drink_counter_table 
                LEFT JOIN $this->users_table ON ($this->users_table.Id = $this->drink_counter_table.UsersId) 
                WHERE UsersId = :userid", 
                array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)
            );

            $drink->execute([':userid' => $user_id]);
            $result = $drink->fetch(PDO::FETCH_ASSOC);

            # Get user email
            $email = $this->conn->prepare("SELECT Email FROM $this->credentials_table WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $email->execute([':userid' => $user_id]);

            $email_result = $email->fetch(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'message' => 'Drink Counter Add.',
                'data' => [
                    'iduser' => $result['Id'],
                    'email' => $email_result['Email'],
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
     * Search and return historic of users
     * @param int $id
     * @return array
     */
    public function getHistoric(int $id) {
        try {
            $historic = $this->conn->prepare("SELECT * FROM $this->historic_table WHERE UsersId = :userid", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $historic->execute([':userid' => $id]);

            $historic_result = $historic->fetchAll(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'message' => '',
                'data' => $historic_result
            ];
        } catch(PDOException $e)   {
            return [
                'success' => false,
                'message' => $sql . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Ranking of users who drank most water on the current day
     * @return array
     */
    public function getRanking() {
        try {
            $dt_init = date('Y-m-d 00:00:00');
            $dt_end = date('Y-m-d 23:59:59');

            $data = $this->conn->prepare("SELECT * FROM $this->historic_table WHERE CreatedAt BETWEEN '$dt_init' AND '$dt_end'", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $data->execute([]);

            $result = $data->fetchAll(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'message' => '',
                'data' => $result
            ];
        } catch(PDOException $e)   {
            return [
                'success' => false,
                'message' => $sql . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * Search and return row count of users, by email
     * @param string $email
     * @return int
     */
    public function find(string $email) {
        $credential = $this->conn->prepare("SELECT * FROM $this->credentials_table WHERE Email = :email", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $credential->execute([':email' => $email]);

        return $credential->rowCount();
    }

    /**
     * Search and return row count of users, by id
     * @param string $email
     * @return int
     */
    public function findById(int $id) {
        $credential = $this->conn->prepare("SELECT * FROM $this->users_table WHERE Id = :id", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $credential->execute([':id' => $id]);

        return $credential->rowCount();
    }

}
