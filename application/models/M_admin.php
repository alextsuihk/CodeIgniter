<?php

class M_Admin extends CI_Model {
    
    function get_seq_number($item)
    {
        // query
        // update next seq_number
        
    }
    function update_seq_number() 
    {
        $record = '';
        $strSQL  = "SELECT * FROM tb_SeqNumber ORDER BY Item ASC";
        $q = $this->db->query($strSQL);
        $items = $q->result_array();
        foreach ($items as $item) {
            if (!empty($item['LinkTable']))
            {
                $record = $record." ".$item['Item'];
                $strSQL  = "SELECT ID FROM ".$item['LinkTable']." ORDER BY ID DESC LIMIT 1";
                $q = $this->db->query($strSQL);
                $next_id = ($q->result_array()[0]['ID'])+1;

                $item['SeqNumber'] = $next_id;
                $item['ChangeLog'] = '{'.date("Y-m-d H:i").', advanced by '.$this->session->userdata['nickname'].'},'.$item['ChangeLog'];
                $this->db->where('Item', $item['Item']);
                $this->db->update('tb_SeqNumber', $item);
            }
        }
        return ($record);
    }
    
    function term_query($table, $limit, $sort_by, $sort_order,$query, $offset, $option) {
       
        $cond = " WHERE";
        foreach ($query as $key => $value)
        {
            if ($key != "NULL")
            {
                if ($key == 'ID') {
                    $cond = $cond." ID = $value AND";
                } else {
                    $cond = $cond." $key LIKE '%$value%' AND";
                }
            } 
        }
        
        if (strpos($option, 'activeonly') !== FALSE) { $cond = $cond." Active = 1 AND"; } 
        
        if ($cond == " WHERE") { 
            $cond = ""; 
        } else {
            $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 
        }
        
        $order_by = ($sort_by=="")? "":" ORDER BY $sort_by $sort_order LIMIT $offset, $limit";

        $strSQL  = "SELECT * FROM $table $cond $order_by";
        $strSQL1 = "SELECT * FROM $table $cond";           
        $q = $this->db->query($strSQL);                     // pull per page data
        $ret['rows'] = $q->result_array();
        
        $q = $this->db->query($strSQL1);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
   
        $strSQL = "SELECT * FROM $table";              // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();

        return $ret;
    }
    
    function term_list($table) {
        $list = array();
        $list['NULL'] = '';
        $strSQL = "SELECT ID, Terms, TermsDetail FROM $table ORDER BY Terms asc";    
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $list[$row->ID] = $row->PartNumber;
        }
        return $list;
    }
}

?>
    
