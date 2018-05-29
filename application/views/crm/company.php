<?php if (($action == 'add' || $action =='edit') && strstr($this->session->userdata['priv'], 'CRM20')): ?>
<table class="EditBox">
     <?php echo form_open('crm/company_edit'); ?>
     <!-- pass parameters from previous controller to next controller -->
     <?php echo form_hidden('company_id', $company_id); ?>
     <?php echo form_hidden('action', $action); ?>
     <?php echo form_hidden('limit', $limit); ?>
     <?php echo form_hidden('sort_by', $sort_by); ?>
     <?php echo form_hidden('sort_order', $sort_order); ?>
     <?php echo form_hidden('query', $query); ?>
     <?php echo form_hidden('offset', $offset); ?>     
     <tr>
        <td colspan="2">
            <div align="left"><?php echo form_label('Company Code: ', 'company_code'); ?>
            <?php echo form_input('company_code', $edit['0']['CompanyCode'], array('maxlength'=>'16', 'size'=>'10') ); ?></div></td>
        <td colspan="2">VendorID (WIP)
            <?php echo form_input('vendor_id', $edit['0']['VendorID'], array('maxlength'=>'8', 'size'=>'8')); ?>
        </td>
        <td colspan="2">CustomerID (WIP)<?php echo form_input('customer_id', $edit['0']['CustomerID'], array('maxlength'=>'8', 'size'=>'8')); ?></td>
        <td colspan="2">SubconID (WIP)<?php echo form_input('subcon_id', $edit['0']['SubconID'], array('maxlength'=>'8', 'size'=>'8')); ?></td>
        <td colspan="1">
            <?php echo form_label('Active:', 'active'); ?>
            <?php echo form_checkbox('active', '1', (!$edit['0']['Active'])? FALSE:TRUE); ?>     
        </td>
     </tr>
    <tr>
        <td colspan="9"><div align="left">   
            Company Name: 
            (中)<?php echo form_input('company_name', $edit['0']['CompanyName'], array('maxlength'=>'64', 'size'=>'55')); ?> &nbsp;
            (英)<?php echo form_input('company_name2', $edit['0']['CompanyName2'], array('maxlength'=>'64', 'size'=>'55')); ?>  
            </div></td>
    </tr>
    <tr><td colspan="9"><div align='left'>
        <?php echo form_label('Website: http://', 'website'); ?>
        <?php echo form_input('website', $edit['0']['Website'], array('maxlength'=>'64', 'size'=>'64')); ?>  
    </div></td></tr>

    <tr><td colspan="9">Bill-To Contact & Address: <?php echo form_dropdown("bill_to_contact_id", $contact_list, $edit['0']['BillToContactID'], 'id="bill_to_contact_id"') ?></td></tr>
    <tr><td colspan="9"><?php echo form_dropdown("bill_to_address_id", $address_list, $edit['0']['BillToAddressID'], 'id="bill_to_address_id"') ?></td></tr>

    <tr><td colspan="9">Ship-To Contact & Address: <?php echo form_dropdown("ship_to_contact_id", $contact_list, $edit['0']['ShipToContactID'], 'id="ship_to_contact_id"') ?></td></tr>    
    <tr><td colspan="9"><?php echo form_dropdown("ship_to_address_id", $address_list, $edit['0']['ShipToAddressID'], 'id="ship_to_address_id"') ?></td></tr>
    <tr>
        <td colspan="9"><div align='left'>
            <?php echo form_label('Note:', 'note'); ?><br>
            <?php echo form_textarea(array('name'=>'note', 'id'=>'note', 'value'=>$edit['0']['Note'], 'cols'=>'40', 'rows'=>'3')); ?>
        </div></td>
    </tr>
    <tr>
        <td colspan="3"><div align="center">
            <input title="Clear Data" type="button" value="CLEAR"onclick="location.href='<?php echo base_url().$clear_url;?>'"></div></td>
        <td colspan="3"><div align="center">
            <button title="<?php echo $submit_button;?> Record" type="submit" name="submit"><?php echo $submit_button;?></button></div></td>
        <td colspan="3"><div align="center">
            <input title="Cancel & Close Window" type="button" value="CANCEL" onclick="self.close()"></div></td>
    </tr>
    <?php echo form_close(); ?>
</table>

<?php elseif ($action == 'view'): ?>
<table class="ViewBox">
     <tr>
        <td colspan="2">
            <div align="left"><b>Company Code: </b><?php echo $edit['0']['CompanyCode'];?></div></td>
        <td colspan="2"><b>VendorID: </b><?php echo $edit['0']['VendorID'];?></td>
        <td colspan="2"><b>CustomerID: </b><?php echo $edit['0']['CustomerID'];?></td>
        <td colspan="2"><b>SubconID: </b><?php echo $edit['0']['SubconID'];?></td>
        <td colspan="1"><b><?php echo (($edit['0']['Active'])? 'Active':'Inactive'); ?></b></td>
     </tr>
    <tr>
        <td colspan="9"><div align="left"><b>Company Name: (中) </b><?php echo $edit['0']['CompanyName'];?> &nbsp;<b>(英)</b> <?php echo $edit['0']['CompanyName2'];?></div></td>
    </tr>
    <tr><td colspan="9"><div align='left'><b>Website: </b>http://<?php echo $edit['0']['Website'];?></div></td></tr>
    <tr>
        <td colspan="9"><b>Bill-To Contact: </b><?php echo $edit['0']['BillToContact']."<BR>".$edit['0']['BillToAddress']; ?></div></td>
    </tr>
    <tr>
        <td colspan="9"><b>Ship-To Contact: </b><?php echo $edit['0']['ShipToContact']."<BR>".$edit['0']['ShipToAddress']; ?></div></td>
    </tr>
    <tr><td colspan='9'><b>Note:</b><br><textarea readonly><?php echo $edit['0']['Note']; ?></textarea></td></tr>
    <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
        <tr><td colspan='9'><div align='left'><small><b>ChangeLot:</b><br><?php echo $edit['0']['ChangeLog']; ?></small></div></td></tr>
    <?php endif; ?>
    <tr>
        <td colspan="9"><div align="center">
            <input title="Close this Window" type="button" value="CLOSE" onclick="self.close()"></div></td>
    </tr>
</table>

<?php else: ?>                                  <!-- listing data -->
<table class="EditBox">
    <?php echo form_open('crm/company_search'); ?>
    <!-- pass parameters from previous controller to next controller -->
    <?php echo form_hidden('limit', $limit); ?>
    <?php echo form_hidden('sort_by', $sort_by); ?>
    <?php echo form_hidden('sort_order', $sort_order); ?>
    <tr>
        <th width="40%" rowspan="2">
            <?php $i=0; ?>
            <div align="left"> &nbsp;&nbsp;&nbsp;&nbsp; Search By:
            <!-- &nbsp;&nbsp;&nbsp;<button title="Add more Search Criteria" type="submit" name="submit" value=add:'.$i.'>+</button> --> </div>
            <?php foreach ($query as $key=>$value): ?>
            <?php echo form_dropdown("searchby[$i]", $searchby_options, $key, "id=\"searchby[$i]\"") ?>
            <?php echo form_input("search_value[$i]", ($key=='ID')?'':$value, array('maxlength'=>'32', 'size'=>'25')); ?>
            <!-- <button title="Remove this Criteria" type="submit" name="submit" value=delete:'.$i.'>-</button> -->
            <br>
            <?php $i++; ?>
            <?php endforeach; ?>
        </th>
        <th width="53%" rowspan="2"><div align="left">
            <?php $active_only = ((strpos($option, 'activeonly') !== FALSE))?TRUE:FALSE; ?>
            <?php echo form_checkbox('active_only', '1', $active_only); ?> Show Active Company Only
        </div></th>
        <th width="7%">
            <?php if ($query != ''):?>
                <button title="Clear All Query" type="button" name="clear" onclick="location.href='<?php echo base_url().$cancel_url;?>'">Clear</button>

            <?php endif; ?>
        </th>
    </tr>  
    <tr><th>
            <button title="Query" type="submit" name="submit" value=search:'.$i.'>Search</button>
    </th></tr>
    <?php echo form_close(); ?>
</table>
<?php endif; ?>

<br>
<div>
    Matched Records: <?php echo $matched_records; ?> of <?php echo $total_records; ?>
</div>

<table class="ListBox">
    <thead>
        <th  width="8%" <?php if ($sort_by == "CompanyCode") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/company?action=$action&sb=CompanyCode&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "Code"); ?></th>
        <th width="27%"<?php if ($sort_by == "CompanyName") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/company?action=$action&sb=CompanyName&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Company Name"); ?></th>
        <th width="7%">Supplier</th>
        <th width="7%">Customer</th>
        <th width="7%">Subcon</th>
        <th width="7%">Bill-To Contact</th>
        <th width="7%">Ship-To Contact</th>
        <th width="20%">Website</th>
        <th width="3%"></th>
        <th width="7%">
            <?php if (strstr($this->session->userdata['priv'], 'CRM20')) :  ?>
                <button title="Add Company" type="button" onclick="window.open('<?php echo base_url();?>crm/company?action=add')">
                    <img src="<?php echo site_url('img/add-big.png'); ?>"/></button>
            <?php endif; ?>
        </th>
    </thead>

    <tbody>  
        <?php foreach($companies as $company): ?>
        <tr>
            <td><?php echo $company['CompanyCode']; ?></td>
            <td><div align="left"><?php echo $company['CompanyName']."<br> ".$company['CompanyName2']; ?><div></td>

            <td>
                <?php if ($company['VendorID'] == 0): ?>
                    <?php $query_url = urlencode(json_encode(array("CID"=>$company['ID']))); ?>
                    <button title="Enter Vendor Info" type="button" onclick="window.open('<?php echo base_url();?>crm/vendor?action=add&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
                <?php else: ?>
                    <?php $query_url = urlencode(json_encode(array("ID"=>$company['VendorID']))); ?>
                    <button title="View Vendor Info" type="button" onclick="window.open('<?php echo base_url();?>crm/vendor?action=view&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/view.png'); ?>"/></button>
                    <button title="Modify Vendor Info" type="button" onclick="window.open('<?php echo base_url();?>crm/vendor?action=edit&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
                <?php endif; ?>
            </td>
            
            <td>
                <?php if ($company['CustomerID'] == 0): ?>
                    <?php $query_url = urlencode(json_encode(array("CID"=>$company['ID']))); ?>
                    <button title="Enter Customer Info" type="button" onclick="window.open('<?php echo base_url();?>crm/customer?action=add&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
                <?php else: ?>
                    <?php $query_url = urlencode(json_encode(array("ID"=>$company['CustomerID']))); ?>
                    <button title="View Customer Info" type="button" onclick="window.open('<?php echo base_url();?>crm/customer?action=view&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/view.png'); ?>"/></button>
                    <button title="Modify Customer Info" type="button" onclick="window.open('<?php echo base_url();?>crm/customer?action=edit&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
                <?php endif; ?>
            </td>

            <td>
                <?php if ($company['CustomerID'] == 0): ?>
                    <?php $query_url = urlencode(json_encode(array("CID"=>$company['ID']))); ?>
                    <button title="Enter Subcon Info" type="button" onclick="window.open('<?php echo base_url();?>crm/subcon?action=add&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
                <?php else: ?>
                    <?php $query_url = urlencode(json_encode(array("ID"=>$company['SubconID']))); ?>
                    <button title="View Subcon Info" type="button"  onclick="window.open('<?php echo base_url();?>crm/subcon?action=view&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/view.png'); ?>"/></button>
                    <button title="Modify Subcon Info" type="button"  onclick="window.open('<?php echo base_url();?>crm/subcon?action=edit&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
                <?php endif; ?>
            </td>
            
            <td>
                <?php if ($company['BillToContact'] == 0): ?>
                    <?php $query_url = urlencode(json_encode(array("CID"=>$company['ID']))); ?>
                    <button title="Link Address" type="button" onclick="window.open('<?php echo base_url();?>crm/link_address?action=add&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
                <?php else: ?>
                    <?php $query_url = urlencode(json_encode(array("ID"=>$company['SubconID']))); ?>
                    <button title="View Address" type="button" onclick="window.open('<?php echo base_url();?>crm/address?action=view&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/view.png'); ?>"/></button>
                    <button title="Modify Address" type="button" onclick="window.open('<?php echo base_url();?>crm/link_address?action=edit&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
                <?php endif; ?>
            </td>

            <td>
                <?php if ($company['ShipToContact'] == 0): ?>
                    <?php $query_url = urlencode(json_encode(array("CID"=>$company['ID']))); ?>
                    <button title="Link Address" type="button" onclick="window.open('<?php echo base_url();?>crm/link_address?action=add&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
                <?php else: ?>
                    <?php $query_url = urlencode(json_encode(array("ID"=>$company['SubconID']))); ?>
                    <button title="View Address" type="button" onclick="window.open('<?php echo base_url();?>crm/address?action=view&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/view.png'); ?>"/></button>
                    <button title="Modify Address" type="button" onclick="window.open('<?php echo base_url();?>crm/link_address?action=edit&query=<?php echo $query_url;?>')">
                        <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
                <?php endif; ?>
            </td>
            
            <td><a href="http://<?php echo $company['Website']; ?>" target="_blank"><?php echo $company['Website']; ?></a></td>
            <td><?php echo ($company['Active'])?"A":"I"; ?></td>
            <?php $query_url = urlencode(json_encode(array("ID"=>$company['ID']))); ?>
            <td><button title="View" type="button" onclick="window.open('<?php echo base_url();?>crm/company?action=view&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
                    <img src="<?php echo site_url('img/view.png'); ?>"/></button>        
            <?php if (strstr($this->session->userdata['priv'], 'CRM20')) :  ?>
                <button title="Modify" type="button" onclick="window.open('<?php echo base_url();?>crm/company?action=edit&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
                    <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
            <?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br>

<?php if (strlen($pagination)): ?>
<div>
    Pages:  <?php echo $pagination; ?>
</div>
<br>
<?php endif; ?>

<table class="NoteBox">
    <tbody>
        <tr><td><div align="left"><small>
        Note: <br>
        V - Vendor
        C - Customer
        S - Subcon
        </small></div></td></tr>
    </tbody>
</table>
