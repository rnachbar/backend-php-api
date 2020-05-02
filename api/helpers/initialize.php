<?php

/**
 * File initialize.php - File initiations
 * @author Raphael Nachbar
 */

include_once 'header.php';
include_once 'database.php';
include_once 'validations.php';
include_once 'response.php';

/**
 * Get header request method
 */
$method = $_SERVER['REQUEST_METHOD'];

/**
 * Initializes classes auxiliaries
 */
$validations = new Validations();
$response = new Response();

/**
 * Body for POST calls
 */
$data = json_decode(file_get_contents("php://input"));
