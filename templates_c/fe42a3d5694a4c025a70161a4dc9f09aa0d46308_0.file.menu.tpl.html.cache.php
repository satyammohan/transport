<?php
/* Smarty version 3.1.39, created on 2022-06-08 05:53:09
  from 'C:\xampp\htdocs\transport\templates\common\menu.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_629febedc91000_53183036',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fe42a3d5694a4c025a70161a4dc9f09aa0d46308' => 
    array (
      0 => 'C:\\xampp\\htdocs\\transport\\templates\\common\\menu.tpl.html',
      1 => 1654445531,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_629febedc91000_53183036 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '1077505171629febedc80f18_25053592';
?>
<div class="sidenav col-2">
  <a class="home active" href="index.php"><i class="fa fa-home"></i> Home</a>
  <button class="dropdown-btn group head area mode rate company vowner vehicle product item transport"><i class="fa fa-bar-chart-o"></i> Master
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a class="group"     href="index.php?module=group&func=listing"><i class="fa fa-group"></i> Group</a>
    <a class="head"      href="index.php?module=head&func=listing"><i class="fa fa-address-book"></i> Head</a>
    <a class="area"      href="index.php?module=area&func=listing"><i class="fa fa-circle"></i> Area</a>
    <a class="mode"      href="index.php?module=mode&func=listing"><i class="fa fa-circle"></i> Mode</a>
    <a class="rate"      href="index.php?module=rate&func=listing"><i class="fa fa-rupee"></i> Rate</a>
    <a class="company"   href="index.php?module=company&func=listing"><i class="fa fa-circle"></i> Company</a>
    <a class="vowner"    href="index.php?module=vowner&func=listing"><i class="fa fa-truck"></i> Vehicle Owner</a>
    <a class="vehicle"   href="index.php?module=vehicle&func=listing"><i class="fa fa-truck"></i> Vehicle</a>
    <a class="product"   href="index.php?module=product&func=listing"><i class="fa fa-circle"></i> Product</a>
    <a class="item"      href="index.php?module=item&func=listing"><i class="fa fa-circle"></i> Item</a>
    <a class="transport" href="index.php?module=transport&func=listing"><i class="fa fa-truck"></i> Transport</a>
  </div>

  <button class="dropdown-btn banquet"><i class="fa fa-file-o"></i> Bill
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a href="index.php?module=bill&func=add"><i class="fa fa-cutlery fa-spin text-primary"></i> Add</a>
    <a href="index.php?module=bill&func=listing"><i class="fa fa-times-circle-o text-danger"></i> Listing</a>
  </div>

  <button class="dropdown-btn consignment"><i class="fa fa-truck"></i> Consignment
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a href="index.php?module=consignment&func=add"><i class="fa fa-cutlery fa-spin text-primary"></i> Add</a>
    <a href="index.php?module=consignment&func=listing"><i class="fa fa-times-circle-o text-danger"></i> Listing</a>
  </div>
  <button class="dropdown-btn challan"><i class="fa fa-truck"></i> Challan-Consignment Note
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a href="index.php?module=challan&func=add"><i class="fa fa-cutlery fa-spin text-primary"></i> Add</a>
    <a href="index.php?module=challan&func=listing"><i class="fa fa-times-circle-o text-danger"></i> Listing</a>
  </div>

  <button class="dropdown-btn banquet"><i class="fa fa-truck"></i> Freight
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a href="index.php?module=consignment&func=add"><i class="fa fa-cutlery fa-spin text-primary"></i> Add</a>
    <a href="index.php?module=consignment&func=listing"><i class="fa fa-times-circle-o text-danger"></i> Listing</a>
  </div>
  <button class="dropdown-btn banquet"><i class="fa fa-bar-chart-o"></i> Accounts
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a href="index.php?module=consignment&func=add"><i class="fa fa-cutlery fa-spin text-primary"></i> Add</a>
    <a href="index.php?module=consignment&func=listing"><i class="fa fa-times-circle-o text-danger"></i> Listing</a>
  </div>
  <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Reports
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">  
    <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Bill
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="group" href="index.php?module=companywise&func=detail"><i class="fa fa-bed"></i> Companywise Detail</a>
      <a class="group" href="index.php?module=companywise&func=acwise"><i class="fa fa-bed"></i> Companywise Ac-wise</a>
    </div>
    <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Consignment
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="group" href="index.php?module=group&func=listing"><i class="fa fa-bed"></i> +++</a>
    </div>
    <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Excel
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="group" href="index.php?module=group&func=listing"><i class="fa fa-bed"></i> ++</a>
    </div>
    <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Shortage/Damage
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="group" href="index.php?module=group&func=listing"><i class="fa fa-bed"></i> ++</a>
    </div>
    <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Own Vehicle
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="group" href="index.php?module=group&func=listing"><i class="fa fa-bed"></i> +++</a>
    </div>
    <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Accounts
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="group" href="index.php?module=accounts&func=listing"><i class="fa fa-bed"></i> Group</a>
    </div>
  </div>

  <button class="dropdown-btn backup restore"><i class="fa fa-gear"></i> Setting
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a class="group" href="index.php?module=util&func=listing"><i class="fa fa-hdd-o"></i> Backup</a>
    <a class="group" href="index.php?module=util&func=import"><i class="fa fa-window-restore"></i> Restore from Neuron</a>
  </div>

  
  <a href="index.php?module=user&func=selectfy"><i class="fa fa-sign-out"></i> Exit</a>
  <a href="#" class="fa fa-sign-out">
    <span class="menu-text" data-target=".bs-logout-modal-sm" data-toggle="modal">Logout</span>
  </a>
  <br>
  <br>
  <br>
</div>


<?php echo '<script'; ?>
>
var dropdown = document.getElementsByClassName("dropdown-btn");
var i;
for (i = 0; i < dropdown.length; i++) {
  dropdown[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var dropdownContent = this.nextElementSibling;
    if (dropdownContent.style.display === "block") {
      dropdownContent.style.display = "none";
    } else {
      dropdownContent.style.display = "block";
    }
  });
}
var showactive = "<?php if ($_REQUEST['module']) {
echo $_REQUEST['module'];
} else { ?>home<?php }?>";
$(document).ready(function() {
    $("."+showactive).trigger('click').addClass("active");
});
<?php echo '</script'; ?>
>

<style>  
.navbar {
  background-color: #09192A !important;
}
.sidenav {
  height: 100%;
  width: 100%;
  font-size: 13px;
  position: fixed;
  z-index: 1;
  top: 30px;
  bottom: 60px;
  left: 0;
  background-color:#09192A;
  overflow-x: hidden;
  padding-top: 20px;
}
.sidenav a, .dropdown-btn {
  padding: 6px 8px 6px 10px;
  text-decoration: none;
  color: #818181;
  display: block;
  border: none;
  background: none;
  width:100%;
  text-align: left;
  cursor: pointer;
  outline: none;
}
.sidenav a:hover, .dropdown-btn:hover {
  color: #f1f1f1;
}
.active {
  background-color: #225081;
  color: white;
}
.dropdown-container {
  display: none;
  padding-left: 8px;
}
.fa-caret-down {
  float: right;
  padding-right: 8px;
}
</style><?php }
}
