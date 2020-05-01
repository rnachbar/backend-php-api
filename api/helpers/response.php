<?php

/**
 * File response.php - Mounts API return in JSON
 * @author Raphael Nachbar
 */

class Response {

    /**
     * Returns JSON data
     * @param int $code
     * @param string $message
     * @param array $return
     * @return json
     */
    public function returnJson(int $code, string $message, array $return = []) {
        $success = $this->checkCode($code);

        http_response_code($code);

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $return
        ]);

        exit;
    }

    /**
     * If code is 200, return true
     * If code is 400, return false
     * @param int $code
     * @return boolean
     */
    private function checkCode(Int $code) {
        if ($code === 200) :
            return true;
        endif;

        return false;
    }

}
