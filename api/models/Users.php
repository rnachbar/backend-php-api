<?php

/**
 * Model Users - Takes care of database and user integration
 * @author Raphael Nachbar
 */

class Users {

    private $conn;
    private $table_name = 'Users';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Search and return users
     * If receive ID, only return the user of that ID, if exist
     * @param int $id
     * @return array
     */
    public function readUsers(Int $id = null) {
        $where = '';
        if ($id != null) :
            $where = "WHERE ID = $id";
        endif;

        $query = "SELECT * FROM $this->table_name $where";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}