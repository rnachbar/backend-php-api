<?php

/**
 * Model Auth - Takes care of database and authetication integration
 * @author Raphael Nachbar
 */

class AuthModel {

    private $conn;

    private $users_table = 'Users';
    private $credentials_table = 'Credentials';
    private $tokens_table = 'Tokens';
    private $drink_counter_table = 'DrinkCounter';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Search users by email and password to authenticate
     * @param object $data
     * @return array
     */
    public function login(object $data) {
        $user = $this->conn->prepare(
            "SELECT $this->tokens_table.Token AS token, $this->users_table.Id as iduser, $this->credentials_table.Email AS email, $this->users_table.Name as name, $this->drink_counter_table.Counter as drink_counter, $this->drink_counter_table.ML as drink_ml
            FROM $this->credentials_table 
            LEFT JOIN $this->users_table ON ($this->users_table.Id = $this->credentials_table.UsersId) 
            LEFT JOIN $this->tokens_table ON ($this->tokens_table.UsersId = $this->users_table.Id) 
            LEFT JOIN $this->drink_counter_table ON ($this->drink_counter_table.UsersId = $this->users_table.Id) 
            WHERE Email = :email AND Password = :password", 
            array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)
        );

        $user->execute([':email' => $data->email, ':password' => md5($data->password)]);
        return $user->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Checks whether the token exists
     * @param string $token
     * @return int
     */
    public function checkToken(string $token) {
        $user = $this->conn->prepare("SELECT * FROM $this->tokens_table WHERE Token = :token", array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user->execute([':token' => $token]);

        return $user->rowCount();
    }

    /**
     * Returns user ID by token
     * @param string $token
     * @return array
     */
    public function checkUserToken(string $token) {
        $user = $this->conn->prepare(
            "SELECT $this->users_table.Id FROM $this->tokens_table 
            LEFT JOIN $this->users_table ON ($this->users_table.Id = $this->tokens_table.UsersId) 
            WHERE Token = :token", 
            array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)
        );

        $user->execute([':token' => $token]);
        return $user->fetch(PDO::FETCH_ASSOC);
    }

}
