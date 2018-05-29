<?php $this->load->view('includes/header_top'); ?> 
<?php if (!empty($scripts)): $this->load->view($scripts); endif;?>
<?php $this->load->view('includes/header'); ?> 
<?php $this->load->view($main_content); ?>
<?php $this->load->view('includes/footer'); ?>