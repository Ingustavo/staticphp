<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>ADMIN</title>
		<style>
		  @import '<?php echo base_url('css/style.css'); ?>';
		</style>
		<script type="text/javascript" src="<?php echo site_url('js/jquery-1.2.6.js'); ?>"></script>
    <script type="text/javascript">
      var AJAX_URL = '<?php echo site_url('', 'ajax'); ?>';
    </script>
	</head>
	<body>
    <div class="page">
      
      <div class="menu">
        <a href="<?php echo site_url('language'); ?>">Languages</a> | 
        <a href="<?php echo site_url('users'); ?>">Users</a> | 
        <a href="<?php echo site_url('login/out'); ?>">Logout</a>
      </div>