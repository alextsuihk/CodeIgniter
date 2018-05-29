<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Mobiledetect
{

    function __construct()
    {
//        $this->load->library('session');
        $CI =& get_instance();
//        $CI->load->helper('url');
    }

function is_mobile(){
    $aMobileUA = array(
        '/iphone/i' => 'iPhone', 
        '/ipod/i' => 'iPod', 
        '/ipad/i' => 'iPad', 
        '/android/i' => 'Android', 
        '/blackberry/i' => 'BlackBerry', 
        '/webos/i' => 'Mobile'
    );

    //Return true if Mobile User Agent is detected
    foreach($aMobileUA as $sMobileKey => $sMobileOS){
        if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
            return true;
        }
    }
  
    return false;
}
}