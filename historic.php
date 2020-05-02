<?php

/**
 * File v1/historic.php - Routes for historic call
 * @author Raphael Nachbar
 */

# Includes auxiliaries
include_once 'api/helpers/initialize.php';
include_once 'api/controllers/V1/BaseController.php';
include_once 'api/controllers/V1/UsersController.php';

/**
 * The value of the variable $method is assigned in the include 'api/helpers/initialize.php'
 */
if (isset($method) && $method != null) :
    /**
     * Initializes the object
     * The value of the variable $conn is assigned in the include 'helpers/database.php'
     */
    $base = new BaseController($conn);
    $controller = new UsersController($conn);

    if ($method === 'GET') :   
        $authorization = $base->Authorization();

        if (!$authorization['success']) :
            $response->returnJson(400, $authorization['message']);
        endif;

        # Checks whether ID is being passed in the call
        $parts = explode('/', basename($_SERVER['REQUEST_URI']));
        $id = end($parts);
        
        if ($id != '' && intval($id) > 0) :
            $historic = $controller->historic($authorization, $id);

            if ($historic['success']) :
                $response->returnJson(200, 'USER.DRINK.HISTORIC.OK', $historic['data']);
            else :
                $response->returnJson(400, $historic['message']);
            endif;
        else :
            $response->returnJson(400, 'Incorrect ID.');
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
