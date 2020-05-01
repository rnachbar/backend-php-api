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

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Search and return users by email and password
     * @param object $data
     * @return array
     */
    public function login(object $data) {
        $user = $this->conn->prepare(
            "SELECT $this->tokens_table.Token AS token, $this->users_table.Id as iduser, $this->credentials_table.Email AS email, $this->users_table.Name as name
            FROM $this->credentials_table 
            LEFT JOIN $this->users_table ON ($this->users_table.Id = $this->credentials_table.UsersId) 
            LEFT JOIN $this->tokens_table ON ($this->tokens_table.UsersId = $this->users_table.Id) 
            WHERE Email = :email AND Password = :password", 
        array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

        // Retornar drink_counter

        $user->execute(array(':email' => $data->email, ':password' => md5($data->password)));
        return $user->fetch(PDO::FETCH_ASSOC);
    }

}
