<?php /* Smarty version Smarty-3.1.21-dev, created on 2022-06-11 09:05:15
         compiled from "templates/user/activate.tpl.html" */ ?>
<?php /*%%SmartyHeaderCode:104407391062a40d733204c6-20022110%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a7e9755ccc8e13e2469e74d3c2ccf0a83675d29e' => 
    array (
      0 => 'templates/user/activate.tpl.html',
      1 => 1654516882,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '104407391062a40d733204c6-20022110',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'info' => 0,
    'mod' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_62a40d733459f2_09576970',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_62a40d733459f2_09576970')) {function content_62a40d733459f2_09576970($_smarty_tpl) {?><?php echo '<script'; ?>
 src="js/ddslick.js"><?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 type="text/javascript">
    var ddData = Array();
    var ddYear = Array();
    <?php  $_smarty_tpl->tpl_vars['mod'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['mod']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['info']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['f']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['mod']->key => $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['f']['index']++;
?>
    ddData['<?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['f']['index'];?>
'] = { text: "<?php echo $_smarty_tpl->tpl_vars['mod']->value['name'];?>
", value: '<?php echo $_smarty_tpl->tpl_vars['mod']->value['name'];?>
', selected: false, description: "&nbsp;" };
    <?php } ?>
    
    $(document).ready(function() {
        $(".classname").focus();
        var fdata=Array();
        $('#dds').ddslick({
            data:ddData,
            width:400,
            <?php if (isset($_SESSION['sid'])) {?>
                'defaultSelectedIndex' : '<?php echo $_SESSION['sid'];?>
',
            <?php }?>
            onSelected: function(selectedData) {
                var cur_comp = selectedData['selectedData'].text;
                $('#cname').val(cur_comp);
                $('#sindex').val(selectedData['selectedIndex']);
                $.getJSON("index.php?module=info&func=ddslick",{ name:cur_comp },function(fdata) {
                    $('#year_select').ddslick("destroy");
                    $('#year_select').ddslick({
                        data:fdata,
                        width:280,
                        <?php if (isset($_SESSION['yselect'])) {?>
                            'defaultSelectedIndex' : '<?php echo $_SESSION['yselect'];?>
',
                        <?php }?>
                        onSelected:function(Data){
                            $('#cid').val((Data['selectedData'].value));
                            $('#yindex').val((Data['selectedIndex']));
                        }
                    })
                })
            }   
        });
    });
<?php echo '</script'; ?>
>
<center>
    <br><br><br><br>
    <h3>Welcome, <?php echo $_SESSION['user'];?>
</h3>
    <br><br><h2>:: Select Company and Financial Year ::</h2><br><br><br>
    <form action="index.php?module=info&func=prefix" method="post">
        <table>
            <tr>
                <td><div id="dds"></div></td>
                <td><div id="year_select"></div></td>
                <td>
                    <input type="hidden" name="cname" id="cname" />
                    <input type="hidden" name="index" id="sindex" />
                    <input type="hidden" name="yindex" id="yindex" />
                    <input type="hidden" name="id" id="cid" />
                    <input type="submit" name="submit" value="Go" class="btn btn-primary" />
                </td>
            </tr>
        </table>
    </form>
</center>

<style>
    .dd-select {
        border: none;
    }
    .dd-selected {
        height:40px !important;
        border: 1px solid #09192A;
        border-radius: 25px;
    }
</style><?php }} ?>
