<?php

/**
 * File v1/users.php - Routes for user calls
 * @author Raphael Nachbar
 */

# Includes auxiliaries
include_once 'api/helpers/initialize.php';
include_once 'api/controllers/V1/UsersController.php';

if (isset($method) && $method != null) :
    /**
     * Initializes the user object
     * The value of the variable $conn is assigned in the include 'helpers/database.php'
     */
    $controller = new UsersController($conn);
    
    /**
     * Method POST - Register a new user, without authentication
     * Method GET without ID - Returns list of all active users (who has not deleted their account)
     * Method GET with ID - Returns specific user
     * Method PUT - Edit and update user registration data
     * Method DELETE - Deletes users themselves
     */
    if ($method === 'POST') :
        $post = $validations->postBody($data);

        if (!$post['success']) :
            $response->returnJson(400, $post['message']);
        endif;

        # Register user after validating data
        $create = $controller->create($data);

        if ($create['success']) :
            $response->returnJson(200, 'CREATE.USER.OK');
        else :
            $response->returnJson(400, $create['message']);
        endif;
    elseif ($method === 'GET') :
        # Checks whether ID is being passed in the call
        $parts = explode('/', $_SERVER['REQUEST_URI']);
        $id = end($parts);

        $return = $controller->read(intval($id));
        $message = 'GETUSER.OK';
    elseif ($method === 'PUT') :
        // $return = $controller->update();
        // $message = 'PUTUSER.OK';
    elseif ($method === 'DELETE') :
        // $return = $controller->delete();
        // $message = 'DELETEUSER.OK';
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
