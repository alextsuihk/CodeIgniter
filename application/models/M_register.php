<?php

class M_Register extends CI_Model {
    function query($limit, $sort_by, $sort_order,$query, $offset, $option, $disti_id) {
               
        $cond = " WHERE fc.ID >=100 AND";
        
       if ($disti_id !=0) { $cond = $cond." fc.DistiID = $disti_id AND"; }
        
        foreach ($query as $key => $value) {
            if ($key != "NULL") { 
                if ($key == 'ID') {
                    $cond = $cond." fc.ID = $value AND";
                } else {
                    $cond = $cond." $key LIKE '%$value%' AND"; 
                }
            } 
        }
               
        if ($cond == " WHERE") { 
            $cond = ""; 
        } else {
            $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 
        }
        
        $order_by = ($sort_by=="")? "":" ORDER BY $sort_by $sort_order LIMIT $offset, $limit";

        $strSQL  = "SELECT *, fc.ID FROM tb_FinalCustomer fc "
                . "LEFT JOIN tb_Company com ON fc.DistiID = com.ID "
                . "$cond $order_by";

        $strSQL1  = "SELECT fc.ID FROM tb_FinalCustomer fc "
                . "LEFT JOIN tb_Company com ON fc.DistiID = com.ID "
                . "$cond ";

        $q = $this->db->query($strSQL);
        $ret['rows'] = $q->result_array();

        $q = $this->db->query($strSQL1);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
   
        $strSQL = "SELECT ID FROM tb_FinalCustomer";     // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();
        return $ret;
    }
    
    function searchby_options() {
        $searchby_options  = array();
        $searchby_options['NULL']           = '';
        $searchby_options['Customer']       = '客人';
        $searchby_options['CompanyCode']    = '代理';
        return $searchby_options;
    }

    function update($id, $data) {
        $this->load->helper('date');
        
        $this->db->where('ID', $id);
        $this->db->update('tb_FinalCustomer', $data);       
        return;
    }
      
    function add($data) {
        $this->load->helper('date');
        
        $change_log = '{'.date("Y-m-d H:i").', modified by '.$this->session->userdata['nickname'].' },';
        $data['ChangeLog'] = $change_log;
        $this->db->insert('tb_FinalCustomer', $data);
        $this->db->trans_complete();
        $id = $this->db->insert_id();
        return ($id);             // added successfully
    }
    

}
?>
