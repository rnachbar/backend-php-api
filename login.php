<?php

/**
 * File v1/login.php - Routes for login call
 * @author Raphael Nachbar
 */

# Includes auxiliaries
include_once 'api/helpers/initialize.php';
include_once 'api/controllers/V1/AuthController.php';

/**
 * The value of the variable $method is assigned in the include 'api/helpers/initialize.php'
 */
if (isset($method) && $method != null) :
    /**
     * Initializes the object
     * The value of the variable $conn is assigned in the include 'helpers/database.php'
     */
    $controller = new AuthController($conn);

    if ($method === 'POST') :
        $post = $validations->postBody($data);

        if (!$post['success']) :
            $response->returnJson(400, $post['message']);
        endif;
        
        $auth = $controller->auth($data);

        if ($auth['success']) :
            $response->returnJson(200, 'AUTH.USER.OK', $auth['data']);
        else :
            $response->returnJson(400, $auth['message']);
        endif;
    else :
        $response->returnJson(400, "$method method is not allowed.");
    endif;
else :
    /**
     * If no method is recognized in the call
     * Returns error 400 with message
     */
    $response->returnJson(400, 'No methods found');
endif;
