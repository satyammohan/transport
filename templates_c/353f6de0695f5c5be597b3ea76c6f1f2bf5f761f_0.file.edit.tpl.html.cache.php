<?php
/* Smarty version 3.1.39, created on 2022-05-29 10:21:36
  from 'C:\xampp\htdocs\transport\templates\product\edit.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6292fbd82a8c10_83709607',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '353f6de0695f5c5be597b3ea76c6f1f2bf5f761f' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\product\\edit.tpl.html',
      1 => 1653799541,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6292fbd82a8c10_83709607 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '10565678656292fbd8278f97_24237510';
?>
<form method="post" action="index.php?module=product&func=<?php if ($_smarty_tpl->tpl_vars['data']->value['id_product']) {?>update<?php } else { ?>insert<?php }?>">
    <fieldset>
        <legend><?php if ($_smarty_tpl->tpl_vars['data']->value['id_product']) {?>Edit<?php } else { ?>Add<?php }?> Product</legend>
        <table>
            <tr>
                <td><b>Name:</b></td>
                <td><input type="text" class="form-control" name="product[name]" id="name" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
" size="40"/>
            </tr>
            <tr>
                <td><b>Convertion:</b></td>
                <td><input type="text" class="form-control" name="product[convert]" id="name" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['convert'];?>
" size="40"/>
            </tr>
            <tr>
                <td><b>Status:</b></td>
                <td><select name="product[status]" class="form-control">
                        <?php ob_start();
echo $_smarty_tpl->tpl_vars['data']->value['status'];
$_prefixVariable1 = ob_get_clean();
echo smarty_function_html_options(array('selected'=>$_prefixVariable1,'options'=>$_smarty_tpl->tpl_vars['ini']->value['status']),$_smarty_tpl);?>

                    </select>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="2">
                    <input type="hidden" id ="hide" name="id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id_product'];?>
">
                    <input class="btn btn-primary" type="submit" id="submit" value="Submit" />
                    <input class="btn btn-danger" type="reset" name="res" value="Reset"/>
                </td>
            </tr>
        </table>
    </fieldset>
</form><?php }
}
