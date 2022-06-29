<?php /* Smarty version Smarty-3.1.21-dev, created on 2022-06-11 09:05:10
         compiled from "templates/user/login.tpl.html" */ ?>
<?php /*%%SmartyHeaderCode:48315448262a40d6e856637-26498514%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2e3b75cdc7e2e0f596e28ad4a28ab1589bba601d' => 
    array (
      0 => 'templates/user/login.tpl.html',
      1 => 1654516882,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '48315448262a40d6e856637-26498514',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_62a40d6e85af54_13253607',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_62a40d6e85af54_13253607')) {function content_62a40d6e85af54_13253607($_smarty_tpl) {?><main id="main" class=" alert-info">
	<div id="login-left"></div>
	<div id="login-right">
		<div class="card col-md-8">
			<div class="card-body">
				<form method="post" action="index.php?module=user&func=login">
					<div class="form-group">
						<label for="uname" class="control-label">Username</label>
						<input type="text" id="uname" name="user[uname]" class="form-control">
					</div>
					<div class="form-group">
						<label for="pass" class="control-label">Password</label>
						<input type="password" id="pass" name="user[pass]" class="form-control">
					</div>
					<center><button class="btn-sm btn-block btn-wave col-md-4 btn-primary">Login</button></center>
				</form>
			</div>
		</div>
	</div>
  </main>
<?php echo '<script'; ?>
>
	$("#uname").focus();
<?php echo '</script'; ?>
>
<style>
	#login-right{
      position: absolute;
      right:0;
      width:40%;
      height: calc(100%);
      background:white;
      display: flex;
      align-items: center;
  }
  #login-left{
      position: absolute;
      left:0;
      width:60%;
      height: calc(100%);
      background:#00000061;
      display: flex;
      align-items: center;
  }
  #login-right .card{
      margin: auto
  }
  #login-left {
    background: url(./img/hotel-cover.jpg);
    background-repeat: no-repeat;
    background-size: cover;
  }
	img#cimg,.cimg{
		max-height: 10vh;
		max-width: 6vw;  
  }
</style><?php }} ?>
