<?php

function fn_print_r()
{
    $args         = func_get_args();
    if (!empty($args)) {
        echo '<ol
                id="fn_print_r"
                style="font-family: Courier;
                        font-size: 12px;
                        border: 1px solid #dedede;
                        background-color: #ba9999;
                        float: left;
                        padding-right:
                        20px; text-align: -webkit-auto;">';
        foreach ($args as $k => $v) {
            $v = htmlspecialchars(print_r($v, true));
            if ($v == '') {
                $v = '    ';
            }
            echo '<li><pre>' . $v . "\n" . '</pre></li>';
        }
        echo '</ol><div style="clear:left;"></div>';
    }
}

/**
 * var_dump a data then exit
 *
 * @param   any data to var_dump
 *
 */
function fn_print_die()
{
    $args = func_get_args();
    call_user_func_array('fn_print_r', $args);
    die();
}

function fn_mrk()
{
    $args               = array();
    $call               = debug_backtrace();
    $call               = $call[0];
    $root               = realpath($_SERVER['DOCUMENT_ROOT']);
    $call['short_path'] = str_replace($root, "", $call['file']);
    $mark_title         = ("MARKED | " . $call['short_path'] . " :: " . $call['line']);
    $args[]             = $mark_title;
    $args[]             = func_get_args();
    call_user_func_array('fn_print_r', $args);
    die();
}
