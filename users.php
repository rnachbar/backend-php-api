<?php

/**
 * File v1/users.php - Routes for user calls
 * @author Raphael Nachbar
 */

# Includes auxiliaries
include_once 'api/helpers/header.php';
include_once 'api/helpers/database.php';
include_once 'api/helpers/response.php';
include_once 'api/controllers/V1/UsersController.php';
 
$method = $_SERVER['REQUEST_METHOD']; # Get header request method
$response = new Response($conn);

if (isset($_SERVER['REQUEST_METHOD']) && $method != null) :
    /**
     * Initializes the object
     * The value of the $conn is assigned in the include 'helpers/database.php'
     */
    $users = new UsersController($conn);

    if ($method === 'POST') :
        // $return = $users->create();
        // $message = 'POSTUSER.OK';
    elseif ($method === 'GET') :
        # Checks whether id is being passed in the call
        $parts = explode('/', $_SERVER['REQUEST_URI']);
        $id = end($parts);

        $return = $users->read(intval($id));
        $message = 'GETUSER.OK';
    elseif ($method === 'PUT') :
        // $return = $users->update();
        // $message = 'PUTUSER.OK';
    elseif ($method === 'DELETE') :
        // $return = $users->delete();
        // $message = 'DELETEUSER.OK';
    else :
        $response->returnJson(400, 'No methods allowed');
    endif;

    $response->returnJson(200, $message, $return);
else :
    $response->returnJson(400, 'No methods found');
endif;
