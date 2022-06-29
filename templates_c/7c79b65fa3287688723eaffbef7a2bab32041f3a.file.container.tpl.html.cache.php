<?php /* Smarty version Smarty-3.1.21-dev, created on 2022-06-11 09:05:26
         compiled from "templates/common/container.tpl.html" */ ?>
<?php /*%%SmartyHeaderCode:114833110662a40d7eb13292-27708351%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7c79b65fa3287688723eaffbef7a2bab32041f3a' => 
    array (
      0 => 'templates/common/container.tpl.html',
      1 => 1654516882,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '114833110662a40d7eb13292-27708351',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'smarty_date' => 0,
    'ini' => 0,
    'page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_62a40d7eb5d192_49673798',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_62a40d7eb5d192_49673798')) {function content_62a40d7eb5d192_49673798($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/html/Smarty-3/libs/plugins/modifier.date_format.php';
?><!DOCTYPE html>
<html>
    <head>
        <title>Transport Management System
            <?php if (isset($_SESSION['companyname'])) {?>
                :: &nbsp;<?php echo $_SESSION['companyname'];?>
&nbsp;(<?php echo smarty_modifier_date_format($_SESSION['sdate'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
-<?php echo smarty_modifier_date_format($_SESSION['edate'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
)
            <?php }?>
        </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
        <link rel="stylesheet" href="js/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.css">

        <?php echo '<script'; ?>
 src="https://code.jquery.com/jquery-3.6.0.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="js/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"><?php echo '</script'; ?>
>

        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
        <?php echo '<script'; ?>
 src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"><?php echo '</script'; ?>
>

        <?php echo '<script'; ?>
 src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"><?php echo '</script'; ?>
>

		<link rel="stylesheet" href="js/jquery.mobile-1.4.5/demos/_assets/css/jqm-demos.css">
		<?php echo '<script'; ?>
 src="js/jquery.mobile-1.4.5/demos/_assets/js/index.js"><?php echo '</script'; ?>
>

        <link rel="stylesheet" href="css/common.css">
        <?php echo '<script'; ?>
 type="text/javascript" src="js/jqprint.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 type="text/javascript" src="js/table2excel.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="js/common.js?time=1"><?php echo '</script'; ?>
>

        <?php $_smarty_tpl->tpl_vars['smarty_date'] = new Smarty_variable($_smarty_tpl->tpl_vars['ini']->value['smarty_date'], null, 3);
$_ptr = $_smarty_tpl->parent; while ($_ptr != null) {$_ptr->tpl_vars['smarty_date'] = clone $_smarty_tpl->tpl_vars['smarty_date']; $_ptr = $_ptr->parent; }
Smarty::$global_tpl_vars['smarty_date'] = clone $_smarty_tpl->tpl_vars['smarty_date'];?>
        <?php echo '<script'; ?>
 language="javascript" type="text/javascript">
            var js_date = "<?php echo $_smarty_tpl->tpl_vars['ini']->value['js_date'];?>
";
            var sdate = "<?php echo smarty_modifier_date_format($_SESSION['sdate'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
";
            var edate = "<?php echo smarty_modifier_date_format($_SESSION['edate'],$_smarty_tpl->tpl_vars['smarty_date']->value);?>
";
        <?php echo '</script'; ?>
>
    </head>
    <body>
        <?php if ($_SESSION['msg']) {?>
            <div class="row w-100">
                <div class="err_msg"><?php echo $_SESSION['msg'];?>
</div>
            </div>
        <?php }?>
        <?php if (isset($_SESSION['id_user'])&&isset($_SESSION['id_info'])) {?>
            <div class="row w-100">
                <div class="col-12">
                    <?php echo $_smarty_tpl->getSubTemplate ("common/header.tpl.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>

                </div>
            </div>
        <?php }?>
        <div class="row grow w-100 h-100">
            <?php if (isset($_SESSION['id_user'])&&isset($_SESSION['id_info'])) {?>
                <div class="col-2 h-100">
                    <br><br>
                    <?php echo $_smarty_tpl->getSubTemplate ("common/menu.tpl.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>

                </div>
                <div class="col-10 w-100 h-100"  style="left: 20px !important;">
                    <br><br><br>
                    <?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['page']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>

                    <br><br>
                </div>
            <?php } else { ?>
                <div class="col-12 w-100 h-100">
                    <?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['page']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>

                </div>
            <?php }?>
            <?php echo $_smarty_tpl->getSubTemplate ("common/footer.tpl.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0);?>

        </div>
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="btn" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
		<div tabindex="-1" class="modal bs-logout-modal-sm" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-sm">
			  <div class="modal-content">
				<div class="modal-header"><h4>Are you sure you want to logout ?</h4></div>
				<div class="modal-footer">
					<a class="btn btn-danger btn-xl" href="index.php?module=user&func=logout">Logout</a>
					<a class="btn btn-primary btn-xl" data-dismiss="modal">Close</a>
				</div>
			  </div>
			</div>
		</div>
    </body>
</html>
<?php }} ?>
