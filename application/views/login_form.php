<!DOCTYPE html>

<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Leahkinn ERP Login</title>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" title="no title" charset="utf-8">

</head>
<body>

<div id="login_form">
    <center><img src="img/login-logo.png"></center>
    <br>
    <center>Welcome</center>
    <br>
    <center><font color="red"><b><?php echo $message; ?></b></font></center>
    <?php echo form_open('login/validate_credentials');?>
    <?php echo form_hidden('url', $url); ?>
    <center><input type="text" name="email" value="" placeholder="Your Email" id="email"></center>
    <br>
    <center><?php echo form_password('password', '');?></center>
    <br>
    <center><?php echo form_submit('submit', 'Login');?></center>
</div>

</body>
</html>

