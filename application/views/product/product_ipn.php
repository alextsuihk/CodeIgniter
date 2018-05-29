<?php if ($action == "all"): ?>
<table class="EditBox">
    <?php echo form_open('product_ipn/search'); ?>
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
            <?php echo form_checkbox('active_only', '1', $active_only); ?> Show Active IPN Only
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

<?php else: ?>
<table class="ListBox">
    <tr>
        <td colspan='3'><div align='left'> Top Level Part Number: <b><?php echo $product['0']['PartNumber'];  ?></b></div></td>
        <td colspan='3'><div align='left'> Description: <b><?php echo $product['0']['Description'];  ?></b></div></td>
    </tr>
</table>
<br>
<?php endif; ?>

<?php if ( ($action == 'add' || $action =='edit') && (strstr($this->session->userdata['priv'], 'IPN20')) ): ?>
<table class="EditBox">
     <?php echo form_open('product_ipn/edit'); ?>
         <!-- pass parameters from previous controller to next controller -->
     <?php echo form_hidden('ipn_id', $ipn_id); ?>
     <?php echo form_hidden('product_id', $product_id); ?>
     <?php echo form_hidden('part_number', $product['0']['PartNumber']); ?>
     <?php echo form_hidden('action', $action); ?>
     <?php echo form_hidden('limit', $limit); ?> 
     <?php echo form_hidden('option', $option); ?> 
     <tr>
        <td colspan="1"><?php echo form_label('IPN: ', 'ipn'); ?></td>
        <td colspan="3"><?php echo form_input('ipn', $edit['0']['IPN'], array('maxlength'=>'32', 'size'=>'32') ); ?></td>

        <td colspan="3">
            <?php echo form_label('Package Size (X,Y,Z):', 'package_size'); ?>
            <?php echo form_input('package_size', $edit['0']['PackageSize'], array('maxlength'=>'16', 'size'=>'16')); ?>
        </td>
        <td colspan="1"><div align="center">   
            <?php echo form_label('Active:', 'active'); ?>
            <?php echo form_checkbox('active', '1', (!$edit['0']['Active'])? FALSE:TRUE); ?>     
        </div></td>
     </tr>
    <tr>
        <td colspan='1'><?php echo form_label('Description:', 'description'); ?></td>
        <td colspan="7"><?php echo form_input('description', $edit['0']['Description'], array('maxlength'=>'128', 'size'=>'115')); ?></td>
    </tr>

    <tr>
        <td colspan='2'>
            <?php echo form_label('SubCon:', 'sub_con'); ?>
            <?php echo form_input('sub_con', $edit['0']['SubCon']); ?>              
        </td>
        <td colspan='2'>
            <?php echo form_label('SubstrateID:', 'substrate_id'); ?>
            <?php echo form_input('substrate_id', $edit['0']['SubstrateID'], array('maxlength'=>'16', 'size'=>'16')); ?>              
        </td>
        <td colspan="2">
            <?php echo form_label('Bond Diagram:', 'bonding_diagram'); ?>
            <?php echo form_input('bonding_diagram', $edit['0']['BondingDiagram'], array('maxlength'=>'16', 'size'=>'16')); ?>          
        </td>
        <td colspan="2">
            <?php echo form_label('Lead Time (Days):', 'lead_time'); ?>
            <?php echo form_input('lead_time', $edit['0']['LeadTime'], array('maxlength'=>'8', 'size'=>'8')); ?>          
        </td>
    </tr>
   
    <tr>
        <td colspan='1'>Die #1 P/N:</td>
        <td colspan='3'><?php echo form_dropdown("die_part_number_id_1", $wafer_list, $edit['0']['DiePartNumberID_1'], 'id="die_part_number_id_1"'); ?></td>
        <td colspan='1'><?php echo form_label('Die #1 Grind Info:', 'die_grind_1'); ?></td>
        <td colspan='3'><?php echo form_input('die_grind_1', $edit['0']['DieGrind_1']); ?></td>
    </tr>
    <tr>
        <td colspan='1'>Die #2:P/N</td>
        <td colspan='3'><?php echo form_dropdown("die_part_number_id_2", $wafer_list, $edit['0']['DiePartNumberID_2'], 'id="die_part_number_id_2"'); ?></td>
        <td colspan='1'><?php echo form_label('Die #2 Grind Info:', 'die_grind_2'); ?></td>
        <td colspan='3'><?php echo form_input('die_grind_2', $edit['0']['DieGrind_2']); ?></td>
    </tr>
    <tr>
        <td colspan='1'>Die #3 P/N:
        <td colspan='3'><?php echo form_dropdown("die_part_number_id_3", $wafer_list, $edit['0']['DiePartNumberID_3'], 'id="die_part_number_id_3"'); ?></td>
        <td colspan='1'><?php echo form_label('Die #3 Grind Info:', 'die_grind_3'); ?></td>
        <td colspan='3'><?php echo form_input('die_grind_3', $edit['0']['DieGrind_3']); ?></td>
    </tr>
    <tr>
        <td colspan='1'>'Die #4 P/N
        <td colspan='3'><?php echo form_dropdown("die_part_number_id_4", $wafer_list, $edit['0']['DiePartNumberID_4'], 'id="die_part_number_id_4"'); ?></td>
        <td colspan='1'><?php echo form_label('Die #4 Grind Info:', 'die_grind_4'); ?></td>
        <td colspan='3'><?php echo form_input('die_grind_4', $edit['0']['DieGrind_4']); ?></td>
    </tr>
    <tr>
        <td colspan='1'>Die #5 P/N:
        <td colspan='3'><?php echo form_dropdown("die_part_number_id_5", $wafer_list, $edit['0']['DiePartNumberID_5'], 'id="die_part_number_id_5"'); ?></td>
        <td colspan='1'><?php echo form_label('Die #5 Grind Info:', 'die_grind_5'); ?></td>
        <td colspan='3'><?php echo form_input('die_grind_5', $edit['0']['DieGrind_5']); ?></td>
    </tr>    

    <tr>
        <td colspan='1'>外購料料:
        <td colspan='7'><?php echo form_dropdown("purchased_item_id", $purchased_item_list, $edit['0']['PurchasedItemID'], 'id="purchased_item_id"'); ?></td>
    </tr> 
    
    <tr>
        <td colspan='2'><?php echo form_label('Package Qual Completed Date:', 'package_qual_date'); ?></td>
        <td colspan='2'><?php echo form_input('package_qual_date', $edit['0']['PackageQualDate'], array('maxlength'=>'10', 'size'=>'10')); ?></td>
        <td colspan='2'><?php echo form_label('PreCond Completed Date:', 'pre_cond_date'); ?></td>
        <td colspan='2'><?php echo form_input('pre_cond_date', $edit['0']['PreCondDate'], array('maxlength'=>'10', 'size'=>'10')); ?></td>
    </tr>

    <tr><td colspan='8'><div align='left'>
        <?php echo form_label('Engineering Note:', 'engineering_note'); ?><br>
        <?php echo form_textarea(array('name'=>'engineering_note', 'id'=>'engineering_note', 'value'=>$edit['0']['EngineeringNote'], 'cols'=>'40', 'rows'=>'4')); ?>
    </div></td></tr>
    
    <tr><td colspan='8'><div align='left'>
        <?php echo form_label('Note:', 'note'); ?><br>
        <?php echo form_textarea(array('name'=>'note', 'id'=>'note', 'value'=>$edit['0']['Note'], 'cols'=>'40', 'rows'=>'4')); ?>
    </div></td></tr>
    <tr>
        <td colspan="3"><div align="center">
            <input title="Clear Data" type="button" value="CLEAR" onclick="location.href='<?php echo base_url().$clear_url;?>'"></div></td>
        <td colspan="2"><div align="center">
            <button title="<?php echo $submit_button;?> Record" type="submit" name="submit"><?php echo $submit_button;?></button></div></td>
        <td colspan="3"><div align="center">
            <input title="Cancel & Close Window" type="button" value="CANCEL" onclick="self.close()"></div></td>
    </tr>
    <?php echo form_close(); ?>

</table>

<?php elseif ($action == 'view') : ?>
<table class="ViewBox">
     <tr>
        <td colspan="1"><b>IPN: </b></td>
        <td colspan="3"><?php echo $edit['0']['IPN'];?></td>
        <td colspan="3"><b>Package Size (X,Y,Z): </b><?php echo $edit['0']['PackageSize'];?></td>
        <td colspan="1"><b><?php echo (($edit['0']['Active'])? "Active":"Inactive"); ?></b></td>
     </tr>
    <tr>
        <td colspan='1'><b>Description:</b></td>
        <td colspan="7"><?php echo $edit['0']['Description']; ?></td>
    </tr>
    <tr>
        <td colspan='2'><b>SubCon: </b><?php echo $edit['0']['SubCon']; ?></td>
        <td colspan='2'><b>SubstrateID: </b><?php echo $edit['0']['SubstrateID']; ?></td>
        <td colspan="2"><b>Bond Diagram:</b><?php echo $edit['0']['BondingDiagram']; ?></td>
        <td colspan="2"><b>Production Lead Time (Days): </b><?php echo $edit['0']['LeadTime']; ?></td>
    </tr>
    <tr>
        <td colspan='1'><b>Die #1 P/N:</b></td>
        <td colspan='5'><?php echo $edit['0']['DiePN_1']." {".$edit['0']['DieDesc_1']."} " ; ?></td>
        <td colspan='1'>Die #1 Grind Info:</td>
        <td colspan='1'><?php echo $edit['0']['DieGrind_1']; ?></td>
    </tr>
    <tr>
        <td colspan='1'><b>Die #2:P/N:</b></td>
        <td colspan='5'><?php echo $edit['0']['DiePN_2']." {".$edit['0']['DieDesc_2']."} " ; ?></td>
        <td colspan='1'>Die #2 Grind Info:</td>
        <td colspan='1'><?php echo $edit['0']['DieGrind_2']; ?></td>
    </tr>
    <tr>
        <td colspan='1'><b>Die #3 P/N:</b></td>
        <td colspan='5'><?php echo $edit['0']['DiePN_3']." {".$edit['0']['DieDesc_3']."} " ; ?></td>
        <td colspan='1'>Die #3 Grind Info:</td>
        <td colspan='1'><?php echo $edit['0']['DieGrind_3']; ?></td>
    </tr>
    <tr>
        <td colspan='1'><b>Die #4 P/N:</b></td>
        <td colspan='5'><?php echo $edit['0']['DiePN_4']." {".$edit['0']['DieDesc_4']."} " ; ?></td>
        <td colspan='1'>Die #4 Grind Info:</td>
        <td colspan='1'><?php echo $edit['0']['DieGrind_4']; ?></td>
    </tr>
    <tr>
        <td colspan='1'><b>Die #5 P/N:<b></td>
        <td colspan='5'><?php echo $edit['0']['DiePN_5']." {".$edit['0']['DieDesc_5']."} " ; ?></td>
        <td colspan='1'>Die #5 Grind Info:</td>
        <td colspan='1'><?php echo $edit['0']['DieGrind_5']; ?></td>
    </tr>    

    <tr>
        <td colspan='1'><b>外購料號 P/N:<b></td>
        <td colspan='7'><?php echo $edit['0']['PurchasedItemPN']." {".$edit['0']['PurchasedItemDesc']."} " ; ?></td>
    </tr>    
    
    <tr>
        <td colspan='2'><b>Package Qual Completed Date: </b></td>
        <td colspan='2'><?php echo $edit['0']['PackageQualDate'];?></td>
        <td colspan='2'><b>PreCond Completed Date: </b></td>
        <td colspan='2'><?php echo $edit['0']['PreCondDate']; ?></td>
    </tr>

    <tr><td colspan='8'><b>Engineering Note: </b><br><textarea readonly><?php echo $edit['0']['EngineeringNote']; ?></textarea></td></tr>
    
    <tr><td colspan='8'><b>Note: </b><br><textarea readonly><?php echo $edit['0']['Note']; ?></textarea></td></tr>
    <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
        <tr><td colspan='8'><div align='left'><small><b>ChangeLot:</b><br><?php echo $edit['0']['ChangeLog']; ?></small></div></td></tr>
    <?php endif; ?>
    <tr>
        <td colspan="8"><div align='center'>
            <input title="Close this Window"" type="button" value="CLOSE" onclick="self.close()"></div></td>
    </tr>


</table>
<?php endif; ?>

<br>
<div>
    Matched Records: <?php echo $matched_records; ?> of <?php echo $total_records; ?>
</div>

<table class="ListBox">
    <thead>
        <th width="19%"<?php if ($sort_by == "IPN") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("product_ipn?action=$action&sb=IPN&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "IPN"); ?></th>
        <th width="7%"<?php if ($sort_by == "SubCon") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("product_ipn?action=$action&sb=SubCon&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "SubCon"); ?></th>
        <th width="32%"<?php if ($sort_by == "Description") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("product_ipn?action=$action&sb=Description&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "Description"); ?></th>
        <th width="10%"<?php if ($sort_by == "PackageSize") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("product_ipn?action=$action&sb=PackageSize&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "Package Size (IPN)"); ?></th>
        <th width="11%"<?php if ($sort_by == "SubstrateID") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("product_ipn?action=$action&sb=SubstrateID&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "SubstrateID"); ?></th>
        <th width="11%"<?php if ($sort_by == "BondingDiagram") echo "class=\"sort_$sort_order\"" ?>>
            <?php echo anchor("product_ipn?action=$action&sb=BondingDiagram&so=" . 
                (($sort_order == 'asc') ? 'desc' : 'asc')."&query=$query_url&option=$option&os=$offset", "Bonding Diagram"); ?></th>
        <th width="3%"></th>
        <th width="7%">
            <?php if ( strstr($this->session->userdata['priv'], 'IPN20') && (strpos($option, 'listall') === FALSE ) ) :  ?>
                <button title="Add IPN" type="button" onclick="window.open('<?php echo base_url()."product_ipn?action=add&pid=".$product[0]['ID'];?>')">
                    <img src="<?php echo site_url('img/add-big.png'); ?>"/></button>
            <?php endif; ?>
        </th>
    </thead>
    
    <tbody>  
        <?php foreach($ipns as $ipn): ?>
        <tr>
            <td><div align="left"><?php echo $ipn['IPN']; ?></div></td>
            <td ><?php echo $ipn['SubCon']; ?></td>
            <td><small><div align="left"><?php echo $ipn['Description']; ?> </div></small></td>
            <td ><?php echo $ipn['PackageSize']; ?></td>
            <td ><?php echo $ipn['SubstrateID']; ?></td>
            <td ><?php echo $ipn['BondingDiagram']; ?></td>
            <!--
            <td ><div align="left"><small><?php echo $ipn['Note']; ?>
                <?php if ($this->session->userdata['view_changelog'] == '1') : ?>
                <br><?php echo substr($ipn['ChangeLog'], 0, 100); ?>
                <?php endif; ?>
                    </small></div></td>  -->
            <td><?php echo ($ipn['Active'])?"A":"I"; ?></td>
            <td><button title="View" type="button" onclick="window.open('<?php echo base_url();?>product_ipn?action=view&pid=<?php echo $ipn['ProductID'];?>&ipn=<?php echo $ipn['ID'];?>&option=<?php echo $option;?>')">
                    <img src="<?php echo site_url('img/view.png'); ?>"/></button> 
                <?php if (strstr($this->session->userdata['priv'], 'IPN20')) :  ?>
                <button title="Modify" type="button" onclick="window.open('<?php echo base_url();?>product_ipn?action=edit&pid=<?php echo $ipn['ProductID'];?>&ipn=<?php echo $ipn['ID'];?>&option=<?php echo $option;?>')">
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
        A: Active Product<br> I: Inactive Product (EOL)
        </small></div></td></tr>
    </tbody>
</table>