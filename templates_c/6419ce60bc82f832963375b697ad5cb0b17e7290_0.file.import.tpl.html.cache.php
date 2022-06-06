<?php
/* Smarty version 3.1.39, created on 2022-06-06 06:32:30
  from 'C:\xampp\htdocs\transport\templates\util\import.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629d5226e7c956_77363654',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6419ce60bc82f832963375b697ad5cb0b17e7290' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\util\\import.tpl.html',
      1 => 1654446120,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_629d5226e7c956_77363654 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '1919429800629d5226e70e47_74343413';
echo '<script'; ?>
 type="text/javascript">
    function checkaccept() {
        var cacc = $("#confirm").is(':checked');
        if (!cacc) {
            alert("Please Check the 'Confirm updating the Current data.'");
        }
        return cacc;
    }
<?php echo '</script'; ?>
>
<fieldset>
    <legend>Data Import Setup</legend>
    <form enctype="multipart/form-data" action="index.php?module=util&func=upload" onsubmit="return checkaccept();" method="post">
        <input type="hidden" name="MAX_FILE_SIZE" value="1073741824" />
        Choose a file : <input type="file" name="upload">
        <br><br>
        <input type="checkbox" id="confirm" name="confirm"><label for="confirm">Confirm updating the Current data.</label>
        <br><br>
        <b>The current data will be overwritten by the uploaded Zip file. <br>No revert will be possible. Please be very very careful before pressing Upload.<br><br></b>
        <input type="submit" value="Upload">
    </form>
</fieldset>
<?php }
}
