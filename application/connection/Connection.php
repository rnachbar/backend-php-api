<?php 

/**
 * Class Connection - Manages the connection to the database
 * Using PDO as a connection method
 * @author Raphael Nachbar
 */

class Connection {

    public $conn;

    # Variables with connection data
    private $host = '127.0.0.1';
    private $user = 'root';
    private $pass = '';
    private $db_name = 'teste';

    /**
     * Connects to the database
     */
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host = $this->host; dbname = $this->db_name", $this->user, $this->pass);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        return $this->conn;
    }

}
