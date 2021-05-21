<? admin_header();
?>

<? admin_sidebar();
$id = urldecode( $id );

$itemname = urldecode($this->uri->segment(6));

$mdb = mongodb;
?>
<style>
	td {
		padding: 8px !important;
	}
	
	#barChart div div div svg g g g{
		
		cursor: pointer !important;
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
						<!--                                    <h4 class="page-title">Form Advanced</h4>-->
						<ol class="breadcrumb">
							<? if($this->session->userdata('role') == 'user'){?>
							
								<li class="breadcrumb-item"><a href="<? echo base_url('user/Userdashboard') ?>">Inventory</a></li>
								
							<? }else{ ?>
							
								<li class="breadcrumb-item"><a href="<? echo base_url('main/Admindashboard') ?>">Inventory</a></li>
								
							<? } ?>
							<li class="breadcrumb-item"><? echo $itemname ?></li>
							<li class="breadcrumb-item active">Location Summary</li>
						</ol>
					</div>
					<div class="col-sm-6">
					
						<a onclick="window.history.back();" class="btn btn-dark btn-sm float-right">
						  <i class="fa fa-arrow-left"></i>
						</a>
						
					
						<? if($show == 'on'){ ?>	
						
							<a class="btn btn-primary btn-sm float-right" id="importTransfer" style="color: white;margin-right: 10px;cursor: pointer">
							  Import Transfer Outs
							</a>
							<a class="btn btn-primary btn-sm float-right" onclick="hideThis();" id="adddTouut" style="color: white;margin-right: 10px;display: none;cursor: pointer">
							  Add Transfer Out
							</a>
							
						<? } ?>	
						
					</div>

				</div>
			</div>
			<!-- end row -->


			<div class="row">
				<div class="col-lg-12">
					<?
					$mng=$this->admin->Mconfig();
					$user = $this->admin->getRow($mng,['email'=>$this->session->userdata('admin_email')],[],"$mdb.tbl_auths");
					$floc = [];
					$tloc = [];
					foreach($user->locations as $key=>$location){
					  if($location->Type == 'from'){
						array_push($floc, $location);
					  }else{
						array_push($tloc, $location);
					  }
					}
					$ldata = $this->admin->getRow($mng,['loccode'=>$id,"status"=>'Active'],[],"$database.tbl_locations");
					$appid=$user->appid;
					$items = $this->admin->getArray($mng,["appId"=>$appid,"status"=>'Active'],[],"$database.tbl_items");
					
					if($show == 'on'){?>
					<div class="card">
						<div class="card-header" onclick="hideThis();" id="addTrans" style="cursor: pointer;">
							<span style="color:#fff;font-weight: bold;">
								<i class="mdi mdi-login-variant"></i> Add Transfer Out
							</span>
						
							<a class="float-right" style="color:#fff;cursor: pointer;font-size: 20px;">
							  <i class="mdi mdi-chevron-down-circle-outline"></i>
							</a>
						
						</div>
						
						<div class="card-header" id="impTransferouts" style="cursor: pointer;display: none">
							<span style="color:#fff;font-weight: bold;">
								<i class="mdi mdi-login-variant"></i> Import Transfer Outs
							</span>
						
							<a class="float-right" style="color:#fff;cursor: pointer;font-size: 20px;">
							  <i class="mdi mdi-chevron-down-circle-outline"></i>
							</a>
						
						</div>
						
						<div class="card-body" id="showImport" style="display:none">
							
							<form id="importTout" method="post" enctype="multipart/form-data">
                                            	
							   <div class="row">

									 <div class="col-md-3"> 

										<div class="form-group">
											<label>Select File</label>
											<input type="file" class="form-control" name="ldata" style="height: 40px"  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
											<small style="color: red; font-size: 14px">Note : Please select <b>.xlsx</b> format</small>
										</div>

									 </div>

									 <div class="col-md-3 m-t-30">

										<input type="hidden" name="user" value="<? echo $user->uname; ?>"/>
										<input type="hidden" name="appId" value="<? echo $user->appid; ?>"/>
										
										<input type="hidden" name="usubmit" value="" class="uploadButton">
										
										<button class="btn btn-primary arrow-none waves-effect waves-light" type="submit" id="iSubmit"><i class="dripicons-upload"></i> Upload</button>

										<button class="btn btn-danger arrow-none waves-effect waves-light cancelForm" type="button" style="display: none"><i class="fa fa-times"></i> Cancel</button>
										
									</div>

									<div class="col-md-3">

									</div> 

									<div class="col-md-3 m-t-30" align="right">

										<a href="<? echo base_url() ?>assets/downloads/Transferout.xlsx" class="btn btn-info"><i class="ion ion-ios-download"></i> Download Template </a>

									</div>

								</div>

								<div class="row">
									<div class="col-md-12">
										<div class="imloader" style="display:none">
											<center><img src="<? echo base_url() ?>assets/images/loader.gif" width="50px" height="50px" ></center>
										</div>
									
									</div>
									
								</div>
								
								<div class="row">
									
									<div class="col-md-6">
										
										<div class="imerror"></div>
										<div id="errorTable"></div>
										
									</div>
									
									<div class="col-md-6">
										
										<div class="iwmerror"></div>
										<div id="warningTable"></div>
										
									</div>
									
								</div>

							</form>
							
							
						</div>
						
												
						
						<div class="card-body" id="hideThis" style="display:none">
						
							<?	
								$cdate=date("Y-m-d", time());	
							?>
							<form id="createTout" autocomplete="off">
								<input type="hidden" name="user" value="<? echo $user->uname; ?>"/>
								<input type="hidden" name="appId" value="<? echo $user->appid; ?>"/>
								<!-- <input type="hidden" name="locationid" value="<? echo $id; ?>"/> -->
								<div class="row">
									<div class="col-md-3">
										Shipper PO <span style="color:red">*</span>
										<input type="text" name="shipperpo" id="shipperpo" maxlength="13" 
       oninvalid="this.setCustomValidity('Character count must be 13 or less, please use the Reference #3 field for the remainder if character count exceeds 13')" class="form-control" required tabindex="1">
									</div>
									<div class="col-md-3">
										ProNum <span style="color:red">*</span>
										<input type="text" name="pronum" id="pronum" class="form-control" required tabindex="2">
									</div>
									<div class="col-md-3">
										Reference #3
										<input type="text" name="reference" id="reference" class="form-control" tabindex="3">
									</div>
									<div class="col-md-3">
										Shipment Date (mm/dd/yyyy) <span style="color:red">*</span>
										<input type="date" name="shippmentdate" min="<? echo date("2015-01-01",time()) ?>" max="<? echo date('Y-m-d',time()) ?>" class="form-control" required tabindex="4">
									</div>
								</div>


								<div class="row" style="margin-top:20px;">
									<div class="col-md-3">
										From Location <span style="color:red">*</span>
										<select name="flcoationcode" class="form-control" required tabindex="6">
<!--											<option value="">Select From Location</option>-->
											<option value="<? echo $id; ?>" selected><? echo $ldata->locname; ?></option>
											
										</select>
									</div>

									<div class="col-md-3">
										To Location <span style="color:red">*</span>
										<select name="tlocationcode" class="form-control" required tabindex="5">
											<option value="">Select To Location</option>
											<? foreach($user->locations as $value){
											   $loccode = $value->loccode;
												$locdata = $this->admin->getRow("",['loccode'=>$loccode],[],"$database.tbl_locations");

												if($value->Type == "to" && $locdata->status == "Active"){	
									
											?>
											<option value="<? echo $value->loccode; ?>">
												<? echo $value->LocationName; ?>
											</option>
											<?}}?>
										</select>
									</div>

									<div class="col-md-3">
										Quantity <span style="color:red">*</span>
										<input type="number" min="1" name="quantity" id="quantity" class="form-control" tabindex="7" onkeyup="checkPositive(this.value)" required>
									</div>
									<div class="col-md-3">
										Item <span style="color:red">*</span>
										<select name="item" class="form-control" required tabindex="8">
<!--											<option value="">Select Item</option>-->
											 <option value="<? echo urldecode($this->uri->segment(6)) ?>" selected><? echo urldecode($this->uri->segment(6)) ?></option> 
											
										</select>
									</div>
								</div>
								<div class="row" style="margin-top:20px;">
									<div class="col-md-7">
										<div class="alert alert-success" style="display:none" id="smsg"></div>
										<div class="alert alert-danger" style="display:none" id="emsg"></div>
									</div>
									<div class="col-md-1">
										<img src="<? echo base_url('assets/images/loader.gif') ?>" width="60" height="60" style="margin-top:-15px;text-align: right;display:none" id="loader">
									</div>
									<div class="col-md-4">
										<input type="submit" name="submit" class="btn btn-primary float-right">
									</div>
								</div>
							</form>
						</div>
					</div>
					<? } ?>

					<div class="card" style="margin-top:20px;">
						<div class="card-header">
							<span style="color:#fff"><i class="mdi mdi-map-marker-multiple"></i> Location Summary</span>
						</div>
						<div class="card-body">
							<?
								$appid = $user->appid;
							
								$locinvdata = $this->admin->getRow("",['appId'=>$appid,"item.item_name"=>$itemname,"loccode"=>$id],[],"$database.tbl_inventory");


								$issues=($this->common->getInventorycount($database,"tbl_issues",$appid,$id,"tlcoationcode",$itemname));
								$returns=($this->common->getInventorycount($database,"tbl_returns",$appid,$id,"tlcoationcode",$itemname));
								$tins=($this->common->getInventorycount($database,"tbl_touts",$appid,$id,"tlocationcode",$itemname));
								$touts=($this->common->getInventorycount($database,"tbl_touts",$appid,$id,"flcoationcode",$itemname));
								$adjusts=($this->common->getInventorycount($database,"tbl_adjustments",$appid,$id,"tlcoationcode",$itemname));

								$eb = ($locinvdata->starting_balance+$issues+$returns+$tins-$touts+$adjusts);
								// echo $this->admin->getCount($mng,"ongweoweh.tbl_touts",["appid"=>$appid,"flcoationcode"=>$id],[]);
							?>
							<div style="padding:0px;width:100%">
								<div class="row" style="margin-top: 20px">
									<div class="col-md-4">
										<table class="table table-bordered">
											<tbody>
												<tr style="background-color:antiquewhite;">
													<td>Location</td>
													<td align="right">
														<? echo $ldata->locname; ?>
													</td>
												</tr>
												<tr>
													<td>Starting Balance</td>
													<td align="right">
														<? echo ($locinvdata->starting_balance != "") ? $locinvdata->starting_balance : 0; ?>
													</td>
												</tr>
												<tr>
													<td>Shipments</td>
													<td align="right">
														<? echo $issues; ?>
													</td>
												</tr>
												<tr>
													<td>Pickups</td>
													<td align="right">
														<? echo $returns; ?>
													</td>
												</tr>
												<tr>
													<td>Transfer Ins</td>
													<td align="right">
														<? echo $tins; ?>
													</td>
												</tr>
												<tr>
													<td>Transfer Outs</td>
													<td align="right">
														<? echo $touts; ?>
													</td>
												</tr>
												<tr>
													<td>Adjustments</td>
													<td align="right">
														<? echo $adjusts; ?>
													</td>
												</tr>
												<tr>
													<td>Ending Bal.</td>
													<td align="right">
														<? echo $eb; ?>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="col-md-4">
										<div id="piechart"></div>
									</div>
									<div class="col-md-4">
										<a href="javascrpt:void(0)" class="btn btn-dark btn-sm float-right hidebarchart" style="margin-top: -30px">
										  <i class="fa fa-arrow-left" style="color: white"></i>
										</a>

										<div id="barChart"></div>
										<div id="mbarChart"></div>
									</div>
								</div>
								<!-- <div class="row">
  <div class="col-md-12">
  <table class="table table-bordered">
  <thead style="background-color:antiquewhite;">
    <tr>
      <th style="padding:5px">Location</th>
      <th style="padding:5px">Issues</th>
      <th style="padding:5px">Returns</th>
      <th style="padding:5px">Transfer Ins</th>
      <th style="padding:5px">Transfer Outs</th>
      <th style="padding:5px">Adjustments</th>
      <th style="padding:5px">Ending Balance</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><? echo $ldata->locname; ?></td>
      <td><? echo $issues; ?></td>
      <td><? echo $returns; ?></td>
      <td><? echo $tins; ?></td>
      <td><? echo $touts; ?></td>
      <td><? echo $adjusts; ?></td>
      <td><? echo $eb; ?></td>
    </tr>
  </tbody>
</table>
  </div>
</div> -->

								<div class="row">
									<div class="col-md-12">
										<ul class="nav nav-tabs" role="tablist">
											<li class="nav-item">
												<a class="nav-link active" data-toggle="tab" href="#tout" role="tab">
                                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                    <span class="d-none d-sm-block">Transfer Outs</span>    
                                                </a>
											
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" href="#tin" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                    <span class="d-none d-sm-block">Transfer Ins</span>    
                                                </a>
											
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" href="#issues" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                                    <span class="d-none d-sm-block">Shipments</span>    
                                                </a>
											
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" href="#returns" role="tab">
                                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                    <span class="d-none d-sm-block">Pickups</span>    
                                                </a>
											
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" href="#adjustments" role="tab">
                                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                    <span class="d-none d-sm-block">Adjustments</span>    
                                                </a>
											
											</li>
										</ul>
										<div class="tab-content">
											<div class="tab-pane active" id="tout" role="tabpanel" style="margin-top:10px;">
											<div class="row">
												<div class="col-md-6">
													<a href="#" style="color:red" onclick="openFilter();">Add Filters</a>
												</div>
												<div class="col-md-6 text-right">
													<a href="#" style="color:green" onclick="window.location.reload();">Reset</a>
												</div>
										    </div>
												<table class="table table-bordered table-striped" id="toutTable">
													<thead style="background-color:antiquewhite;">
														<tr>
															<th style="padding:6px">Sno</th>
															<th style="padding:6px">Shipper PO</th>
															<th style="padding:6px">Shipment Date</th>
															<th style="padding:6px">Pro Num</th>
															<th style="padding:6px">To Location</th>
															<th style="padding:6px">Quantity</th>
															<th style="padding:6px">Report Date</th>
														</tr>
													</thead>
													<tbody></tbody>
												</table>

												<!-- <div class="row">
  <div class="col-md-12">
<a class="btn btn-primary" href="#" onclick="showCgraph('toutbar');">SHOW CONSOLIDATED GRAPH</a>
    <div style="width: 100%;overflow-x: auto;" id="toutbar">
      <div id="barchart_material" style="width: 100%; height: 500px;max-width: 6000px;display:none"></div>  
    </div>
  </div>
</div> -->
											</div>
											<div class="tab-pane" id="tin" role="tabpanel" style="margin-top:10px;">
                                               <div class="row">
												<div class="col-md-6">
													<a href="#" style="color:red" onclick="openFilter_Transfersins();">Add Filters</a>
												</div>
												<div class="col-md-6 text-right">
													<a href="#" style="color:green" onclick="window.location.reload();">Reset</a>
												</div>
										    </div>
												<table class="table table-bordered table-striped" id="tinTable" style="width: 100%">
													<thead style="background-color:antiquewhite;">
														<tr>
															<th style="padding:6px">Sno</th>
															<th style="padding:6px">Shipper PO</th>
															<th style="padding:6px">Shipment Date</th>
															<th style="padding:6px">Pro Num</th>
															<th style="padding:6px">From Location</th>
															<th style="padding:6px">Quantity</th>
															<th style="padding:6px">Report Date</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
												</table>

											</div>
											<div class="tab-pane" id="issues" role="tabpanel" style="margin-top:10px;">
                                                <div class="row">
												<div class="col-md-6">
													<a href="#" style="color:red" onclick="openFilter_shipments();">Add Filters</a>
												</div>
												<div class="col-md-6 text-right">
													<a href="#" style="color:green" onclick="window.location.reload();">Reset</a>
												</div>
										    </div>
												<table class="table table-bordered table-striped" id="issuesTable" style="width: 100%">
													<thead style="background-color:antiquewhite;">
														<tr>
															<th style="padding:6px">Sno</th>
															<th style="padding:6px">Customer Reference</th>
															<th style="padding:6px">Ongweoweh Reference</th>
															<th style="padding:6px">Shipment Date</th>
															<th style="padding:6px">Location</th>
															<th style="padding:6px">Quantity</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
												</table>

											</div>
											<div class="tab-pane" id="returns" role="tabpanel" style="margin-top:10px;">
                                                <div class="row">
												<div class="col-md-6">
													<a href="#" style="color:red" onclick="openFilter_pickup();">Add Filters</a>
												</div>
												<div class="col-md-6 text-right">
													<a href="#" style="color:green" onclick="window.location.reload();">Reset</a>
												</div>
										    </div>
												<table class="table table-bordered table-striped" id="returnsTable" style="width: 100%">
													<thead style="background-color:antiquewhite;">
														<tr>
															<th style="padding:6px">Sno</th>
															<th style="padding:6px">Customer Reference</th>
															<th style="padding:6px">Ongweoweh Reference</th>
															<th style="padding:6px">Shipment Date</th>
															<th style="padding:6px">Location</th>
															<th style="padding:6px">Quantity</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
												</table>

											</div>
											<div class="tab-pane" id="adjustments" role="tabpanel" style="margin-top:10px;">
                                                <div class="row">
												<div class="col-md-6">
													<a href="#" style="color:red" onclick="openFilter_adjus();">Add Filters</a>
												</div>
												<div class="col-md-6 text-right">
													<a href="#" style="color:green" onclick="window.location.reload();">Reset</a>
												</div>
										    </div>
												<table class="table table-bordered table-striped" id="adjustmentsTable" style="width: 100%">
													<thead style="background-color:antiquewhite;">
														<tr>
															<th style="padding:6px">Sno</th>
															<th style="padding:6px">Customer Reference</th>
															<th style="padding:6px">Ongweoweh Reference</th>
															<th style="padding:6px">Shipment Date</th>
															<th style="padding:6px">Location</th>
															<th style="padding:6px">Quantity</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
												</table>

											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>


				</div>
			</div>
        
		  <div id="myFilter" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-lg" style="width: 60%">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header" style="display: block">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Add Filters</h4>
		  </div>
		  <div class="modal-body">

				<form class="submitFilter">
				<!-- <form action="<? echo base_url('admin/apps/addFilter') ?>" method="post">
				<input type="hidden" name="id" value="<? echo $appid; ?>">
				<input type="hidden" name="table" value="tbl_touts"> -->
				<div id="top">
				<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first0">

				<div class="row">
					<div class="col-md-1">
						<select name="cause[]" id="cause" class="form-control causeData" style="display: none"><option value="where">where</option></select>
						<p style="margin-top: 5px;font-weight: bold">Where</p>
					</div>
					<div class="col-md-3">
						<!-- <select name="field[]" id="field" class="form-control fieldData">
							<option value="item_name">Item Name</option>
							<option value="item_code">Item Code</option>
						</select> -->
						<select class="form-control fieldData loc_filter" name="field[]" id="updLoc1" lopid="updLoc1" lo_id="locgetwhenRef">
							<option value="">Select</option>
							<option value="shipperpo">Shipper PO</option>		
							<option value="shippmentdate">Shipment Date</option>
							<option value="pronum">Pro Number</option>
							<option value="tlcoation">To Location</option>
							<option value="quantity">Quantity</option>
							<option value="reportdate">Report Date</option>
						</select>
						
						
						
					</div>
					<div class="col-md-3 dynlocgetwhenRef">
						<select name="value[]" id="value" class="form-control valueData_filter">
							<option value="">Select</option>
							<option value="contains">contains</option>
							<option value="does not contain">does not contain</option>
							<option value="is">is</option>
							<option value="is not">is not</option>
							<option value="starts with">starts with</option>
							<option value="ends with">ends with</option>
							<option value="is blank">is blank</option>
							<option value="is not blank">is not blank</option>
						</select>
					</div>
					<div class="col-md-4 locgetwhenRef updLoc1">
						<div id="setDvalue"><input type="text" name="svalue[]" id="svalue"  class="form-control svalueData"></div>
					</div>
					<input type="hidden" value="transfer_outs" id="filter_type" class="filter_types">
					<div class="col-md-1">
						<p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter('first0');"><i class="fa fa-trash"></i></p>
					</div>
				</div>

				</div>
				</div>
				<hr/>
				<a href="#" onclick="addFilter();">Add Filter</a>
				<hr/>
				<center>
					<input type="submit" name="submit" class="btn btn-primary" value="Submit">
				</center>
				</form>
		  </div>
		</div>

	  </div>
	</div>

    <div id="myFilter_Transfersins" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-lg" style="width: 60%">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header" style="display: block">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Add Filters</h4>
		  </div>
		  <div class="modal-body">

				<form class="submitFilter_transfersins">
				<!-- <form action="<? echo base_url('admin/apps/addFilter') ?>" method="post">
				<input type="hidden" name="id" value="<? echo $appid; ?>">
				<input type="hidden" name="table" value="tbl_touts"> -->
				<div id="top_transferins">
				<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first0">

				<div class="row">
					<div class="col-md-1">
						<select name="cause[]" id="cause" class="form-control causeDataouts" style="display: none"><option value="where">where</option></select>
						<p style="margin-top: 5px;font-weight: bold">Where</p>
					</div>
					<div class="col-md-3">
						<!-- <select name="field[]" id="field" class="form-control fieldData">
							<option value="item_name">Item Name</option>
							<option value="item_code">Item Code</option>
						</select> -->
						<select class="form-control fieldDataouts loc_filter_forms" name="field[]" id="updLoc1_ins" lopidouts="updLoc1_ins" lo_id="locgetwhenRef_ins">
							<option value="">Select</option>
							<option value="shipperpo">Shipper PO</option>		
							<option value="shippmentdate">Shipment Date</option>
							<option value="pronum">Pro Number</option>
							<option value="flocation">From Location</option>
							<option value="quantity">Quantity</option>
							<option value="reportdate">Report Date</option>
						</select>
						
						
						
					</div>
					<div class="col-md-3 dynlocgetwhenRef_ins">
						<select name="value[]" id="value" class="form-control valueDataouts valueData_filter">
							<option value="">Select</option>
							<option value="contains">contains</option>
							<option value="does not contain">does not contain</option>
							<option value="is">is</option>
							<option value="is not">is not</option>
							<option value="starts with">starts with</option>
							<option value="ends with">ends with</option>
							<option value="is blank">is blank</option>
							<option value="is not blank">is not blank</option>
						</select>
					</div>
					<div class="col-md-4 locgetwhenRef_ins updLoc1_ins">
						<div id="setDvalue"><input type="text" name="svalue[]" id="svalue"  class="form-control svalueDataouts"></div>
					</div>
					<input type="hidden" value="transfer_ins" id="filter_type" class="filter_types">
					<div class="col-md-1">
						<p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter_transferins('first0');"><i class="fa fa-trash"></i></p>
					</div>
				</div>

				</div>
				</div>
				<hr/>
				<a href="#" onclick="addFilter_transferins();">Add Filter</a>
				<hr/>
				<center>
					<input type="submit" name="submit" class="btn btn-primary" value="Submit">
				</center>
				</form>
		  </div>
		</div>

	  </div>
	</div>
	
	
	<div id="myFilter_shipments" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-lg" style="width: 60%">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header" style="display: block">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Add Filters</h4>
		  </div>
		  <div class="modal-body">

				<form class="submitFilter_shipments">
				<!-- <form action="<? echo base_url('admin/apps/addFilter') ?>" method="post">
				<input type="hidden" name="id" value="<? echo $appid; ?>">
				<input type="hidden" name="table" value="tbl_touts"> -->
				<div id="top_shipments">
				<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first0">

				<div class="row">
					<div class="col-md-1">
						<select name="cause[]" id="cause" class="form-control causeDataship" style="display: none"><option value="where">where</option></select>
						<p style="margin-top: 5px;font-weight: bold">Where</p>
					</div>
					<div class="col-md-3">
						<!-- <select name="field[]" id="field" class="form-control fieldData">
							<option value="item_name">Item Name</option>
							<option value="item_code">Item Code</option>
						</select> -->
						<select class="form-control fieldDataship loc_filter_ship" name="field[]" id="updLoc1_ship" lopidship="updLoc1_ship" lo_id="locgetwhenRef_ship">
							<option value="">Select</option>		
							<option value="chepreference">Vendor Reference</option>
							<option value="ongreference">Ongweoweh Reference</option>
							<option value="shippmentdate">Shipment Date</option>
							<option value="quantity">Quantity</option>
							<option value="tlocation">Location</option>
						</select>
						
						
						
					</div>
					<div class="col-md-3 dynlocgetwhenRef_ship">
						<select name="value[]" id="value" class="form-control valueDataship valueData_filter">
							<option value="">Select</option>
							<option value="contains">contains</option>
							<option value="does not contain">does not contain</option>
							<option value="is">is</option>
							<option value="is not">is not</option>
							<option value="starts with">starts with</option>
							<option value="ends with">ends with</option>
							<option value="is blank">is blank</option>
							<option value="is not blank">is not blank</option>
						</select>
					</div>
					<div class="col-md-4 locgetwhenRef_ship updLoc1_ship">
						<div id="setDvalue"><input type="text" name="svalue[]" id="svalue"  class="form-control svalueDataship"></div>
					</div>
					<input type="hidden" value="ship" id="filter_type" class="filter_types">
					<div class="col-md-1">
						<p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter_shipments('first0');"><i class="fa fa-trash"></i></p>
					</div>
				</div>

				</div>
				</div>
				<hr/>
				<a href="#" onclick="addFilter_shipments();">Add Filter</a>
				<hr/>
				<center>
					<input type="submit" name="submit" class="btn btn-primary" value="Submit">
				</center>
				</form>
		  </div>
		</div>

	  </div>
	</div>
	
     <div id="myFilter_pickup" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-lg" style="width: 60%">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header" style="display: block">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Add Filters</h4>
		  </div>
		  <div class="modal-body">

				<form class="submitFilter_pickup">
				<!-- <form action="<? echo base_url('admin/apps/addFilter') ?>" method="post">
				<input type="hidden" name="id" value="<? echo $appid; ?>"> -->
				<div id="top_pickup">
				<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first0">

				<div class="row">
					<div class="col-md-1">
						<select name="cause[]" id="cause" class="form-control causeDatapickup" style="display: none"><option value="where">where</option></select>
						<p style="margin-top: 5px;font-weight: bold">Where</p>
					</div>
					<div class="col-md-3">
						<select name="field[]" class="form-control fieldDatapickup getLocdata loc_filter_pickup" id="updLoc1_pickup" lopidpickup="updLoc1_pickup" lo_id="locgetwhenRef_pickup">
							<option value="">Select</option>
							<option value="chepreference">Vendor Reference</option>
							<option value="ongreference">Ongweoweh Reference</option>
							<option value="shippmentdate">Shipment Date</option>
							<option value="quantity">Quantity</option>
							<option value="tlocation">Location</option>
						</select>
					</div>
					<div class="col-md-3 dynlocgetwhenRef_pickup">
						<select name="value[]" id="value" class="form-control valueDatapickup">
							<option value="">Select</option>
							<option value="contains">contains</option>
							<option value="does not contain">does not contain</option>
							<option value="is">is</option>
							<option value="is not">is not</option>
							<option value="starts with">starts with</option>
							<option value="ends with">ends with</option>
							<option value="is blank">is blank</option>
							<option value="is not blank">is not blank</option>
						</select>
					</div>
					<div class="col-md-4 locgetwhenRef_pickup updLoc1_pickup">
						<input type="text" name="svalue[]" id="svalue" class="form-control svalueDatapickup">
					</div>
					<input type="hidden" value="pickup" id="filter_type" class="filter_types">
					<div class="col-md-1">
						<p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter_pickup('first0');"><i class="fa fa-trash"></i></p>
					</div>
				</div>

				</div>
				</div>
				<hr/>
				<a href="#" onclick="addFilter_pickup();">Add Filter</a>
				<hr/>
				<center>
					<input type="submit" name="submit" class="btn btn-primary" value="Submit">
				</center>
				</form>
						  </div>
						</div>

					  </div>
					</div>
					
	<div id="myFilter_adjus" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-lg" style="width: 60%">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header" style="display: block">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Add Filters</h4>
		  </div>
		  <div class="modal-body">

			<form class="submitFilter_adjus">
			<!-- <form action="<? echo base_url('admin/apps/addFilter') ?>" method="post">
			<input type="hidden" name="id" value="<? echo $appid; ?>"> -->
			<div id="top_adjus">
			<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first0">

			<div class="row">
				<div class="col-md-1">
					<select name="cause[]" id="cause" class="form-control causeDataadjus" style="display: none"><option value="where">where</option></select>
					<p style="margin-top: 5px;font-weight: bold">Where</p>
				</div>
				<div class="col-md-3">
					<select name="field[]" class="form-control fieldDataadjus getLocdata loc_filter_adjus" id="updLoc1_adjus" lopidadjus="updLoc1_adjus" lo_id="locgetwhenRef_adjus">
						<option value="">Select</option>
						<option value="chepreference">Vendor Reference</option>
						<option value="ongreference">Ongweoweh Reference</option>
						<option value="shippmentdate">Shipment Date</option>
						<option value="quantity">Quantity</option>
						<option value="tlocation">Location</option>
					</select>
				</div>
				<div class="col-md-3 dynlocgetwhenRef_adjus">
					<select name="value[]" id="value" class="form-control valueDataadjus">
						<option value="">Select</option>
						<option value="contains">contains</option>
						<option value="does not contain">does not contain</option>
						<option value="is">is</option>
						<option value="is not">is not</option>
						<option value="starts with">starts with</option>
						<option value="ends with">ends with</option>
						<option value="is blank">is blank</option>
						<option value="is not blank">is not blank</option>
					</select>
				</div>
				<div class="col-md-4 locgetwhenRef_adjus updLoc1_adjus">
					<input type="text" name="svalue[]" id="svalue" class="form-control svalueDataadjus">
				</div>
				<div class="col-md-1">
					<p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter_adjus('first0');"><i class="fa fa-trash"></i></p>
				</div>
			</div>

			</div>
			</div>
			<hr/>
			<a href="#" onclick="addFilter_adjus();">Add Filter</a>
			<hr/>
			<center>
				<input type="submit" name="submit" class="btn btn-primary" value="Submit">
			</center>
			</form>
					  </div>
					</div>

				  </div>
				</div>
					
		</div>
		<!-- container-fluid -->

	</div>
	<!-- content -->



	<? admin_footer(); ?>
<input type="hidden" name="base_url" id="base_url" value="<? echo base_url() ?>">
<input type="hidden" name="item_name" id="item_name" value="<? echo $itemname ?>">
<input type="hidden" name="loc_id" id="loc_id" value="<? echo $id ?>">
<input type="hidden" name="app_id" id="app_id" value="<? echo $appid; ?>">
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script src="<? echo base_url(); ?>assets/plugins/select2/js/select2.min.js"></script>
	<script type="text/javascript">
		function openFilter(){
		$("#myFilter").modal('show');
	    }
		function openFilter_Transfersins(){
		$("#myFilter_Transfersins").modal('show');
	    }
		function openFilter_shipments(){
		$("#myFilter_shipments").modal('show');
	    }
		function openFilter_pickup(){
		$("#myFilter_pickup").modal('show');
	    }
		function openFilter_adjus(){
		$("#myFilter_adjus").modal('show');
	    }
		$("#importTout").on("submit",function(e){
			e.preventDefault();
			var form_data = new FormData($(this)[0]);
			$.ajax({

				type : "POST",
				url : "<? echo base_url() ?>main/inventory/importTouts",
				data: form_data,
				cache: false,
				contentType: false,
				enctype: 'multipart/form-data',
				processData: false,
				dataType : "json",
				beforeSend : function(){

					$('.imloader').show();
					 $("#iSubmit").hide();

				},
				success : function(data){

					$('.imloader').hide();
					
					$(".uploadButton").val("");
					$(".cancelForm").hide();
					$("#iSubmit").show();

					if(data.Status == "success"){

						$('.imerror').html('<div class="alert alert-success">Successfully Transfer Outs Imported</div>');
						setTimeout(function(){
							location.reload()
						},2000);

					}else if(data.Status == 'Dups'){
						
						if(data.Message.length <= 10){
						
							$("#errorTable").hide();

							var append='';
							data.Message.forEach(function(item, index){
								append+=item.Msg+"<br>";
							})
							if(append != ''){
								$(".imerror").show();
							$(".imerror").html('<div class="alert alert-danger">'+append+'</div>');
							}
							

						}else{

							$("#errorTable").show();
							$(".imerror").hide();

							var append='<table class="table errorData table-striped" width="100%"><thead class="thead-light"><tr><th>Error</th><th>Value</th></tr></thead><tbody>';
							data.Message.forEach(function(item, index){

								append+='<tr><td>'+item.Msg+'</td><td>'+item.Error+'</td></tr>';

							})

							append += '</tbody></table>';
							$("#errorTable").html(append);
							$(".errorData").dataTable();
						}
						
						if(data.WarMsg.length <= 10){
							
							if(data.Message.length == 0){
							
								$(".uploadButton").val("Upload");
							
							}
							
							$("#warningTable").hide();
							$(".cancelForm").show();

							var append='';
							data.WarMsg.forEach(function(item, index){
								append+=item.Msg+"<br>";
							})
							$(".iwmerror").show();
							$(".iwmerror").html('<div class="alert alert-warning">'+append+'</div>');

						}else{
							
							if(data.Message.length == 0){
							
								$(".uploadButton").val("Upload");
							
							}
							
							$(".cancelForm").show();
							$("#warningTable").show();
							$(".iwmerror").hide();

							var append='<table class="table warData table-striped" width="100%"><thead class="thead-light"><tr><th>Warning</th><th>Value</th></tr></thead><tbody>';
							data.WarMsg.forEach(function(item, index){

								append+='<tr><td>'+item.Msg+'</td><td>'+item.Error+'</td></tr>';

							})

							append += '</tbody></table>';
							$("#warningTable").html(append);
							$(".warData").dataTable();
						}
						
					}else{

					}
	
				},
				error : function(jq,txt,error){

					$('.imloader').hide();
					$("#iSubmit").show();
	

				}

			});

		});
		
		$(".cancelForm").click(function(){
			
			$("#importTout")[0].reset();
			$(".uploadButton").val("");
			$(".cancelForm").hide();
			$("#warningTable").hide();
			$("#errorTable").hide();
			$(".imerror").hide();
			
		})

		$("#importTransfer").click(function(){
			
			$("#showImport").slideToggle();
			$("#hideThis").hide();
			$("#addTrans").hide();
			$("#impTransferouts").show();
			$("#adddTouut").show();
			$("#importTransfer").hide();
			
		});
		
		google.charts.load( 'current', {
			'packages': [ 'bar', 'corechart' ]
		} );
		// google.charts.setOnLoadCallback(drawChart);
		google.charts.setOnLoadCallback( drawPieChart );
		google.charts.setOnLoadCallback( drawBarChart );

		/*
		function drawChart() {

			$.ajax( {
				url: "<? //echo base_url();?>/main/Locations/getGraphData",
				dataType: 'json',
				data: {
					'loccode': '<? //echo $id; ?>'
				},
				type: 'post',
				success: function ( jsonData ) {
					console.log( jsonData );
					var out = [];
					out.push( [ 'Location', 'Quantity', {
						role: 'style'
					} ] );
					jsonData.forEach( function ( item, index ) {
						out.push( [ item.Location, item.Quantity, '#b87333' ] );
					} );
					var data = google.visualization.arrayToDataTable( out );

					var options = {
						bar: {
							groupWidth: "20%"
						},
						chart: {
							title: 'Transfer Outs',
							subtitle: '<? //echo $ldata->locname; ?>',
						},
						bars: 'horizontal',
						hAxis: {
							slantedText: true,
						},
						colors: [ '#CBD570', '#FCC100' ]
					};

					var chart = new google.charts.Bar( document.getElementById( 'barchart_material' ) );

					chart.draw( data, google.charts.Bar.convertOptions( options ) );
				}

			} );
		}
		*/		
		
		function drawPieChart() {
			var data = google.visualization.arrayToDataTable( [
				[ 'Task', 'Count' ],
//				[ 'Starting Balance', <? //echo ($locinvdata->starting_balance != "") ? $locinvdata->starting_balance : 0;?> ],
				[ 'Shipments', <? echo $issues; ?> ],
				[ 'Pickups', <? echo $returns; ?> ],
				[ 'Transfer Ins', <? echo $tins; ?> ],
				[ 'Transfer Outs', <? echo $touts; ?> ],
//				[ 'Adjustments', <? //echo $adjusts; ?> ]
			] );

			// Optional; add a title and set the width and height of the chart
			var options = {
				title: "Pie Chart for <? echo $ldata->locname; ?>",
				'width': '100%',
				'height': '300',
				'chartArea': {
					'width': '100%',
					'height': '80%',
					'left' : '5%'
				}
			};

			// Display the chart inside the <div> element with id="piechart"
			var chart = new google.visualization.PieChart( document.getElementById( 'piechart' ) );
			chart.draw( data, options );
		}
		
		$(".hidebarchart").click(function(){
			
			
			$(".hidebarchart").hide();
			$("#barChart").show();
			$("#mbarChart").hide();
			
		})
		
		function getbarchartbymonth(month){
			
			$("#barChart").hide();
			$("#mbarChart").show();
			
			$(".hidebarchart").show();
			
			$.ajax({
				
				type : "post",
				url : "<? echo base_url('main/inventory/getToutsbarchartbymonth') ?>",
				data : {loccode:"<? echo urldecode($this->uri->segment(4)) ?>",item:"<? echo urldecode($this->uri->segment(6)) ?>",appid:"<? echo $appid ?>",month:month},
				success : function(mdata){
					
					console.log(mdata);
					
					var fdata = JSON.parse(mdata);
					
					var data = google.visualization.arrayToDataTable( fdata );

					var view = new google.visualization.DataView( data );
					view.setColumns( [ 0, 1, {
							calc: "stringify",
							sourceColumn: 1,
							type: "string",
							role: "annotation"
						},
						2
					] );

					var options = {
						title: "Bar Chart for Transfer Outs (<? echo $ldata->locname; ?>)",
						width: '100%',
						height: 300,
						bar: {
							groupWidth: "95%"
						},
						legend: "none",

					};
					var chart = new google.visualization.BarChart( document.getElementById( "mbarChart" ) );
					chart.draw( view, options );

					
				},
				error : function(data){
					
					console.log(data);
					
				}
				
			});
			
		}

		function drawBarChart() {
			
			$(".hidebarchart").hide();
			
			$.ajax({
				
				type : "post",
				url : "<? echo base_url('main/inventory/getToutsgraphdata') ?>",
				data : {loccode:"<? echo urldecode($this->uri->segment(4)) ?>",item:"<? echo urldecode($this->uri->segment(6)) ?>",appid:"<? echo $appid ?>"},
				success : function(mdata){
					
					var fdata = JSON.parse(mdata);
					
					var data = google.visualization.arrayToDataTable( [
					[ "Task", "count", {
						role: "style"
					} ],fdata[0],fdata[1],fdata[2],fdata[3],fdata[4],fdata[5],fdata[6],fdata[7],fdata[8],fdata[9],fdata[10],fdata[11]

					
					] );

					var view = new google.visualization.DataView( data );
					view.setColumns( [ 0, 1, {
							calc: "stringify",
							sourceColumn: 1,
							type: "string",
							role: "annotation"
						},
						2
					] );

					var options = {
						title: "Bar Chart for Transfer Outs (<? echo $ldata->locname; ?>)",
						width: '100%',
						height: 300,
						bar: {
							groupWidth: "95%"
						},
						legend: "none",

					};
					var chart = new google.visualization.BarChart( document.getElementById( "barChart" ) );
					google.visualization.events.addListener(chart, 'click', function(e) {
					var selection;
					if (e.targetID) {
						  selection = e.targetID.split('#');
						  if (selection[0].indexOf('vAxis') > -1) {
							console.log('label clicked = ' + data.getValue(parseInt(selection[selection.length - 1]), parseInt(selection[1])));
							  
							  getbarchartbymonth(data.getValue(parseInt(selection[selection.length - 1]), parseInt(selection[1])))
						  }
						}
					  });
					chart.draw( view, options );

					
				},
				error : function(data){
					
					
					
				}
				
			});
			
		}
		
		
	</script>

<? $itemid = $this->uri->segment(6); ?>

	<script>
		
		$(document).ready(function(){
			
			$('#toutTable').DataTable({
			  'processing': true,
			  'serverSide': true,
			  'serverMethod': 'post',
			  "dom": 'Bfrtip',	
			  "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			  buttons: ['csv', 'excel', 'pageLength'],	
			  'ajax': {
				  'url':'<? echo base_url('main/Locations/getDynamicData/').$id.'/'.$appid.'/'.$itemid.'/transferout'; ?>',
//				  'success' : function(data){
//
//					  console.log(data);
//
//				  },
//				  'error' : function(data){
//
//					  console.log(data);
//
//				  }
			  },
			  'columns': [ {
					data: 'Sno',
					defaultContent: ""
				},{
					data: 'shipperpo',
					defaultContent: ""
				}, {
					data: 'shippmentdate',
					defaultContent: ""
				}, {
					data: 'pronum',
					defaultContent: ""
				}, {
					data: 'tlcoation',
					defaultContent: ""
				}, {
					data: 'quantity',
					defaultContent: ""
				}, {
					data: 'reportdate',
					defaultContent: ""
				} ],
		    });

			$( "#tinTable" ).DataTable( {
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				"dom": 'Bfrtip',	
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				 buttons: ['csv', 'excel', 'pageLength'],
				'ajax': {
				  'url':'<? echo base_url('main/Locations/getDynamicData/').$id.'/'.$appid.'/'.$itemid.'/transferin'; ?>',
//				  'success' : function(data){
//
//					  console.log(data);
//
//				  },
//				  'error' : function(data){
//
//					  console.log(data);
//
//				  }
			    },
				"columns": [{
					data: 'Sno',
					defaultContent: ""
				}, {
					data: 'shipperpo',
					defaultContent: ""
				}, {
					data: 'shippmentdate',
					defaultContent: ""
				}, {
					data: 'pronum',
					defaultContent: ""
				}, {
					data: 'flocation',
					defaultContent: ""
				}, {
					data: 'quantity',
					defaultContent: ""
				}, {
					data: 'reportdate',
					defaultContent: ""
				} ],
			} );

			$( "#issuesTable" ).DataTable( {
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				"dom": 'Bfrtip',	
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				 buttons: ['csv', 'excel', 'pageLength'],
				'ajax': {
				  'url':'<? echo base_url('main/Locations/getDynamicData/').$id.'/'.$appid.'/'.$itemid.'/issues'; ?>',
//				  'success' : function(data){
//
//					  console.log(data);
//
//				  },
//				  'error' : function(data){
//
//					  console.log(data);
//
//				  }
			    },
				"columns": [{
					data: 'Sno',
					defaultContent: ""
				}, {
					data: 'chepreference',
					defaultContent: ""
				}, {
					data: 'ongreference',
					defaultContent: ""
				}, {
					data: 'shippmentdate',
					defaultContent: ""
				}, {
					data: 'tlocation',
					defaultContent: ""
				}, {
					data: 'quantity',
					defaultContent: ""
				} ],
			});

			$( "#returnsTable" ).DataTable( {
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				"dom": 'Bfrtip',	
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				 buttons: ['csv', 'excel', 'pageLength'],
				'ajax': {
				  'url':'<? echo base_url('main/Locations/getDynamicData/').$id.'/'.$appid.'/'.$itemid.'/returns'; ?>',
//				  'success' : function(data){
//
//					  console.log(data);
//
//				  },
//				  'error' : function(data){
//
//					  console.log(data);
//
//				  }
			    },
				"columns": [{
					data: 'Sno',
					defaultContent: ""
				}, {
					data: 'chepreference',
					defaultContent: ""
				}, {
					data: 'ongreference',
					defaultContent: ""
				}, {
					data: 'shippmentdate',
					defaultContent: ""
				}, {
					data: 'tlocation',
					defaultContent: ""
				}, {
					data: 'quantity',
					defaultContent: ""
				} ],

			} );

			$( "#adjustmentsTable" ).DataTable( {
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				"dom": 'Bfrtip',	
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				 buttons: ['csv', 'excel', 'pageLength'],
				'ajax': {
				  'url':'<? echo base_url('main/Locations/getDynamicData/').$id.'/'.$appid.'/'.$itemid.'/adjustments'; ?>',
//				  'success' : function(data){
//
//					  console.log(data);
//
//				  },
//				  'error' : function(data){
//
//					  console.log(data);
//
//				  }
			    },
				"columns": [ {
					data: 'Sno',
					defaultContent: ""
				},{
					data: 'chepreference',
					defaultContent: ""
				}, {
					data: 'ongreference',
					defaultContent: ""
				}, {
					data: 'shippmentdate',
					defaultContent: ""
				}, {
					data: 'tlocation',
					defaultContent: ""
				}, {
					data: 'quantity',
					defaultContent: ""
				} ],
			} );
			
			$( "#createTout" ).on( 'submit', function ( e ) {
				e.preventDefault();
				var fdata = $( "#createTout" ).serialize();
				$.ajax( {
					url: "<? echo base_url() ?>main/inventory/saveshipment",
					data: fdata,
					type: "post",
					dataType: 'json',
					beforeSend: function () {
						$( "#loader" ).show();
					},
					success: function ( data ) {
						$( "#loader" ).hide();
						$( "#emsg" ).hide();
						$( "#smsg" ).hide();
						console.log( data );
						// return false;
						if ( data.Status == 'Success' ) {
							$( "#smsg" ).show();
							$( "#smsg" ).html( data.Message+'<br/>'+data.Warning );
							setTimeout( function () {
								location.reload();
							}, 3000 );
						} else {
							$( "#emsg" ).show();
							$( "#emsg" ).html( data.Message );
						}
					},
					error: function ( jq, txt, error ) {
						$( "#emsg" ).show();
						$( "#emsg" ).html( error );
						console.log( jq );
					}
				} );
			} );
			
		});

		function archiveFunction( id ) {
			Swal( {
				title: 'Are you sure?',
				text: 'You will not be able to recover this selected location!',
				type: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes, delete it!',
				cancelButtonText: 'No, keep it'
			} ).then( ( result ) => {
				if ( result.value ) {

					Swal(
						'Deleted!',
						'Your Selected Location has been deleted.',
						'success'
					)
					$.ajax( {
						method: 'POST',
						data: {
							'id': id
						},
						url: '<?php echo base_url() ?>admin/locations/delLocation/' + id,
						success: function ( data ) {
							location.reload();
						}
					} );

				} else if ( result.dismiss === Swal.DismissReason.cancel ) {
					Swal(
						'Cancelled',
						'Your Selected Location is safe :)',
						'success',

					)
				}
			} )
		}

		function hideThis() {
			$( "#hideThis" ).slideToggle();
			$("#showImport").hide();
			$("#addTrans").show();
			$("#impTransferouts").hide();
			$("#adddTouut").hide();
			$("#importTransfer").show();
			
		}

		function showCgraph( show ) {
			$( "#barchart_material" ).show();
		}

		function checkPositive( val ) {
			if ( val != '' ) {
				if ( val > 0 ) {} else {
					$( "#emsg" ).show();
					$( "#emsg" ).html( "Quantity must be greater than 0" );
					$( "#quantity" ).val( '' );
					setTimeout( function () {
						$( "#emsg" ).hide();
					}, 2000 );
				}
			}
		}
		$(document).on("change",".loc_filter_forms",function(){		
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("lo_id");
			
			var lopid = $(this).attr("lopidouts");
			var col_val = "";
			$("."+ref).show();
			

			if(column == "shipperpo"){
				col_val = "shipperpo-text";
			}
			if(column == "shippmentdate"){
				col_val = "shippmentdate-date";
			}
			if(column == "pronum"){
				col_val = "pronum-text";
			}
			if(column == "tlcoation"){
				col_val = "tlcoation-select";
			}
			if(column == "flocation"){
				col_val = "flocation-select";
			}
			if(column == "quantity"){
				col_val = "quantity-number";
			}
			if(column == "reportdate"){
				col_val = "reportdate-date";
			}
			
			
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions_dyn",
				dataType : 'json',
				data : {column : col_val,form_type : "outs",table:"tbl_touts","onchangeColref":"updateonchangeConditionLocation_transferins",uopid:lopid},
				success : function(data){
					console.log(data);
					
					$(".dyn"+ref).html(data.operators);
					
					if(data.fields.locnames != null){
						
						$("."+ref).html(data.fields.locnames);
						$(".select2").select2();
						
					}else if(data.fields.status != null){
						
						$("."+ref).html(data.fields.status);
						
					}else if(data.fields.location_type != null){
						
						$("."+ref).html(data.fields.location_type);
						
					}else if(data.fields.import_date != null){
						
						$("."+ref).html(data.fields.import_date);
						
					}else if(data.fields.accounts != null){
						
						$("."+ref).html(data.fields.accounts);
						$(".select2").select2();
						
					}else{
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
					console.log(data.operators);
					
				},
				error : function(data){
					
					console.log(data);
					
				}
				
			});
			
		});
		$(document).on("change",".loc_filter_forms_dyn",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("lo_id");
			var lopid = $(this).attr("lopidouts");
			var col_val = "";
			$("."+ref).show();
			
			if(column == "shipperpo"){
				col_val = "shipperpo-text";
			}
			if(column == "shippmentdate"){
				col_val = "shippmentdate-date";
			}
			if(column == "pronum"){
				col_val = "pronum-text";
			}
			if(column == "tlcoation"){
				col_val = "tlcoation-select";
			}
			if(column == "flocation"){
				col_val = "flocation-select";
			}
			if(column == "quantity"){
				col_val = "quantity-number";
			}
			if(column == "reportdate"){
				col_val = "reportdate-date";
			}
			
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions_dyn",
				dataType : 'json',
				data : {column : col_val,form_type : "outs",table:"tbl_touts","onchangeColref":"updateonchangeConditionLocation_transferins",uopid:lopid},
				success : function(data){
					console.log(data);
					$(".dyn"+ref).html(data.operators);
					
					if(data.fields.locnames != null){
						
						$("."+ref).html(data.fields.locnames);
						$(".select2").select2();
						
					}else if(data.fields.status != null){
						
						$("."+ref).html(data.fields.status);
						
					}else if(data.fields.location_type != null){
						
						$("."+ref).html(data.fields.location_type);
						
					}else if(data.fields.import_date != null){
						
						$("."+ref).html(data.fields.import_date);
						
					}else if(data.fields.accounts != null){
						
						$("."+ref).html(data.fields.accounts);
						$(".select2").select2();
						
					}else{
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
		});
		$(document).on("change",".loc_filter",function(){
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("lo_id");
			var lopid = $(this).attr("lopid");
			var col_val = "";
			$("."+ref).show();
			

			if(column == "shipperpo"){
				col_val = "shipperpo-text";
			}
			if(column == "shippmentdate"){
				col_val = "shippmentdate-date";
			}
			if(column == "pronum"){
				col_val = "pronum-text";
			}
			if(column == "tlcoation"){
				col_val = "tlcoation-select";
			}
			if(column == "flocation"){
				col_val = "flocation-select";
			}
			if(column == "quantity"){
				col_val = "quantity-number";
			}
			if(column == "reportdate"){
				col_val = "reportdate-date";
			}
			
			
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions",
				dataType : 'json',
				data : {column : col_val,table:"tbl_touts","onchangeColref":"updateonchangeConditionLocation",uopid:lopid},
				success : function(data){
					console.log(data);
					
					$(".dyn"+ref).html(data.operators);
					
					if(data.fields.locnames != null){
						
						$("."+ref).html(data.fields.locnames);
						$(".select2").select2();
						
					}else if(data.fields.status != null){
						
						$("."+ref).html(data.fields.status);
						
					}else if(data.fields.location_type != null){
						
						$("."+ref).html(data.fields.location_type);
						
					}else if(data.fields.import_date != null){
						
						$("."+ref).html(data.fields.import_date);
						
					}else if(data.fields.accounts != null){
						
						$("."+ref).html(data.fields.accounts);
						$(".select2").select2();
						
					}else{
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
					console.log(data.operators);
					
				},
				error : function(data){
					
					console.log(data);
					
				}
				
			});
			
		});
        $(document).on("change",".loc_filter_dyn",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("lo_id");
			var lopid = $(this).attr("lopid");
			var col_val = "";
			$("."+ref).show();
			
			if(column == "shipperpo"){
				col_val = "shipperpo-text";
			}
			if(column == "shippmentdate"){
				col_val = "shippmentdate-date";
			}
			if(column == "pronum"){
				col_val = "pronum-text";
			}
			if(column == "tlcoation"){
				col_val = "tlcoation-select";
			}
			if(column == "flocation"){
				col_val = "flocation-select";
			}
			if(column == "quantity"){
				col_val = "quantity-number";
			}
			if(column == "reportdate"){
				col_val = "reportdate-date";
			}
			
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions",
				dataType : 'json',
				data : {column : col_val,table:"tbl_touts","onchangeColref":"updateonchangeConditionLocation",uopid:lopid},
				success : function(data){
					console.log(data);
					$(".dyn"+ref).html(data.operators);
					
					if(data.fields.locnames != null){
						
						$("."+ref).html(data.fields.locnames);
						$(".select2").select2();
						
					}else if(data.fields.status != null){
						
						$("."+ref).html(data.fields.status);
						
					}else if(data.fields.location_type != null){
						
						$("."+ref).html(data.fields.location_type);
						
					}else if(data.fields.import_date != null){
						
						$("."+ref).html(data.fields.import_date);
						
					}else if(data.fields.accounts != null){
						
						$("."+ref).html(data.fields.accounts);
						$(".select2").select2();
						
					}else{
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
		});
		$(document).on("change",".updateonchangeConditionLocation",function(){
			
			var cond = $(this).val();
			var bind = $(this).attr("lopid");
			var selection = $("#"+bind).val();
			
			if(selection == "shippmentdate"){
				selection = "shippmentdate-date";
			}
			if(selection == "reportdate"){
				selection = "reportdate-date";
			}
			if(selection == "processdate"){
				selection = "processdate-date";
			}
			if(selection == "chepprocessdate"){
				selection = "chepprocessdate-date";
			}
			var date = <?php echo date('m-d-Y'); ?>;
			
			if(cond == "is blank" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time" || cond == "is any"){
				
				$("."+bind).hide();
				
			}else if(cond == "is during the current"){
				
				$("."+bind).html('<select name="cond_value[]" class="form-control svalueData"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$("."+bind).show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				
				$("."+bind).html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days[]" class="form-control svalueData">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value[]" class="form-control dvalueData"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option></select></div></div>');
				
				$("."+bind).show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$("."+bind).html('<input type="date" class="form-control svalueData" name="cond_value[]" value="'+date+'">');
				
				$("."+bind).show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$("."+bind).html('<input type="date" class="form-control svalueData" name="cond_value[]" value="'+date+'">');
				
				}
				$("."+bind).show();
				
			}else{
				
				$("."+bind).show();
				
			}
			
		})
		
		$(document).on("change",".updateonchangeConditionLocation",function(){
			
			var cond = $(this).val();
			var bind = $(this).attr("lopid");
			var selection = $("#"+bind).val();
			
			if(selection == "shippmentdate"){
				selection = "shippmentdate-date";
			}
			if(selection == "reportdate"){
				selection = "reportdate-date";
			}
			if(selection == "processdate"){
				selection = "processdate-date";
			}
			if(selection == "chepprocessdate"){
				selection = "chepprocessdate-date";
			}
			var date = <?php echo date('m-d-Y'); ?>;
			
			if(cond == "is blank" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time" || cond == "is any"){
				
				$("."+bind).hide();
				
			}else if(cond == "is during the current"){
				
				$("."+bind).html('<select name="cond_value[]" class="form-control svalueData"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$("."+bind).show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				
				$("."+bind).html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days[]" class="form-control svalueData">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value[]" class="form-control dvalueData"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option></select></div></div>');
				
				$("."+bind).show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$("."+bind).html('<input type="date" class="form-control svalueData" name="cond_value[]" value="'+date+'">');
				
				$("."+bind).show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$("."+bind).html('<input type="date" class="form-control svalueData" name="cond_value[]" value="'+date+'">');
				
				}
				$("."+bind).show();
				
			}else{
				
				$("."+bind).show();
				
			}
			
		})
		
		var i=2;		
			function addFilter(){
			var n = '<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first'+i+'"><div class="row"><div class="col-md-1"><select name="cause[]" id="cause" class="form-control causeData"><option value="and">and</option><option value="or">or</option></select></div><div class="col-md-3"><select name="field[]" class="form-control getUlocdata fieldData loc_filter_dyn" lid="locid'+i+'" id="updLoc'+i+'" lopid="updLoc'+i+'" lo_id="locgetwhenRef'+i+'""><option value="">Select</option><option value="shipperpo">Shipper PO</option><option value="shippmentdate">Shipment Date</option><option value="pronum">Pro Number</option><option value="tlcoation">To Location</option><option value="quantity">Quantity</option><option value="reportdate">Report Date</option></select></div><div class="col-md-3 dynlocgetwhenRef'+i+'"><select name="value[]" id="value" class="form-control valueData_filter"><option value="">Select</option><option value="contains">contains</option><option value="does not contain">does not contain</option><option value="is">is</option><option value="is not">is not</option><option value="starts with">starts with</option><option value="ends with">ends with</option><option value="is blank">is blank</option><option value="is not blank">is not blank</option></select></div><div class="col-md-4"><div class="locid'+i+' locgetwhenRef'+i+' updLoc'+i+'"><input type="text" name="svalue[]" id="svalue" class="form-control svalueData"></div></div><div class="col-md-1"><p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter(\'first'+i+'\');"><i class="fa fa-trash"></i></p></div></div></div>';
			$("#top").append(n);
			i++;
			}
		function removeFilter(first){
			// console.log(first);
			$("."+first).remove();
		i--;
		}
		
		var i=2;		
			function addFilter_transferins(){
			var n = '<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first'+i+'"><div class="row"><div class="col-md-1"><select name="cause[]" id="cause" class="form-control causeDataouts"><option value="and">and</option><option value="or">or</option></select></div><div class="col-md-3"><select name="field[]" class="form-control getUlocdata fieldDataouts loc_filter_forms_dyn" lid="locid'+i+'" id="updLoc'+i+'_ins" lopidouts="updLoc'+i+'_ins" lo_id="locgetwhenRef'+i+'_ins""><option value="">Select</option><option value="shipperpo">Shipper PO</option><option value="shippmentdate">Shipment Date</option><option value="pronum">Pro Number</option><option value="flocation">From Location</option><option value="quantity">Quantity</option><option value="reportdate">Report Date</option></select></div><div class="col-md-3 dynlocgetwhenRef'+i+'_ins"><select name="value[]" id="value" class="form-control valueDataouts valueData_filter"><option value="">Select</option><option value="contains">contains</option><option value="does not contain">does not contain</option><option value="is">is</option><option value="is not">is not</option><option value="starts with">starts with</option><option value="ends with">ends with</option><option value="is blank">is blank</option><option value="is not blank">is not blank</option></select></div><div class="col-md-4"><div class="locid'+i+' locgetwhenRef'+i+'_ins updLoc'+i+'_ins"><input type="text" name="svalue[]" id="svalue" class="form-control svalueDataouts"></div></div><div class="col-md-1"><p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter_transfersins(\'first'+i+'\');"><i class="fa fa-trash"></i></p></div></div></div>';
			$("#top_transferins").append(n);
			i++;
			}
		function removeFilter_transfersins(first){
			// console.log(first);
			$("."+first).remove();
		i--;
		}
		
		
		
		var i=2;		
			function addFilter_shipments(){
			var n = '<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first'+i+'"><div class="row"><div class="col-md-1"><select name="cause[]" id="cause" class="form-control causeDataship"><option value="and">and</option><option value="or">or</option></select></div><div class="col-md-3"><select name="field[]" class="form-control getUlocdata fieldDataship loc_filter_ship_dyn" lid="locid'+i+'" id="updLoc'+i+'_ship" lopidship="updLoc'+i+'_ship" lo_id="locgetwhenRef'+i+'_ship""><option value="">Select</option><option value="chepreference">Vendor Reference</option><option value="ongreference">Ongweoweh Reference</option><option value="shippmentdate">Shipment Date</option><option value="quantity">Quantity</option><option value="tlocation">Location</option></select></div><div class="col-md-3 dynlocgetwhenRef'+i+'_ship"><select name="value[]" id="value" class="form-control valueDataship valueData_filter"><option value="">Select</option><option value="contains">contains</option><option value="does not contain">does not contain</option><option value="is">is</option><option value="is not">is not</option><option value="starts with">starts with</option><option value="ends with">ends with</option><option value="is blank">is blank</option><option value="is not blank">is not blank</option></select></div><div class="col-md-4"><div class="locid'+i+' locgetwhenRef'+i+'_ship updLoc'+i+'_ship"><input type="text" name="svalue[]" id="svalue" class="form-control svalueDataship"></div></div><div class="col-md-1"><p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter_shipments(\'first'+i+'\');"><i class="fa fa-trash"></i></p></div></div></div>';
			$("#top_shipments").append(n);
			i++;
			}
		function removeFilter_shipments(first){
			// console.log(first);
			$("."+first).remove();
		i--;
		}
		
		    var i=2;		
			function addFilter_pickup(){
			var n = '<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first'+i+'"><div class="row"><div class="col-md-1"><select name="cause[]" id="cause" class="form-control causeDatapickup"><option value="and">and</option><option value="or">or</option></select></div><div class="col-md-3"><select name="field[]" class="form-control getUlocdata fieldDatapickup loc_filter_pickup_dyn" lid="locid'+i+'" id="updLoc'+i+'_pickup" lopidpickup="updLoc'+i+'_pickup" lo_id="locgetwhenRef'+i+'_pickup"><option value="">Select</option><option value="chepreference">Vendor Reference</option><option value="ongreference">Ongweoweh Reference</option><option value="shippmentdate">Shipment Date</option><option value="quantity">Quantity</option><option value="tlocation">Location</option></select></div><div class="col-md-3 dynlocgetwhenRef'+i+'_pickup"><select name="value[]" id="value" class="form-control valueDatapickup"><option value="">Select</option><option value="contains">contains</option><option value="does not contain">does not contain</option><option value="is">is</option><option value="is not">is not</option><option value="starts with">starts with</option><option value="ends with">ends with</option><option value="is blank">is blank</option><option value="is not blank">is not blank</option></select></div><div class="col-md-4"><div class="locid'+i+' locgetwhenRef'+i+'_pickup updLoc'+i+'_pickup"><input type="text" name="svalue[]" id="svalue" class="form-control svalueDatapickup"></div></div><div class="col-md-1"><p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter_pickup(\'first'+i+'\');"><i class="fa fa-trash"></i></p></div></div></div>';
			$("#top_pickup").append(n);
			i++;
			}

			function removeFilter_pickup(first){
				// console.log(first);
				$("."+first).remove();
			i--;
			}
		
		var i=2;		
	   function addFilter_adjus(){
		var n = '<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first'+i+'"><div class="row"><div class="col-md-1"><select name="cause[]" id="cause" class="form-control causeDataadjus"><option value="and">and</option><option value="or">or</option></select></div><div class="col-md-3"><select name="field[]" class="form-control getUlocdata fieldDataadjus loc_filter_adjus_dyn" lid="locid'+i+'" id="updLoc'+i+'_adjus" lopidadjus="updLoc'+i+'_adjus" lo_id="locgetwhenRef'+i+'_adjus"><option value="">Select</option><option value="chepreference">Vendor Reference</option><option value="ongreference">Ongweoweh Reference</option><option value="shippmentdate">Shipment Date</option><option value="quantity">Quantity</option><option value="tlocation">Location</option></select></div><div class="col-md-3 dynlocgetwhenRef'+i+'_adjus"><select name="value[]" id="value" class="form-control valueDataadjus"><option value="">Select</option><option value="contains">contains</option><option value="does not contain">does not contain</option><option value="is">is</option><option value="is not">is not</option><option value="starts with">starts with</option><option value="ends with">ends with</option><option value="is blank">is blank</option><option value="is not blank">is not blank</option></select></div><div class="col-md-4"><div class="locid'+i+' locgetwhenRef'+i+'_adjus updLoc'+i+'_adjus"><input type="text" name="svalue[]" id="svalue" class="form-control svalueDataadjus"></div></div><div class="col-md-1"><p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter_adjus(\'first'+i+'\');"><i class="fa fa-trash"></i></p></div></div></div>';
		$("#top_adjus").append(n);
		i++;
			}
		
		function removeFilter_adjus(first){
		// console.log(first);
		$("."+first).remove();
		i--;
	     }
		
		$(".submitFilter").on('submit', function(e){
		e.preventDefault();
		function exportAll(){		
			window.location.href = '<? echo base_url('admin/apps/filter_excel_download') ?>';
		}
		var field = [];
		var cause = [];
		var value = [];
		var svalue = [];
		var dvalue = [];
			$(".fieldData").each(function(){
			field.push($(this).val());
			});
			$(".causeData").each(function(){
				cause.push($(this).val());
			});
			$(".valueData_filter").each(function(){
				value.push($(this).val());
			});
			$(".svalueData").each(function(){
				svalue.push($(this).val());
			});
			$(".dvalueData").each(function(){
				
				dvalue.push($(this).val());
			});

		var item = $("#item_name").val();
		var loc_code = $("#loc_id").val();
		var app_id = $("#app_id").val();
		
        
		var fdata = {"cause":cause,"field":field,"value":value,"svalue":svalue,"dvalue":dvalue,"item":item,"loc_code":loc_code,filter_type:"transfer_outs","app_id":app_id,"id":'<? echo $appid; ?>',"table":"tbl_touts" };

		// console.log(fdata);
		// return false;
		$("#myFilter").modal("hide");
		
		var table = $('#toutTable').dataTable({
			 "bProcessing": true,
			 "ajax": {
				"url": "<?php echo base_url("admin/apps/addFilter_mainAdmin") ?>",
				"type": "POST",
				"data" : fdata
		      /* success : function(data){
					 
					 console.log(data);
					 
				 },
				 error : function(data){
					 
					 console.log(data);
					 
				 } */   
			  },
			 'columns': [ {
					data: 'Sno',
					defaultContent: ""
				},{
					data: 'shipperpo',
					defaultContent: ""
				}, {
					data: 'shippmentdate',
					defaultContent: ""
				}, {
					data: 'pronum',
					defaultContent: ""
				}, {
					data: 'tlcoation',
					defaultContent: ""
				}, {
					data: 'quantity',
					defaultContent: ""
				}, {
					data: 'reportdate',
					defaultContent: ""
				} ],
			  "aaSorting": [[ 0, "asc" ]],
			  "bLengthChange": true,
			  "pageLength":10,
			  "destroy" : 'true',
			  "dom": 'Bfrtip',
			   "lengthMenu": [[10, 25, 50,100,500], [10, 25, 50,100,500]],
			  "buttons": [
				'pageLength',
				{
		 		extend: 'excelHtml5',
		 		title:'Transfers Ongoweoweh',
	                exportOptions: {
	                    columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]
	                }
		 		},
		 		{
		 		extend: 'csvHtml5',
		 		title:'Transfers Ongoweoweh',
	                exportOptions: {
	                    columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]
	                }
		 		},
		 		{
				   "extend": 'excel',
				   "text": 'Export All',
				   "titleAttr": 'Export All',                               
				   "action": exportAll
			    }
			  ]
		  });

	});
	
	
	//transfers outs filter submit
	
	$(".submitFilter_transfersins").on('submit', function(e){
		e.preventDefault();
		function exportAll(){		
			window.location.href = '<? echo base_url('admin/apps/filter_excel_download') ?>';
		}
		var field = [];
		var cause = [];
		var value = [];
		var svalue = [];
		var dvalue = [];
		
		    $(".fieldDataouts").each(function(){
			    field.push($(this).val());
			});
			$(".causeDataouts").each(function(){
				cause.push($(this).val());
			});
			$(".valueDataouts").each(function(){
	
				value.push($(this).val());
			});
			$(".svalueDataouts").each(function(){
				svalue.push($(this).val());
			});
			$(".dvalueDataouts").each(function(){				
				dvalue.push($(this).val());
			});
        
		var item = $("#item_name").val();
		var loc_code = $("#loc_id").val();
		var app_id = $("#app_id").val();
		
        
		var fdata = {"cause":cause,"field":field,"value":value,"svalue":svalue,"dvalue":dvalue,"item":item,"loc_code":loc_code,filter_type:"transfer_ins","app_id":app_id,"id":'<? echo $appid; ?>',"table":"tbl_touts" };

		// console.log(fdata);
		// return false;
		$("#myFilter_Transfersins").modal("hide");
		
		var table = $('#tinTable').dataTable({
			 "bProcessing": true,
			 "ajax": {
				"url": "<?php echo base_url("admin/apps/addFilter_mainAdmin") ?>",
				"type": "POST",
				"data" : fdata
		       /* success : function(data){
					 
					 console.log(data);
					 
				 },
				 error : function(data){
					 
					 console.log(data);
					 
				 } */   
			  },
			 "columns": [{
					data: 'Sno',
					defaultContent: ""
				}, {
					data: 'shipperpo',
					defaultContent: ""
				}, {
					data: 'shippmentdate',
					defaultContent: ""
				}, {
					data: 'pronum',
					defaultContent: ""
				}, {
					data: 'flocation',
					defaultContent: ""
				}, {
					data: 'quantity',
					defaultContent: ""
				}, {
					data: 'reportdate',
					defaultContent: ""
				} ],
			  "aaSorting": [[ 0, "asc" ]],
			  "bLengthChange": true,
			  "pageLength":10,
			  "destroy" : 'true',
			  "dom": 'Bfrtip',
			   "lengthMenu": [[10, 25, 50,100,500], [10, 25, 50,100,500]],
			  "buttons": [
				'pageLength',
				{
		 		extend: 'excelHtml5',
		 		title:'Transfers Ongoweoweh',
	                exportOptions: {
	                    columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]
	                }
		 		},
		 		{
		 		extend: 'csvHtml5',
		 		title:'Transfers Ongoweoweh',
	                exportOptions: {
	                    columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]
	                }
		 		},
		 		{
				   "extend": 'excel',
				   "text": 'Export All',
				   "titleAttr": 'Export All',                               
				   "action": exportAll
			    }
			  ]
		  });

	});
	$(".submitFilter_shipments").on('submit', function(e){
		e.preventDefault();
		function exportAll(){		
			window.location.href = '<? echo base_url('admin/apps/filter_excel_download') ?>';
		}
		var field = [];
		var cause = [];
		var value = [];
		var svalue = [];
		var dvalue = [];
		
		    $(".fieldDataship").each(function(){
			    field.push($(this).val());
			});
			$(".causeDataship").each(function(){
				cause.push($(this).val());
			});
			$(".valueDataship").each(function(){
	
				value.push($(this).val());
			});
			$(".svalueDataship").each(function(){
				svalue.push($(this).val());
			});
			$(".dvalueDataship").each(function(){				
				dvalue.push($(this).val());
			});

		var item = $("#item_name").val();
		var loc_code = $("#loc_id").val();
		var app_id = $("#app_id").val();
		
        
		var fdata = {"cause":cause,"field":field,"value":value,"svalue":svalue,"dvalue":dvalue,"item":item,"loc_code":loc_code,filter_type:"shipments","app_id":app_id,"id":'<? echo $appid; ?>',"table":"tbl_issues" };

		// console.log(fdata);
		// return false;
		$("#myFilter_shipments").modal("hide");
		
		var table = $('#issuesTable').dataTable({
			 "bProcessing": true,
			 "ajax": {
				"url": "<?php echo base_url("admin/apps/addFilter_mainAdmin") ?>",
				"type": "POST",
				"data" : fdata
		        /* success : function(data){
					 
					 console.log(data);
					 
				 },
				 error : function(data){
					 
					 console.log(data);
					 
				 } */    
			  },
			 "columns": [{
					data: 'Sno',
					defaultContent: ""
				}, {
					data: 'chepreference',
					defaultContent: ""
				}, {
					data: 'ongreference',
					defaultContent: ""
				}, {
					data: 'shippmentdate',
					defaultContent: ""
				}, {
					data: 'tlocation',
					defaultContent: ""
				}, {
					data: 'quantity',
					defaultContent: ""
				} ],
			  "aaSorting": [[ 0, "asc" ]],
			  "bLengthChange": true,
			  "pageLength":10,
			  "destroy" : 'true',
			  "dom": 'Bfrtip',
			   "lengthMenu": [[10, 25, 50,100,500], [10, 25, 50,100,500]],
			  "buttons": [
				'pageLength',
				{
		 		extend: 'excelHtml5',
		 		title:'Transfers Ongoweoweh',
	                exportOptions: {
	                    columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]
	                }
		 		},
		 		{
		 		extend: 'csvHtml5',
		 		title:'Transfers Ongoweoweh',
	                exportOptions: {
	                    columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]
	                }
		 		},
		 		{
				   "extend": 'excel',
				   "text": 'Export All',
				   "titleAttr": 'Export All',                               
				   "action": exportAll
			    }
			  ]
		  });

	});
	$(document).on("change",".updateonchangeConditionLocation_transferins",function(){
			
			var cond = $(this).val();
			var bind = $(this).attr("lopidouts");
			var selection = $("#"+bind).val();
			if(selection == "shippmentdate"){
				selection = "shippmentdate-date";
			}
			if(selection == "reportdate"){
				selection = "reportdate-date";
			}
			if(selection == "processdate"){
				selection = "processdate-date";
			}
			if(selection == "chepprocessdate"){
				selection = "chepprocessdate-date";
			}
			var date = <?php echo date('m-d-Y'); ?>;
			
			if(cond == "is blank" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time" || cond == "is any"){
				
				$("."+bind).hide();
				
			}else if(cond == "is during the current"){
				
				$("."+bind).html('<select name="cond_value[]" class="form-control svalueDataouts"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$("."+bind).show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				
				$("."+bind).html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days[]" class="form-control svalueDataouts">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value[]" class="form-control dvalueDataouts"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option></select></div></div>');
				
				$("."+bind).show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$("."+bind).html('<input type="date" class="form-control svalueDataouts" name="cond_value[]" value="'+date+'">');
				
				$("."+bind).show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$("."+bind).html('<input type="date" class="form-control svalueDataouts" name="cond_value[]" value="'+date+'">');
				
				}
				$("."+bind).show();
				
			}else{
				
				$("."+bind).show();
				
			}
			
			
		})
		$(document).on("change",".updateonchangeConditionLocation_shipments",function(){
			
			var cond = $(this).val();
			var bind = $(this).attr("lopidship");
			var selection = $("#"+bind).val();
			if(selection == "shippmentdate"){
				selection = "shippmentdate-date";
			}
			var date = <?php echo date('m-d-Y'); ?>;
			
			if(cond == "is blank" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time" || cond == "is any"){
				
				$("."+bind).hide();
				
			}else if(cond == "is during the current"){
				
				$("."+bind).html('<select name="cond_value[]" class="form-control svalueDataship"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$("."+bind).show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				
				$("."+bind).html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days[]" class="form-control svalueDataship">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value[]" class="form-control dvalueDataship"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option></select></div></div>');
				
				$("."+bind).show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$("."+bind).html('<input type="date" class="form-control svalueDataship" name="cond_value[]" value="'+date+'">');
				
				$("."+bind).show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$("."+bind).html('<input type="date" class="form-control svalueDataship" name="cond_value[]" value="'+date+'">');
				
				}
				$("."+bind).show();
				
			}else{
				
				$("."+bind).show();
				
			}
			
			
		})
		$(document).on("change",".updateonchangeConditionLocation_pickup",function(){
			
			var cond = $(this).val();
			var bind = $(this).attr("lopidpickup");
			var selection = $("#"+bind).val();
			if(selection == "shippmentdate"){
				selection = "shippmentdate-date";
			}
			var date = <?php echo date('m-d-Y'); ?>;
			
			if(cond == "is blank" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time" || cond == "is any"){
				
				$("."+bind).hide();
				
			}else if(cond == "is during the current"){
				
				$("."+bind).html('<select name="cond_value[]" class="form-control svalueDatapickup"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$("."+bind).show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				
				$("."+bind).html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days[]" class="form-control svalueDatapickup">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value[]" class="form-control dvalueDatapickup"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option></select></div></div>');
				
				$("."+bind).show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$("."+bind).html('<input type="date" class="form-control svalueDatapickup" name="cond_value[]" value="'+date+'">');
				
				$("."+bind).show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$("."+bind).html('<input type="date" class="form-control svalueDatapickup" name="cond_value[]" value="'+date+'">');
				
				}
				$("."+bind).show();
				
			}else{
				
				$("."+bind).show();
				
			}
			
			
		})
		
		$(document).on("change",".updateonchangeConditionLocation_adjus",function(){
			
			var cond = $(this).val();
			var bind = $(this).attr("lopidadjus");
			var selection = $("#"+bind).val();
			if(selection == "shippmentdate"){
				selection = "shippmentdate-date";
			}
			var date = <?php echo date('m-d-Y'); ?>;
			
			if(cond == "is blank" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time" || cond == "is any"){
				
				$("."+bind).hide();
				
			}else if(cond == "is during the current"){
				
				$("."+bind).html('<select name="cond_value[]" class="form-control svalueDataadjus"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$("."+bind).show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				
				$("."+bind).html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days[]" class="form-control svalueDataadjus">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value[]" class="form-control dvalueDataadjus"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option></select></div></div>');
				
				$("."+bind).show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$("."+bind).html('<input type="date" class="form-control svalueDataadjus" name="cond_value[]" value="'+date+'">');
				
				$("."+bind).show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$("."+bind).html('<input type="date" class="form-control svalueDataadjus" name="cond_value[]" value="'+date+'">');
				
				}
				$("."+bind).show();
				
			}else{
				
				$("."+bind).show();
				
			}
			
			
		})
		
		$(".submitFilter_pickup").on('submit', function(e){
		e.preventDefault();
		function exportAll(){		
			window.location.href = '<? echo base_url('admin/apps/filter_excel_download') ?>';
		}
		var field = [];
		var cause = [];
		var value = [];
		var svalue = [];
		var dvalue = [];
		
		    $(".fieldDatapickup").each(function(){
			    field.push($(this).val());
			});
			$(".causeDatapickup").each(function(){
				cause.push($(this).val());
			});
			$(".valueDatapickup").each(function(){
	
				value.push($(this).val());
			});
			$(".svalueDatapickup").each(function(){
				svalue.push($(this).val());
			});
			$(".dvalueDatapickup").each(function(){				
				dvalue.push($(this).val());
			});

		var item = $("#item_name").val();
		var loc_code = $("#loc_id").val();
		var app_id = $("#app_id").val();
		
        
		var fdata = {"cause":cause,"field":field,"value":value,"svalue":svalue,"dvalue":dvalue,"item":item,"loc_code":loc_code,filter_type:"pickup","app_id":app_id,"id":'<? echo $appid; ?>',"table":"tbl_returns" };

		// console.log(fdata);
		// return false;
		$("#myFilter_pickup").modal("hide");
		
		var table = $('#returnsTable').dataTable({
			 "bProcessing": true,
			 "ajax": {
				"url": "<?php echo base_url("admin/apps/addFilter_mainAdmin") ?>",
				"type": "POST",
				"data" : fdata
		        /* success : function(data){
					 
					 console.log(data);
					 
				 },
				 error : function(data){
					 
					 console.log(data);
					 
				 } */    
			  },
			 "columns": [{
					data: 'Sno',
					defaultContent: ""
				}, {
					data: 'chepreference',
					defaultContent: ""
				}, {
					data: 'ongreference',
					defaultContent: ""
				}, {
					data: 'shippmentdate',
					defaultContent: ""
				}, {
					data: 'tlocation',
					defaultContent: ""
				}, {
					data: 'quantity',
					defaultContent: ""
				} ],
			  "aaSorting": [[ 0, "asc" ]],
			  "bLengthChange": true,
			  "pageLength":10,
			  "destroy" : 'true',
			  "dom": 'Bfrtip',
			   "lengthMenu": [[10, 25, 50,100,500], [10, 25, 50,100,500]],
			  "buttons": [
				'pageLength',
				{
		 		extend: 'excelHtml5',
		 		title:'Transfers Ongoweoweh',
	                exportOptions: {
	                    columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]
	                }
		 		},
		 		{
		 		extend: 'csvHtml5',
		 		title:'Transfers Ongoweoweh',
	                exportOptions: {
	                    columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]
	                }
		 		},
		 		{
				   "extend": 'excel',
				   "text": 'Export All',
				   "titleAttr": 'Export All',                               
				   "action": exportAll
			    }
			  ]
		  });

	});
	
	$(".submitFilter_adjus").on('submit', function(e){
		e.preventDefault();
		function exportAll(){		
			window.location.href = '<? echo base_url('admin/apps/filter_excel_download') ?>';
		}
		var field = [];
		var cause = [];
		var value = [];
		var svalue = [];
		var dvalue = [];
		
		    $(".fieldDataadjus").each(function(){
			    field.push($(this).val());
			});
			$(".causeDataadjus").each(function(){
				cause.push($(this).val());
			});
			$(".valueDataadjus").each(function(){
	
				value.push($(this).val());
			});
			$(".svalueDataadjus").each(function(){
				svalue.push($(this).val());
			});
			$(".dvalueDataadjus").each(function(){				
				dvalue.push($(this).val());
			});

		var item = $("#item_name").val();
		var loc_code = $("#loc_id").val();
		var app_id = $("#app_id").val();
		
        
		var fdata = {"cause":cause,"field":field,"value":value,"svalue":svalue,"dvalue":dvalue,"item":item,"loc_code":loc_code,filter_type:"adjus","app_id":app_id,"id":'<? echo $appid; ?>',"table":"tbl_adjustments" };

		// console.log(fdata);
		// return false;
		$("#myFilter_adjus").modal("hide");
		
		var table = $('#adjustmentsTable').dataTable({
			 "bProcessing": true,
			 "ajax": {
				"url": "<?php echo base_url("admin/apps/addFilter_mainAdmin") ?>",
				"type": "POST",
				"data" : fdata
		        /* success : function(data){
					 
					 console.log(data);
					 
				 },
				 error : function(data){
					 
					 console.log(data);
					 
				 } */    
			  },
			 "columns": [ {
					data: 'Sno',
					defaultContent: ""
				},{
					data: 'chepreference',
					defaultContent: ""
				}, {
					data: 'ongreference',
					defaultContent: ""
				}, {
					data: 'shippmentdate',
					defaultContent: ""
				}, {
					data: 'tlocation',
					defaultContent: ""
				}, {
					data: 'quantity',
					defaultContent: ""
				} ],
			  "aaSorting": [[ 0, "asc" ]],
			  "bLengthChange": true,
			  "pageLength":10,
			  "destroy" : 'true',
			  "dom": 'Bfrtip',
			   "lengthMenu": [[10, 25, 50,100,500], [10, 25, 50,100,500]],
			  "buttons": [
				'pageLength',
				{
		 		extend: 'excelHtml5',
		 		title:'Transfers Ongoweoweh',
	                exportOptions: {
	                    columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]
	                }
		 		},
		 		{
		 		extend: 'csvHtml5',
		 		title:'Transfers Ongoweoweh',
	                exportOptions: {
	                    columns: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]
	                }
		 		},
		 		{
				   "extend": 'excel',
				   "text": 'Export All',
				   "titleAttr": 'Export All',                               
				   "action": exportAll
			    }
			  ]
		  });

	});
	
$(document).on("change",".loc_filter_ship",function(){		
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("lo_id");
			var lopid = $(this).attr("lopidship");
			var col_val = "";
			$("."+ref).show();
			

			if(column == "shipperpo"){
				col_val = "shipperpo-text";
			}
			if(column == "shippmentdate"){
				col_val = "shippmentdate-date";
			}
			if(column == "pronum"){
				col_val = "pronum-text";
			}
			if(column == "tlocation"){
				col_val = "tlocation-select";
			}
			if(column == "flocation"){
				col_val = "flocation-select";
			}
			if(column == "quantity"){
				col_val = "quantity-number";
			}
			if(column == "reportdate"){
				col_val = "reportdate-date";
			}
			
			
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions_dyn",
				dataType : 'json',
				data : {column : col_val,form_type : "ship",table:"tbl_issues","onchangeColref":"updateonchangeConditionLocation_shipments",uopid:lopid},
				success : function(data){
					console.log(data);
					
					$(".dyn"+ref).html(data.operators);
					
					if(data.fields.locnames != null){
						
						$("."+ref).html(data.fields.locnames);
						$(".select2").select2();
						
					}else if(data.fields.status != null){
						
						$("."+ref).html(data.fields.status);
						
					}else if(data.fields.location_type != null){
						
						$("."+ref).html(data.fields.location_type);
						
					}else if(data.fields.import_date != null){
						
						$("."+ref).html(data.fields.import_date);
						
					}else if(data.fields.accounts != null){
						
						$("."+ref).html(data.fields.accounts);
						$(".select2").select2();
						
					}else{
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
					console.log(data.operators);
					
				},
				error : function(data){
					
					console.log(data);
					
				}
				
			});
			
		});
		$(document).on("change",".loc_filter_ship_dyn",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("lo_id");
			var lopid = $(this).attr("lopidship");
			var col_val = "";
			$("."+ref).show();
			
			if(column == "shipperpo"){
				col_val = "shipperpo-text";
			}
			if(column == "shippmentdate"){
				col_val = "shippmentdate-date";
			}
			if(column == "pronum"){
				col_val = "pronum-text";
			}
			if(column == "tlocation"){
				col_val = "tlocation-select";
			}
			if(column == "flocation"){
				col_val = "flocation-select";
			}
			if(column == "quantity"){
				col_val = "quantity-number";
			}
			if(column == "reportdate"){
				col_val = "reportdate-date";
			}
			
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions_dyn",
				dataType : 'json',
				data : {column : col_val,form_type : "ship",table:"tbl_issues","onchangeColref":"updateonchangeConditionLocation_shipments",uopid:lopid},
				success : function(data){
					console.log(data);
					$(".dyn"+ref).html(data.operators);
					
					if(data.fields.locnames != null){
						
						$("."+ref).html(data.fields.locnames);
						$(".select2").select2();
						
					}else if(data.fields.status != null){
						
						$("."+ref).html(data.fields.status);
						
					}else if(data.fields.location_type != null){
						
						$("."+ref).html(data.fields.location_type);
						
					}else if(data.fields.import_date != null){
						
						$("."+ref).html(data.fields.import_date);
						
					}else if(data.fields.accounts != null){
						
						$("."+ref).html(data.fields.accounts);
						$(".select2").select2();
						
					}else{
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
		});
		
		
		
		
		$(document).on("change",".loc_filter_pickup",function(){		
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("lo_id");
			var lopid = $(this).attr("lopidpickup");
			var col_val = "";
			$("."+ref).show();
			

			if(column == "shipperpo"){
				col_val = "shipperpo-text";
			}
			if(column == "shippmentdate"){
				col_val = "shippmentdate-date";
			}
			if(column == "pronum"){
				col_val = "pronum-text";
			}
			if(column == "tlocation"){
				col_val = "tlocation-select";
			}
			if(column == "flocation"){
				col_val = "flocation-select";
			}
			if(column == "quantity"){
				col_val = "quantity-number";
			}
			if(column == "reportdate"){
				col_val = "reportdate-date";
			}
			
			
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions_dyn",
				dataType : 'json',
				data : {column : col_val,form_type : "pickup",table:"tbl_returns","onchangeColref":"updateonchangeConditionLocation_pickup",uopid:lopid},
				success : function(data){
					console.log(data);
					
					$(".dyn"+ref).html(data.operators);
					
					if(data.fields.locnames != null){
						
						$("."+ref).html(data.fields.locnames);
						$(".select2").select2();
						
					}else if(data.fields.status != null){
						
						$("."+ref).html(data.fields.status);
						
					}else if(data.fields.location_type != null){
						
						$("."+ref).html(data.fields.location_type);
						
					}else if(data.fields.import_date != null){
						
						$("."+ref).html(data.fields.import_date);
						
					}else if(data.fields.accounts != null){
						
						$("."+ref).html(data.fields.accounts);
						$(".select2").select2();
						
					}else{
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
					console.log(data.operators);
					
				},
				error : function(data){
					
					console.log(data);
					
				}
				
			});
			
		});
		$(document).on("change",".loc_filter_pickup_dyn",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("lo_id");
			var lopid = $(this).attr("lopidpickup");
			var col_val = "";
			$("."+ref).show();
			
			if(column == "shipperpo"){
				col_val = "shipperpo-text";
			}
			if(column == "shippmentdate"){
				col_val = "shippmentdate-date";
			}
			if(column == "pronum"){
				col_val = "pronum-text";
			}
			if(column == "tlocation"){
				col_val = "tlocation-select";
			}
			if(column == "flocation"){
				col_val = "flocation-select";
			}
			if(column == "quantity"){
				col_val = "quantity-number";
			}
			if(column == "reportdate"){
				col_val = "reportdate-date";
			}
			
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions_dyn",
				dataType : 'json',
				data : {column : col_val,form_type : "pickup",table:"tbl_returns","onchangeColref":"updateonchangeConditionLocation_pickup",uopid:lopid},
				success : function(data){
					console.log(data);
					$(".dyn"+ref).html(data.operators);
					
					if(data.fields.locnames != null){
						
						$("."+ref).html(data.fields.locnames);
						$(".select2").select2();
						
					}else if(data.fields.status != null){
						
						$("."+ref).html(data.fields.status);
						
					}else if(data.fields.location_type != null){
						
						$("."+ref).html(data.fields.location_type);
						
					}else if(data.fields.import_date != null){
						
						$("."+ref).html(data.fields.import_date);
						
					}else if(data.fields.accounts != null){
						
						$("."+ref).html(data.fields.accounts);
						$(".select2").select2();
						
					}else{
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
		});
		
		
		
		$(document).on("change",".loc_filter_adjus",function(){		
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("lo_id");
			var lopid = $(this).attr("lopidadjus");
			var col_val = "";
			$("."+ref).show();
			

			if(column == "shipperpo"){
				col_val = "shipperpo-text";
			}
			if(column == "shippmentdate"){
				col_val = "shippmentdate-date";
			}
			if(column == "pronum"){
				col_val = "pronum-text";
			}
			if(column == "tlocation"){
				col_val = "tlocation-select";
			}
			if(column == "flocation"){
				col_val = "flocation-select";
			}
			if(column == "quantity"){
				col_val = "quantity-number";
			}
			if(column == "reportdate"){
				col_val = "reportdate-date";
			}
			
			
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions_dyn",
				dataType : 'json',
				data : {column : col_val,form_type : "adjus",table:"tbl_adjustments","onchangeColref":"updateonchangeConditionLocation_adjus",uopid:lopid},
				success : function(data){
					console.log(data);
					
					$(".dyn"+ref).html(data.operators);
					
					if(data.fields.locnames != null){
						
						$("."+ref).html(data.fields.locnames);
						$(".select2").select2();
						
					}else if(data.fields.status != null){
						
						$("."+ref).html(data.fields.status);
						
					}else if(data.fields.location_type != null){
						
						$("."+ref).html(data.fields.location_type);
						
					}else if(data.fields.import_date != null){
						
						$("."+ref).html(data.fields.import_date);
						
					}else if(data.fields.accounts != null){
						
						$("."+ref).html(data.fields.accounts);
						$(".select2").select2();
						
					}else{
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
					console.log(data.operators);
					
				},
				error : function(data){
					
					console.log(data);
					
				}
				
			});
			
		});
		$(document).on("change",".loc_filter_adjus_dyn",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("lo_id");
			var lopid = $(this).attr("lopidadjus");
			var col_val = "";
			$("."+ref).show();
			
			if(column == "shipperpo"){
				col_val = "shipperpo-text";
			}
			if(column == "shippmentdate"){
				col_val = "shippmentdate-date";
			}
			if(column == "pronum"){
				col_val = "pronum-text";
			}
			if(column == "tlocation"){
				col_val = "tlocation-select";
			}
			if(column == "flocation"){
				col_val = "flocation-select";
			}
			if(column == "quantity"){
				col_val = "quantity-number";
			}
			if(column == "reportdate"){
				col_val = "reportdate-date";
			}
			
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions_dyn",
				dataType : 'json',
				data : {column : col_val,form_type : "adjus",table:"tbl_adjustments","onchangeColref":"updateonchangeConditionLocation_adjus",uopid:lopid},
				success : function(data){
					console.log(data);
					$(".dyn"+ref).html(data.operators);
					
					if(data.fields.locnames != null){
						
						$("."+ref).html(data.fields.locnames);
						$(".select2").select2();
						
					}else if(data.fields.status != null){
						
						$("."+ref).html(data.fields.status);
						
					}else if(data.fields.location_type != null){
						
						$("."+ref).html(data.fields.location_type);
						
					}else if(data.fields.import_date != null){
						
						$("."+ref).html(data.fields.import_date);
						
					}else if(data.fields.accounts != null){
						
						$("."+ref).html(data.fields.accounts);
						$(".select2").select2();
						
					}else{
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
		});
		
		
		
	
	</script>