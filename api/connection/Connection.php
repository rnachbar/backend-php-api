<?php 

/**
 * Class Connection - Manages the connection to the database
 * Using PDO as a connection method
 * @author Raphael Nachbar
 */

class Connection {

    public $conn;

    # Variables with connection data
    private $host = 'localhost';
    private $db_name = 'app_drink_water';
    private $user = 'root';
    private $pass = '';

    /**
     * Connects to the database
     * @return object
     */
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->user, $this->pass);
            $this->conn->exec("set names utf8");
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        return $this->conn;
    }

}
