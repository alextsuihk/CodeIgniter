<?php

class M_Dpa extends CI_Model {
    function quote_query($limit, $sort_by, $sort_order,$query, $offset, $option, $disti_id) {
               
        if ($sort_by == "PartNumber") {
            $order_by = " ORDER BY pro.PartNumber $sort_order LIMIT $offset, $limit"; 
        } elseif ($sort_by == "RequestDate" || $sort_by == "") {
            $order_by = " ORDER BY dq.RequestDate $sort_order, dq.RequestTime $sort_order LIMIT $offset, $limit"; 
        } else {
            $order_by = " ORDER BY $sort_by $sort_order LIMIT $offset, $limit";
        }
        
        $cond = " WHERE";
        
        if ($disti_id!=0)   { $cond = $cond." fc.DistiID = $disti_id AND"; }
        
        foreach ($query as $key => $value)
        {
            if ($key != "NULL")
            {
                if ($key == 'ID') {
                    $cond = $cond." dq.ID = $value AND";
                } elseif ($key =='Requester') {
                    $cond = $cond." req.Nickname LIKE '%$value%' AND";
                } elseif ($key == 'PartNumber') {
                    $cond = $cond." pro.$key LIKE '%$value%' AND";
                } elseif ($key == 'Customer') {
                    $cond = $cond." (fc.$key LIKE '%$value%' OR com.CompanyCode LIKE '%$value%') AND";
                } else {
                    $cond = $cond." $key LIKE '%$value%' AND";
                }
            } 
        }
        
        if (strpos($option, 'hidecancel') !== FALSE)    { $cond = $cond." dq.Status < 99 AND"; }
        if (strpos($option, 'hidecompleted') !== FALSE) { $cond = $cond." dq.Status != 5 AND"; }
        if (strpos($option, 'requested') !== FALSE)     { $cond = $cond." dq.Status <3  AND"; }
        
        if ($cond == " WHERE") { 
            $cond = ""; 
        } else {
            $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 
        }
         
        $strSQL  = "SELECT *, dq.ID, pro.PartNumber, dq.ChangeLog, "
                . " com.CompanyCode AS CompanyCode, fc.Customer AS FinalCustomer, "
                . "req.Nickname AS Requester, appr.Nickname AS Approver , "
                . "dq.RequestDate AS RequestDate, dq.ApproveDate AS ApproveDate "
                . "FROM tb_DpaQuote dq "
                . "LEFT JOIN tb_FinalCustomer fc ON dq.FinalCustomerID = fc.ID "
                . "LEFT JOIN tb_CompanyUser req  ON dq.RequesterID = req.ID "
                . "LEFT JOIN tb_Company com      ON com.ID = req.CompanyID " 
                . "LEFT JOIN tb_CompanyUser appr ON dq.ApproverID = appr.ID "
                . "LEFT JOIN tb_Product     pro  ON dq.ProductID = pro.ID $cond $order_by";
        
        $strSQL1  = "SELECT *, dq.ID, pro.PartNumber, dq.ChangeLog, "
                . " com.CompanyCode AS CompanyCode, fc.Customer AS FinalCustomer, "
                . "req.Nickname AS Requester, appr.Nickname AS Approver , "
                . "dq.RequestDate AS RequestDate, dq.ApproveDate AS ApproveDate "
                . "FROM tb_DpaQuote dq "
                . "LEFT JOIN tb_FinalCustomer fc ON dq.FinalCustomerID = fc.ID "
                . "LEFT JOIN tb_CompanyUser req  ON dq.RequesterID = req.ID "
                . "LEFT JOIN tb_Company com      ON com.ID = req.CompanyID " 
                . "LEFT JOIN tb_CompanyUser appr ON dq.ApproverID = appr.ID "
                . "LEFT JOIN tb_Product     pro  ON dq.ProductID = pro.ID $cond";
        
        $q = $this->db->query($strSQL);
        $ret['rows'] = $q->result_array();
        
        $q = $this->db->query($strSQL1);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
   
        $strSQL = "SELECT ID FROM tb_DpaQuote";     // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();
        return $ret;
    }
    
    function quote_update($id, $data, $comment, $sys_comment) {
        $this->load->helper('date');
        
        $q = $this->db->select('ChangeLog')
                ->from('tb_DpaQuote')
                ->where('ID', $id)
                ->limit(1);
        
        $change_log = $q->get()->result()[0]->ChangeLog;
        $change_log = '{'.date("Y-m-d H:i").', modified by '.$this->session->userdata['nickname'].' },'.$change_log;;
        $info['ChangeLog'] = $change_log;
        $this->db->where('ID', $id);
        $this->db->update('tb_DpaQuote', $data);

        $this->quote_addcomment(0, $id, $sys_comment); // system comment
        
        if (!empty($comment))
        { $this->quote_addcomment($this->session->userdata['user_id'], $id, $comment); }
        
        return;
    }
      
    function quote_add($info, $requester, $comment) {
        $this->load->helper('date');
        
        $change_log = '{'.date("Y-m-d H:i").', created by '.$this->session->userdata['nickname'].' '.$info['PartNumber'].' '.$info['QuotePrice'].' },';
        $info['ChangeLog'] = $change_log;
        $this->db->insert('tb_DpaQuote', $info);             // add tb_SalesBacklog first
        $this->db->trans_complete();
        $id = $this->db->insert_id();

        $customer_list   = $this->m_dpa->final_customer_list(0);
        $this->quote_addcomment(0, $id, $requester." 申请 ".$customer_list[$info['FinalCustomerID']]." ".$info['PartNumber']." $".$info['QuotePrice'].
                " Max Qty:".$info['QuoteMaxQty']." Start Date:".$info['QuoteStartDate']." Expiry Date:".$info['QuoteExpiryDate'] ); // system comment
        
        if (!empty($comment))
        { $this->quote_addcomment($info['RequesterID'], $id, $comment); }
        
        return ($id);             // added successfully
    }
    
    function quote_addcomment($user_id, $id, $comment)
    {
        $data['QuoteID']    = $id;
        $data['UserID']     = $user_id;
        $data['Comment']    = $comment;
        $this->db->insert('tb_DpaQuoteComment', $data);
    }
    
    function quote_searchby_options() {
        $searchby_options  = array();
        $searchby_options['NULL']           = '';
        $searchby_options['PartNumber']     = 'Part Number';
        $searchby_options['Customer']       = '客人';
        return $searchby_options;
    }
    
    function get_quote_status() {
        $strSQL  = "SELECT * FROM tb_DpaQuoteStatus ";
        
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $status[$row->ID] =  $row->Status;
        }

        return ($status);
    }

    function get_quote_comment($id) {
        $strSQL  = "SELECT tb_DpaQuoteComment.TimeStamp, Comment, c.Nickname AS Commenter FROM tb_DpaQuoteComment "
                . "LEFT JOIN tb_CompanyUser c ON tb_DpaQuoteComment.UserID = c.ID "
                . "WHERE QuoteID = $id ORDER BY TimeStamp DESC";
        
        $q = $this->db->query($strSQL);
        $comment = $q->result_array();
        
        return $comment;
    }   

    function final_customer_list($disti_id) {
        $customer_list = array();
        if ($disti_id == 0) {
            $cond = "WHERE fc.ID>100 ";     // select ALL customer
        } else {
            $cond = "WHERE fc.ID>100 AND DistiID = $disti_id ";
        }

        $strSQL = "SELECT fc.ID, fc.Customer, com.CompanyCode FROM tb_FinalCustomer fc "
                . "LEFT JOIN tb_Company com ON fc.DistiID = com.ID "
                . "$cond ORDER BY fc.Customer asc"; 
           
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $customer_list[$row->ID] = $row->Customer." (".$row->CompanyCode.")";
        }
        return $customer_list;
    }
    
    function recipientXXX($id) {
        $strSQL = "SELECT *, sr.ID  FROM tb_SampleRecipient sr "       // get customer address
                . "LEFT JOIN tb_FinalCustomer fc ON fc.ID = sr.FinalCustomerID  "
                . "WHERE sr.ID = $id "
                . "ORDER BY Name asc"; 
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $recipient = $row->Name." (".$row->Customer.") ".$row->Phone." ".$row->Address;
        }
        return $recipient;
        
    }
    
    function recipient_list($final_customer_id) {
        $recipient_list = array();
        
        if ($final_customer_id <= 100) {    // if this is disti final_customer_id
            $strSQL = "SELECT IsDisti, sr.ID, Name, CompanyCode, Phone, Address FROM tb_SampleRecipient sr INNER JOIN "    // get Disti address
                    . "(SELECT fc.DistiID FROM tb_FinalCustomer fc LEFT JOIN tb_SampleRecipient sr2 ON sr2.FinalCustomerID = fc.ID WHERE fc.ID=$final_customer_id) "
                    . "AS Disti "
                    . "LEFT JOIN tb_FinalCustomer fc2 ON fc2.ID=sr.FinalCustomerID "
                    . "LEFT JOIN tb_Company com ON com.ID = fc2.DistiID "
                    . "WHERE com.ID=Disti.DistiID AND sr.FinalCustomerID = $final_customer_id AND sr.Active=1 ORDER BY sr.NAME asc";        
            $q = $this->db->query($strSQL);

            foreach($q->result() as $row) {
                $recipient_list[$row->ID] = $row->Name." (".$row->CompanyCode.") ".$row->Phone." ".$row->Address."<BR>";
            }
        } else {                //if NOT isDisti, then  pull tb_SampleRecipient tb_FinalCustomer
            $strSQL = "SELECT *, sr.ID FROM tb_SampleRecipient sr INNER JOIN "    // get Disti address
                    . "(SELECT fc.DistiID FROM tb_FinalCustomer fc LEFT JOIN tb_SampleRecipient sr2 ON sr2.FinalCustomerID = fc.ID WHERE fc.ID=$final_customer_id) "
                    . "AS Disti "
                    . "LEFT JOIN tb_FinalCustomer fc2 ON fc2.ID=sr.FinalCustomerID "
                    . "LEFT JOIN tb_Company com ON com.ID = fc2.DistiID "
                    . "WHERE fc2.DistiID=Disti.DistiID AND fc2.ID<=100 AND sr.Active=1 ORDER BY sr.NAME asc";        
            $q = $this->db->query($strSQL);

            foreach($q->result() as $row) {
                $recipient_list[$row->ID] = $row->Name." (".$row->CompanyCode.") ".$row->Phone." ".$row->Address."<BR>";
            }
            
            $strSQL = "SELECT *, sr.ID  FROM tb_SampleRecipient sr "       // get customer address
                    . "LEFT JOIN tb_FinalCustomer fc ON fc.ID = sr.FinalCustomerID  "
                    . "WHERE sr.FinalCustomerID = $final_customer_id AND sr.Active =1 "
                    . "ORDER BY Name asc"; 
            $q = $this->db->query($strSQL);
            foreach($q->result() as $row) {
                $recipient_list[$row->ID] = $row->Name." (".$row->Customer.") ".$row->Phone." ".$row->Address;
            }
        }
        return $recipient_list;
        
        // get customer address
    }
    
    function courier_list() {
        $courier_list = array();
        $strSQL = "SELECT ID, Courier FROM tb_SampleCourier "
                . "ORDER BY ID asc"; 
           
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $courier_list[$row->ID] = $row->Courier;
        }
        return $courier_list;
    }
    
    function warehouse_list($user_id=0) {
        $warehouse_list = array();
        if ($user_id == 0) {
            $strSQL = "SELECT tb_SampleWarehouse.ID, Warehouse, Nickname, RMA FROM tb_SampleWarehouse "
                    . "LEFT JOIN tb_CompanyUser ON tb_SampleWarehouse.OwnerID=tb_CompanyUser.ID "
                    . "ORDER BY Warehouse asc";
            $q = $this->db->query($strSQL);
            foreach($q->result() as $row) {
                $warehouse_list[$row->ID] = $row->Warehouse;
            }
            return $warehouse_list;
        } else {
            $strSQL = "SELECT tb_SampleWarehouse.ID FROM tb_SampleWarehouse "
                    . "LEFT JOIN tb_CompanyUser ON tb_SampleWarehouse.OwnerID=tb_CompanyUser.ID "
                    . "WHERE tb_CompanyUser.ID=$user_id AND RMA=0 "
                    . "ORDER BY Warehouse asc";      
            $q = $this->db->query($strSQL);
            $tmp = (array)$q->result()[0];
            return ( intval($tmp['ID']));
        }
    }

    function inventory_query($limit, $sort_by, $sort_order,$query, $offset, $option, $disti_id, $user_id, $type) {
             
        $cond = " WHERE";
        
//        if ($user_id !=0 )     { $cond = $cond." tb_CompanyUser.ID = $user_id AND"; }
        if ($disti_id!="") { $cond = $cond." com.ID = $disti_id AND"; }
 
        if ($user_id!=0)       { $cond = $cond." wh.OwnerID = '$user_id' AND"; }
        
        foreach ($query as $key => $value) {
            if ($key != "NULL") {
                if ($key == 'ID') {
                    $cond = $cond." si.ID = $value AND";
                } elseif ($key == 'PartNumber') {
                    $cond = $cond." pro.PartNumber LIKE '%$value%' AND";
                } else {
                    $cond = $cond." $key LIKE '%$value%' AND";
                }
            } 
        }
        
        if (strpos($option, 'showrma') !== FALSE) {
            
        } else {
            $cond = $cond." wh.RMA != 1 AND";
        }
        
    
        if (strpos($option, 'grpcomp') !== FALSE) {
            $group_by =" GROUP BY pro.PartNumber, cu.CompanyID ";
        } else {
            $group_by = " GROUP BY pro.PartNumber, si.WarehouseID ";
        }
        $qty = " sum(Qty) AS Qty";
        
        if ($type=="detail") { $group_by = ""; $qty=" Qty "; }
        
        if (strpos($option, 'hiderma') !== FALSE)    { $cond = $cond." si.RMA!=1 AND"; }

        if ($cond == " WHERE") { 
            $cond = ""; 
        } else {
            $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 
        }
        
        if ($sort_by =="Date") { $sort_by = "si.TimeStamp ";}
        
        $order_by = ($sort_by=="")? "":" ORDER BY $sort_by $sort_order LIMIT $offset, $limit";
        
        $strSQL = "SELECT si.Date, si.Time, si.RequestID, si.Comment, "
                . "pro.PartNumber, com.CompanyCode, wh.Warehouse, $qty "
                . "FROM tb_SampleInventory si "
                . "LEFT JOIN tb_SampleWarehouse wh ON si.WarehouseID = wh.ID "
                . "LEFT JOIN tb_CompanyUser cu     ON wh.OwnerID = cu.ID "
                . "LEFT JOIN tb_Company com        ON cu.CompanyID = com.ID "
                . "LEFT JOIN tb_Product pro        ON si.ProductID = pro.ID "
                . " $cond $group_by  $order_by";
        
        $strSQL1 = "SELECT pro.PartNumber  "
                . "FROM tb_SampleInventory si "
                . "LEFT JOIN tb_SampleWarehouse wh ON si.WarehouseID = wh.ID "
                . "LEFT JOIN tb_CompanyUser cu     ON wh.ownerID = cu.ID "
                . "LEFT JOIN tb_Company com        ON cu.CompanyID = com.ID "
                . "LEFT JOIN tb_Product pro        ON si.ProductID = pro.ID "
                . " $cond $group_by ";

        $q = $this->db->query($strSQL);
        $ret['rows'] = $q->result_array();
        
        $q = $this->db->query($strSQL1);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
   
        $strSQL = "SELECT ID FROM tb_SampleInventory";      // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();
        return $ret;
    }

    function inventory_searchby_options($type) {
        $searchby_options  = array();
        $searchby_options['NULL']           = '';
        $searchby_options['PartNumber']     = 'Part Number';
        $searchby_options['CompanyCode']        = 'Company';
        if ($type=="detail") { $searchby_options['Warehouse'] = 'Depot';}
        return $searchby_options;
    }    
}
?>
