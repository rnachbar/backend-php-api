<?php

/**
 * Class BaseController - Responsible for basic use
 * @author Raphael Nachbar
 */

include_once './api/models/AuthModel.php';

class BaseController {

    private $AuthModel;
    private $headers;
    private $requestHeaders;

    public function __construct($conn) {
        $this->AuthModel = new AuthModel($conn);
    }

    /**
     * Checks user authorization to access apis by receiving an authentication token
     * @return array
     */
    public function Authorization() {
        $this->getAuthorizationHeader();

		if($this->headers == '') :
			return [
                'success' => false,
                'message' => 'Empty Header (Token).'
            ];
        endif;

        $token = $this->AuthModel->checkToken($this->headers);

        if ($token <= 0) :
            return [
                'success' => false,
                'message' => 'Token not found.'
            ];
        endif;

        return [
            'success' => true,
            'message' => '',
            'token' => $this->headers,
            'user' => $this->AuthModel->checkUserToken($this->headers)
        ];
    }

    /**
     * Checks whether the authorization token exists in the call header
     * @return string
     */
    private function getAuthorizationHeader() {
        $this->requestHeaders = apache_request_headers();

		if (isset($_SERVER['Authorization'])) {
			$this->headers = trim($_SERVER["Authorization"]);
		} else if (isset($_SERVER['authorization'])) {
			$this->headers = trim($_SERVER["authorization"]);
		} else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
			$this->headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		} elseif (function_exists('apache_request_headers')) {
			$this->requestHeaders = array_combine(array_map('ucwords', array_keys($this->requestHeaders)), array_values($this->requestHeaders));

			if (isset($this->requestHeaders['authorization'])) :
				$this->headers = trim($this->requestHeaders['authorization']);
			endif;

			if (isset($this->requestHeaders['Authorization'])) :
				$this->headers = trim($this->requestHeaders['Authorization']);
			endif;
		}

		return $this->headers;
    }
        
}
