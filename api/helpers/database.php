<?php

/**
 * File database.php - Manages database connection for API calls
 * @author Raphael Nachbar
 */

include_once './api/connection/Connection.php';

$connection = new Connection();
$conn = $connection->connect();
