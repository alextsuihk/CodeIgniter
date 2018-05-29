<?php

class M_Product_Ipn extends CI_Model {
    
    function ipn_search($action, $product_id, $ipn_id, $limit, $sort_by, $sort_order="",$query="", $offset=0, $option="") {
        if ($action == 'all') {
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
        }
        
        
        if ($action=='edit' || $action=='view') {
            $strSQL  = "SELECT * FROM tb_Product WHERE ID = '$product_id'";
            $strSQL1 = "SELECT *, "
               // Die 1 Information
                . " (SELECT CONCAT(Vendor, ' ', PartNumber) FROM tb_WaferPartNumber "
                . " LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumber.VendorID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_1) AS DiePN_1, "
                
                . " (SELECT CONCAT(DescriptionDesign, ' : ', Description, ' : ', DescriptionInternal) FROM tb_WaferPartNumber  "
                . " LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumberTop.ID = tb_WaferPartNumber.DesignID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_1) AS DieDesc_1,  "
                
                // Die2 Information
                . " (SELECT CONCAT(Vendor, ' ', PartNumber) FROM tb_WaferPartNumber "
                . " LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumber.VendorID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_2) AS DiePN_2, "
                
                . " (SELECT CONCAT(DescriptionDesign, ' : ', Description, ' : ', DescriptionInternal) FROM tb_WaferPartNumber  "
                . " LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumberTop.ID = tb_WaferPartNumber.DesignID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_2) AS DieDesc_2,  "
                
                // Die 3 Information
                . " (SELECT CONCAT(Vendor, ' ', PartNumber) FROM tb_WaferPartNumber "
                . " LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumber.VendorID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_3) AS DiePN_3, "
                
                . " (SELECT CONCAT(DescriptionDesign, ' : ', Description, ' : ', DescriptionInternal) FROM tb_WaferPartNumber  "
                . " LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumberTop.ID = tb_WaferPartNumber.DesignID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_3) AS DieDesc_3,  "
                
                // Die 4 Information
                . " (SELECT CONCAT(Vendor, ' ', PartNumber) FROM tb_WaferPartNumber "
                . " LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumber.VendorID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_4) AS DiePN_4, "
                
                . " (SELECT CONCAT(DescriptionDesign, ' : ', Description, ' : ', DescriptionInternal) FROM tb_WaferPartNumber  "
                . " LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumberTop.ID = tb_WaferPartNumber.DesignID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_4) AS DieDesc_4,  "
                
                // Die 5 information
                . " (SELECT CONCAT(Vendor, ' ', PartNumber) FROM tb_WaferPartNumber "
                . " LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumber.VendorID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_5) AS DiePN_5, "
                
                . " (SELECT CONCAT(DescriptionDesign, ' : ', Description, ' : ', DescriptionInternal) FROM tb_WaferPartNumber  "
                . " LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumberTop.ID = tb_WaferPartNumber.DesignID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_5) AS DieDesc_5, "
                
                // PurchasedItem (外購料料)
                . " (SELECT PartNumber  FROM tb_Product "
                . " WHERE tb_Product.ID = PurchasedItemID) AS PurchasedItemPN,  "
                    
                . " (SELECT Description  FROM tb_Product "
                . " WHERE tb_Product.ID = PurchasedItemID) AS PurchasedItemDesc  "
                    
                . " FROM tb_ProductDetail "
                . " WHERE tb_ProductDetail.ID ='$ipn_id' ";
            
            $strSQL2 = "SELECT * FROM tb_ProductDetail WHERE ID ='$ipn_id'";
        } elseif ($action == 'list') {                   // listing a special ProductID
            $strSQL  = "SELECT * FROM tb_Product WHERE ID = '$product_id'";
            $strSQL1 = "SELECT * FROM tb_ProductDetail WHERE ProductID = '$product_id' ORDER BY IPN ASC";
            $strSQL2 = "SELECT * FROM tb_ProductDetail WHERE ProductID = '$product_id' ORDER BY IPN ASC";
        } else {                                        // listing all IPN
            $strSQL  = "SELECT * FROM tb_Product ";     // dummy, just for code simplicity & re-use
            $strSQL1 = "SELECT * FROM tb_ProductDetail $cond $order_by";
            $strSQL2 = "SELECT * FROM tb_ProductDetail $cond";
        }

        $q = $this->db->query($strSQL);
        $ret['product'] = $q->result_array();               // get tb_Product
        $q = $this->db->query($strSQL1);                    // get from tb_ProductDetail info  // could be across multiple pages 
        $ret['ipn'] = $q->result_array();

        $q = $this->db->query($strSQL2);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
        
        $strSQL = "SELECT ID FROM tb_ProductDetail";              // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();             // num of records in IPN
        return $ret;
    }

    function ipn_get_all() {     
        $strSQL  = "SELECT *, tb_ProductDetail.Active AS IpnActive, tb_ProductDetail.Description AS IpnDescription, "
                . "tb_ProductDetail.PackageSize AS IpnPackageSize, tb_ProductDetail.Note AS IpnNote, tb_ProductDetail.ID AS IpnID, "
                
                // Die 1 Information
                . " (SELECT CONCAT(PartNumber) FROM tb_WaferPartNumber "
                . " LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumber.VendorID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_1) AS DiePN_1, "
                
                . " (SELECT CONCAT(DescriptionDesign, ' : ', Description, ' : ', DescriptionInternal) FROM tb_WaferPartNumber  "
                . " LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumberTop.ID = tb_WaferPartNumber.DesignID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_1) AS DieDesc_1,  "
                
                // Die2 Information
                . " (SELECT CONCAT(PartNumber) FROM tb_WaferPartNumber "
                . " LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumber.VendorID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_2) AS DiePN_2, "
                
                . " (SELECT CONCAT(DescriptionDesign, ' : ', Description, ' : ', DescriptionInternal) FROM tb_WaferPartNumber  "
                . " LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumberTop.ID = tb_WaferPartNumber.DesignID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_2) AS DieDesc_2,  "
                
                // Die 3 Information
                . " (SELECT CONCAT(PartNumber) FROM tb_WaferPartNumber "
                . " LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumber.VendorID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_3) AS DiePN_3, "
                
                . " (SELECT CONCAT(DescriptionDesign, ' : ', Description, ' : ', DescriptionInternal) FROM tb_WaferPartNumber  "
                . " LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumberTop.ID = tb_WaferPartNumber.DesignID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_3) AS DieDesc_3,  "
                
                // Die 4 Information
                . " (SELECT CONCAT(PartNumber) FROM tb_WaferPartNumber "
                . " LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumber.VendorID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_4) AS DiePN_4, "
                
                . " (SELECT CONCAT(DescriptionDesign, ' : ', Description, ' : ', DescriptionInternal) FROM tb_WaferPartNumber  "
                . " LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumberTop.ID = tb_WaferPartNumber.DesignID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_4) AS DieDesc_4,  "
                
                // Die 5 information
                . " (SELECT CONCAT(PartNumber) FROM tb_WaferPartNumber "
                . " LEFT JOIN tb_WaferVendor ON tb_WaferVendor.ID = tb_WaferPartNumber.VendorID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_5) AS DiePN_5, "
                
                . " (SELECT CONCAT(DescriptionDesign, ' : ', Description, ' : ', DescriptionInternal) FROM tb_WaferPartNumber  "
                . " LEFT JOIN tb_WaferPartNumberTop ON tb_WaferPartNumberTop.ID = tb_WaferPartNumber.DesignID "
                . " WHERE tb_WaferPartNumber.ID = DiePartNumberID_5) AS DieDesc_5, "

                // 外購料料
                . " (SELECT CONCAT(PartNumber) FROM tb_Product "
                . " WHERE tb_Product.ID = PurchasedItemID) AS PurchasedItemPN, "
                
                . " (SELECT CONCAT(Description, '; ', Description2) FROM tb_Product  "
                . " WHERE tb_Product.ID = PurchasedItemID) AS PurchasedItemDesc "
                
                . " FROM  tb_ProductDetail, tb_Product "
                . " WHERE tb_Product.ID = tb_ProductDetail.ProductID "
                . " ORDER BY tb_ProductDetail.IPN ASC";
             
        $q = $this->db->query($strSQL);
        $ret['ipn'] = $q->result_array();

        return $ret;
    }
    
    function update_record($ipn_id, $data) {
        $this->load->helper('date');

        $q = $this->db->select('ChangeLog')
                ->from('tb_ProductDetail')
                ->where('ID', $ipn_id)
                ->limit(1);
        
        $change_log = $q->get()->result()[0]->ChangeLog;
        $change_log = '{'.date("Y-m-d H:i").', modified by '.$this->session->userdata['nickname'].'},'.$change_log;
        $data['ChangeLog'] = $change_log;

        $this->db->where('ID', $ipn_id);
        $this->db->update('tb_ProductDetail', $data);
    }
    
    function add_record($data) {
        $this->load->helper('date');
        
        // check if PartNumber already exists
        $ipn        = $data['IPN'];
        $sub_con    = $data['SubCon'];
        $strSQL     = "SELECT ID, COUNT(*) as count FROM tb_ProductDetail WHERE IPN LIKE '$ipn' AND SubCon = '$sub_con'";
        $result     = $this->db->query($strSQL);
        if (($result->row()->count) != '0') {
            return (-1);                             // return -1 if P/N exists
        }

        $ChangeLog = '{'.date("Y-m-d H:i").', added by '.$this->session->userdata['nickname'].'},';
        $data['ChangeLog'] = $ChangeLog;

        $this->db->insert('tb_ProductDetail', $data);
        $this->db->trans_complete();
        return $this->db->insert_id();
    }
    
    function searchby_options() {
        $searchby_options  = array();
        $searchby_options['NULL']           = '';
        $searchby_options['IPN']            = 'IPN';
        $searchby_options['PartNumber']     = 'Part Number';
        $searchby_options['SubCon']         = 'SubCon';
        $searchby_options['Description']    = 'IPN Description';
        $searchby_options['PackageSize']    = 'IPN Package Size';
        $searchby_options['SubstrateID']    = 'Substrate ID';
        $searchby_options['BondingDiagram'] = 'B.D.';
        return $searchby_options;
    }
}
?>