<?php /* Smarty version Smarty-3.1.21-dev, created on 2022-06-11 09:05:26
         compiled from "templates/common/header.tpl.html" */ ?>
<?php /*%%SmartyHeaderCode:64948665462a40d7eb603c1-94816538%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9cb8aa65e50982f4fcf257fe07357a53d779a6d8' => 
    array (
      0 => 'templates/common/header.tpl.html',
      1 => 1654516882,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '64948665462a40d7eb603c1-94816538',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'smarty_date' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_62a40d7eb6a038_19337213',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_62a40d7eb6a038_19337213')) {function content_62a40d7eb6a038_19337213($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/html/Smarty-3/libs/plugins/modifier.date_format.php';
?><div class="header">
  <nav class="navbar fixed-top" style="padding: 0">
    <div class="container-fluid mt-2 mb-2">
      <div class="col-lg-12 text-white">
        <div class="float-left" >
          <b>Welcome Admin</b>
        </div>
        <div class="float-right">
            <a href="#" class="text-white">
              <span data-target=".bs-logout-modal-sm" data-toggle="modal"><i class="fa fa-power-off"></i></span>
            </a>  
        </div>
        <div class="text-center">
            <?php echo $_SESSION['cname'];?>
&nbsp;
            <?php echo smarty_modifier_date_format($_SESSION['sdate'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
-<?php echo smarty_modifier_date_format($_SESSION['edate'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>

        </div>
      </div>
    </div>
  </nav>
</div>
<?php }} ?>
