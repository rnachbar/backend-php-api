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

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Create user
     * @param object $data
     * @return array
     */
    public function create(object $data) {
        try {
            $insertUser = $this->conn->prepare("INSERT INTO $this->users_table (Name) VALUES (:name)");
            $insertUser->execute([':name' => $data->name]);
            $insertUser = null;

            $id = $this->conn->lastInsertId();

            $insertCredential = $this->conn->prepare("INSERT INTO $this->credentials_table (UsersId, Email, Password) VALUES (:userid, :email, :password)");
            $insertCredential->execute([':userid' => $id, ':email' => $data->email, ':password' => md5($data->password)]);
            $insertCredential = null;

            $token = md5(uniqid(rand(), true));

            $insertToken = $this->conn->prepare("INSERT INTO $this->tokens_table (UsersId, Token) VALUES (:userid, :token)");
            $insertToken->execute([':userid' => $id, ':token' => $token]);
            $insertToken = null;

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
            $where = "WHERE ID = :id";
            $execute = [':id' => $id];
        endif;

        $query = $this->conn->prepare("SELECT * FROM $this->users_table $where", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $query->execute($execute);
    
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search and return users by email
     * @param string $email
     * @return int
     */
    public function find(string $email) {
        $credential = $this->conn->prepare("SELECT * FROM $this->credentials_table WHERE Email = :email", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $credential->execute(array(':email' => $email));

        return $credential->rowCount();
    }

}
