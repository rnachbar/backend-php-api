<?php

/**
 * Class AuthController - Responsible for authenticating the user
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
     * Validates body fields
     * @param object $data
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
