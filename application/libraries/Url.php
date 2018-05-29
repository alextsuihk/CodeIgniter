<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Url
{

    function __construct()
    {
    }

    function get_parm($field, $valid, $default)
    {
        var_dump($var);
        $valid_value = json_decode($valid);
        var_dump($valid_value);
        return (in_array($field, $valid_value)) ? $field : $defailt;
    }
    function get_parm2() 
    {
        $split_parameters = explode('&',$_SERVER['QUERY_STRING']);

        $parm = array();
        for($i = 0; $i < count($split_parameters); $i++) {
            $final = explode('=', $split_parameters[$i]);
            $parm[$final[0]] = urldecode($final[1]);
        }
        return($parm);
    }
}