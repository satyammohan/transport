<?php
/* Smarty version 3.1.39, created on 2022-05-29 10:57:42
  from 'C:\xampp\htdocs\transport\templates\transport\edit.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6293044e1e61f6_15038184',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cbde3321b9abd0cef41d7cb1a9323b81a0cf4458' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\transport\\edit.tpl.html',
      1 => 1653801948,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6293044e1e61f6_15038184 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '8103384496293044e1ccdc9_17594646';
?>
<form method="post" action="index.php?module=transport&func=<?php if ($_smarty_tpl->tpl_vars['data']->value['id_transport']) {?>update<?php } else { ?>insert<?php }?>">
    <fieldset>
        <legend><?php if ($_smarty_tpl->tpl_vars['data']->value['id_transport']) {?>Edit<?php } else { ?>Add<?php }?> Transport</legend>
        <table border="0" cellspacing="2" cellpadding="2">
            <tr>
                <td><b>Name:</b></td>
                <td><input type="text" required name="transport[name]" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
" size="40"/></td>
            </tr>
            <td><b>Addresss:</b></td>
                <td><input type="text" required name="transport[address]" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['address'];?>
" size="40"/></td>
            </tr>
            <td><b>Phone:</b></td>
                <td><input type="text" required name="transport[phone]" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['phone'];?>
" size="40"/></td>
            </tr>
            <tr>
                <td><b>Status:</b></td>
                <td><select name="transport[status]" class="form-control">
                        <?php echo smarty_function_html_options(array('selected'=>$_smarty_tpl->tpl_vars['data']->value['status'],'options'=>$_smarty_tpl->tpl_vars['ini']->value['status']),$_smarty_tpl);?>

                    </select>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="2">
                    <input type="hidden" id="hide" name="id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id_transport'];?>
">
                    <input class="btn btn-primary" type="submit" id="submit" value="Submit" />
                    <input class="btn btn-danger" type="reset" name="res" value="Reset"/>
                </td>
            </tr>
        </table>
    </fieldset>
</form><?php }
}
