<?php 

class Privilege
{

    function __construct()
    {
//        $this->load->library('session');

//        $CI->load->helper('url'); 
    }

    function permission($required_priv) 
    {   
        $CI =& get_instance();
        $CI->load->model('m_login');

        $url = "http://".($_SERVER['SERVER_NAME']).($_SERVER['REQUEST_URI']); 

        $base_url = base_url();
        $url = str_replace($base_url, "", $url);           // remove base_url
        
        $is_logged_in = $CI->session->userdata('is_logged_in');  
        if (!isset($is_logged_in) || $is_logged_in != true)
        {
            $message = "Please Login again";
            $CI->session->set_flashdata('message',$message);
            redirect("login?$url");                      // go back to Login page  
        }
       
        if (empty($required_priv)) {
            return;                             // if no rquired priv, just allows
        }
        
        // reload Priv & Favorite
        $query = $CI->m_login->validate(intval($CI->session->userdata['user_id']));  
        $CI->session->userdata['favorite'] = (array) $CI->m_login->get_favorite($query->Favorite);
        $CI->session->userdata['priv']     = $query->Privilege;

        $permission = 0;
        foreach ($required_priv as $required)
        {
            if (strstr($CI->session->userdata['priv'], $required)) {
                $permission = 1;
                break;
            }
        }
     
        if ($permission == 0) {
            $message = "You do NOT have privilege to access, return to MainMenu";
            $CI->session->set_flashdata('message',$message);
            redirect('main_menu');             // no privilege, redirect to main menu      
        }
    }
}