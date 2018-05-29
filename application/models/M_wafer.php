<?php

class M_Wafer extends CI_Model {
    function query($limit, $sort_by, $sort_order,$query, $offset, $option) {

        $cond = " WHERE";
        foreach ($query as $key => $value)
        {
            if ($key != "NULL")
            {
                if ($key == 'ID') {
                    $cond = $cond." tb_WaferPartNumber.ID = $value AND";
                } elseif ($key == 'Description') {
                    $cond = $cond." (tb_WaferPartNumber.Description LIKE '%$value%' OR tb_WaferPartNumber.DescriptionInternal LIKE '%$value%' OR tb_WaferPartNumberTop.DescriptionDesign LIKE '%$value%') AND"; 
                } else {
                    $cond = $cond." $key LIKE '%$value%' AND";
                }
            } 
        }
        
        if (strpos($option, 'activeonly') !== FALSE) { $cond = $cond." tb_WaferPartNumber.Active = 1 AND"; } 
        
        if ($cond == " WHERE") { 
            $cond = ""; 
        } else {
            $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 
        }
        
//        if (strpos($option, 'hidecancel') !== FALSE)    { $cond = $cond." AND tb_SalesBacklogItem.Status != 'cancel'"; }
//        if (strpos($option, 'hidecompleted') !== FALSE) { $cond = $cond." AND tb_SalesBacklogItem.Status != 'completed'"; }
        
        $order_by = ($sort_by=="")? "":" ORDER BY $sort_by $sort_order LIMIT $offset, $limit";

        $strSQL  = "SELECT *, tb_WaferPartNumber.ID as ID, tb_WaferPartNumber.Note AS Note, tb_WaferPartNumber.ChangeLog as ChangeLog "
                . "FROM tb_WaferPartNumber "
                . "LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumber.DesignID = tb_WaferPartNumberTop.ID "
                . "LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumberTop.VendorID "
                . "$cond $order_by";
        
        $strSQL1 = "SELECT *, tb_WaferPartNumber.ID as ID, tb_WaferPartNumber.Note AS Note, tb_WaferPartNumber.ChangeLog as ChangeLog "
                . "FROM tb_WaferPartNumber "
                . "LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumber.DesignID = tb_WaferPartNumberTop.ID "
                . "LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumberTop.VendorID "
                . "$cond ";
        
        $q = $this->db->query($strSQL);                     // pull per page data
        $ret['rows'] = $q->result_array();
        
        $q = $this->db->query($strSQL1);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
   
        $strSQL = "SELECT ID FROM tb_WaferPartNumber";              // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();

        return $ret;
    }
    
    function update_record($id, $data) {
        $this->load->helper('date');

        $q = $this->db->select('ChangeLog')
                ->from('tb_WaferPartNumber')
                ->where('ID', $id)
                ->limit(1);
        
        $change_log = $q->get()->result()[0]->ChangeLog;
        // append ChangeLog in the front
        $change_log = '{'.date("Y-m-d H:i").', modified by '.$this->session->userdata['nickname'].'},'.$change_log;
        $data['ChangeLog'] = $change_log;        
        
        $this->db->where('ID', $id);
        $this->db->update('tb_WaferPartNumber', $data);
    }
    
    function add_record($data) {
        $this->load->helper('date');
        
        // check if PartNumber already exists
        $part_number = $data['PartNumber'];
        $strSQL = "SELECT ID, COUNT(*) as count FROM tb_WaferPartNumber WHERE PartNumber LIKE '$part_number'";
        $result = $this->db->query($strSQL);
        if (($result->row()->count) != '0') {
            return (-1);                            // return 1 if P/N exists
        }

        $change_log = '{'.date("Y-m-d H:i").', added by '.$this->session->userdata['nickname'].'},';
        $data['ChangeLog'] = $change_log;
        $this->db->insert('tb_WaferPartNumber', $data);
        $this->db->trans_complete();
        $product_id =$this->db->insert_id();
       
        return ($product_id);             // added successfully
    }
    
    
    function searchby_options() {
        $searchby_options  = array();
        $searchby_options['NULL']           = '';
        $searchby_options['PartNumber']     = 'Part Number';
        $searchby_options['Vendor']         = 'Vendor';        
        $searchby_options['Design']         = 'Design';
        $searchby_options['Description']    = 'Description';
        return $searchby_options;
    }

    function wafer_vendor_list() {
        $list = array();
        $strSQL = "SELECT ID, Vendor FROM tb_WaferVendor ORDER BY Vendor asc"; 
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $list[$row->ID] = $row->Vendor;
        }
        return $list;
    }

    function wafer_design_list() {
        $list = array();
        $list['NULL'] = '';
        $strSQL = "SELECT tb_WaferPartNumberTop.ID, Vendor, Design FROM tb_WaferPartNumberTop "
                . "LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumberTop.VendorID "
                . "ORDER BY Vendor asc, Design asc ";   
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $list[$row->ID] = $row->Vendor." ".$row->Design;
        }
        return $list;
    }

    function wafer_partnumber_list() {
        $list = array();
        $list['NULL'] = '';
        $strSQL = "SELECT *, tb_WaferPartNumber.ID "
                . "FROM tb_WaferPartNumber "
                . "LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumber.DesignID = tb_WaferPartNumberTop.ID "
                . "LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumberTop.VendorID "
                . "WHERE tb_WaferPartNumber.Active=1 "
                . "ORDER BY Vendor asc, PartNumber asc";
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $list[$row->ID] = $row->Vendor." ".$row->PartNumber;
        }
        return $list;
    }
      
    
}