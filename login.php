<?php

/**
 * File v1/users.php - Routes for login call
 * @author Raphael Nachbar
 */

# Includes auxiliaries
include_once 'api/helpers/headerPost.php';
include_once 'api/helpers/database.php';
include_once 'api/helpers/response.php';
include_once 'api/controllers/V1/AuthController.php';

$method = $_SERVER['REQUEST_METHOD'];
$response = new Response($conn);

if (isset($_SERVER['REQUEST_METHOD']) && $method != null) :
    /**
     * Initializes the object
     * The value of the $conn is assigned in the include 'helpers/database.php'
     */
    $auth = new AuthController($conn);

    if ($method === 'POST') :
        // $return = $users->create();
        // $message = 'POSTUSER.OK';

        $response->returnJson(200, $message, $return);
    else :
        $response->returnJson(400, "$method method is not allowed.");
    endif;
else :
    $response->returnJson(400, 'No methods found');
endif;
