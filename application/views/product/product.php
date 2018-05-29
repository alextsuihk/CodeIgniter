<?php if ( ($action == 'add' || $action =='edit') && (strstr($this->session->userdata['priv'], 'PRO20')) ): ?>
<table class="EditBox">
     <?php echo form_open('product/edit'); ?>
     <!-- pass parameters from previous controller to next controller -->
     <?php echo form_hidden('product_id', $product_id); ?>
     <?php echo form_hidden('action', $action); ?>
     <?php echo form_hidden('limit', $limit); ?>
     <?php echo form_hidden('sort_by', $sort_by); ?>
     <?php echo form_hidden('sort_order', $sort_order); ?>
     <?php echo form_hidden('query', $query); ?>
     <?php echo form_hidden('offset', $offset); ?>     
     <tr>
        <td colspan="1"><?php echo form_label('Part Number: ', 'part_number'); ?></td>
        <td colspan="4"><?php echo form_input('part_number', $edit['0']['PartNumber'], array('maxlength'=>'32', 'size'=>'32') ); ?> </td>
        <td colspan="1"><div align ="center">
            <?php echo form_label('Active:', 'active'); ?>
            <?php echo form_checkbox('active', '1', (!$edit['0']['Active'])? FALSE:TRUE); ?>     
        </div></td>
     </tr>
    <tr>
        <td colspan='1'><?php echo form_label('Description:', 'description'); ?></td>
        <td colspan="5"><?php echo form_input('description', $edit['0']['Description'], array('maxlength'=>'128', 'size'=>'115')); ?></td>
    </tr>
    <tr>
        <td colspan='1'><?php echo form_label('Description2:', 'description2'); ?></td>
        <td colspan='5'><?php echo form_input('description2', $edit['0']['Description2'], array('maxlength'=>'128', 'size'=>'115')); ?></td>
    </tr>
    <tr>
        <td colspan='2'>
            <?php echo form_label('Ball Type:', 'ball_size'); ?>
            <?php echo form_input('ball_type', $edit['0']['BallType'], array('maxlength'=>'8', 'size'=>'8')); ?>              
        </td>
        <td colspan='2'>
            <?php echo form_label('Package Size (x,y only):', 'package_size'); ?>
            <?php echo form_input('package_size', $edit['0']['PackageSize'], array('maxlength'=>'16', 'size'=>'16')); ?>              
        </td>
        <td colspan='2'>
            <?php echo form_label('Weight (mg):', 'weight'); ?>
            <?php echo form_input('weight', $edit['0']['Weight'], array('maxlength'=>'8', 'size'=>'8')); ?>              
        </td>
    </tr>
        <td colspan='2'>
            <?php echo form_label('Allow to Purchase:', 'allow_purchase'); ?>
            <?php echo form_checkbox('allow_purchase', '1', (!$edit['0']['AllowPurchase'])? FALSE:TRUE); ?>            
        </td>
        <td colspan="2">
            <?php echo form_label('Allow to Sell:', 'allow_sell'); ?>
            <?php echo form_checkbox('allow_sell', '1', (!$edit['0']['AllowSell'])? FALSE:TRUE); ?>    
        </td>
        <td colspan="2">
            <?php echo form_label('Allow to Sample:', 'allow_sample'); ?>
            <?php echo form_checkbox('allow_sample', '1', (!$edit['0']['AllowSample'])? FALSE:TRUE); ?>    
        </td>
    </tr>
    <tr>
        <td colspan='1'><?php echo form_label('Datasheet Link:', 'datasheet'); ?></td>
        <td colspan='5'><?php echo form_input('datasheet', $edit['0']['Datasheet'], array('maxlength'=>'128', 'size'=>'115')); ?></td>
    </tr>
    <tr>
        <td colspan='6'>
            <?php echo form_label('Note:', 'note'); ?><br>
            <?php echo form_textarea(array('name'=>'note', 'id'=>'note', 'value'=>$edit['0']['Note'], 'cols'=>'40', 'rows'=>'3')); ?>
        </td>
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
     <tr>
        <td colspan="1"><b>Part Number:</b></td>
        <td colspan="4"><?php echo $edit['0']['PartNumber']; ?></td>
        <td colspan="1"><div align="center"><b><?php echo (($edit['0']['Active'])? "Active":"Inactive"); ?></b></div></td>
     </tr>
    <tr>
        <td colspan='1'><b>Description:</b></td>
        <td colspan="5"><?php echo $edit['0']['Description']; ?></td>
    </tr>
    <tr>
        <td colspan='1'><b>Description2:</b></td>
        <td colspan='5'><?php echo $edit['0']['Description2']; ?></td>
    </tr>
    <tr>
        <td colspan='2'><b>Ball Type: </b><?php echo $edit['0']['BallType']; ?></td>
        <td colspan='2'><b>Package Size (x,y only): </b><?php echo $edit['0']['PackageSize']; ?></td>
        <td colspan='2'><b>Weight (mg): </b><?php echo $edit['0']['Weight']; ?></td>
    </tr>
    <tr>
        <td colspan='2'><b>Allow to Purchase: </b><?php echo (($edit['0']['AllowPurchase'])? "Allowed":"Disallowed"); ?></td>
        <td colspan="2"><b>Allow to Sell: </b><?php echo (($edit['0']['AllowSell'])? "Allowed":"Disallowed"); ?></td>
        <td colspan="2"><b>Allow to Sample: </b><?php echo (($edit['0']['AllowSample'])? "Allowed":"Disallowed"); ?></td>
    </tr>
    <tr>
        <td colspan='1'><b>Datasheet Link: </b></td>
        <td colspan='5'><?php echo $edit['0']['Datasheet'] ?></td>
    </tr>
    <tr><td colspan='6'><b>Note:</b><br><textarea readonly><?php echo $edit['0']['Note']; ?></textarea></td></tr>
    <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
        <tr><td colspan='6'><small><b>ChangeLot:</b><br><small><?php echo $edit['0']['ChangeLog']; ?></small></td></tr>
    <?php endif; ?>
    <tr><td colspan="6"><div align="center">
        <input title="Close this Window" type="button" value="CLOSE" onclick="self.close()">
    </div></td></tr>
</table>

<?php else: ?>                                  <!-- listing data -->

<table class="EditBox">
    <?php echo form_open('product/search'); ?>
    <!-- pass parameters from previous controller to next controller -->
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
            <?php echo form_checkbox('active_only', '1', $active_only); ?> Show Active Product Only
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
        <th  width="17%" <?php if ($sort_by == "PartNumber") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("product?action=$action&sb=PartNumber&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "Part Number"); ?></th>
        <th width="44%"<?php if ($sort_by == "Description") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("product?action=$action&sb=Description&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Description"); ?></th>
        <th width="5%" <?php if ($sort_by == "BallType") echo "class=\"sort_$sort_order\"" ?>> 
            <?php echo anchor("product?action=$action&sb=BallType&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Ball Type"); ?></th>
        <th width="10%" <?php if ($sort_by == "PackageSize") echo "class=\"sort_$sort_order\"" ?>> 
            <?php echo anchor("product?action=$action&sb=PackageSize&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset" , "Package Size"); ?></th>
        <?php if ($this->session->userdata['view_fg_cost'] == '1') :  ?><th width="10%">
            Selling Price (WIP)
        </th><?php endif ?>
        <th width="3%"></th>
        <th width="11%">
            <?php if (strstr($this->session->userdata['priv'], 'PRO20')) :  ?>
                <button title="Add Product" type="button" onclick="window.open('<?php echo base_url();?>product?action=add')">
                    <img src="<?php echo site_url('img/add-big.png'); ?>"/></button>
            <?php endif; ?>
        </th>
    </thead>

    <tbody>  
        <?php foreach($products as $product): ?>
        <tr>
            <td><div align="left"><?php echo $product['PartNumber']; ?></div></td>
            <td><div align="left"><?php echo $product['Description']."; ".$product['Description2']; ?></div></td>
            <td><?php echo $product['BallType']; ?></td>
            <td><?php echo $product['PackageSize']; ?></td>
            <?php if ($this->session->userdata['view_fg_cost'] == '1') :  ?><td>
                $0.123  &nbsp;
                <?php $query_url = urlencode(json_encode(array("ProductID"=>$product['ID']))); ?>
                <button title="Sales History" type="button" onclick="window.open('<?php echo base_url();?>sales?action=list&query=<?php echo $query_url;?>')">
                    <img src="<?php echo site_url('img/database.png'); ?>"/></button>
            </td><?php endif ?>  
            <td><?php echo ($product['Active'])?"A":"I"; ?></td>
            <?php $query_url = urlencode(json_encode(array("ID"=>$product['ID']))); ?>
                 <td><button title="View" type="button" onclick="window.open('<?php echo base_url();?>product?action=view&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
                         <img src="<?php echo site_url('img/view.png'); ?>"/></button>  
                <?php if (strstr($this->session->userdata['priv'], 'PRO20')) :  ?>
                <button title="Modify" type="button"  onclick="window.open('<?php echo base_url();?>product?action=edit&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
                    <img src="<?php echo site_url('img/edit.png'); ?>"/></button>
                <?php endif; ?>
                <?php if (strstr($this->session->userdata['priv'], 'IPN')) :  ?>
                    <button title="IPN detail" type="button" onclick="window.open('<?php echo base_url();?>product_ipn?action=list&pid=<?php echo $product['ID'];?>')">IPN</button>
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
        <tr><td><small>
        Note: <br>
        A: Active Product<br> I: Inactive Product (EOL)
        </small></td></tr>
    </tbody>
</table>
