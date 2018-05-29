<?php if ( ($action == 'add' || $action =='edit') && (strstr($this->session->userdata['priv'], 'BLG2')) ): ?>
<?php echo form_open('backlog/edit'); ?>
<!-- pass parameters from previous controller to next controller -->
<?php echo form_hidden('backlog_id', $backlog_id); ?>
<?php echo form_hidden('action', $action); ?>
<?php echo form_hidden('limit', $limit); ?>
<?php echo form_hidden('sort_by', $sort_by); ?>
<?php echo form_hidden('sort_order', $sort_order); ?>
<?php echo form_hidden('query', $query); ?>
<?php echo form_hidden('offset', $offset); ?>  
    
<table class="EditBox">
    <tr>
        <td colspan="3">
            <?php echo form_label('Submitter: ', 'submitter'); ?>
            <?php echo form_input('submitter', $backlog['Submitter'], array('maxlength'=>'5', 'size'=>'5') ); ?>
        </td>
        <td colspan="3">
            <?php echo form_label('Entity: ', 'entity'); ?>
            <?php echo form_dropdown("entity", $entity_list, $backlog['Entity'], 'id="entity"') ?>
        </td>
        <td colspan="3">
            <?php echo form_dropdown("company_id", $company_list, $backlog['CompanyID'], 'id="company_id"') ?>
        </td>
        <td colspan="6">
            <?php echo form_label('Customer PO: ', 'customer_po'); ?>
            <?php echo form_input('customer_po', $backlog['CustomerPO'], array('maxlength'=>'16', 'size'=>'16') ); ?>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <?php echo form_label('Order Date: ', 'order_date'); ?>
            <?php echo form_input('order_date', $backlog['OrderDate'], array('maxlength'=>'16', 'size'=>'16') ); ?>
        </td>
        <td colspan="4">Payment Term</td>
        <td colspan="4">Shipment Term</td>
        <td colspan="3">
            Revision: <?php echo $backlog['Revision']; ?>
        </td>
    </tr>
    <tr><td colspan="15">
        <?php echo form_label('Ship To Address: ', 'ship_to_contact_id'); ?>
        <?php echo form_input('ship_to_contact_id', $backlog['ShipToContactID'], array('maxlength'=>'16', 'size'=>'16') ); ?>
    </td></tr>
    <tr><td colspan="15">
        <?php echo form_label('Order Note:', 'order_note'); ?><br>
        <?php echo form_textarea(array('name'=>'order_note', 'id'=>'order_note', 'value'=>$backlog['Note'], 'cols'=>'40', 'rows'=>'3')); ?>
    </td></tr>
</table>
<table class="EditBox">
    <tr>
        <td colspan='1'>Item</td>
        <td colspan='6'>ProductID:</td>
        <td colspan='2'>Booking Price:</td>
        <td colspan='3'>Request Date:</td>
        <td colspan='2'>Order Qty:</td>
        <td colspan='2'>Shipped Qty:</td>
        <td colspan='3'>Status:</td>
        <td colspan='11'></td>    
    </tr>
</table>

<?php foreach ($backlog_items as $i=>$backlog_item): ?>
<table class="EditBox">
    <tr>
        <td colspan='1'><?php echo $backlog_item['Item']; ?></td>
        <td colspan='6'><?php echo form_dropdown("product_id[$i]", $product_list, $backlog_item['ProductID'], 'id="product_id[$i]"') ?></td>
        <td colspan='2'><?php echo form_input("booking_price[$i]", $backlog_item['BookingPrice'], array('maxlength'=>'8', 'size'=>'4')); ?></td>
        <td colspan='3'><?php echo form_input("request_date[$i]", $backlog_item['RequestDate'], array('maxlength'=>'10', 'size'=>'6')); ?></td>
        <td colspan='2'><?php echo form_input("ordered_qty[$i]", $backlog_item['OrderedQty'], array('maxlength'=>'6', 'size'=>'4')); ?></td>
        <td colspan='2'><?php echo form_input("shipped_qty[$i]", $backlog_item['ShippedQty'], array('maxlength'=>'6', 'size'=>'4')); ?></td>
        <td colspan='3'><?php echo form_dropdown("status[$i]", $status_list, $backlog_item['Status'], 'id="status[$i]"') ?></td>
        <td colspan='11'></td>    
    </tr>
    <tr><td colspan='30'>
            <?php $j=$i+1; echo "Shipping Record ($j)  ". form_input("record[$i]", $backlog_item['Record'], array('maxlength'=>'1024', 'size'=>'123') ); ?>  
    </td></tr>
    <tr><td colspan='30'>
            <?php $j=$i+1; echo "Item Note ($j)  ". form_input("item_note[$i]", $backlog_item['Note'], array('maxlength'=>'1024', 'size'=>'129') ); ?>  
    </td></tr>
</table>    
<?php endforeach; ?>
<table class="EditBox">
    <tr>
        <td colspan="10"><div align="center">
            <input title="Clear Data" type="button" value="CLEAR" onclick="location.href='<?php echo base_url().$clear_url;?>'"></div></td>
        <td colspan="10"><div align="center">
            <button title="<?php echo $submit_button;?> Record" type="submit" name="submit"><?php echo $submit_button;?></button></div></td>
        <td colspan="10"><div align="center">
            <input title="Cancel & Close Window" type="button" value="CANCEL"  onclick="self.close()"></div></td>    </tr>

</table>
<?php echo form_close(); ?>

<?php elseif ($action == 'view'): ?>
<table class="ViewBox">
    <tr>
        <td colspan="3">Submitter: <?php echo $backlog['Nickname']; ?></td>
        <td colspan="3">Entity: <?php echo $backlog['Entity']; ?></td>
        <td colspan="3">CompanyID: <?php echo $backlog['CompanyCode']; ?></td>
        <td colspan="6">Customer PO: <?php echo $backlog['CustomerPO']; ?></td>
    </tr>
    <tr>
        <td colspan="4">Order Date: <?php echo $backlog['OrderDate']; ?></td>
        <td colspan="4">Payment Term</td>
        <td colspan="4">Shipment Term</td>
        <td colspan="3">Revision: <?php echo $backlog['Revision']; ?></td>
    </tr>
    <tr><td colspan="15">Ship To Address: 
            <?php echo $backlog['ShipToContactID']; ?>
    </td></tr>

    <tr><td colspan="15">Order Note:<br><textarea readonly><?php echo $backlog['Note']; ?></textarea></td></tr>
    <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
        <tr><td colspan='15'><div align='left'><small><b>ChangeLot:</b><br><?php echo $backlog['ChangeLog']; ?></small></div></td></tr>
    <?php endif; ?>
</table>
<table class="ViewBox">
    <tr>
        <td colspan='1'>Item</td>
        <td colspan='4'>ProductID:</td>
        <td colspan='3'>Booking Price:</td>
        <td colspan='3'>Request Date:</td>
        <td colspan='3'>Order Qty:</td>
        <td colspan='3'>Shipped Qty:</td>
        <td colspan='3'>Status:</td>
        <td colspan='10'></div></td>    
    </tr>
</table>

<?php foreach ($backlog_items as $i=>$backlog_item): ?>
<table class="ViewBox">
    <tr>
        <td colspan='1'><?php echo $backlog_item['Item']; ?></td>
        <td colspan='4'><?php echo $backlog_item['PartNumber']; ?></td>
        <td colspan='3'><div align="center"><?php echo "$".number_format($backlog_item['BookingPrice'],3); ?></div></td>
        <td colspan='3'><div align="center"><?php echo $backlog_item['RequestDate']; ?></div></td>
        <td colspan='3'><div align="right"><?php echo number_format($backlog_item['OrderedQty']); ?>&nbsp;</div></td>
        <td colspan='3'><div align="right"><?php echo number_format($backlog_item['ShippedQty']); ?>&nbsp;</div></td>
        <td colspan='3'><div align="center"><?php echo $backlog_item['Status']; ?></div></td>
        <td colspan='10'></td>    
    </tr>
    <tr><td colspan='30'><?php $j=$i+1; echo "Shipping Record ($j)";?><textarea readonly><?php echo $backlog_item['Record']; ?></textarea></td></tr>
    <tr><td colspan='30'><?php $j=$i+1; echo "Item Note ($j)";?><textarea readonly><?php echo $backlog_item['Note']; ?></textarea></td></tr>
    <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
        <tr><td colspan='30'><div align='left'><small><b>ChangeLot:</b><br><?php echo $backlog_item['ChangeLog']; ?></small></div></td></tr>
    <?php endif; ?>
</table>    
<?php endforeach; ?>
<table class="ViewBox">
    <tr>
        <td colspan="30"><div align="center">
        <input title="Close this Window" type="button" value="CLOSE" onclick="self.close()">
    </div></td></tr>    </tr>

</table>

<?php else: ?>

<table class="EditBox">
    <?php echo form_open('backlog/search'); ?>
    <?php echo form_hidden('limit', $limit); ?>
    <?php echo form_hidden('sort_by', $sort_by); ?>
    <?php echo form_hidden('sort_order', $sort_order); ?>
    <tr>
        <th width="40%" rowspan="2">
            <?php $i=0; ?>
            <div align="left"> &nbsp;&nbsp;&nbsp;&nbsp; Search By:
            &nbsp;&nbsp;&nbsp;<button title="Add more Search Criteria" type="submit" name="submit" value=add:'.$i.'>+</button></div>
            <?php foreach ($query as $key=>$value): ?>
            <?php echo form_dropdown("searchby[$i]", $searchby_options, $key, "id=\"searchby[$i]\"") ?>
            <?php echo form_input("search_value[$i]", ($key=='ID')?'':$value, array('maxlength'=>'32', 'size'=>'25')); ?>
            <button title="Remove this Criteria" type="submit" name="submit" value=delete:'.$i.'>-</button>
            <br>
            <?php $i++; ?>
            <?php endforeach; ?>
        </th>
        <th width="53%" rowspan="2"><div align="left">
            <?php $hide_cancel = ((strpos($option, 'hidecancel') !== FALSE))?TRUE:FALSE; ?>
            <?php echo form_checkbox('hide_cancel', '1', $hide_cancel); ?> Hide Cancel Backlog
            <BR>
            <?php $hide_completed = (strpos($option, 'hidecompleted') !== FALSE)?TRUE:FALSE; ?>
            <?php echo form_checkbox('hide_completed', '1', $hide_completed); ?> Hide Completed Backlog
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
        <th width="5%"></th>   
        <th width="7%"<?php if ($sort_by == "CompanyCode") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("backlog?action=$action&sb=CompanyCode&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "Code"); ?></th>
        <th width="9%"<?php if ($sort_by == "OrderDate") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("backlog?action=$action&sb=OrderDate&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Order Date"); ?></th>
        <th width="15%">PO Number</th>
        <th width="3%"<?php if ($sort_by == "Item") echo "class=\"sort_$sort_order\"" ?>><small>
            <?php echo anchor("backlog?action=$action&sb=Item&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "Item"); ?></small></th>
        <th width="18%" <?php if ($sort_by == "PartNumber") echo "class=\"sort_$sort_order\"" ?>> 
            <?php echo anchor("backlog?action=$action&sb=PartNumber&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Part Number"); ?></th>
        <th width="8%" <?php if ($sort_by == "RequestDate") echo "class=\"sort_$sort_order\"" ?>> 
            <?php echo anchor("backlog?action=$action&sb=RequestDate&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Request Date"); ?></th>
        <th width="7%">Booking Price</th>
        <th width="7%">Ordered Qty</th>
        <th width="7%">Shipped Qty</th>
        <th width="7%">Status</th>
        <th width="7%">
            <?php if (strstr($this->session->userdata['priv'], 'BLG2')) :  ?>
                <button title="Add Backlog" type="button" onclick="window.open('<?php echo base_url();?>backlog?action=add')">
                    <img src="<?php echo site_url('img/add-big.png'); ?>"/></button>
            <?php endif; ?>
        </th>
    </thead>

    <tbody>  
        <?php foreach($items as $item): ?>
        <tr>
            <?php $query_url = urlencode(json_encode(array("ID"=>$item['BacklogID']))); ?>
            <td><small><?php echo $item['Entity']; ?></small></td>
            <td><?php echo $item['CompanyCode']; ?></td>
            <td><?php echo $item['OrderDate']; ?></td>
            <td><div align="left"><?php echo $item['CustomerPO']; ?></div></td>
            <td><?php echo $item['Item']; ?></td>
            <td><div align="left"><?php echo $item['PartNumber']; ?></div></td>
            <?php if ($item['RequestDate'] < date("Y-m-d", strtotime('+3 day'))) : ?>
                <td><div style="color:red"><b><?php echo $item['RequestDate']; ?></b></div></td>
            <?php elseif ($item['RequestDate'] < date("Y-m-d", strtotime('+10 day'))) : ?>
                <td><div style="color:navy"><b><?php echo $item['RequestDate']; ?></b></div></td>
            <?php else: ?>
                <td><div style="color:green"><?php echo $item['RequestDate']; ?></div></td>
            <?php endif; ?>
            <td><div align="right"><?php echo "$".number_format($item['BookingPrice'],3); ?></div></td>
            <td><div align="right"><?php echo number_format($item['OrderedQty']); ?></div></td>
            <td><div align="right"><?php echo number_format($item['ShippedQty']); ?></div></td>
            <td><?php echo $item['Status']; ?></td>
            <td><button title="View" type="button" onclick="window.open('<?php echo base_url();?>backlog?action=view&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
                    <img src="<?php echo site_url('img/view.png'); ?>"/></button>  
                <?php if (strstr($this->session->userdata['priv'], 'BLG2')) : ?>
                    <button title="Modify" type="button" onclick="window.open('<?php echo base_url();?>backlog?action=edit&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
                        <img src="<?php echo site_url('img/edit.png'); ?>"/></button>    
                <?php endif; ?>
            </small></td>
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
        <div style="color:red">Due within 3 days or overdue </div>
        <div style="color:navy">Due within 10 Days</div>
        
        </small></div></td></tr>
    </tbody>
</table>
