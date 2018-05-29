<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo (!empty($html_title)?$html_title:'ERP');?></title>


    <link rel="icon" href="<?php echo base_url();?>img/favicon.png" type="image/png">

<!--    <script src="<?php echo base_url();?>js/jquery-1.7.1.js"></script> -->
<!--
        <script src="<?php echo base_url();?>js/jquery-3.1.1.min.js">
-->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url();?>css/style.css" type="text/css" media="screen" title="no title" charset="utf-8">     
        
    <style>
    #hintbox{
        display:none;
        position: absolute;
        font-size:12px;
        background-color: #ffff99;
        color: #000066;
        border: 1px solid red;
        padding: 4px;
        border-radius: 5px;
    }
    </style>


  
    <script>
        $(document).ready(function() {
            $('.hover').mousemove(function(e) {
                var hovertext = $(this).attr('hinttext');
                $('#hintbox').text(hovertext).show();
                $('#hintbox').css('top',e.clientY+10).css('left',e.clientX+5);})
            .mouseout(function(){
                $('#hintbox').hide();
                });
        });
    </script>

    <script>
        function blinker() {
           $('.SlowBlink').fadeOut(400);
           $('.SlowBlink').fadeIn(400);
       }
       setInterval(blinker, 1000);
    </script>
    
    <script>
        function blinker2() {
           $('.FastBlink').fadeOut(100);
           $('.FastBlink').fadeIn(200);
       }
       setInterval(blinker2, 1000);
    </script>
    