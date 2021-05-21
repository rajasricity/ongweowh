
<? admin_header(); ?> 

           
<? admin_sidebar(); ?>            
<?
$mdb = mongodb;

$mng=$this->admin->Mconfig();
$query = $this->mongo_db->get_where("tbl_auths",array("email"=>$this->session->userdata("admin_email")));
$locations = $query[0]["locations"];
$aData = $this->mongo_db->get_where("tbl_apps",array("appId"=>$query[0]["appid"]));
$user = $this->admin->getRow($mng,['email'=>$this->session->userdata('admin_email')],[],"$mdb.tbl_auths");
$this->session->set_userdata(array("appid"=>$query[0]["appid"]));

$sappid = $query[0]["appid"];
$this->mongo_db->switch_db("$database")
?>

 <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="page-title-box">
                            <div class="row align-items-center">
                                
                                <div class="col-sm-6">
                                    <h4 class="page-title"><i class="mdi mdi-tag-text-outline"></i> <? echo $aData[0]['appname']; ?></h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item active">Welcome to Ampcus Inventory</li>
                                    </ol>

                                </div>
                                
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- container-fluid -->
                    
                    
                    
                    
                    <div class="row">
                    
                    	<div class="col-xl-12 col-md-12">
                    	
                    		<div class="card-body">
                    		
								<h6><i class="mdi mdi-map-marker-multiple"></i> Location Summary</h6>
								Click on location name to view details and enter transactions
								<div id="accordion">

						  			<? $items = $this->admin->getRows("",["status"=>"Active"],[],"$database.tbl_items");
									   $id = 0;			

									   foreach($items as $item){	
									?>

									  <div class="card">
										<div class="card-header">
										  <a class="card-link" data-toggle="collapse" style="color: white;text-transform: uppercase" href="#collapse<? echo $id ?>">
											<? echo $item->item_name ?>
										  </a>
										</div>
										<div id="collapse<? echo $id ?>" class="collapse <? echo ($id == 0) ? 'show' : '' ?>" data-parent="#accordion">
										  <div class="card-body">
										  
										  <div class="table-responsive">
										  
											<table class="table table-bordered table-striped">
												<thead class="thead-light">
													<tr>
														<th width="25%"><i class="mdi mdi-map-marker-outline"></i> Location Details</th>
														<th><i class="mdi mdi-star-box-outline"></i> Starting Balance</th>
														<th><i class="mdi mdi-star-box-outline"></i> Shipments</th>
														<th><i class="mdi mdi-keyboard-return"></i> Pickups</th>
														<th><i class="mdi mdi-login"></i> Transfer Ins</th>
														<th><i class="mdi mdi-logout"></i> Transfer Outs</th>
														<th><i class="mdi mdi-format-align-justify"></i> Adjustments</th>
														<th><i class="mdi mdi-scale-balance"></i> Ending Balance</th>
													</tr>
												</thead>
												<tbody>
													<?

													foreach($locations as $location){
														
														$loccode = $location->loccode;
													$locdata = $this->admin->getRow("",['loccode'=>$loccode],[],"$database.tbl_locations");

													if($location->Type == "from" && $locdata->status == "Active"){
														$appid = $user->appid;
														
														$locinvdata = $this->admin->getRow("",['appId'=>$appid,"item.item_name"=>$item->item_name,"loccode"=>$loccode],[],"$database.tbl_inventory");

														
														$issues=($this->common->getInventorycount($database,"tbl_issues",$appid,$loccode,"tlcoationcode",$item->item_name));
														$returns=($this->common->getInventorycount($database,"tbl_returns",$appid,$loccode,"tlcoationcode",$item->item_name));
														$tins=($this->common->getInventorycount($database,"tbl_touts",$appid,$loccode,"tlocationcode",$item->item_name));
														$touts=($this->common->getInventorycount($database,"tbl_touts",$appid,$loccode,"flcoationcode",$item->item_name));
														$adjusts=($this->common->getInventorycount($database,"tbl_adjustments",$appid,$loccode,"tlcoationcode",$item->item_name));
														$eb = ($locinvdata->starting_balance+$issues+$returns+$tins)-($touts+$adjusts);
													?>
											<tr>
												<td>
													<a href="<? echo base_url('main/inventory/location/').$location->loccode ?>/on/<? echo $item->item_name ?>">
													<span class="badge badge-primary" style="font-size: 14px;white-space: unset !important;"><? echo $location->LocationName." - ".$loccode; ?></span>
													</a>
												</td>
												<td><? echo ($locinvdata->starting_balance != "") ? $locinvdata->starting_balance : 0; ?></td>
												<td><? echo $issues; ?></td>
												<td><? echo $returns; ?></td>
												<td align="right"><? echo $tins; ?></td>
												<td align="right"><? echo $touts; ?></td>
												<td align="right"><? echo $adjusts; ?></td>
												<td align="right"><? echo $eb; ?></td>
											</tr>
													<?}}?>
												</tbody>

											</table>
										  </div>
										  </div>
										</div>
									  </div>

									 <? 
									   $id++;
									   } ?> 

								</div>
							
                   			</div>
                    	
                    	

<? if(count($locations) == 0){
	echo "<center><b>List of Locations are empty</b></center>";
}
?>
		</div>
	</div>
						</div>

					</div>
                    

                </div>
                <!-- content -->
                
                
                



<? admin_footer(); ?>