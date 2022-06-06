<?php
/* Smarty version 3.1.39, created on 2022-05-29 10:11:24
  from 'C:\xampp\htdocs\transport\templates\vehicle\edit.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6292f974989509_81160482',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f0c1f64bdd2d93e7c621f345bd8807d64d79614c' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\vehicle\\edit.tpl.html',
      1 => 1653799264,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6292f974989509_81160482 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '15495025116292f974951f01_14198182';
?>
<form method="post" action="index.php?module=vehicle&func=<?php if ($_smarty_tpl->tpl_vars['data']->value['id_vehicle']) {?>update<?php } else { ?>insert<?php }?>">
    <fieldset>
        <legend><?php if ($_smarty_tpl->tpl_vars['data']->value['id_vehicle']) {?>Edit<?php } else { ?>Add<?php }?> Vehicle</legend>
        <table>
            <tr>
                <td><b>Vehicle No:</b></td>
                <td><input type="text" class="form-control" name="vehicle[vehno]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['vehno'];?>
" size="40"/>
            </tr>
            <tr>
                <td><b>Vehicle Type:</b></td>
                <td><input type="text" class="form-control" name="vehicle[vehtype]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['vehtype'];?>
" size="40"/>
            </tr>
            <tr>
                <td><b>Owner:</b></td>
                <td><select class="form-control" name="vehicle[id_vowner]" required>
                    <option>Select Vehicle Owner</option>
                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['vowner']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['id_vowner']),$_smarty_tpl);?>

                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Status:</b></td>
                <td><select name="vehicle[status]" class="form-control">
                        <?php ob_start();
echo $_smarty_tpl->tpl_vars['data']->value['status'];
$_prefixVariable1 = ob_get_clean();
echo smarty_function_html_options(array('selected'=>$_prefixVariable1,'options'=>$_smarty_tpl->tpl_vars['ini']->value['status']),$_smarty_tpl);?>

                    </select>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="2">
                    <input type="hidden" id ="hide" name="id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id_vehicle'];?>
">
                    <input class="btn btn-primary" type="submit" id="submit" value="Submit" />
                    <input class="btn btn-danger" type="reset" name="res" value="Reset"/>
                </td>
            </tr>
        </table>
    </fieldset>
</form> 
<?php }
}
