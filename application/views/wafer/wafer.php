<?php if ( ($action == 'add' || $action =='edit') && (strstr($this->session->userdata['priv'], 'PRO20')) ): ?>
<table class="EditBox">
     <?php echo form_open('wafer/edit'); ?>
     <!-- pass parameters from previous controller to next controller -->
     <?php echo form_hidden('product_id', $product_id); ?>
     <?php echo form_hidden('action', $action); ?>
     <?php echo form_hidden('limit', $limit); ?>
     <?php echo form_hidden('sort_by', $sort_by); ?>
     <?php echo form_hidden('sort_order', $sort_order); ?>
     <?php echo form_hidden('query', $query); ?>
     <?php echo form_hidden('offset', $offset); ?>
    <tr>
        <td colspan='2'>
            <?php echo form_label('Vendor:', 'vendor_id'); ?>
            <?php echo form_dropdown("vendor_id", $vendor_list, $edit['0']['VendorID'], 'id="vendor_id"') ?>            
        </td>
        <td colspan='2'>
            <?php echo form_label('Design:', 'design_id'); ?>
            <?php echo form_dropdown("design_id", $design_list, $edit['0']['DesignID'], 'id="design_id"') ?>            
        </td>
        <td colspan="2"></td>
    </tr>
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
        <td colspan='1'><?php echo form_label('Internal Description:', 'description_internal'); ?></td>
        <td colspan="5"><?php echo form_input('description_internal', $edit['0']['DescriptionInternal'], array('maxlength'=>'128', 'size'=>'115')); ?></td>
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
            <button title="<?php echo $submit_button;?> Record"" type="submit" name="submit"><?php echo $submit_button;?></button></div></td>
        <td colspan="2"><div align="center">
            <input title="Cancel & Close Window" type="button" value="CANCEL" onclick="self.close()"></div></td>
    </tr>
    <?php echo form_close(); ?>

</table>

<?php elseif ($action == 'view'): ?>
<table class="ViewBox">
    <tr>
        <td colspan='2'><b>Vendor: </b><?php echo $edit['0']['Vendor']; ?></td>
        <td colspan='2'><b>Design: </b><?php echo $edit['0']['Design']; ?></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="1"><b>Part Number:</b></td>
        <td colspan="4"><?php echo $edit['0']['PartNumber']; ?></td>
        <td colspan="1"><div align="center"><b><?php echo (($edit['0']['Active'])? "Active":"Inactive"); ?></b></div></td>
     </tr>
    <tr>
        <td colspan='1'><b>Description:</b></td>
        <td colspan="5"><?php echo $edit['0']['DescriptionDesign']; ?> ;  <?php echo $edit['0']['Description']; ?></td>
    </tr>
    <tr>
        <td colspan='1'><b>Internal Description:</b></td>
        <td colspan="5"><?php echo $edit['0']['DescriptionInternal']; ?></td>
    </tr>
    <tr><td colspan='6'><b>Note:</b><br><textarea readonly><?php echo $edit['0']['Note']; ?></textarea></td></tr>
    <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
        <tr><td colspan='6'><small><b>ChangeLot:</b><br><small><?php echo $edit['0']['ChangeLog']; ?></small></td></tr>
    <?php endif; ?>
    <tr><td colspan="6"><div align="center">
        <input title="Close this Window"" type="button" value="CLOSE" onclick="self.close()">
    </div></td></tr>
</table>

<?php else: ?>                                  <!-- listing data -->

<table class="EditBox">
    <?php echo form_open('wafer/search'); ?>
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
            <?php echo form_checkbox('active_only', '1', $active_only); ?> Show Active Wafer P/N Only
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
        <th  width="7%" <?php if ($sort_by == "Vendor") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("wafer?action=$action&sb=Vendor&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&os=$offset", "Vendor"); ?></th>        
        <th  width="11%" <?php if ($sort_by == "Design") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("wafer?action=$action&sb=Design&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&os=$offset", "Design"); ?></th>  
        <th  width="22%" <?php if ($sort_by == "PartNumber") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("wafer?action=$action&sb=PartNumber&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&os=$offset", "Part Number"); ?></th>
        <th width="30%"<?php if ($sort_by == "Description") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("wafer?actio=$action&sb=Description&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&os=$offset" , "Description"); ?></th>
        <th width="10%">Qty Available</th>

        <?php if ($this->session->userdata['view_mat_cost'] == '1') :  ?><th width="10%">
            Cost History (WIP)
        </th><?php endif ?>
        <th width="3%"></th>
        <th width="7%"><?php if (strstr($this->session->userdata['priv'], 'WAF20')) :  ?>
            <button title="Add Wafer P/N" type="button" onclick="window.open('<?php echo base_url();?>wafer?action=add')">
                <img src="<?php echo site_url('img/add-big.png'); ?>"/></button>
        <?php endif; ?></th>
    </thead>

    <tbody>  
        <?php foreach($products as $product): ?>
        <tr>
            <td><div align="left"><?php echo $product['Vendor']; ?></div></td>
            <td><div align="left"><?php echo $product['Design']; ?></div></td>
            <td><div align="left"><?php echo $product['PartNumber']; ?></div></td>
            <td><div align="left"><?php echo $product['DescriptionDesign'].";  ".$product['Description']; ?>
                <?php echo ($product['DescriptionInternal']) =="" ? "" : " [".$product['DescriptionInternal']."]"; ?>
            </div></td>
            <td></td>
            <?php if ($this->session->userdata['view_mat_cost'] == '1') :  ?><td>
                $0.123  &nbsp;
                <?php $query_url = urlencode(json_encode(array("ProductID"=>$product['ID']))); ?>
                <button title="Cost History" type="button" onclick="location.href='<?php echo base_url();?>purchase?action=list&query=<?php echo $query_url;?>'">
                    <img src="<?php echo site_url('img/database.png'); ?>"/></button>
            </td><?php endif ?>  
            <td><?php echo ($product['Active'])?"A":"I"; ?></td>
            <?php $query_url = urlencode(json_encode(array("ID"=>$product['ID']))); ?>
            <td><button title="View" type="button" onclick="window.open('<?php echo base_url();?>wafer?action=view&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
                    <img src="<?php echo site_url('img/view.png'); ?>"/></button> 
            <?php if (strstr($this->session->userdata['priv'], 'WAF20')) :  ?>
                <button title="Modify" type="button" onclick="window.open('<?php echo base_url();?>wafer?action=edit&query=<?php echo $query_url;?>&option=<?php echo $option;?>')">
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
        <tr><td><small>
        Note: <br>
        A: Active Product<br> I: Inactive Product (EOL)
        </small></td></tr>
    </tbody>
</table>
