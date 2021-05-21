
<? admin_header(); ?> 

           
<? admin_sidebar(); ?>            


 <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="page-title-box">
                            <div class="row align-items-center">
                                
                                <div class="col-sm-6">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/dashboard') ?>"><i class="fas fa-home"></i></a></li>
                                        <li class="breadcrumb-item active">Batch Process</li>
                                    </ol>

                                </div>
                                <div class="col-sm-6">
                                

                                </div>
                                
                            </div>
                        </div>
                        <!-- end row -->
                        
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
<!--                                        <h4 class="mt-0 header-title">All Locations</h4>-->
                                        
                                        <div class="table-responsive">
                                            <table class="table mb-0 table-bordered" id="usersTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                       
                                                        <th>#</th>
                                                        <th>Customer Name</th>
                                                        <th>User Name</th>
                                                        <th>Module Name</th>
                                                        <th>Status</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                  
                                                  <? 
													$udata = $this->mongo_db->get("tbl_import_data"); 
													$i = 1;
													foreach($udata as $ud){
														
														$module = "";
														
														if($ud["table"] == "tbl_inventory"){
															
															$module = "Inventory";
															
														}elseif($ud["table"] == "tbl_adjustments"){
															
															$module = "Adjustments";
															
														}elseif($ud["table"] == "tbl_issues"){
															
															$module = "Shipments";
															
														}elseif($ud["table"] == "tbl_returns"){
															
															$module = "Pickups";
															
														}elseif($ud["table"] == "tbl_touts"){
															
															$module = "Transfers";
															
														}

													?>
                                                    
                                                    <tr>
                                                       
                                                        <td><? echo $i ?></td>
                                                        <td><? echo $this->mongo_db->get_where("tbl_apps",["appId"=>$ud['appId']])[0]["appname"]; ?></td>
                                                        <td><? echo ucfirst($ud['imported_user']) ?></td>
                                                        <td><? echo $module ?></td>
                                                        <td><? if($ud["status"] == "processing"){echo '<i class="badge badge-info" style="font-size:14px">'.$ud["status"].'</i>';}else{echo '<i class="badge badge-warning" style="font-size:14px">'.$ud["status"].'</i>';} ?></td>
                                                    </tr>
                                                    
                                                  <? $i++;} ?>  
                                                  
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        

                    </div>
                    <!-- container-fluid -->

                </div>
                <!-- content -->



<? admin_footer(); ?>

<script>
				
$(document).ready(function() {
    $('#usersTable').DataTable({});
});		
	
</script>

 