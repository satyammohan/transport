<?php
/* Smarty version 3.1.39, created on 2022-05-08 05:40:50
  from 'C:\xampp\htdocs\hotel\templates\common\container.tpl.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_62770a8a8f1c45_18925221',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7ac79931ec5acad9ab2aef24995aa63f22c958a0' => 
    array (
      0 => 'C:\\xampp\\htdocs\\hotel\\templates\\common\\container.tpl.html',
      1 => 1631240806,
      2 => 'file',
    ),
    '061885be641aaf1b71a591adf86f7edd6c6952a3' => 
    array (
      0 => 'C:\\xampp\\htdocs\\hotel\\templates\\common\\header.tpl.html',
      1 => 1630891650,
      2 => 'file',
    ),
    '0c987914a0a603a05eff36b43930c5f32a8052fa' => 
    array (
      0 => 'C:\\xampp\\htdocs\\hotel\\templates\\common\\menu.tpl.html',
      1 => 1636858460,
      2 => 'file',
    ),
    'ccf3bfdfb77377e7b7f3feb5bb02a95f300dd87f' => 
    array (
      0 => 'C:\\xampp\\htdocs\\hotel\\templates\\info\\welcome.tpl.html',
      1 => 1630646412,
      2 => 'file',
    ),
    '0d580e3bd9a227168a4ff1f0a0e956c4b37754f5' => 
    array (
      0 => 'C:\\xampp\\htdocs\\hotel\\templates\\common\\footer.tpl.html',
      1 => 1630646412,
      2 => 'file',
    ),
  ),
  'cache_lifetime' => 3600,
),true)) {
function content_62770a8a8f1c45_18925221 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html>
    <head>
        <title>Hotel Management System
                            :: &nbsp;ODI RAY INDUSTRIES LIMITED&nbsp;(Apr  1, 2022-Mar 31, 2023)
                    </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
        <link rel="stylesheet" href="js/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.css">

        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="js/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

		<link rel="stylesheet" href="js/jquery.mobile-1.4.5/demos/_assets/css/jqm-demos.css">
		<script src="js/jquery.mobile-1.4.5/demos/_assets/js/index.js"></script>

        <link rel="stylesheet" href="css/common.css">
        <script type="text/javascript" src="js/jqprint.js"></script>
        <script type="text/javascript" src="js/table2excel.js"></script>
        <script src="js/common.js?time=1"></script>

                <script language="javascript" type="text/javascript">
            var js_date = "dd/mm/yy";
            var sdate = "01/04/2022";
            var edate = "31/03/2023";
        </script>
    </head>
    <body>
                            <div class="row w-100">
                <div class="col-12">
                    <div class="header">
  <nav class="navbar fixed-top" style="padding: 0">
    <div class="container-fluid mt-2 mb-2">
      <div class="col-lg-12 text-white">
        <div class="float-left" >
          <b>Welcome Admin</b>
        </div>
        <div class="float-right">
            <a href="#" class="text-white">
              <span data-target=".bs-logout-modal-sm" data-toggle="modal"><i class="fa fa-power-off"></i></span>
            </a>  
        </div>
        <div class="text-center">
            <font size='+1'>ODI RAY INDUSTRIES LIMITED</font>&nbsp;
            01/04/2022-31/03/2023
        </div>
      </div>
    </div>
  </nav>
</div>
                </div>
            </div>
                <div class="row grow w-100 h-100">
                            <div class="col-2 h-100">
                    <br><br>
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
   
      <button class="dropdown-btn management"><i class="fa fa-tasks"></i> Management
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-container">
      <a class="" href="index.php?module=management&func=import"><i class="fa fa-object-group"></i> Import</a>
      <a class="" href="index.php?module=management&func=reconcile"><i class="fa fa-check"></i> Reconcile</a>
      <a class="" href="index.php?module=management&func=report"><i class="fa fa-bar-chart-o"></i> Report</a>
    </div>
  
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
        <a class="" href="index.php?module=report&func=mgm_roomdetail"><i class="fa fa-bar-chart-o"></i> (*) Room Detail</a>
    <a class="" href="index.php?module=report&func=mgm_mrdetail"><i class="fa fa-bar-chart-o"></i> (*) MR Detail</a>
    <a class="" href="index.php?module=report&func=inout"><i class="fa fa-bar-chart-o"></i> Check in-out Report</a>
    <a class="" href="index.php?module=report&func=error"><i class="fa fa-bar-chart-o"></i> Error Report</a>
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
<script>
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
var showactive = "home";
$(document).ready(function() {
    $("."+showactive).trigger('click').addClass("active");
});
</script>

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
</style>                </div>
                <div class="col-10 w-100 h-100"  style="left: 20px !important;">
                    <br><br><br>
                                        <br><br>
                </div>
                        <div class="footer">
    <div class="float-left">Copyright &copy; 2021 - All Rights Reserved </div>
    <div class="float-right">Powered by : @ Solutions</div>
</div>        </div>
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="btn" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
		<div tabindex="-1" class="modal bs-logout-modal-sm" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-sm">
			  <div class="modal-content">
				<div class="modal-header"><h4>Are you sure you want to logout ?</h4></div>
				<div class="modal-footer">
					<a class="btn btn-danger btn-xl" href="index.php?module=user&func=logout">Logout</a>
					<a class="btn btn-primary btn-xl" data-dismiss="modal">Close</a>
				</div>
			  </div>
			</div>
		</div>
    </body>
</html>
<?php }
}
