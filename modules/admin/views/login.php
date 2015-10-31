<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title>登录</title>
<?= HTML::style('media/bootstrap/css/bootstrap.min.css')?>
<style>
<?php include MODPATH . '/admin/media/signin.css';?>
</style>
</head>
<body>
<div class="container">
	<form class="form-signin" action="" method="post">
	    <h2 class="form-signin-heading"></h2>
		<input type="hidden" name="csrf" value="<?= Security::token()?>"> 
		<input type="text" name="username" class="form-control" placeholder="用户名" required>
		<input type="password" name="password" class="form-control" placeholder="密码" required>
		<button class="btn btn-lg btn-info btn-block" type="submit">登录</button>
	</form>
</div>
<?= HTML::script('media/js/jquery.min.js')?>
<?= HTML::script('media/js/jquery.cookie.js')?>
</body>
</html>
