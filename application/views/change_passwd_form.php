<!DOCTYPE html>

<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title></title>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" title="no title" charset="utf-8">

</head>
<body>

<div id="login_form">
    <center>
    <h1>Change Password</h1>
    <?php if ($message) : ?>
    <font color="red"><b><?php echo $message; ?></b></font><br><br>
    <?php endif; ?>
    <?php echo form_open('change_passwd/validate');?>
    <?php echo form_label($email, 'account'); ?>
    <?php echo form_hidden('account', "$email");?><br><br>
    New Password <br>
    <?php echo form_password('password', '');?><br><br>

    Confirm Password<br>
    <?php echo form_password('password2', '');?>
    <br><br>
    <?php echo form_button(array('name'=>'clear', 'content'=>'CLEAR', 'onclick'=>"location.href='".base_url()."change_passwd'")); ?>
    <?php echo form_submit('submit', 'UPDATE');?>
    <?php echo form_button(array('name'=>'cancel', 'content'=>'CANCEL', 'onclick'=>"location.href='".base_url()."'")); ?>
    </center>
</div>

</body>
</html>

