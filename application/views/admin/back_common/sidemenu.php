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

                             <? if($_SESSION['appid']){?>
                                <li>
<a href="<? echo base_url('admin/apps/editApp/'.$_SESSION['appid']); ?>" class="waves-effect">
                <i class="fa fa-map-marker"></i> <span> Locations </span>
</a>
        </li>
        <li>
<a href="<? echo base_url('admin/apps/items/'.$_SESSION['appid']); ?>" class="waves-effect">
                <i class="fa fa-list"></i> <span> Items </span>
</a>
        </li>
        <li>
<a href="<? echo base_url('admin/apps/transfers/'.$_SESSION['appid']); ?>" class="waves-effect">
<i class="mdi mdi-arrow-collapse-horizontal"></i> <span> Transfers </span>
</a>
        </li>
        <li>
<a href="<? echo base_url('admin/apps/issues/'.$_SESSION['appid']); ?>" class="waves-effect">
                <i class="dripicons-contract"></i> <span> Shipments </span>
</a>
        </li>
        <li>
<a href="<? echo base_url('admin/apps/returns/'.$_SESSION['appid']); ?>" class="waves-effect">
                <i class="dripicons-return"></i> <span> Pickups </span>
</a>
        </li>
        <li>
<a href="<? echo base_url('admin/apps/adjustments/'.$_SESSION['appid']); ?>" class="waves-effect">
                <i class="dripicons-align-justify"></i> <span> Adjustments </span>
</a>
        </li>
        <li>
<a href="<? echo base_url('admin/apps/locationInventory/'.$_SESSION['appid']); ?>" class="waves-effect">
                <i class="mdi mdi-home-map-marker"></i> <span> Location Inventory </span>
</a>
        </li>
        <li>
            <a href="<? echo base_url('admin/apps/locationAccess') ?>/<? echo $_SESSION['appid']; ?>" class="waves-effect">
                <i class="mdi mdi-map-marker-check"></i> <span> Location Access </span>
            </a>
        </li>
        <?}?>
                            
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
                                <a href="<? echo base_url('user/Userdashboard') ?>" class="waves-effect">
                                    <i class="ti-home"></i> <span> Dashboard </span>
                                </a>
                            </li>
                            
                          <? } ?>
                          
                          <? if($d->session->userdata("role") == "customer_admin"){ ?>    
                            <li>
                                <a href="<? echo base_url('main/Admindashboard') ?>" class="waves-effect">
                                    <i class="ti-home"></i> <span> Inventory </span>
                                </a>
                            </li>
                            <li>
                                <a href="<? echo base_url('main/locations/ChepAdmin') ?>" class="waves-effect">
                                    <i class="ti-user"></i> <span> Inventory Admin </span>
                                </a>
                            </li>
                            <li>
                                <a href="<? echo base_url('main/locations/AdminLocations') ?>" class="waves-effect">
                                    <i class="ti-location-pin"></i> <span> Locations </span>
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