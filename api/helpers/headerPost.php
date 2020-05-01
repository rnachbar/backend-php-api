<?php

/**
 * File headerPost.php - Manages headers for API calls via POST
 * @author Raphael Nachbar
 */

include_once 'header.php';

header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
