<?php
/* Smarty version 3.1.39, created on 2022-05-08 05:40:50
  from 'C:\xampp\htdocs\hotel\templates\common\header.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_62770a8a5bf235_58505153',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '061885be641aaf1b71a591adf86f7edd6c6952a3' => 
    array (
      0 => 'C:\\xampp\\htdocs\\hotel\\templates\\common\\header.tpl.html',
      1 => 1630891650,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_62770a8a5bf235_58505153 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
$_smarty_tpl->compiled->nocache_hash = '184453125462770a8a5b6c57_11095590';
?>
<div class="header">
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
<?php }
}
