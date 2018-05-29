<?php

class M_Product_ListPrice extends CI_Model {
    function query($action, $limit, $sort_by, $sort_order, $query, $offset, $option) {
        
        $cond = " WHERE tb_Product.Active=1 AND tb_Product.AllowSell=1 AND";
        foreach ($query as $key => $value)
        {
            if ($key != "NULL")
            {
                if ($key == 'ID') {
                    $cond = $cond." ProductID = $value AND";
                } elseif ($key == 'PartNumber') {
                    $cond = $cond." tb_Product.PartNumber LIKE '%$value%' AND"; 
                } else {
                    $cond = $cond." $key LIKE '%$value%' AND";
                }
            } 
        }
        
//        if (strpos($option, 'activeonly') !== FALSE) { $cond = $cond." Active = 1 AND"; } 
        if (!strstr($this->session->userdata['priv'], "IPN")) { $cond = $cond." AllowSell = 1 AND"; }
        
        $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 

        if ($sort_by == "") {
            $order_by = " ";
        } elseif ($sort_by == "PartNumber") {
            $order_by = " ORDER BY tb_Product.$sort_by $sort_order LIMIT $offset, $limit";
        } else {
            $order_by = " ORDER BY tb_ProductListPrice.$sort_by $sort_order LIMIT $offset, $limit";
        }
        
        if ($action=='update') {                    
            $strSQL  = "SELECT *, tb_Product.PartNumber, tb_Product.ID, EffectiveDate, ListPrice FROM tb_Product "
                    . "LEFT JOIN tb_ProductListPrice ON tb_Product.ID = tb_ProductListPrice.ProductID AND tb_ProductListPrice.Expired = '0' "
                    . "WHERE ProductID = $value $order_by";                    
            $strSQL1 = $strSQL;             // meaningless, always return ONE record
            
        } elseif ($action=='history') {
            $strSQL  = "SELECT * FROM tb_ProductListPrice WHERE ProductID = $value ORDER BY EffectiveDate DESC";                    
            $strSQL1 = $strSQL;             // meaningless. no use
            
        } else {      // listing with filter
            $strSQL  = "SELECT *, tb_Product.PartNumber, tb_Product.ID, EffectiveDate, ListPrice FROM tb_Product "
                    . "LEFT JOIN tb_ProductListPrice ON tb_Product.ID = tb_ProductListPrice.ProductID AND tb_ProductListPrice.Expired = '0' "
                    . "$cond $order_by";
            $strSQL1 = "SELECT *, tb_Product.PartNumber, tb_Product.ID, EffectiveDate, ListPrice FROM tb_Product "
                    . "LEFT JOIN tb_ProductListPrice ON tb_Product.ID = tb_ProductListPrice.ProductID AND tb_ProductListPrice.Expired = '0' "
                    . "$cond"; // #strSQL does not limit $limit by page  
        } 
            
        $q = $this->db->query($strSQL);
        $ret['rows'] = $q->result_array();       
        $q = $this->db->query($strSQL1);                    // could be across multiple pages         
        $ret['matched_records'] = $q->num_rows();           // # of records matched
        $strSQL = "SELECT ID FROM tb_Product WHERE Active='1'";              // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();

        return $ret;
    }
    
    
    function update_record($product_id, $data) {
        $this->load->helper('date');
         // check if date is earlier than the latest
        $effective_date = $data['EffectiveDate'];
        $strSQL  = "SELECT EffectiveDate from tb_ProductListPrice where ProductID = '".$product_id."' AND EffectiveDate >= '".$effective_date."'";
        $q = $this->db->query($strSQL);
        if ($q->num_rows() > 0) {
            return (1);                     // if any exiting record is newer than submit date, report error
        }
        
        // expire OLD reocrd
        $this->db->where('ProductID',$product_id);
        $this->db->update('tb_ProductListPrice', array('Expired' => '1'));
        
        $change_log = '{'.date("Y-m-d H:i").', added by '.$this->session->userdata['nickname'].'},';
        $data['ChangeLog'] = $change_log;
        $this->db->insert('tb_ProductListPrice', $data);
        
        return (0);             // added successfully
    }
    
    
    function searchby_options() {
        $searchby_options  = array();
        $searchby_options['NULL']           = '';
        $searchby_options['PartNumber']     = 'Part Number';
        $searchby_options['Description']    = 'Description';        
        $searchby_options['BallType']       = 'Ball Type';
        return $searchby_options;
    }
    
    
}
?>