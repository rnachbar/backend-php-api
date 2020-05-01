<?php

/**
 * Class UsersController - Manages user calls
 * @author Raphael Nachbar
 */

include_once './api/models/Users.php';

class UsersController {

    private $user;

    public function __construct($conn) {
        $this->user = new Users($conn);
    }

    /**
     * GET users
     * @param int $id
     * @return array
     */
    function read(Int $id) {
        return $this->user->readUsers($id);
    }
        
}