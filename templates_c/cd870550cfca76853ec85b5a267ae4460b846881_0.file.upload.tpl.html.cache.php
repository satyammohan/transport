<?php
/* Smarty version 3.1.39, created on 2022-06-05 21:53:42
  from 'C:\xampp\htdocs\transport\templates\util\upload.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629cd88e0b2585_88554297',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cd870550cfca76853ec85b5a267ae4460b846881' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\util\\upload.tpl.html',
      1 => 1630646412,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_629cd88e0b2585_88554297 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '404548282629cd88e0b0184_40952951';
?>
<fieldset>
    <legend>Upload Message from Server</legend>

    
    <?php echo $_smarty_tpl->tpl_vars['msg']->value;?>
<br><br>

<b>
<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value;?>
">Click To Continue..</a>
</b>
    <br><br>
    Please Click the above link to continue data import into the Software.
    
</fieldset><?php }
}
