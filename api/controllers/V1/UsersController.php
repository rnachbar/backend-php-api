<?php

/**
 * Class UsersController - Manages user calls
 * @author Raphael Nachbar
 */

include_once './api/models/UsersModel.php';

class UsersController {

    private $model;
    private $error;

    public function __construct($conn) {
        $this->model = new UsersModel($conn);
    }

    /**
     * CREATE user
     * @param object $data
     * @return array
     */
    function create(object $data) {
        $this->checkPostData($data);

        if ($this->error) :
            return [
                'success' => false,
                'message' => 'Empty fields to create user.'
            ];
        endif;

        $find = $this->model->find($data->email);

        if ($find > 0) :
            return [
                'success' => false,
                'message' => 'User already registered.'
            ];
        endif;

        $create = $this->model->create($data);

        return [
            'success' => $create['success'],
            'message' => $create['message']
        ];
    }

    /**
     * GET user(s)
     * If receiving ID returns specific user, otherwise returns all users
     * @param int $id
     * @return object
     */
    function read(int $id) {
        return $this->model->read($id);
    }

    /**
     * Validates body fields
     * @param object $data
     */
    private function checkPostData(object $data) {
        if (!isset($data->email) || $data->email == null) :
            $this->error = true;
        endif;

        if (!isset($data->name) || $data->name == null) :
            $this->error = true;
        endif;

        if (!isset($data->password) || $data->password == null) :
            $this->error = true;
        endif;
    }
        
}