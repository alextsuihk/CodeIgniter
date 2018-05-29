<?php

class M_Backlog extends CI_Model {
    function query($limit, $sort_by, $sort_order,$query, $offset, $option) {
        
 //       if ($query_field != 'ID') { $query_value="%".$query_value."%"; }
 //       $cond     = ($query_field=="")? "WHERE Expired=0 ":" WHERE Expired=0 AND $query_field LIKE '".$query_value."'";
        
        if ($sort_by == "PartNumber") {$sort_by = "tb_Product.PartNumber"; }
        
        $cond = " WHERE Expired=0 AND";
        foreach ($query as $key => $value)
        {
            if ($key != "NULL")
            {
                if ($key == 'ID') {
                    $cond = $cond." tb_SalesBacklogItem.BacklogID = $value AND";
                } elseif ($key == 'PartNumber') {
                    $cond = $cond." tb_Product.$key LIKE '%$value%' AND";
                } else {
                    $cond = $cond." $key LIKE '%$value%' AND";
                }
            } 
        }
        $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 
        
        if (strpos($option, 'hidecancel') !== FALSE)    { $cond = $cond." AND tb_SalesBacklogItem.Status != 'cancel'"; }
        if (strpos($option, 'hidecompleted') !== FALSE) { $cond = $cond." AND tb_SalesBacklogItem.Status != 'completed'"; }
        
        $order_by = ($sort_by=="")? "":" ORDER BY $sort_by $sort_order, Item asc LIMIT $offset, $limit";

        $strSQL  = "SELECT *, tb_Product.PartNumber, tb_SalesBacklogItem.Note as ItemNote, tb_SalesBacklog.Note as OrderNote"
                . " FROM tb_SalesBacklogItem "
                . "LEFT JOIN tb_SalesBacklog ON tb_SalesBacklog.ID = tb_SalesBacklogItem.BacklogID "
                . "LEFT JOIN tb_Company ON tb_SalesBacklogItem.CompanyID = tb_Company.ID "
                . "LEFT JOIN tb_Product ON tb_SalesBacklogItem.ProductID = tb_Product.ID  $cond $order_by";
        $strSQL1 = "SELECT *, tb_Product.PartNumber, tb_SalesBacklogItem.Note as ItemNote, tb_SalesBacklog.Note as OrderNote"
                . " FROM tb_SalesBacklogItem "
                . "LEFT JOIN tb_SalesBacklog ON tb_SalesBacklog.ID = tb_SalesBacklogItem.BacklogID "
                . "LEFT JOIN tb_Company ON tb_SalesBacklogItem.CompanyID = tb_Company.ID "
                . "LEFT JOIN tb_Product ON tb_SalesBacklogItem.ProductID = tb_Product.ID $cond";         

        $q = $this->db->query($strSQL);
        $ret['rows'] = $q->result_array();
        
        $q = $this->db->query($strSQL1);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
   
        $strSQL = "SELECT ID FROM tb_SalesBacklogItem WHERE Expired = 0";     // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();
        return $ret;
    }
    
    function get_single_backlog($id) {
        $strSQL  = "SELECT *, tb_SalesBacklog.ChangeLog, tb_SalesBacklog.Note, tb_SalesBacklog.CompanyID FROM tb_SalesBacklog "
                . "LEFT JOIN tb_Company ON tb_SalesBacklog.CompanyID = tb_Company.ID "
                . "LEFT JOIN tb_CompanyUser ON tb_SalesBacklog.Submitter = tb_CompanyUser.ID "
                . "WHERE tb_SalesBacklog.ID ='".$id."'"; 
        $strSQL1 = "SELECT *, tb_SalesBacklogItem.ChangeLog, tb_SalesBacklogItem.Note FROM tb_SalesBacklogItem "
                . "LEFT JOIN tb_Product ON tb_SalesBacklogItem.ProductID = tb_Product.ID "
                . "WHERE Expired=0 AND tb_SalesBacklogItem.BacklogID ='".$id."'"; 
        
        $q = $this->db->query($strSQL);
        $ret['backlog'] = $q->result_array();
        $q = $this->db->query($strSQL1);
        $ret['items'] = $q->result_array();
        
        return ($ret);
        }
    
    function update_record($backlog_id, $backlog, $items) {
        
        $this->load->helper('date');

        $q = $this->db->select('ChangeLog, Revision')
                ->from('tb_SalesBacklog')
                ->where('ID', $backlog_id)
                ->limit(1);
        
        foreach ($q->get()->result() as $row)
        {
            $change_log = $row->ChangeLog;               //get original ChangeLog
            $revision   = $row->Revision;
        }

        // append ChangeLog in the front
        $change_log = '{'.date("Y-m-d H:i").', modified by '.$this->session->userdata['nickname'].'},'.$change_log;
        $backlog['ChangeLog'] = $change_log;  
        $backlog['Revision']  = $revision + 1;
        
        $this->db->where('ID', $backlog_id);
        $this->db->update('tb_SalesBacklog', $backlog);
        
        // expired old backlog items
        $this->db->where('BacklogID', $backlog_id);
        $this->db->update('tb_SalesBacklogItem', ['Expired' => 1]);

        // re-insert backlog items
        $sizeof_item = sizeof($items);
        for ($i=0; $i<$sizeof_item; $i++)
        {
            if ($items[$i]['ProductID'] != "NULL" || $items[$i]['ProductID'] != 0) {
                $items[$i]['BacklogID']  = $backlog_id;
                $items[$i]['ChangeLog']  = $change_log;
                $this->db->insert('tb_SalesBacklogItem', $items[$i]);
            }
        }
        return;

        
        foreach ($items as $i=>$item) 
        {
            $j = $i+1;
            $strSQL  = "SELECT BacklogID, Item FROM tb_SalesBacklogItem "
                    . "WHERE BacklogID=$backlog_id AND Item=$j";
            $q = $this->db->query($strSQL);
            
            $rows = $q->num_rows();
            switch ($q->num_rows()) {
            case "0":                 // new record 
                $item['ChangeLog'] = $change_log; 
                $this->db->insert('tb_SalesBacklogItem', $item);
                break; 
            case "1":                 // if record exists 
                $item['ChangeLog'] = $change_log;  
                $this->db->where('BacklogID', $backlog_id);
                $this->db->where('Item', $j);
                $this->db->update('tb_SalesBacklogItem', $item);
                break; 
            default: die(" $rows ERROR: M_backlog -> update; repeating BacklogID & Item");
            }
        }
    }
    
    function add_record($backlog, $items) {
        $this->load->helper('date');
        
        $change_log = '{'.date("Y-m-d H:i").', added by '.$this->session->userdata['nickname'].'},';
        $backlog['ChangeLog'] = $change_log;
        $backlog['Revision']  = 1;
        $this->db->insert('tb_SalesBacklog', $backlog);             // add tb_SalesBacklog first
        $this->db->trans_complete();
        $backlog_id = $this->db->insert_id();

        $sizeof_item = sizeof($items);
        for ($i=0; $i<$sizeof_item; $i++)
        {
            if ($items[$i]['ProductID'] == NULL)  { break; }          // if product_id is null, write no more
            $items[$i]['BacklogID']  = $backlog_id;
            $items[$i]['ChangeLog']  = $change_log;
            $this->db->insert('tb_SalesBacklogItem', $items[$i]);
        }
        
        return ($backlog_id);             // added successfully
    }
    
    
    function searchby_options() {
        $searchby_options  = array();
        $searchby_options['NULL']           = '';
        $searchby_options['PartNumber']     = 'Part Number';
        $searchby_options['CompanyCode']    = 'Company Code';        
        $searchby_options['CustomerPO']     = 'Customer PO';
        $searchby_options['Status']         = 'Status';
        return $searchby_options;
    }

    function status_list() {
        $status_list  = array();
        $status_list['open']        = 'open';
        $status_list['cancel']      = 'cancel';        
        $status_list['completed']   = 'completed';
        $status_list['other']       = 'other';
        return $status_list;
    }    
    
}
?>