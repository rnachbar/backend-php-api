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
     * CREATE new  user
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
    function read(string $uri) {
        # Checks whether ID is being passed in the call
        $parts = explode('/', $uri);
        $id = end($parts);

        if ($id != '' && intval($id) <= 0) :
            return false;
        endif;

        $read = $this->model->read(intval($id));

        if (intval($id) > 0 && count($read) > 0) :
            $read = $read[0];
        endif;

        return $read;
    }

    /**
     * UPDATE user
     * @param object $data
     * @return array
     */
    function update(object $data, string $uri, array $authorization) {
        # Checks id the ID is being passed in the call
        $parts = explode('/', $uri);
        $id = end($parts);

        if ($id == '' || intval($id) <= 0) :
            return [
                'success' => false,
                'message' => 'Incorrect ID.'
            ];
        endif;

        $find = $this->model->findById($id);

        if ($find <= 0) :
            return [
                'success' => false,
                'message' => 'User not found.'
            ];
        endif;

        if (intval($id) != intval($authorization['user']['Id'])) :
            return [
                'success' => false,
                'message' => 'ID does not match the user token.'
            ];
        endif;

        $update = $this->model->update($data, intval($id));

        return [
            'success' => true,
            'message' => $update['message']
        ];
    }

    /**
     * DELETE user
     * @param object $data
     * @return array
     */
    function delete(string $uri, array $authorization) {
        # Checks id the ID is being passed in the call
        $parts = explode('/', $uri);
        $id = end($parts);

        if ($id == '' || intval($id) <= 0) :
            return [
                'success' => false,
                'message' => 'Incorrect ID.'
            ];
        endif;

        $find = $this->model->findById($id);

        if ($find <= 0) :
            return [
                'success' => false,
                'message' => 'User not found.'
            ];
        endif;

        if (intval($id) != intval($authorization['user']['Id'])) :
            return [
                'success' => false,
                'message' => 'ID does not match the user token.'
            ];
        endif;

        $delete = $this->model->delete(intval($id));

        return [
            'success' => true,
            'message' => $delete['message']
        ];
    }

    /**
     * Validates body fields
     * @param object $data
     * @return array
     */
    public function addDrink(object $data, int $id, array $authorization) {
        if ($id != intval($authorization['user']['Id'])) :
            return [
                'success' => false,
                'message' => 'ID does not match the user token.'
            ];
        endif;

        $add = $this->model->addDrink($data, intval($authorization['user']['Id']));

        return [
            'success' => $add['success'],
            'message' => $add['message'],
            'data' => $add['data'],
        ];
    }

    /**
     * Get user historic of drinks
     * @param array $authorization
     * @param int $id
     * @return array
     */
    public function historic(array $authorization, int $id) {
        if ($id != intval($authorization['user']['Id'])) :
            return [
                'success' => false,
                'message' => 'ID does not match the user token.'
            ];
        endif;

        $add = $this->model->getHistoric(intval($authorization['user']['Id']));

        $data = [];
        foreach ($add['data'] as $result) :
            $data[] = [
                'drink_ml' => $result['ML'],
                'created_at' => $result['CreatedAt']
            ];
        endforeach;

        return [
            'success' => $add['success'],
            'message' => $add['message'],
            'data' => $data,
        ];
    }

    /**
     * Ranking of users who drank most water on the current day
     * @return array
     */
    public function ranking() {
        $ranking = $this->model->getRanking();

        # Sorting records by user
        $users = [];

        foreach ($ranking['data'] as $key => $data) : 
            $users[$data['UsersId']]['ID'] = $data['UsersId'];
            $users[$data['UsersId']][] = $data['ML'];
        endforeach;

        foreach ($users as $key => $user) :
            $total = 0;

            foreach ($user as $k => $value) :
                $total += intval($value);
            endforeach;

            # Correcting total value
            $total -= intval($users[$key]['ID']);

            # Dividing to have resulted in milliliters
            $totalML = $total / 0.0010000;

            $read = $this->model->read(intval($users[$key]['ID']));

            $return[] = [
                'iduser' => $read[0]['iduser'],
                'name' => $read[0]['name'],
                'email' => $read[0]['email'],
                'totalML' => $totalML
            ];
        endforeach;

        # Sorting values
        usort($return, function($a, $b) {
            return $b['totalML'] <=> $a['totalML'];
        });

        return [
            'success' => $ranking['success'],
            'message' => $ranking['message'],
            'data' => $return,
        ];
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