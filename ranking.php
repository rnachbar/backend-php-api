<?php

/**
 * File v1/ranking.php - Routes for ranking call
 * @author Raphael Nachbar
 */

# Includes auxiliaries
include_once 'api/helpers/initialize.php';
include_once 'api/controllers/V1/UsersController.php';

/**
 * The value of the variable $method is assigned in the include 'api/helpers/initialize.php'
 */
if (isset($method) && $method != null) :
    /**
     * Initializes the object
     * The value of the variable $conn is assigned in the include 'helpers/database.php'
     */
    $controller = new UsersController($conn);

    if ($method === 'GET') :   
        $ranking = $controller->ranking();

        if ($ranking['success']) :
            $response->returnJson(200, 'USER.RANKING.OK', $ranking['data']);
        else :
            $response->returnJson(400, $ranking['message']);
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
