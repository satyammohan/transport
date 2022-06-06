<?php
/* Smarty version 3.1.39, created on 2022-06-06 06:32:30
  from 'C:\xampp\htdocs\transport\templates\common\container.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629d5226b46824_99707152',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '598138f4762750406538f73c71317c77dbe8c864' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\common\\container.tpl.html',
      1 => 1651977349,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:common/header.tpl.html' => 1,
    'file:common/menu.tpl.html' => 1,
    'file:common/footer.tpl.html' => 1,
  ),
),false)) {
function content_629d5226b46824_99707152 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
$_smarty_tpl->compiled->nocache_hash = '20856974629d5226acfe40_51391914';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Transport Management System
            <?php if ((isset($_SESSION['companyname']))) {?>
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

        <?php $_smarty_tpl->_assignInScope('smarty_date', $_smarty_tpl->tpl_vars['ini']->value['smarty_date'] ,false ,32);?>
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
        <?php if ((isset($_SESSION['id_user'])) && (isset($_SESSION['id_info']))) {?>
            <div class="row w-100">
                <div class="col-12">
                    <?php $_smarty_tpl->_subTemplateRender("file:common/header.tpl.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                </div>
            </div>
        <?php }?>
        <div class="row grow w-100 h-100">
            <?php if ((isset($_SESSION['id_user'])) && (isset($_SESSION['id_info']))) {?>
                <div class="col-2 h-100">
                    <br><br>
                    <?php $_smarty_tpl->_subTemplateRender("file:common/menu.tpl.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                </div>
                <div class="col-10 w-100 h-100"  style="left: 20px !important;">
                    <br><br><br>
                    <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['page']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                    <br><br>
                </div>
            <?php } else { ?>
                <div class="col-12 w-100 h-100">
                    <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['page']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                </div>
            <?php }?>
            <?php $_smarty_tpl->_subTemplateRender("file:common/footer.tpl.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
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
<?php }
}
