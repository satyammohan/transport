<?php
/* Smarty version 3.1.39, created on 2022-05-08 05:40:50
  from 'C:\xampp\htdocs\hotel\templates\common\menu.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_62770a8a67e6e4_95082391',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0c987914a0a603a05eff36b43930c5f32a8052fa' => 
    array (
      0 => 'C:\\xampp\\htdocs\\hotel\\templates\\common\\menu.tpl.html',
      1 => 1636858460,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_62770a8a67e6e4_95082391 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '165524564262770a8a671ef5_78766146';
?>
<div class="sidenav col-2">
  <a class="home active" href="index.php"><i class="fa fa-home"></i> Home</a>
  <button class="dropdown-btn roomtype rooms user message taxmaster"><i class="fa fa-bar-chart-o"></i> Master
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a class="roomtype" href="index.php?module=roomtype&func=listing"><i class="fa fa-bed"></i> Room Type</a>
    <a class="rooms" href="index.php?module=rooms&func=listing"><i class="fa fa-bed"></i> Room</a>
    <a class="user" href="index.php?module=user&func=listing"><i class="fa fa-user"></i> User</a>
    <!---<a class="message" href="index.php?module=message&func=listing"><i class="fa fa-desktop"></i> News Letters</a>--->
    <a class="taxmaster" href="index.php?module=taxmaster&func=listing"><i class="fa fa-percent"></i> GST Tax</a>
    <a class="corporate" href="index.php?module=corporate&func=listing"><i class="fa fa-industry"></i> Corporate</a>
    <a class="partner" href="index.php?module=partner&func=listing"><i class="fa fa-handshake-o"></i> Partner</a>
  </div>
   
  <?php if ($_SESSION['is_admin']) {?>
    <button class="dropdown-btn management"><i class="fa fa-tasks"></i> Management
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="" href="index.php?module=management&func=import"><i class="fa fa-object-group"></i> Import</a>
      <a class="" href="index.php?module=management&func=reconcile"><i class="fa fa-check"></i> Reconcile</a>
      <a class="" href="index.php?module=management&func=report"><i class="fa fa-bar-chart-o"></i> Report</a>
    </div>
  <?php }?>

  <br>
  <a class="reservation" href="index.php?module=reservation&func=dashboard"><i class="fa fa-users fa-spin"></i> Room Dashboard</a>
  <a class="reservation" href="index.php?module=reservation&func=checkin"><i class="fa fa-users fa-spin"></i> Guest Check-In</a>
  <a class="reservation" href="index.php?module=reservation&func=checkout"><i class="fa fa-users fa-spin"></i> Guest Check-out</a>
  <a class="reservation" href="index.php?module=reservation&func=listing"><i class="fa fa-users fa-spin"></i> Guest Booking Listing</a>
  <a class="reservation" href="index.php?module=reservation&func=foodlist"><i class="fa fa fa-file-text-o"></i> Food Listing</a>
  <a class="reservation" href="index.php?module=reservation&func=otherlist"><i class="fa fa fa-file-text-o"></i> Other Listing</a>
  <a class="reservation" href="index.php?module=reservation&func=cancellist"><i class="fa fa-times-circle-o text-danger"></i> Cancelled Booking</a>
  <a class="reservation" href="index.php?module=reservation&func=mrlist"><i class="fa fa fa-file-text-o text-success"></i> MR Listing</a>
  <br>
  <button class="dropdown-btn banquet"><i class="fa fa-bar-chart-o"></i> Banquet
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a href="index.php?module=banquet&func=listing"><i class="fa fa-cutlery fa-spin text-primary"></i> Banquet Booking</a>
    <a href="index.php?module=banquet&func=cancellist"><i class="fa fa-times-circle-o text-danger"></i> Cancelled Booking</a>
  </div>
  <button class="dropdown-btn report"><i class="fa fa-bar-chart-o"></i> Reports
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <!-- <a class="" href="index.php?module=report&func=gst"><i class="fa fa-bar-chart-o"></i> GST Report</a> -->
    <?php if ($_SESSION['is_admin']) {?>
    <a class="" href="index.php?module=report&func=mgm_roomdetail"><i class="fa fa-bar-chart-o"></i> (*) Room Detail</a>
    <a class="" href="index.php?module=report&func=mgm_mrdetail"><i class="fa fa-bar-chart-o"></i> (*) MR Detail</a>
    <a class="" href="index.php?module=report&func=inout"><i class="fa fa-bar-chart-o"></i> Check in-out Report</a>
    <a class="" href="index.php?module=report&func=error"><i class="fa fa-bar-chart-o"></i> Error Report</a>
    <?php }?>
    <a class="" href="index.php?module=report&func=gstnew"><i class="fa fa-bar-chart-o"></i> GST Report</a>
    <a class="" href="index.php?module=report&func=collection"><i class="fa fa-bar-chart-o"></i> Collection Report</a>
    <a class="" href="index.php?module=report&func=room"><i class="fa fa-bar-chart-o"></i> Room Booking</a>
    <a class="" href="index.php?module=report&func=user"><i class="fa fa-bar-chart-o"></i> Commission</a>
    <a class="" href="index.php?module=report&func=pay"><i class="fa fa-bar-chart-o"></i> Pay In/Out</a>
    <a class="" href="index.php?module=report&func=due"><i class="fa fa-bar-chart-o"></i> Due Report</a>

    <a class="gst" href="index.php?module=report&func=roomdet"><i class="fa fa-bar-chart-o"></i> Room Detail</a>
    <a class="gst" href="index.php?module=report&func=discount"><i class="fa fa-bar-chart-o"></i> Discount Report</a>
    <a class="gst" href="index.php?module=report&func=entry"><i class="fa fa-bar-chart-o"></i> Daily Entry</a>
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
