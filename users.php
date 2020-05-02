<?php

/**
 * File v1/users.php - Routes for user calls
 * @author Raphael Nachbar
 */

# Includes auxiliaries
include_once 'api/helpers/initialize.php';
include_once 'api/controllers/V1/BaseController.php';
include_once 'api/controllers/V1/UsersController.php';

if (isset($method) && $method != null) :
    /**
     * Initializes the user object
     * The value of the variable $conn is assigned in the include 'helpers/database.php'
     */
    $base = new BaseController($conn);
    $controller = new UsersController($conn);

    /**
     * Method POST - Register a new user, without authentication
     * Method GET without ID - Returns list of all active users (who has not deleted their account)
     * Method GET with ID - Returns specific user
     * Method PUT - Edit and update user registration data
     * Method DELETE - Deletes users themselves
     */
    if ($method === 'POST') :
        # Checks whether ID is being passed in the call
        $parts = explode('/', basename(dirname($_SERVER['REQUEST_URI'])));
        $id = end($parts);

        if ($id != '' && intval($id) > 0) :
            $authorization = $base->Authorization();

            if (!$authorization['success']) :
                $response->returnJson(400, $authorization['message']);
            endif;
            
            $data = (object) $data;
            $add_drink = $controller->addDrink($data, intval($id), $authorization);

            if ($add_drink['success']) :
                $response->returnJson(200, '', $add_drink['data']);
            else :
                $response->returnJson(400, $add_drink['message']);
            endif;
        else :
            $post = $validations->postBody($data);

            if (!$post['success']) :
                $response->returnJson(400, $post['message']);
            endif;

            $create = $controller->create($data);

            if ($create['success']) :
                $response->returnJson(200, 'CREATE.USER.OK');
            else :
                $response->returnJson(400, $create['message']);
            endif;
        endif;
    elseif ($method === 'GET') :
        $authorization = $base->Authorization();

        if (!$authorization['success']) :
            $response->returnJson(400, $authorization['message']);
        endif;

        $read = $controller->read($_SERVER['REQUEST_URI']);

        if ($read) :            
            $response->returnJson(200, 'GET.USER.OK', $read);
        else :
            $response->returnJson(400, 'User not found/Incorrect ID.');
        endif;
    elseif ($method === 'PUT') :
        $authorization = $base->Authorization();

        if (!$authorization['success']) :
            $response->returnJson(400, $authorization['message']);
        endif;

        $data = (object) $data;
        $update = $controller->update($data, $_SERVER['REQUEST_URI'], $authorization);

        if ($update['success']) :
            $response->returnJson(200, $update['message']);
        else :
            $response->returnJson(400, $update['message']);
        endif;
    elseif ($method === 'DELETE') :
        $authorization = $base->Authorization();

        if (!$authorization['success']) :
            $response->returnJson(400, $authorization['message']);
        endif;

        $delete = $controller->delete($_SERVER['REQUEST_URI'], $authorization);

        if ($delete['success']) :
            $response->returnJson(200, $delete['message']);
        else :
            $response->returnJson(400, $delete['message']);
        endif;
    else :
        $response->returnJson(400, 'No methods allowed');
    endif;
else :
    /**
     * If no method is recognized in the call
     * Returns error 400 with message
     */
    $response->returnJson(400, 'No methods found');
endif;
