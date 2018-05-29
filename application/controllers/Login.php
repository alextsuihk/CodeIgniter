<?php

class Login extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Taipei");
        $is_logged_in = $this->session->userdata('is_logged_in');        
        if ($is_logged_in == true)
        {
            redirect('main_menu');
        }
        $this->load->model('m_login');    
    }

    function index()
    {
        $url = $_SERVER["QUERY_STRING"];
        
        $is_logged_in = $this->session->userdata('is_logged_in');      
        if ($is_logged_in == true)
        {
            redirect('main_menu');
        } else {
            $data['url']     = $url;
            $data['message'] = $this->session->flashdata('message');
            $this->load->view('login_form', $data);
        }
    
    }
    
    function validate_credentials()
    {
        $url   = $this->input->post('url');
        $query = $this->m_login->validate();   
        if ($query)
        {             
            $data = array(
                'user_id'           => $query->ID,
                'email'             => $query->Email,
                'nickname'          => $query->Nickname,
                'passwd_expiry'     => $query->PasswordExpiryDate,
                'active'            => $query->Active,
                'company_code'      => $query->CompanyCode,
                'company_id'        => $query->CompanyID,
                'priv'              => $query->Privilege,
                'setting'           => '',
                'is_vendor'         => $query->IsVendor,
                'is_disti'          => $query->IsDisti,
                'is_customer'       => $query->IsCustomer,
                'is_subcon'         => $query->IsSubcon,
                'view_mat_cost'     => $query->ViewMatCost,
                'view_man_cost'     => $query->ViewManCost,
                'view_fg_cost'      => $query->ViewFgCost,
                'view_changelog'    => FALSE,
                'is_logged_in'      => TRUE
            ); 
            
            $data['favorite'] = (array) $this->m_login->get_favorite($query->Favorite);
            
            // extract user individual setting from tb_CompanyUser.Setting, put them in to an array
            $setting = (array) json_decode($query->Setting);
            foreach ($setting as $key => $value) {
                $data['setting'][$key] = $value;
            }

            $this->session->set_userdata($data);
            
            if ($query->AllowLogin == "0")          // record found, but not allow to login
            {
                $this->session->unset_userdata('is_logged_in');
                $message = "Invalid Account";
                $this->session->set_flashdata('message',$message);
                $data = array(
                    'User_ID'       => $query->ID,
                    'Event'    => 'Login disallowed: ' . $this->input->post('email'),
                    'RemoteIP' => (isset($_SERVER['HTTP_X_REAL_IP']))?$_SERVER['HTTP_X_REAL_IP']:"unknown",
                    'UserAgent'=> $_SERVER['HTTP_USER_AGENT'] 
                );
                $this->m_login->login_log($data);
                redirect("login?$url"); 
            }
            
            if ($query->Active == "0")          // record found, but set inactive
            {
                $this->session->unset_userdata('is_logged_in');
                $message = "Your Account is Locked";
                $this->session->set_flashdata('message',$message);
                $data = array(
                    'UserID'       => $query->ID,
                    'Event'    => 'locked: ' . $this->input->post('email'),
                    'RemoteIP' => (isset($_SERVER['HTTP_X_REAL_IP']))?$_SERVER['HTTP_X_REAL_IP']:"unknown",
                    'UserAgent'=> $_SERVER['HTTP_USER_AGENT'] 
                );
                $this->m_login->login_log($data);
                redirect("login?$url"); 
            }
            else                                    
            {       // Password expires
                if ($query->PasswordExpiryDate != '0000-00-00' &&  $query->PasswordExpiryDate < date("Y-m-d")) {
                    $message = 'Your Password has Expired <br>please contact <br><a href="mailto:erp@leahkinn.com?subject=ERP Support">erp@leahkinn.com <br><br>';
                    $this->session->set_flashdata('message',$message);
                    $data = array(
                        'UserID'       => $query->ID,
                        'Event'    => 'Passwd Expired: ' . $this->input->post('email'),
                        'RemoteIP' => (isset($_SERVER['HTTP_X_REAL_IP']))?$_SERVER['HTTP_X_REAL_IP']:"unknown",
                        'UserAgent'=> $_SERVER['HTTP_USER_AGENT'] 
                    );
                    $this->m_login->login_log($data);
                    $this->session->unset_userdata('is_logged_in');     // log-out, kill session
                    redirect("login?$url"); 
                }
                
                if ( $query->PasswordExpiryDate != '0000-00-00' && $query->PasswordExpiryDate < date("Y-m-d" , strtotime('+1 month'))) {
                    $message = 'Your Password will expired on '.$query->PasswordExpiryDate.' <br> <a href="./change_passwd">Change Password Now</a>';
                } else {
                    $message = 'Welcome Back';
                }
                $this->session->set_flashdata('message',$message);
                $data = array(
                    'UserID'       => $query->ID,
                    'Event'    => 'login: ' . $this->input->post('email'),
                    'RemoteIP' => (isset($_SERVER['HTTP_X_REAL_IP']))?$_SERVER['HTTP_X_REAL_IP']:"unknown",
                    'UserAgent'=> $_SERVER['HTTP_USER_AGENT'] 
                );
                $this->m_login->login_log($data); 
                if ($url != "")
                {
                    redirect($url);
                } else {
                    redirect('main_menu');
                }
            }
        }
        else
        {
            $message = "Authentication Error";
            $this->session->set_flashdata('message',$message);
           
            $data = array(
                'UserID'       => "0",
                'Event'    => 'auth err: ' . $this->input->post('email'),
                'RemoteIP' => (isset($_SERVER['HTTP_X_REAL_IP']))?$_SERVER['HTTP_X_REAL_IP']:"unknown",
                'UserAgent'=> $_SERVER['HTTP_USER_AGENT'] 
            );
            $this->m_login->login_log($data);
            redirect("login?$url"); 
        }
        
        die('ERROR');       //should never come here
    }
}
