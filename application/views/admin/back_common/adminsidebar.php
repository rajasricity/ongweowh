<? $d = &get_instance(); ?> 
            
            <!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
<div class="slimscroll-menu" id="remove-scroll">

<!--- Sidemenu -->
<div id="sidebar-menu">
    <!-- Left Menu Start -->
    <ul class="metismenu" id="side-menu">
        <li class="menu-title">Main</li>
        
      <? if($d->session->userdata("role") == "superadmin"){ ?>    
        <li>
            <a href="<? echo base_url('admin/dashboard') ?>" class="waves-effect">
                <i class="ti-home"></i> <span> Dashboard </span>
            </a>
        </li>
        
        <li>
            <a href="<? echo base_url('admin/apps') ?>" class="waves-effect">
                <i class="ion ion-ios-apps"></i> <span> Customers </span>
            </a>
        </li>

        
<!--
        <li>
            <a href="<? //echo base_url('admin/Users') ?>" class="waves-effect">
                <i class="ti-user"></i> <span> Users </span>
            </a>
        </li>
        <li>
            <a href="<? //echo base_url('admin/locations') ?>" class="waves-effect">
                <i class="ti-location-pin"></i> <span> Locations </span>
            </a>
        </li>
-->
        
      <? } ?>  
       
      <? if($d->session->userdata("role") == "user"){ ?>    
        <li>
            <a href="<? echo base_url('user/userdashboard') ?>" class="waves-effect">
                <i class="ti-home"></i> <span> Dashboard </span>
            </a>
        </li>
        
      <? } ?>       
        
    </ul>

</div>
<!-- Sidebar -->
<div class="clearfix"></div>

</div>
<!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->