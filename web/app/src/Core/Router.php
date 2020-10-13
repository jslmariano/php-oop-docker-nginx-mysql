<?php

namespace App\Josel\Core;

/**
 * This class describes a router.
 */
class Router
{

    /**
     * parse the request url to MVC convention
     *
     * @param      Request  $request  The request
     * @param      string   $url      The url
     */
    public static function parse(Request $request, $url = "/")
    {
        $url       = trim($url);
        $url       = trim($url, '/');
        $url_break = explode('?', $url);
        $url_path  = $url_break[0];

        /**
         * Initial mvc convention
         */
        $request->controller = "Index";
        $request->action     = "index";
        $request->params     = [];

        if (!empty($url_path) && $url_path != "/") {
            $explode_url         = explode('/', $url_path);
            $request->controller = $explode_url[0];

            if (isset($explode_url[1])) {
                $request->action     = $explode_url[1];
            }

            if (isset($explode_url[2])) {
                $request->params     = array_slice($explode_url, 2);
            }
        }
    }
}
