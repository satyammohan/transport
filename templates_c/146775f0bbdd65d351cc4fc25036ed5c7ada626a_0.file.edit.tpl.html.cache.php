<?php
/* Smarty version 3.1.39, created on 2022-05-29 10:44:41
  from 'C:\xampp\htdocs\transport\templates\item\edit.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6293014103acb2_39500889',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '146775f0bbdd65d351cc4fc25036ed5c7ada626a' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\item\\edit.tpl.html',
      1 => 1653801146,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6293014103acb2_39500889 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '177678988462930141014130_66379203';
?>
<form method="post" action="index.php?module=item&func=<?php if ($_smarty_tpl->tpl_vars['data']->value['id_item']) {?>update<?php } else { ?>insert<?php }?>">
    <fieldset>
        <legend><?php if ($_smarty_tpl->tpl_vars['data']->value['id_item']) {?>Edit<?php } else { ?>Add<?php }?> Item</legend>
        <table>
            <tr>
                <td><b>Name:</b></td>
                <td><input type="text" class="form-control" name="item[name]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
" size="40"/>
            </tr>
            <tr>
                <td><b>Short Name:</b></td>
                <td><input type="text" class="form-control" name="item[short]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['short'];?>
" size="40"/>
            </tr>
            <tr>
                <td><b>Weight:</b></td>
                <td><input type="text" class="form-control" name="item[weight]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['weight'];?>
" size="40"/>
            </tr>
            <tr>
                <td><b>Product:</b></td>
                <td><select class="form-control" name="item[id_product]" required>
                    <option>Select Product</option>
                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['product']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['id_product']),$_smarty_tpl);?>

                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Company:</b></td>
                <td><select class="form-control" name="item[id_company]" required>
                    <option>Select Company</option>
                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['company']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['id_company']),$_smarty_tpl);?>

                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Status:</b></td>
                <td><select name="item[status]" class="form-control">
                        <?php ob_start();
echo $_smarty_tpl->tpl_vars['data']->value['status'];
$_prefixVariable1 = ob_get_clean();
echo smarty_function_html_options(array('selected'=>$_prefixVariable1,'options'=>$_smarty_tpl->tpl_vars['ini']->value['status']),$_smarty_tpl);?>

                    </select>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="2">
                    <input type="hidden" id ="hide" name="id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id_item'];?>
">
                    <input class="btn btn-primary" type="submit" id="submit" value="Submit" />
                    <input class="btn btn-danger" type="reset" name="res" value="Reset"/>
                </td>
            </tr>
        </table>
    </fieldset>
</form><?php }
}
