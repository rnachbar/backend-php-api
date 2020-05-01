<?php

/**
 * File response.php - Mounts API return in JSON
 * @author Raphael Nachbar
 */

class Response {

    public function returnJson($code, $message, $return = []) {
        $success = $this->checkCode($code);

        http_response_code($code);

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $return
        ]);
    }

    private function checkCode(Int $code) {
        if ($code === 200) :
            return true;
        endif;

        return false;
    }

}


