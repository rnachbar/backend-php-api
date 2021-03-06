<?php

/**
 * Class AuthController - Responsible for authenticating the user
 * @author Raphael Nachbar
 */

include_once './api/models/AuthModel.php';

class AuthController {

    private $model;
    private $error;

    public function __construct($conn) {
        $this->model = new AuthModel($conn);
    }

    /**
     * Auth user
     * @param object $data
     * @return array
     */
    function auth(object $data) {
        $this->checkPostData($data);

        if ($this->error) :
            return [
                'success' => false,
                'message' => 'Empty fields to authenticate user.'
            ];
        endif;

        $login = $this->model->login($data);

        if ($login) :
            return [
                'success' => true,
                'data' => $login
            ];
        else :
            return [
                'success' => false,
                'data' => 'User does not exist or password is incorrect.'
            ];
        endif;
    }

    /**
     * Validates body fields
     * @param object $data
     * @return boolean
     */
    private function checkPostData(object $data) {
        if (!isset($data->email) || $data->email == null) :
            $this->error = true;
        endif;

        if (!isset($data->password) || $data->password == null) :
            $this->error = true;
        endif;
    }
        
}
