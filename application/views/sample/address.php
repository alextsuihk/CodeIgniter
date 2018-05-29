<?php if ( ($action == 'addr_add' || $action =='addr_edit') 
        && (strstr($this->session->userdata['priv'], 'SAM31') || strstr($this->session->userdata['priv'], 'SAM32')) ): ?>
<?php echo form_open('sample/addr_edit'); ?>
<?php echo form_hidden('action', $action); ?>
<?php echo form_hidden('limit', $limit); ?>
<?php echo form_hidden('sort_by', $sort_by); ?>
<?php echo form_hidden('sort_order', $sort_order); ?>
<?php echo form_hidden('query', $query); ?>
<?php echo form_hidden('offset', $offset); ?>  
<?php echo form_hidden('id', $id); ?>  


<table class="EditBox">
    <tr><td colspan="7"><center>样品收件人</center></td></tr>
    <tr>
        <td colspan="2">公司<?php echo form_dropdown("final_customer_id", $customer_list, $edit['0']['FinalCustomerID'], 'id="final_customer_id"'); ?></td>
        <td colspan="2">收件人： <?php echo form_input('name', $edit['0']['Name'], array('maxlength'=>'32', 'size'=>'16')); ?> </td>  
        <td colspan="2">电话： <?php echo form_input('phone', $edit['0']['Phone'], array('maxlength'=>'32', 'size'=>'16')); ?> </td>  
        <td colspan="1">有效： <?php echo form_checkbox('active', '1', (!$edit['0']['Active'])? FALSE:TRUE); ?>   </td>  
    </tr>
    <tr><td colspan="7">
        <b>地址: </b><br>
        <?php echo form_textarea(array('name'=>'address', 'id'=>'address', 'value'=>$edit['0']['Address'], 'cols'=>'40', 'rows'=>'2')); ?>
    </td></tr>
</table>

<table class="EditBox">
    <tr>
        <td><div align="center">
            <input title="Clear Data" type="button" value="CLEAR" onclick="location.href='<?php echo base_url().$clear_url;?>'"></div></td>
        <td><div align="center">
            <input title="Cancel & Close Window" type="button" value="CANCEL" onclick="self.close()"></div></td>
        <td><div align="center">
            <button title="<?php echo $submit_button;?>" type="submit" name="submit"><?php echo $submit_button;?></button></div></td>
    </tr>
</table>
<?php echo form_close(); ?>

<?php else: ?>

<table class="EditBox">
    <?php echo form_open('sample/addr_search'); ?>
    <?php echo form_hidden('limit', $limit); ?>
    <?php echo form_hidden('sort_by', $sort_by); ?>
    <?php echo form_hidden('sort_order', $sort_order); ?>
    <tr>
        <th width="40%" rowspan="2">
            <?php $i=0; ?>
            <div align="left"> &nbsp;&nbsp;&nbsp;&nbsp; Search By:
            &nbsp;&nbsp;&nbsp;<button title="Add more Search Criteria" type="submit" name="submit" value=add:'.$i.'>+</button> </div>
            <?php foreach ($query as $key=>$value): ?>
            <?php echo form_dropdown("searchby[$i]", $searchby_options, $key, "id=\"searchby[$i]\"") ?>
            <?php echo form_input("search_value[$i]", ($key=='ID')?'':$value, array('maxlength'=>'32', 'size'=>'25')); ?>
            <button title="Remove this Criteria" type="submit" name="submit" value=delete:'.$i.'>-</button>
            <br>
            <?php $i++; ?>
            <?php endforeach; ?>
        </th>
        <th width="53%" rowspan="2"><div align="left">
            <?php $active_only = ((strpos($option, 'activeonly') !== FALSE))?TRUE:FALSE; ?>
            <?php echo form_checkbox('active_only', '1', $active_only); ?> Show Active Recipient Only
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
        <th width="14%"<?php if ($sort_by == "Customer") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("sample?action=$action&sb=Customer&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "客人[代理]"); ?></th>
        
        <th width="12%"<?php if ($sort_by == "Name") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("sample?action=$action&sb=Name&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "收件人"); ?></th>
        
        <th width="14%"<?php if ($sort_by == "Phone") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("sample?action=$action&sb=Phone&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "电话"); ?></th>
        
        <th width="50%"><center>地址</center></th>
        <th width="10%">
           <button title="新增收件人" type="button" onclick="window.open('<?php echo base_url();?>sample?action=addr_add')">
             <img src="<?php echo site_url('img/add-big.png'); ?>"/></button>
        </th>
    </thead>

    <tbody>         
        <?php foreach($addresses as $i=>$address): ?>
        <tr>
            <td><div align="left"><?php echo $address['Customer']." [".$address['CompanyCode'],"]"; ?></div></td>
            <td><div align="left"><?php echo $address['Name']; ?></div></td>
            <td><div align="left"><?php echo $address['Phone']; ?></div></td>
            <td><div align="left"><?php echo $address['Address']; ?></div></td>

            <td><div align="center">
            <?php if ((strstr($this->session->userdata['priv'], 'SAM31')) || (strstr($this->session->userdata['priv'], 'SAM32'))) : ?>
              
                <?php $query_url = urlencode(json_encode(array("ID"=>$address['ID']))); ?>
                <button title="修改收件人信息" onclick="window.open('<?php echo base_url();?>sample?action=addr_edit&query=<?php echo $query_url;?>&option=<?php echo $option;?>')" 
                        type="button"><img src="<?php echo site_url('img/edit.png'); ?>"/></button>

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
              
        </small></div></td></tr>
    </tbody>
</table>
