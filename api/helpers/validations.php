<?php

/**
 * File validations.php - Validates calls to the API
 * @author Raphael Nachbar
 */

class Validations {

    /**
     * Checks if there is a body and if it is in the correct format
     * @param $data
     * @return array
     */
    public function postBody($data) {
        if (gettype($data) != 'object') :
            return [
                'success' => false,
                'message' => 'Create user body is not an object.'
            ];
        endif;

        $array = (array) $data;

        if (!$array) :
            return [
                'success' => false,
                'message' => 'Empty body to create user.'
            ];
        endif;

        return [
            'success' => true,
            'message' => ''
        ];
    }

}
