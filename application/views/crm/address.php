<?php if (($action == 'add' || $action =='edit') && strstr($this->session->userdata['priv'], 'CRM20')): ?>
<table class="EditBox">
     <?php echo form_open('crm/address_edit'); ?>
     <!-- pass parameters from previous controller to next controller -->
     <?php echo form_hidden('address_id', $address_id); ?>
     <?php echo form_hidden('action', $action); ?>
     <?php echo form_hidden('limit', $limit); ?>
     <?php echo form_hidden('sort_by', $sort_by); ?>
     <?php echo form_hidden('sort_order', $sort_order); ?>
     <?php echo form_hidden('query', $query); ?>
     <?php echo form_hidden('offset', $offset); ?>     
     <tr>
        <td colspan="6"><div align="left">
            <?php echo form_label('Address: ', 'address1'); ?>
            <?php echo form_input('address1', $edit['0']['Address1'], array('maxlength'=>'64', 'size'=>'63') ); ?>
            <?php echo form_input('address2', $edit['0']['Address2'], array('maxlength'=>'64', 'size'=>'62') ); ?>
        </div></td>
     </tr>
     <tr>
        <td colspan="3"><div align="left">
            <?php echo form_input('address3', $edit['0']['Address3'], array('maxlength'=>'64', 'size'=>'64') ); ?>
            </div></td>
        <td colspan="3"><div align="left">
            <?php echo form_label('City:', 'city'); ?>
            <?php echo form_input('city', $edit['0']['City'], array('maxlength'=>'32', 'size'=>'32') ); ?>
        </div></td>
     </tr>
     <tr>
        <td colspan="2"><div align="left">
            <?php echo form_label('County:', 'county'); ?>
            <?php echo form_input('county', $edit['0']['County'], array('maxlength'=>'32', 'size'=>'32') ); ?>
        </div></td>
        <td colspan="2"><div align="left">
            <?php echo form_label('Country:', 'country'); ?>
            <?php echo form_input('country', $edit['0']['Country'], array('maxlength'=>'16', 'size'=>'16') ); ?>
        </div></td>
        <td colspan="2"><div align="left">
            <?php echo form_label('Postal Code:', 'postal_code'); ?>
            <?php echo form_input('postal_code', $edit['0']['PostalCode'], array('maxlength'=>'8', 'size'=>'8') ); ?>
        </div></td>
     </tr>
    <tr>
        <td colspan="6"><div align='left'>
            <?php echo form_label('Note:', 'note'); ?><br>
            <?php echo form_textarea(array('name'=>'note', 'id'=>'note', 'value'=>$edit['0']['Note'], 'cols'=>'40', 'rows'=>'3')); ?>
        </div></td>
    </tr>
    <tr>
        <td colspan="2"><div align="center">
            <input title="Clear Data" type="button" value="CLEAR" onclick="location.href='<?php echo base_url().$clear_url;?>'"></div></td>
        <td colspan="2"><div align="center">
            <button title="<?php echo $submit_button;?> Record" type="submit" name="submit"><?php echo $submit_button;?></button></div></td>
        <td colspan="2"><div align="center">
            <input title="Cancel & Close Window" type="button" value="CANCEL" onclick="self.close()"></div></td>
    </tr>
    <?php echo form_close(); ?>

</table>

<?php elseif ($action == 'view'): ?>
<table class="ViewBox">
     <tr><td colspan="6"><div align="left"><b>Address: </b> (<?php echo $edit['0']['ID'];?>)<br>
        <?php echo $edit['0']['Address1'];?><br><?php echo $edit['0']['Address2'];?><br><?php echo $edit['0']['Address3'];?>
        <?php echo $edit['0']['City'];?>, 
        <?php echo $edit['0']['County'];?>, 
        <?php echo $edit['0']['Country'];?>
        <?php echo $edit['0']['PostalCode'];?>
    </div></td></tr> 
    <tr><td colspan='6'><b>Note:</b><br><textarea readonly><?php echo $edit['0']['Note']; ?></textarea></td></tr>
    <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
        <tr><td colspan='6'><small><b>ChangeLot:</b><br><?php echo $edit['0']['ChangeLog']; ?></small></td></tr>
    <?php endif; ?>
    <tr>
        <td colspan='6'><div align="center">
            <input title="Close this Window" type="button" value="CLOSE" onclick="self.close()">
        </div></td></tr>
</table>
<?php else: ?>                                  <!-- listing data -->
    
<table class="EditBox">
     <?php echo form_open('crm/address_search'); ?>
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
            <!-- <?php echo form_checkbox('active_only', '1', $active_only); ?> Show Active Address Only -->
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
        <th  width="57%" <?php if ($sort_by == "Address1") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/address?action=$action&sb=Address1&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "Address"); ?></th>
        <th width="10%"<?php if ($sort_by == "City") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/address?action=$action&sb=City&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "City"); ?></th>
        <th width="8%">County</th>
        <th width="10%"<?php if ($sort_by == "Country") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/address?action=$action&sb=Country&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Country"); ?></th>
        <th width="8%"<?php if ($sort_by == "PostalCode") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("crm/address?action=$action&sb=PostalCode&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Postal Code"); ?></th>
        <th width="7%">
            <?php if (strstr($this->session->userdata['priv'], 'CRM20')) :  ?>
                <button title="Add Address" type="button" onclick="window.open('<?php echo base_url();?>crm/address?action=add')">
                    <img src="<?php echo site_url('img/add-big.png'); ?>"/></button>
            <?php endif; ?>
        </th>
    </thead>

    <tbody>  
        <?php foreach($addresses as $address): ?>
        <tr>
            <td><div align="left">
             <?php echo $address['Address1']; 
            echo "&nbsp"; echo $address['Address2']; 
            echo "&nbsp"; echo $address['Address3']; ?></div></td>
            <td><?php echo $address['City']; ?></td>
            <td><?php echo $address['County']; ?></td>
            <td><?php echo $address['Country']; ?></td>
            <td><?php echo $address['PostalCode']; ?></td>
            <?php $query_url = urlencode(json_encode(array("ID"=>$address['ID']))); ?>
            <td><button title="View" type="button" onclick="window.open('<?php echo base_url();?>crm/address?action=view&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
                    <img src="<?php echo site_url('img/view.png'); ?>"/></button>  
            <?php if (strstr($this->session->userdata['priv'], 'CRM20')) :  ?>
                <button title="Modify" type="button" onclick="window.open('<?php echo base_url();?>crm/address?action=edit&query=<?php echo $query_url;?>&option=<?php echo $option;?>')" >
                    <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
            <?php endif; ?>
            </td>
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


