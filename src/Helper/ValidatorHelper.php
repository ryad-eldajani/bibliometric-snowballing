<?php
/*
 * This file is part of the Bibliometric Snowballing project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace BS\Helper;


use BS\Model\Http\Http;

class ValidatorHelper
{
    /**
     * @var ValidatorHelper $instance ValidatorHelper instance
     */
    protected static $instance = null;

    /**
     * @var Http $http Http instance
     */
    protected $http = null;

    /**
     * ValidatorHelper constructor.
     */
    private function __construct()
    {
        $this->http = Http::instance();
    }

    /**
     * Returns the singleton.
     *
     * @return ValidatorHelper instance
     */
    public static function instance()
    {
        if (!isset(ValidatorHelper::$instance)) {
            ValidatorHelper::$instance = new ValidatorHelper();
        }

        return ValidatorHelper::$instance;
    }

    /**
     * Validates POST variables by validation information.
     * Validation information must be an array with the structure:
     * array(
     *  'post_variable' => array(
     *    'type' => 'string',  # data type
     *    'required' => true,  # if true, POST variable must be set
     *    'min' => 4,          # minimum number or character length
     *    'max' => 255,        # maximum number or character length
     *    'func' => function($p) { return strtoupper($p); }, # optional function
     *  )
     * )
     *
     * @param array $validationInfo validation information
     * @return bool True, if all POST variables are valid
     */
    public function validate(array $validationInfo = array()) {
        foreach ($validationInfo as $postKey => $validation) {
            $postValue = $this->http->getPostParam($postKey);

            // If validation is required and POST variable is not set, return false.
            if (
                isset($validation['required'])
                && $validation['required'] === true
                && $postValue === null
            ) {
                return false;
            }

            // If a function is set, apply the function.
            if (isset($validation['func']) && is_callable($validation['func'])) {
                $postValue = $this->http->alterPostParam(
                    $postKey,
                    $validation['func']($postValue)
                );
            }

            // Check data type, if set.
            if (isset($validation['type'])) {
                // If data type has to be int, alter to int
                if ($validation['type'] == 'int') {
                    if (!is_numeric($postValue)) {
                        return false;
                    }

                    $this->http->alterPostParam(
                        $postKey,
                        intval($postValue)
                    );
                } elseif ($validation['type'] == 'double') {
                    if (!is_numeric($postValue)) {
                        return false;
                    }

                    $this->http->alterPostParam(
                        $postKey,
                        floatval($postValue)
                    );
                } elseif ($validation['type'] == 'bool') {
                    if (!is_numeric($postValue)) {
                        return false;
                    }

                    $this->http->alterPostParam(
                        $postKey,
                        (boolval($postValue) ? 1 : 0)
                    );
                }
            }
        }

        return true;
    }
}
