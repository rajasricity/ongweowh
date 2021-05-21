
<? admin_header(); ?> 

           
<? admin_sidebar(); ?>            
<?
$mng = $this->admin->Mconfig();
?>
<style>
    table{
        width:100% !important;
    }
    td{
        padding:6px !important;
    }
    .apexcharts-canvas {
    position: relative;
    user-select: none;
}
.card{
    margin-bottom: 0px;
}
thead{
    background: #f1f1f1;
}
thead, tr, th{
    padding: 5px !important;
}
.location{
    font-size: 12px;
    font-weight: bold;
}
</style>

 <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="page-title-box">
                            <div class="row align-items-center">
                                
                                <div class="col-sm-6">
                                    <h4 class="page-title">Dashboard</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item active">Welcome to Ongweoweh</li>
                                    </ol>

                                </div>
                                
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- container-fluid -->
                    
                    
                    
<div class="row">
    <div class="col-md-4">
        <a href="<? echo base_url('admin/apps') ?>">
                                <div class="card mini-stat bg-primary text-white">
                                    <div class="card-body">
                                        <div class="">
                                            <div class="float-left mini-stat-img mr-4">
                                                <img src="<? echo base_url() ?>assets/images/customers.png" alt="" >
                                            </div>
                                            <h5 class="font-16 text-uppercase mt-0 text-white-50">CUSTOMERS</h5>
                                            <h5 class="font-500">
                                            <i class="mdi mdi-account-star-outline"></i>    
                                            <? echo count($this->mongo_db->get_where("tbl_apps",array("status"=>"Active","deleted"=>0))) ?>
                                            &nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;
                                            <i class="mdi mdi-map-marker-multiple"></i>
                                            <? echo $locationsCount ?>
                                            </h5>

                                        </div>
                                    </div>
                                </div>
                            </a>
    </div>
    <div class="col-md-4">
            <a href="<? echo base_url('admin/apps') ?>">
                                <div class="card mini-stat bg-primary text-white">
                                    <div class="card-body">
                                        <div class="">
                                            <div class="float-left mini-stat-img mr-4">
                                                <img src="<? echo base_url() ?>assets/images/users.png" alt="" >
                                            </div>
                                            <h5 class="font-16 text-uppercase mt-0 text-white-50">Users</h5>
                                            <h5 class="font-500">
                                                <!-- <i class="mdi mdi-shield-account-outline"></i> 
                                                <? //echo count($this->mongo_db->get_where("tbl_auths",array("status"=>"Active",""))) ?>
                                                &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; -->
                                                <i class="mdi mdi-account-multiple-plus-outline"></i>
                                                <? echo count($this->mongo_db->get_where("tbl_auths",array("status"=>"Active","role"=>"user"))) ?>
                                                &nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;
                                                <i class="mdi mdi-account-check-outline"></i>
                                                <? echo count($this->mongo_db->get_where("tbl_auths",array("status"=>"Active","role"=>"customer_admin"))) ?>
                                            </h5>

                                        </div>
                                    </div>
                                </div>
                            </a>
    </div>

     <div class="col-md-4">
            <a href="<? echo base_url('admin/apps') ?>">
                                <div class="card mini-stat bg-primary text-white">
                                    <div class="card-body">
                                        <div class="">
                                            <div class="float-left mini-stat-img mr-4">
                                                <img src="<? echo base_url() ?>assets/images/item.png" alt="" >
                                            </div>
                                            <h5 class="font-16 text-uppercase mt-0 text-white-50">Items</h5>
                                            <h5 class="font-500">
                                                <i class="dripicons-archive"></i>
                                                <? echo $itemsCount; ?>
                                            </h5>

                                        </div>
                                    </div>
                                </div>
                            </a>
    </div>

    </div>

    <div class="row">
    <div class="col-md-6">
    <?

    
    ?>
    <div class="card mt-4">
            <div class="card-body" style="padding: 0px;height: 480px;">
        <div class="row">
            <div class="col-md-12">
                    <select name="countItem" id="countItem" class="form-control" onchange="updateDataCounts(this.value)">
                        <?
                        foreach($items as $item){
        foreach($item as $key=>$i){
                if($key !== 'database' && $key !== 'appName'){
echo "<option value='".$i->item_name."#".$item['database']."'>".$i->item_name." / ".$item['appName']."</option>";    
                                       }
                    
                                 }
                                                }
    ?>
                    </select>
                    </div>
        </div>

        
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-justified" role="tablist"  style="margin-top:5px;">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                                                    <span class="d-block d-sm-none"><i class="dripicons-contract"></i></span>
                                                    <span class="d-none d-sm-block"><i class="dripicons-contract"></i> Shipments</span>    
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#profile" role="tab">
                                                    <span class="d-block d-sm-none"><i class="dripicons-return"></i></span>
                                                    <span class="d-none d-sm-block"><i class="dripicons-return"></i> Pickups</span>    
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#messages" role="tab">
                                                    <span class="d-block d-sm-none"><i class="ion ion-md-arrow-round-back"></i></span>
                                                    <span class="d-none d-sm-block"><i class="ion ion-md-arrow-round-back"></i> Transfers</span>    
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#adjustments" role="tab">
                                                    <span class="d-block d-sm-none"><i class="dripicons-align-justify"></i></i></span>
                                                    <span class="d-none d-sm-block"><i class="dripicons-align-justify"></i> Adjustments</span>    
                                                </a>
                                            </li>
                                        </ul>
        
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="home" role="tabpanel">
<table class="table table-nowrap" id="shipments"></table>
                                            </div>
                                            <div class="tab-pane" id="profile" role="tabpanel">
<table class="table table-nowrap" id="pickups"></table>
                                            </div>
                                            <div class="tab-pane" id="messages" role="tabpanel">
<table class="table table-nowrap" id="transfers"></table>
                                            </div>
                                            <div class="tab-pane" id="adjustments" role="tabpanel">
<div id="adjustments"></div>

                                            </div>
                                        </div>
        
                                    </div>
                                </div>
                                
    </div>
    <div class="col-md-6">
        
        <div class="card mt-4" style="max-height: 480px;overflow-y: auto">
                                    <div class="card-body">
                                        <h4 class="mt-0 header-title mb-4">Task Activity</h4>
                                        <ol class="activity-feed">
<? foreach($activities as $act){?>
<li class="feed-item">
    <div class="feed-item-list">
        <span class="date"><? echo date("d M, Y H:i A", strtotime($act['DateTime'])); ?></span>
            <span class="activity-text">
                <span class="badge badge-primary"><? echo $act['Customer']; ?></span> /
                <? echo $act['Module']; ?> /
                <? echo $act['TaskName']; ?>
            </span>
    </div>
</li>
<?}?>
                                        </ol>
                                    </div>
                                </div>
                                
        

        <!-- <div class="card" style="margin-top: 23px;">
            <div class="card-body">
                <div id='calendar'></div>
                <div style='clear:both'></div>
            </div>
        </div> -->

    </div>

</div>
<div class="row">
    <div class="col-md-6">
        <div class="card" style="margin-top: 23px;">
            <div class="card-body">
                <div id='calendar'></div>
                <div style='clear:both'></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
                  <div class="card mt-4" style="margin-top:10px;height: 480px;">
            <div class="card-body" style="padding: 3px;">
            <h4 class="mt-0 header-title">USA Map</h4>
                <!-- <div id="world-map-markers" class="vector-map-height"></div> -->
                <div id="usa" class="vector-map-height"></div>
            </div>
        </div>
    </div>
</div>

                <!-- content -->
                
                
              



<? admin_footer(); ?>
<script src="<? echo base_url() ?>assets/pages/calendar-init.js"></script>
<script>
$(function(){
    updateDataCounts($("#countItem").val());
});

function updateDataCounts(data){
    var fdata = {"data":data};
    $.ajax({
        url:"<? echo base_url(); ?>admin/Dashboard/updateData",
        data: fdata,
        type:"post",
        beforeSend: function(){
$("#shipments").html('<center><img src="<? echo base_url(); ?>assets/images/loader.gif" width="50" height="50">Loading ....</center>');
$("#pickups").html('<center><img src="<? echo base_url(); ?>assets/images/loader.gif" width="50" height="50">Loading ....</center>');
$("#transfers").html('<center><img src="<? echo base_url(); ?>assets/images/loader.gif" width="50" height="50">Loading ....</center>');
$("#adjustments").html('<center><img src="<? echo base_url(); ?>assets/images/loader.gif" width="50" height="50">Loading ....</center>');
        },
        success: function(data){
            console.log(JSON.parse(data));

            $("#shipments").html('No data found');
            $("#pickups").html('No data found');
            $("#transfers").html('No data found');
            $("#adjustments").html('<No data found');

            var append_ships = '<thead><tr><th>Shipment Date</th><th>To Location</th><th>Quantity</th></tr></thead><tbody>';
            var ships =  JSON.parse(data)['Shipments'];
            ships.forEach(function(item, index){
               var date = item.shippmentdate;
               date = new Date(date);
               date = new Intl.DateTimeFormat('en-US').format(date);
               // console.log(date);
                append_ships += '<tr><td><span class="badge badge-primary">'+date+'</span></td><td class="location">'+item.tlocation.locname+'</td><td style="text-align:right"><span class="badge badge-success">'+item.quantity+'</span></td></tr>';
            });
            append_ships+='</tbody';
            $("#shipments").html(append_ships);


            var append_picks = '<thead><tr><th>Shipment Date</th><th>To Location</th><th>Quantity</th></tr></thead><tbody>';
            var ships =  JSON.parse(data)['Pickups'];
            ships.forEach(function(item, index){
               var date = item.shippmentdate;
               date = new Date(date);
               date = new Intl.DateTimeFormat('en-US').format(date);
               // console.log(date);
                append_picks += '<tr><td><span class="badge badge-primary">'+date+'</span></td><td class="location">'+item.tlocation.locname+'</td><td style="text-align:right"><span class="badge badge-success">'+item.quantity+'</span></td></tr>';
            });
            append_picks+='</tbody';
            $("#pickups").html(append_picks);


            var append_transfers = '<thead><tr><th>Shipment Date</th><th>From Location</th><th>To Location</th><th>Quantity</th></tr></thead><tbody>';
            var transfers =  JSON.parse(data)['Transfers'];
            transfers.forEach(function(item, index){
               var date = item.shippmentdate;
               date = new Date(date);
               date = new Intl.DateTimeFormat('en-US').format(date);
               // console.log(date);
                append_transfers += '<tr><td><span class="badge badge-primary">'+date+'</span></td><td class="location">'+item.flocation.locname+'</td><td class="location">'+item.tlcoation.locname+'</td><td style="text-align:right"><span class="badge badge-success">'+item.quantity+'</span></td></tr>';
            });
            append_transfers+='</tbody';
            $("#transfers").html(append_transfers);


            var append_adjustments = '<table class="table table-nowrap"><thead><tr><th>Shipment Date</th><th>To Location</th><th>Adj. Direction</th><th>Quantity</th></tr></thead><tbody>';
            var adjustments =  JSON.parse(data)['Adjustments'];
            adjustments.forEach(function(item, index){
               var date = item.shippmentdate;
               date = new Date(date);
               date = new Intl.DateTimeFormat('en-US').format(date);
               // console.log(date);
                append_adjustments += '<tr><td><span class="badge badge-primary">'+date+'</span></td><td class="location">'+item.tlocation.locname+'</td><td class="location">'+item.adjdirection+'</td><td style="text-align:right"><span class="badge badge-success">'+item.quantity+'</span></td></tr>';
            });
            append_adjustments+='</tbody></table>';
            $("#adjustments").html(append_adjustments);


        },
        error: function(jqxhr, txtStatus, error){

        }
    });
}
</script>
 