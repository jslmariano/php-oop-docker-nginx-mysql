<?php

namespace App\Josel\Core;

use App\Josel\Helpers\Common as CommonHelper;

/**
 * This class describes a request.
 */
class Request extends VarienObject
{
    /**
     * URL
     */
    public $url;

    /**
     * URL object
     */
    public $url_object;

    /**
     * Constructs a new instance.
     */
    public function __construct()
    {
        $this->initGlobals();
        $this->initUrl();
    }

    protected function initUrl()
    {
        $this->url = "/";
        if (array_key_exists("REQUEST_URI", $_SERVER)) {
            $this->url = $_SERVER["REQUEST_URI"];
        }
    }

    /**
     * Initializes the globals.
     */
    protected function initGlobals()
    {
        $posts = CommonHelper::convertKeysToSnakeCase($_POST);
        $gets = CommonHelper::convertKeysToSnakeCase($_GET);
        $this->setData('input_post', new VarienObject($posts));
        $this->setData('input_get', new VarienObject($gets));
        $this->setData('validate_entries', new VarienObject());
        // Takes raw data from the request
        $json = file_get_contents('php://input');
        // Converts it into a PHP object
        $data = json_decode($json, true);
        $this->setData('input_json', new VarienObject($data));
        $this->syncOrigDatas('input_post');
        $this->syncOrigDatas('input_get');
        $this->syncOrigDatas('input_json');
        $this->syncOrigDatas('validate_entries');
    }

    /**
     * Sync inputs original datas
     */
    protected function syncOrigDatas($input)
    {
        foreach ($this->getData($input)->getData() as $key => $value) {
            $this->getData($input)->setOrigData($key, $value);
        }
    }

    /**
     * Validate entries amd sanitize
     *
     * @param      array  $input_entries           The input entries
     * @param      bool   $allow_validate_failure  The throw failure
     *
     * @return     array  Validate the entries
     */
    public function validate($input_entries)
    {
        $validated  = array();
        $entries    = $this->getValidateEntries()->getData();
        foreach ($entries as $entry => $filters) {
            if (!array_key_exists($entry, $input_entries)) {
                continue;
            }

            $var = $input_entries[$entry];

            foreach ($filters as $filter) {
                if (strpos($filter, "VALIDATE") !== false) {
                    $var = filter_var($input_entries[$entry], $this->getFilters($filter));
                }
            }

            if (empty($var)) {
                continue;
            }
            $validated[$entry] = $var;
        }
        return $validated;
    }

    /**
     * Sanitize entries
     *
     * @param      array  $input_entries           The input entries
     * @param      bool   $allow_validate_failure  The throw failure
     *
     * @return     array  Validate the entries
     */
    public function sanitize($input_entries)
    {
        $validated  = array();
        $entries    = $this->getValidateEntries()->getData();
        foreach ($entries as $entry => $filters) {
            if (!array_key_exists($entry, $input_entries)) {
                continue;
            }

            $var = $input_entries[$entry];

            foreach ($filters as $filter) {
                if (strpos($filter, "SANITIZE") !== false) {
                    $var = filter_var($input_entries[$entry], $this->getFilters($filter));
                }
            }

            /**
             * default sanitize
             */
            $var = filter_var($var, FILTER_SANITIZE_ADD_SLASHES);
            $var = filter_var($var, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (empty($var)) {
                continue;
            }
            $validated[$entry] = $var;
        }
        return $validated;
    }

    /**
     * Gets the filters.
     *
     * @param      string  $filter_string  The filter string
     *
     * @return     int     The filters.
     */
    public function getFilters($filter_string = '')
    {
        /**
         * We can add more here in the future
         */
        switch ($filter_string) {
            case 'VALIDATE_BOOLEAN':
                $filter = FILTER_VALIDATE_BOOLEAN;
                break;
            case 'VALIDATE_INT':
                $filter = FILTER_VALIDATE_INT;
                break;
            case 'VALIDATE_FLOAT':
                $filter = FILTER_VALIDATE_FLOAT;
                break;
            case 'VALIDATE_REGEXP':
                $filter = FILTER_VALIDATE_REGEXP;
                break;
            case 'VALIDATE_IP':
                $filter = FILTER_VALIDATE_IP;
                break;
            case 'VALIDATE_EMAIL':
                $filter = FILTER_VALIDATE_EMAIL;
                break;
            case 'VALIDATE_URL':
                $filter = FILTER_VALIDATE_URL;
                break;
            case 'SANITIZE_EMAIL':
                $filter = FILTER_SANITIZE_EMAIL;
                break;
            case 'SANITIZE_ENCODED':
                $filter = FILTER_SANITIZE_ENCODED;
                break;
            case 'SANITIZE_MAGIC_QUOTES':
                $filter = FILTER_SANITIZE_MAGIC_QUOTES;
                break;
            case 'SANITIZE_NUMBER_FLOAT':
                $filter = FILTER_SANITIZE_NUMBER_FLOAT;
                break;
            case 'SANITIZE_NUMBER_INT':
                $filter = FILTER_SANITIZE_NUMBER_INT;
                break;
            case 'SANITIZE_SPECIAL_CHARS':
                $filter = FILTER_SANITIZE_SPECIAL_CHARS;
                break;
            case 'SANITIZE_FULL_SPECIAL_CHARS':
                $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS;
                break;
            case 'FLAG_NO_ENCODE_QUOTES':
                $filter = FILTER_FLAG_NO_ENCODE_QUOTES;
                break;
            case 'SANITIZE_STRING':
                $filter = FILTER_SANITIZE_STRING;
                break;
            case 'SANITIZE_STRIPPED':
                $filter = FILTER_SANITIZE_STRIPPED;
                break;
            case 'SANITIZE_STRING':
                $filter = FILTER_SANITIZE_STRING;
                break;
            case 'SANITIZE_URL':
                $filter = FILTER_SANITIZE_URL;
                break;
            default:
                $filter = FILTER_DEFAULT;
                break;
        }
        return $filter;
    }
}
