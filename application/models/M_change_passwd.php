<?php

class M_Change_Passwd extends CI_Model {
        
    function update_record($id, $data) {
        $this->load->helper('date');

        $q = $this->db->select('ChangeLog')
                ->from('tb_CompanyUser')
                ->where('ID', $id)
                ->limit(1);
        
        foreach ($q->get()->result() as $row)
        {
            $ChangeLog = $row->ChangeLog;               //get original ChangeLog
        }
 
        $ChangeLog = '{'.date("Y-m-d H:i").' Passwd updated},'.$ChangeLog;
        $data['ChangeLog'] = $ChangeLog;
        $data['PasswordExpiryDate'] = date("Y-m-d" , strtotime('+1 year'));
        $this->db->where('ID', $id);
        $this->db->update('tb_CompanyUser', $data);        
    }
}

?>


