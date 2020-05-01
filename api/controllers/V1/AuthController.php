<?php

/**
 * Class AuthController - Responsavel por autenticar o usuário
 * @author Raphael Nachbar
 */

class AuthController {

    private $conn;
    private $table_name = 'Credentials';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function read() {
        $query = "SELECT * FROM $this->table_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        
}