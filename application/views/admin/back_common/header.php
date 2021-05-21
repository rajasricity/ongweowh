<? $d = &get_instance();
$email = $d->session->userdata("admin_email");
$udata = $d->admin->get_admin();
$pic = ($udata["profile_pic"] != "") ? $udata["profile_pic"] : 'assets/images/users/superAdmin.jpg';
?>


<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <title>Ampcus | Logsitics Management</title>
        <link rel="shortcut icon" href="<? echo base_url() ?>assets/logo/favicon.ico">

        <link href="<? echo base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<? echo base_url() ?>assets/css/metismenu.min.css" rel="stylesheet" type="text/css">
        <link href="<? echo base_url() ?>assets/css/icons.css" rel="stylesheet" type="text/css">
        <link href="<? echo base_url() ?>assets/css/style.css" rel="stylesheet" type="text/css">
        <link href="<? echo base_url() ?>assets/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
        <link href="<? echo base_url() ?>assets/dtable/datatablebootstrap.css" rel="stylesheet" type="text/css">
        <link href="<? echo base_url() ?>assets/dtable/datatablebuttons.css" rel="stylesheet" type="text/css">
        <link href="<? echo base_url() ?>assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
         <link href="<? echo base_url() ?>assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet" type="text/css">
        <link href="<? echo base_url() ?>assets/plugins/fullcalendar/css/fullcalendar.min.css" rel="stylesheet" type="text/css">
        <link href="<? echo base_url(); ?>assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="https://cdn.datatables.net/scroller/2.0.1/css/scroller.dataTables.min.css" rel="stylesheet" type="text/css" />
        
        <style>
		.datepicker-inline{
			width: 100%;
		}
			
			
		</style>
    </head>

    <body class="enlarged">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Top Bar Start -->
            <div class="topbar">
            <? if($d->session->userdata("role") == "superadmin"){ ?>
                <!-- LOGO -->
                <div class="topbar-left">
                    <a href="<? echo base_url('admin/dashboard') ?>" class="logo">
                        <span><img src="<? echo base_url() ?>assets/logo/ongweoweh.png" alt="" height="40"></span>
                        <i><img src="<? echo base_url() ?>assets/logo/logo_small.png" alt="" height="22"></i>
                    </a>
                </div>
            <? } ?>
            <? if($d->session->userdata("role") == "customer_admin"){ ?>

                <div class="topbar-left">
                    <a href="<? echo base_url('main/Admindashboard') ?>" class="logo">
                        <span><img src="<? echo base_url() ?>assets/logo/ongweoweh.png" alt="" height="40"></span>
                        <i><img src="<? echo base_url() ?>assets/logo/logo_small.png" alt="" height="22"></i>
                    </a>
                </div>
            <? } ?>

            <? if($d->session->userdata("role") == "user"){ ?>

                <div class="topbar-left">
                    <a href="<? echo base_url('user/Userdashboard') ?>" class="logo">
                        <span><img src="<? echo base_url() ?>assets/logo/ongweoweh.png" alt="" height="40"></span>
                        <i><img src="<? echo base_url() ?>assets/logo/logo_small.png" alt="" height="22"></i>
                    </a>
                </div>
            <? } ?>
                <nav class="navbar-custom">
                    <ul class="navbar-right list-inline float-right mb-0">
                        
                       <!--  <li class="dropdown notification-list list-inline-item d-none d-md-inline-block">
                            <form role="search" class="app-search">
                                <div class="form-group mb-0">
                                    <input type="text" class="form-control" placeholder="Search..">
                                    <button type="button"><i class="fa fa-search"></i></button>
                                </div>
                            </form>
                        </li> -->
                        
                        <li class="dropdown notification-list list-inline-item">
                            <div class="dropdown notification-list nav-pro-img">
                                <a class="dropdown-toggle nav-link arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                   
                                    <img src="<? echo base_url().$pic ?>" alt="user" class="rounded-circle">
                                    
                                </a>
                                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                    <!-- item-->
                                    <a class="dropdown-item" href="<? echo base_url('admin/dashboard/profile') ?>"><i class="mdi mdi-account-circle m-r-5"></i> Profile</a>
                                    <div class="dropdown-divider"></div>                                    
                                    
                                    <? if($d->session->userdata("role") == "superadmin"){ ?>
                                    
                                    	<a class="dropdown-item" href="<? echo base_url('admin/dashboard/batchprocess') ?>"><i class="mdi mdi-chart-timeline m-r-5"></i> Batch Process</a>
                                    	<div class="dropdown-divider"></div>
                                    
                                    <? } ?>
                                    
                                    <? if($d->session->userdata("role") == "customer_admin"){ ?>
                                    
                                    	<a class="dropdown-item" href="<? echo base_url('main/Admindashboard/myLocations') ?>"><i class="mdi mdi-chart-timeline m-r-5"></i> Locations</a>
                                    	<div class="dropdown-divider"></div>
                                    
                                    <? } ?>
                                    
                                    <a class="dropdown-item text-danger" href="<? echo base_url('admin/dashboard/logout') ?>"><i class="mdi mdi-power text-danger"></i> Logout</a>
                                </div>
                            </div>
                        </li>

                    </ul>

                    <ul class="list-inline menu-left mb-0">
                        <li class="float-left">
                            <button class="button-menu-mobile open-left waves-effect">
                                <i class="mdi mdi-menu"></i>
                            </button>
                        </li>
                    </ul>

                </nav>

            </div>
            <!-- Top Bar End -->