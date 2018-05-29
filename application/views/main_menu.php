<!-- only PrivAdmin see the sub-menu below -->
<?php if (!empty($button_adm)): ?>
    <table class="MenuList"> 
        <tr>
            <td colspan="1"><?php echo $button_adm[0];?></td>
            <td colspan="5"><h3>Admin</h3></td>
        </tr>
        <?php foreach (array_slice($button_adm,1) as $key=>$button): ?>   <!-- skip first element -->
        <?php if (!($key % 6)): echo "</tr><tr>"; endif ?>      <!-- every 5 column, close & open new row  -->
        <td><?php echo $button; ?></td>
        <?php endforeach;?>
        </tr>

    </table>
    <br><br>
<?php endif;?>

<?php if (!empty($button_crm)): ?>
    <table class="MenuList"> 
        <tr>
            <td colspan="1"><?php echo $button_crm[0];?></td>
            <td colspan="5"><h3>CRM</h3></td>
        </tr>
        <?php foreach (array_slice($button_crm,1) as $key=>$button): ?>   <!-- skip first element -->
        <?php if (!($key % 6)): echo "</tr><tr>"; endif ?>      <!-- every 5 column, close & open new row  -->
        <td><?php echo $button; ?></td>
        <?php endforeach;?>
        </tr>
    </table>
    <br><br>
<?php endif;?>

<?php if (!empty($button_pro)): ?>
    <table class="MenuList"> 
        <tr>
            <td colspan="1"><?php echo $button_pro[0];?></td>
            <td colspan="5"><h3>Product & Backlog</h3></td>
        </tr>
        <?php foreach (array_slice($button_pro,1) as $key=>$button): ?>   <!-- skip first element -->
        <?php if (!($key % 6)): echo "</tr><tr>"; endif ?>      <!-- every 5 column, close & open new row  -->
        <td><?php echo $button; ?></td>
        <?php endforeach;?>
        </tr>
    </table>
    <br><br>
<?php endif;?>

<?php if (!empty($button_man)): ?>
    <table class="MenuList"> 
        <tr>
            <td colspan="1"><?php echo $button_man[0];?></td>
            <td colspan="5"><h3>Manufacturing</h3></td>
        </tr>
        <?php foreach (array_slice($button_man,1) as $key=>$button): ?>   <!-- skip first element -->
        <?php if (!($key % 6)): echo "</tr><tr>"; endif ?>      <!-- every 5 column, close & open new row  -->
        <td><?php echo $button; ?></td>
        <?php endforeach;?>
        </tr>
    </table>
    <br><br>
<?php endif;?>

<?php if (!empty($button_pur)): ?>
    <table class="MenuList"> 
        <tr>
            <td colspan="1"><?php echo $button_pur[0];?></td>
            <td colspan="5"><h3>Purchasing</h3></td>
        </tr>
        <?php foreach (array_slice($button_pur,1) as $key=>$button): ?>   <!-- skip first element -->
        <?php if (!($key % 6)): echo "</tr><tr>"; endif ?>      <!-- every 5 column, close & open new row  -->
        <td><?php echo $button; ?></td>
        <?php endforeach;?>
        </tr>
    </table>
    <br><br>
<?php endif;?>

<?php if (!empty($button_sal)): ?>
    <table class="MenuList"> 
        <tr>
            <td colspan="1"><?php echo $button_sal[0];?></td>
            <td colspan="5"><h3>Sales</h3></td>
        </tr>
        <?php foreach (array_slice($button_sal,1) as $key=>$button): ?>   <!-- skip first element -->
        <?php if (!($key % 6)): echo "</tr><tr>"; endif ?>      <!-- every 5 column, close & open new row  -->
        <td><?php echo $button; ?></td>
        <?php endforeach;?>
        </tr>
    </table>
    <br><br>
<?php endif;?>
 
<br><br>

