<?php

class M_Sample extends CI_Model {
    function request_query($limit, $sort_by, $sort_order,$query, $offset, $option, $disti_id, $user_id) {
               
        if ($sort_by == "PartNumber") {
            $order_by = " ORDER BY pro.PartNumber $sort_order LIMIT $offset, $limit"; 
        } elseif ($sort_by == "RequestDate" || $sort_by == "") {
            $order_by = " ORDER BY sr.RequestDate $sort_order, sr.RequestTime $sort_order LIMIT $offset, $limit"; 
        } else {
            $order_by = " ORDER BY $sort_by $sort_order LIMIT $offset, $limit";
        }
        
        $cond = " WHERE da.Active = 1 AND";
        
        if ($user_id !=0 )  { $cond = $cond." req.ID = $user_id AND"; }
        if ($disti_id!=0)   { $cond = $cond." fc.DistiID = $disti_id AND"; }
        
        foreach ($query as $key => $value)
        {
            if ($key != "NULL")
            {
                if ($key == 'ID') {
                    $cond = $cond." sr.ID = $value AND";
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
        
        if (strpos($option, 'hidecancel') !== FALSE)    { $cond = $cond." sr.Status < 99 AND"; }
        if (strpos($option, 'hidecompleted') !== FALSE) { $cond = $cond." sr.Status != 5 AND"; }
        if (strpos($option, 'requested') !== FALSE)     { $cond = $cond." sr.Status <3  AND"; }
        
        if ($cond == " WHERE") { 
            $cond = ""; 
        } else {
            $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 
        }
         
        $strSQL  = "SELECT *, sr.ID, pro.PartNumber, sr.ChangeLog, "
                . " com.CompanyCode AS CompanyCode, fc.Customer AS FinalCustomer, "
                . "req.Nickname AS Requester, pm.Nickname AS PmApprover, appr.Nickname AS Approver , "
                . "dock.Nickname AS Docker, ship.Nickname AS Shipper, "
                . "sr.RequestDate AS RequestDate, sr.ApproveDate AS ApproveDate, "
                . "concat(da.Name, da.Phone, da.Address) AS Recipient, "
                . "sc.Courier AS Courier "
                . "FROM tb_SampleRequest sr "
                . "LEFT JOIN tb_FinalCustomer fc ON sr.FinalCustomerID = fc.ID "
                . "LEFT JOIN tb_CompanyUser req  ON sr.RequesterID = req.ID "
                . "LEFT JOIN tb_Company com      ON com.ID = req.CompanyID " 
                . "LEFT JOIN tb_CompanyUser pm   ON sr.PmApproverID = pm.ID "
                . "LEFT JOIN tb_CompanyUser appr ON sr.ApproverID = appr.ID "
                . "LEFT JOIN tb_CompanyUser dock ON sr.DockerID = dock.ID "
                . "LEFT JOIN tb_CompanyUser ship ON sr.ShipperID = ship.ID "
                . "LEFT JOIN tb_SampleRecipient da ON sr.RecipientID = da.ID "
                . "LEFT JOIN tb_SampleCourier sc ON sr.CourierID = sc.ID "
                . "LEFT JOIN tb_Product     pro  ON sr.ProductID = pro.ID $cond $order_by";
        $strSQL1 = "SELECT * FROM tb_SampleRequest sr "
                . "LEFT JOIN tb_FinalCustomer fc ON sr.FinalCustomerID = fc.ID "
                . "LEFT JOIN tb_CompanyUser req  ON sr.RequesterID = req.ID "
                . "LEFT JOIN tb_Company com      ON com.ID = req.CompanyID " 
                . "LEFT JOIN tb_CompanyUser pm   ON sr.PmApproverID = pm.ID "
                . "LEFT JOIN tb_CompanyUser appr ON sr.ApproverID = appr.ID "
                . "LEFT JOIN tb_CompanyUser dock ON sr.DockerID = dock.ID "
                . "LEFT JOIN tb_CompanyUser ship ON sr.ShipperID = ship.ID "
                . "LEFT JOIN tb_SampleRecipient da ON sr.RecipientID = da.ID "
                . "LEFT JOIN tb_SampleCourier sc ON sr.CourierID = sc.ID "
                . "LEFT JOIN tb_Product     pro  ON sr.ProductID = pro.ID $cond";
        $q = $this->db->query($strSQL);
        $ret['rows'] = $q->result_array();
        
        $q = $this->db->query($strSQL1);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
   
        $strSQL = "SELECT ID FROM tb_SampleRequest";     // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();
        return $ret;
    }
    
    function request_update($id, $data, $comment, $sys_comment) {
        $this->load->helper('date');
        
        $q = $this->db->select('ChangeLog')
                ->from('tb_SampleRequest')
                ->where('ID', $id)
                ->limit(1);
        
        $change_log = $q->get()->result()[0]->ChangeLog;
        $change_log = '{'.date("Y-m-d H:i").', modified by '.$this->session->userdata['nickname'].' },'.$change_log;;
        $info['ChangeLog'] = $change_log;
        $this->db->where('ID', $id);
        $this->db->update('tb_SampleRequest', $data);

        $this->request_addcomment(0, $id, $sys_comment); // system comment
        
        if (!empty($comment))
        { $this->request_addcomment($this->session->userdata['user_id'], $id, $comment); }
        
        return;
    }
      
    function request_add($info, $requester, $comment) {
        $this->load->helper('date');
        
        $change_log = '{'.date("Y-m-d H:i").', created by '.$this->session->userdata['nickname'].' '.$info['RequestQty'].' '.$info['PartNumber'].' },';
        $info['ChangeLog'] = $change_log;
        $this->db->insert('tb_SampleRequest', $info);             // add tb_SalesBacklog first
        $this->db->trans_complete();
        $id = $this->db->insert_id();

        $this->request_addcomment(0, $id, $requester." 申请 ".$info['RequestQty']." ".$info['PartNumber']); // system comment
        
        if (!empty($comment))
        { $this->request_addcomment($info['RequesterID'], $id, $comment); }
        
        return ($id);             // added successfully
    }
    
    function request_addcomment($user_id, $id, $comment)
    {
        $data['RequestID']  = $id;
        $data['UserID']     = $user_id;
        $data['Comment']    = $comment;
        $this->db->insert('tb_SampleRequestComment', $data);
    }
    
    function request_searchby_options() {
        $searchby_options  = array();
        $searchby_options['NULL']           = '';
        $searchby_options['PartNumber']     = 'Part Number';
        $searchby_options['Customer']       = '代理/客人';
        $searchby_options['Requester']      = '申请人';
        return $searchby_options;
    }

    function addr_query($limit, $sort_by, $sort_order,$query, $offset, $option, $disti_id) {
               
       
        $cond = " WHERE";
        
       if ($disti_id !=0) { $cond = $cond." fc.DistiID = $disti_id AND"; }
        
        foreach ($query as $key => $value)
        {
            if ($key != "NULL")
            {
                if ($key == 'ID') {
                    $cond = $cond." sr.ID = $value AND";
                } elseif ($key == 'Customer') {
                    $cond = $cond." (fc.Customer LIKE '%$value%' OR com.CompanyCode LIKE '%$value%') AND";
                } else {
                    $cond = $cond." $key LIKE '%$value%' AND";
                }
            } 
        }
        
        if (strpos($option, 'activeonly') !== FALSE) { $cond = $cond." sr.Active = 1 AND"; } 
        
        if ($cond == " WHERE") { 
            $cond = ""; 
        } else {
            $cond = preg_replace('/AND$/', '', $cond);      // remove the last AND 
        }
        
        $order_by = ($sort_by=="")? "":" ORDER BY $sort_by $sort_order LIMIT $offset, $limit";

        $strSQL  = "SELECT *, sr.ID "
                . "FROM tb_SampleRecipient sr "
                . "LEFT JOIN tb_FinalCustomer fc ON sr.FinalCustomerID = fc.ID "
                . "LEFT JOIN tb_Company com ON fc.DistiID = com.ID "
                . "$cond $order_by";
        
        $strSQL1  = "SELECT *, sr.ID "
                . "FROM tb_SampleRecipient sr "
                . "LEFT JOIN tb_FinalCustomer fc ON sr.FinalCustomerID = fc.ID "
                . "LEFT JOIN tb_Company com ON fc.DistiID = com.ID "
                . "$cond";
         

        $q = $this->db->query($strSQL);
        $ret['rows'] = $q->result_array();
        
        $q = $this->db->query($strSQL1);                    
        $ret['matched_records'] = $q->num_rows();           // # of records matched
   
        $strSQL = "SELECT ID FROM tb_SampleRecipient";     // total record in the table
        $q = $this->db->query($strSQL);                     // total record in the table
        $ret['total_records'] = $q->num_rows();
        return $ret;
    }
    
    function addr_searchby_options() {
        $searchby_options  = array();
        $searchby_options['NULL']           = '';
        $searchby_options['Customer']       = '代理/客人';
        $searchby_options['Name']           = '收件人';
        $searchby_options['Phone']          = '电话';
        return $searchby_options;
    }

    function addr_update($id, $data) {
        $this->load->helper('date');
        
        $q = $this->db->select('ChangeLog')
                ->from('tb_SampleRequest')
                ->where('ID', $id)
                ->limit(1);
        
        $change_log = $q->get()->result()[0]->ChangeLog;
        $change_log = '{'.date("Y-m-d H:i").', modified by '.$this->session->userdata['nickname'].' },'.$change_log;
        $data['ChangeLog'] = $change_log;
        $this->db->where('ID', $id);
        $this->db->update('tb_SampleRecipient', $data);       
        return;
    }
      
    function addr_add($data) {
        $this->load->helper('date');
        
        $change_log = '{'.date("Y-m-d H:i").', modified by '.$this->session->userdata['nickname'].' },';
        $data['ChangeLog'] = $change_log;
        $this->db->insert('tb_SampleRecipient', $data);
        $this->db->trans_complete();
        $id = $this->db->insert_id();
        return ($id);             // added successfully
    }
    
    function get_request_status() {
        $strSQL  = "SELECT * FROM tb_SampleStatus ";
        
        $q = $this->db->query($strSQL);
        foreach($q->result() as $row) {
            $status[$row->ID] =  $row->Status;
        }

        return ($status);
    }

    function get_request_comment($id) {
        $strSQL  = "SELECT tb_SampleRequestComment.TimeStamp, Comment, c.Nickname AS Commenter FROM tb_SampleRequestComment "
                . "LEFT JOIN tb_CompanyUser c ON tb_SampleRequestComment.UserID = c.ID "
                . "WHERE RequestID = $id ORDER BY TimeStamp DESC";
        
        $q = $this->db->query($strSQL);
        $comment = $q->result_array();
        
        return $comment;
    }   

    function inventory_move($info) {
        $this->load->helper('date');
        
        $change_log = '{'.date("Y-m-d H:i").', created by '.$this->session->userdata['nickname'].' '.$info['Qty'].' '.$info['PartNumber'].' },';
        $info['ChangeLog'] = $change_log;
        $this->db->insert('tb_SampleInventory', $info);
        $this->db->trans_complete();
        $id = $this->db->insert_id();        
        return ($id);             // added successfully
    }
    
    function final_customer_list($disti_id) {
        $customer_list = array();
        if ($disti_id == 0) {
            $cond = "";     // select ALL customer
        } else {
            $cond = "WHERE DistiID = $disti_id ";
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
    
    function recipient($id) {
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
