<?php

class M_Product extends CI_Model {
    function query($limit, $sort_by, $sort_order,$query, $offset, $option) {
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
        if (!strstr($this->session->userdata['priv'], "IPN")) { $cond = $cond." AllowSell = 1 AND"; }
        
        if ($cond == " WHERE") { 
            $cond = ""; 
        } else {
            $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 
        }
        
        $order_by = ($sort_by=="")? "":" ORDER BY $sort_by $sort_order LIMIT $offset, $limit";

        $strSQL  = "SELECT * FROM tb_Product $cond $order_by";
        $strSQL1 = "SELECT * FROM tb_Product $cond";           
        $q = $this->db->query($strSQL);                     // pull per page data
        $ret['rows'] = $q->result_array();
        
        $q = $this->db->query($strSQL1);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
   
        $strSQL = "SELECT ID FROM tb_Product";              // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();

        return $ret;
    }
    
    function update_record($id, $data) {
        $this->load->helper('date');

        $q = $this->db->select('ChangeLog')
                ->from('tb_Product')
                ->where('ID', $id)
                ->limit(1);
        
        $change_log = $q->get()->result()[0]->ChangeLog;
        // append ChangeLog in the front
        $change_log = '{'.date("Y-m-d H:i").', modified by '.$this->session->userdata['nickname'].'},'.$change_log;
        $data['ChangeLog'] = $change_log;        
        
        $this->db->where('ID', $id);
        $this->db->update('tb_Product', $data);
    }
    
    function add_record($data) {
        $this->load->helper('date');
        
        // check if PartNumber already exists
        $part_number = $data['PartNumber'];
        $strSQL = "SELECT ID FROM tb_Product WHERE PartNumber LIKE '$part_number'";
        $result = $this->db->query($strSQL);
        if (($result->num_rows()) != '0') {
            return (-1);                            // return 1 if P/N exists
        }

        $change_log = '{'.date("Y-m-d H:i").', added by '.$this->session->userdata['nickname'].'},';
        $data['ChangeLog'] = $change_log;
        $this->db->insert('tb_Product', $data);
        $this->db->trans_complete();
        $product_id =$this->db->insert_id();
       
        // add a default record in IPN
        $data_detail = array(
            'ProductID'      => $product_id,
            'Active'        => '0',                 // set the 1st IPN inactive by default (because no data)
            'IPN'           => $part_number,        // first IPN = tb_Product.PartNumber
            'Description'   => 'initial IPN, please update, this record is created by creating P/N',
            'ChangeLog'     => $change_log
        );
        $this->db->insert('tb_ProductDetail', $data_detail);
        
        return ($product_id);             // added successfully
    }
    
    
    function searchby_options() {
        $searchby_options  = array();
        $searchby_options['NULL']           = '';
        $searchby_options['PartNumber']     = 'Part Number';
        $searchby_options['Description']    = 'Description';        
        $searchby_options['BallType']       = 'Ball Type';
        $searchby_options['PackageSize']    = 'Package Size';
        return $searchby_options;
    }
    
    function product_list($cond="") {
        $product_list = array();
        $product_list['NULL'] = '';
        switch ($cond) {
            case "purchased" : $strSQL = "SELECT ID, PartNumber FROM tb_Product WHERE Active=1 AND AllowPurchase=1 ORDER BY PartNumber asc";break;
            case "sample"    : $strSQL = "SELECT ID, PartNumber FROM tb_Product WHERE Active=1 AND AllowSample=1 ORDER BY PartNumber asc";break;
            case "sell"      : $strSQL = "SELECT ID, PartNumber FROM tb_Product WHERE Active=1 AND AllowSell=1 ORDER BY PartNumber asc";break;
            default          : $strSQL = "SELECT ID, PartNumber FROM tb_Product WHERE Active=1 AND AllowSell=1 ORDER BY PartNumber asc"; break;
        } 
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $product_list[$row->ID] = $row->PartNumber;
        }
        return $product_list;
    }

    function get_part_number($product_id="") {
        $part_number = "";
        $strSQL = "SELECT PartNumber FROM tb_Product WHERE ID = '$product_id'  "; 
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $part_number =  $row->PartNumber;
        }
        return $part_number;
    }
}