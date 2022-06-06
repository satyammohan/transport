<?php
/* Smarty version 3.1.39, created on 2022-05-29 10:10:29
  from 'C:\xampp\htdocs\transport\templates\area\add.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6292f93d55d5f8_84851498',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '97e340f4686433f7ba36f65623050342096e2ed3' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\area\\add.tpl.html',
      1 => 1653795862,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6292f93d55d5f8_84851498 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\Smarty-3\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->compiled->nocache_hash = '5883133846292f93d5351d2_66128123';
?>
<form method="post" action="index.php?module=area&func=<?php if ($_smarty_tpl->tpl_vars['data']->value['id_area']) {?>update<?php } else { ?>insert<?php }?>">
    <fieldset>
        <legend><?php if ($_smarty_tpl->tpl_vars['data']->value['id_area']) {?>Edit<?php } else { ?>Add<?php }?> Area</legend>
        <table>
            <tr>
                <td><b>Name:</b></td>
                <td><input type="text" name="area[name]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
" class="form-control" required size="40"/></td>
            </tr>
            <tr>
                <td><b>Distance:</b></td>
                <td><input type="text" name="area[distance]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['distance'];?>
" class="form-control" required size="40"/></td>
            </tr>
            <tr>
                <td><b>Freight:</b></td>
                <td><input type="text" name="area[freight]" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['freight'];?>
" class="form-control" required size="40"/></td>
            </tr>
            <tr>
                <td><b>Status:</b></td>
                <td><select name="area[status]" class="form-control">
                        <?php ob_start();
echo $_smarty_tpl->tpl_vars['data']->value['status'];
$_prefixVariable1 = ob_get_clean();
echo smarty_function_html_options(array('selected'=>$_prefixVariable1,'options'=>$_smarty_tpl->tpl_vars['ini']->value['status']),$_smarty_tpl);?>

                    </select>
                </td>
            </tr> 
            <tr>
                <td align="center" colspan="2">
                    <input type="hidden" id="hide" name="id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id_area'];?>
" />
                    <input class="btn btn-primary" type="submit" value="Submit" />
                    <input class="btn btn-danger" type="reset" value="Reset"/>
                </td>
            </tr>
        </table>
    </fieldset>
</form>
<?php }
}
