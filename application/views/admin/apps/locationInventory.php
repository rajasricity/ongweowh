<?php admin_header(); ?> 

           
<?php 
$_SESSION['appid'] = $l[0]['appId'];
admin_sidebar(); 
$appid = $l[0]['appId'];
$aid = $this->uri->segment(4);

$tasksCount = $this->mongo_db->where(["table"=>"tbl_inventory","appId"=>$_SESSION['appid']])->count("tbl_tasks");

$invHistory = $this->mongo_db->order_by(["_id"=>"desc"])->limit(10)->get_where("tbl_inventory_update_history",["appId"=>$appid]);


$this->mongo_db->switch_db($database);
$fdata = $this->mongo_db->get_where("settings",array("table"=>"tbl_inventory"))[0];

$mdb = mongodb;
$lcolumns = $this->admin->getRow("",["table"=>"tbl_inventory"],[],$database.".settings");

$times = ['12:00am','12:15am','12:30am','12:45am','01:00am','01:15am','01:30am','01:45am','02:00am','02:15am','02:30am','02:45am','03:00am','03:15am','03:30am','03:45am','04:00am','04:15am','04:30am','04:45am','05:00am','05:15am','05:30am','05:45am','06:00am','06:15am','06:30am','06:45am','07:00am','07:15am','07:30am','08:00am','08:15am','08:30am','08:45am','09:00am','09:15am','09:30am','10:00am','10:15am','10:30am','10:45am','11:00am','11:15am','11:30am','11:45am','12:00pm','12:15pm','12:30pm','12:45pm','01:00pm','01:15pm','01:30pm','01:45pm','02:00pm','02:15pm','02:30pm','02:45pm','03:00pm','03:15pm','03:30pm','03:45pm','04:00pm','04:15pm','04:30pm','04:45pm','05:00pm','05:15pm','05:30pm','05:45pm','06:00pm','06:15pm','06:30pm','06:45pm','07:00pm','07:15pm','07:30pm','08:00pm','08:15pm','08:30pm','08:45pm','09:00pm','09:15pm','09:30pm','10:00pm','10:15pm','10:30pm','10:45pm','11:00pm','11:15pm','11:30pm','11:45pm'];

$mng = $this->admin->Mconfig();
$row = $this->admin->getRow($mng,["table"=>"tbl_inventory"],[],$database.".settings");
$labels = $row->labels;
$columns_import = $row->columns;

?> 
<style>
.modal-dialog1 {
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
  max-width: 100%;	
}

.modal-content1 {
  height: auto;
  min-height: 100%;
  border-radius: 0;
}	

.modal-open .select2-dropdown {
z-index: 10060;
}

.modal-open .select2-close-mask {
z-index: 10055;
}	
	
.progress-outer{
    background: #fff;
    border-radius: 50px;
    padding: 10px;
    margin: 10px 0;
    box-shadow: 0 0  10px rgba(209, 219, 231,0.7);
}
.progress{
    height: 27px;
    margin: 0;
    overflow: visible;
    border-radius: 50px;
    background: #eaedf3;
    box-shadow: inset 0 10px  10px rgba(244, 245, 250,0.9);
}
.progress .progress-bar{
    border-radius: 50px;
}
.progress .progress-value{
    position: relative;
    left: -38px;
    top: 4px;
    font-size: 14px;
    font-weight: bold;
    color: #fff;
    letter-spacing: 2px;
}
.progress-bar.active{
    animation: reverse progress-bar-stripes 0.40s linear infinite, animate-positive 2s;
}
@-webkit-keyframes animate-positive{
    0% { width: 0%; }
}
@keyframes animate-positive {
    0% { width: 0%; }
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
                                    <h4 class="page-title"><? echo $l[0]["appname"] ?></h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/dashboard') ?>"><i class="fas fa-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/apps') ?>">Customers</a></li>
                                        <li class="breadcrumb-item active">Location Inventory</li>
                                    </ol>

                                </div>
                                <div class="col-sm-2"></div>
                                <div class="col-sm-4 invCount" style="display: none">
                                   
                                    <h4 class="page-title">Updating Location Inventory Count</h4>
                                    <div class="progress-outer">
										<div class="progress" style="width: 100%">
											<div class="progress-bar progress-bar-info progress-bar-striped active" style="box-shadow:-1px 5px 10px rgba(91, 192, 222, 0.7);"></div>
											
										</div>
<!--										<div class="progress-value">100%</div>-->
									</div>
                                </div>
                                
                                <? if(count($invHistory) > 0){ ?>
                                
									<div class="col-sm-4 invHistory" style="display: block">


	<!--                                    <h4 class="page-title">Updating Location Inventory Records</h4>-->
										<div class="" align="right">

											<a class="btn btn-info" href="javascript:void(0)" data-toggle="modal" data-target="#inventoryHistory">Inventory Update History</a><br>
											<small style="font-weight: 600">Last Updated At : <? echo date("m-d-Y H:i:s",strtotime($invHistory[0]["endTme"])) ?></small>									
										</div>
									</div>
                                <? } ?>
                            </div>
                        </div>
                        <!-- end row -->
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body" style="padding:0px;">
                                    
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs active nav-tabs-custom" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#messages1" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                              <span class="d-none d-sm-block"><i class="dripicons-location"></i> Location Inventory</span>   
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#import" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-download"></i></span>
                                                    <span class="d-none d-sm-block"><i class="ti-import"></i> Import</span>   
                                                </a>
                                            </li>
                                           <!-- <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#settings" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-cogs"></i></span>
                                                    <span class="d-none d-sm-block"><i class="fa fa-cogs"></i> Settings</span>   
                                                </a>
                                            </li>-->
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#create" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-cogs"></i></span>
                                                    <span class="d-none d-sm-block"><i class="fa fa-plus"></i> Add Inventory</span>   
                                                </a>
                                            </li>
                                            
                                             <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#tasks" role="tab">
                                                    <span class="d-none d-sm-block"><i class="mdi mdi-clipboard-text-outline"></i> Tasks <? if($tasksCount > 0){ ?><i class="badge badge-success" style="font-size: 13px;border-radius: 10px;"> <? echo $tasksCount ?> </i> <? } ?></span>   
                                                </a>
                                            </li>
                                            
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#fields" role="tab">
                                                    <span class="d-none d-sm-block"><i class="mdi mdi-clipboard-text-outline"></i> Fields</span>   
                                                </a>
                                            </li>
                                        </ul>
        
                                        <!-- Tab panes -->
                                        <div class="tab-content">

                                            <div class="tab-pane active p-3" id="messages1" role="tabpanel">
                                                
                                                <div class="row">
													<div class="col-lg-12">
														<div class="">
															<div class="card-body" style="padding:0px;">
															<div class="row">
																<div class="col-md-4">
																	<a href="#" style="color:red" onclick="openFilter();">Add Filters</a>
																</div>
																
																<div class="col-md-4" align="center">
																	
																	<div class="form-group">
																		<select class="form-control" id="sItem" style="background-color: #f1f1f1">
																			
<? $sitems = $this->mongo_db->get_where("tbl_items",["status"=>"Active"]); 
																			
																			   foreach($sitems as $item){
																				   
																				   echo '<option value="'.$item["item_name"].'">'.$item["item_name"].'</option>';
																				   
																			   }	
																			?>
																			
																		</select>
																		
																	</div>
																	
																</div>
																
																<div class="col-md-4 text-right">
																	<a href="#" style="color:green" onclick="window.location.reload();">Reset</a>
																</div>
															</div>


															<div class="row" id="bulkActions" style="display: none">

																<div class="col-md-2"><p style="font-weight: 600; margin-top: 8px">With Checked (<span class="count"></span>)</p></div>	
																<div class="col-md-4">
																	<a class="btn btn-info updateBulk" data-toggle="modal" data-target="#modal-fullscreen" href="javascript:void(0)"><i class="fa fa-edit"></i> Update</a>
																	<a class="btn btn-danger deleteBulk" href="javascript:void(0)"><i class="fa fa-trash"></i> Delete</a>

																</div>

																<div class="col-md-6" align="left">

																	<div class="berror"></div>
																	<div class="bloader" style="display: none">
																		<center><img src="<? echo base_url('assets/images/loader.gif') ?>" width="50" height="50" ></center>
																	</div>

																</div>

															</div>													
<!-- <?
// echo "<pre>";
// print_r($labels);
// print_r($columns);
?> -->
																<div class="table-rep-plugin">
																<div class="table-responsive allLoc">
																<table class="table mb-0 table-bordered" style="width:800px" id="inventoryTable">
																	<thead class="thead-light">
																		<tr>
																			<th style="width:10px !important" data-orderable="false"><input type="checkbox" id="selectAll"></th>
																			<th style="width:10px !important" data-orderable="false">Actions</th>
																			<th style="width:10px !important">Inventory ID</th>
																			<? $i=0; foreach($columns as $key=>$value){

																				if($key == "_id"){?>
<!--																			<th style="width:100px"></th>-->
																				<?}else{?>
																					<th style="width:100px" class="filter"><? echo $labels[$i]; ?></th>
																				<? $i++; }?>
																			<?}?>
																		</tr>
																	</thead>
																	
																	<tfoot>
																		<tr style="background-color: #f1f1f1">
																			<th style="width:10px !important"></th>
																			<th style="width:10px !important">Actions</th>
																			<th style="width:10px !important">Inventory ID</th>
																			<? $i=0; foreach($columns as $key=>$value){
																				if($key == "_id"){?>
<!--																					<th style="width:100px"></th>-->
																				<?}else{?>
																					<th style="width:100px"><? echo $labels[$i]; ?></th>
																				<? $i++; }?>
																			<?}?>
																		</tr>
																	</tfoot>
																</table>
																</div>
																</div>
																
																

															</div>
														</div>
													</div>
												</div>
                                                
                                            </div>

                                            <div class="tab-pane p-3" id="import" role="tabpanel">
<!-- <?
$mng = $this->admin->Mconfig();
$row = $this->admin->getRow($mng,["table"=>"tbl_inventory"],[],$database.".settings");
$labels = $row->labels;
$columns_import = $row->columns;
?> -->

<div id="screen1">
<!-- <form method="post" action="<? echo base_url('admin/ImportData/uploadFile') ?>" enctype="multipart/form-data"> -->
<form id="fileinfo" method="post" enctype="multipart/form-data">
                                            	<input type="hidden" name="appId" value="<? echo $aid ?>">
												   <div class="row">

														 <div class="col-md-6"> 

															<div class="form-group">
									<label>Select a spreadsheet (<small style="color:red;font-size: 14px;"> in a XLSX format </small>) to import</label>
<input type="file" class="form-control" name="ldata" id="ldata" style="height: 40px;width: 60%"  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required onchange="uploadDocument();">
															</div>
<br>
<h6>Does the Excel have a row at the top with a name for each column?</h6>
<select name="headers" class="form-control" style="width: 60%">
	<option value="1">Yes, the headers are on row 1.</option>
	<option value="2">Yes, the headers are on row 2.</option>
	<option value="0">Nope, the spreadsheet doesn't have a headers row</option>
</select>
<br>
<h6>Select a field to match records</h6>
<select name="field" class="form-control" style="width: 60%">
	<option value="0">Default, add all imported</option>
	<? foreach($labels as $key=>$value){
		if($columns_import[$key] != "location"){
	?>
	<option value="<? echo $columns_import[$key]; ?>"><? echo $value; ?></option>
	<?}}?>
	<!-- <option value="2">Yes, the headers are on row 2.</option>
	<option value="0">Nope, the spreadsheet doesn't have a headers row</option> -->
</select>
<p>Match a field to a column from your CSV. The import will use this match to search for an existing record to update. A new record will be added if no match exists.</p>

<button class="btn btn-primary arrow-none waves-effect waves-light" type="submit" id="iSubmit" disabled="disabled">
<i class="dripicons-upload"></i> Upload</button>
															
															<div class="mloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 25%"></div>
															<div class="merror"></div>

														 </div>
                                          
                                          
													    <div class="col-md-3">
													    	
													    </div> 
                                          
                                          				<div class="col-md-3 m-t-30" align="right">
                                          					
                  <a href="<? echo base_url('assets/downloads/LocationInventory.xlsx') ?>" class="btn btn-info">
                              <i class="ion ion-ios-download"></i> Download Template
                              </a>
                                          					
                                          				</div>
                                           
													</div>
                                           
												</form> 
</div>
<div id="screen2" style="display: none">
	<div class="row">
	<div class="col-md-8">
		<h5>Import new records</h5>
	</div>
	<div class="col-md-4 text-right">
		<i class="fa fa-times fa-2x" onclick="showScreens();"></i>
	</div>
	</div>
	<hr/>
	<p><b>Map</b> each column to an existing <b>field</b>.</p>
	<table class="table table-bordered" style="display: none">
		<tr>
			<td id="clmns">
				<select name="column[]" class="form-control">
						<option value="0">Default</option>
	<? foreach($labels as $key=>$value){
		if($columns_import[$key] != "location" && $columns_import[$key] != "issues" && $columns_import[$key] != "returns" && $columns_import[$key] != "adjustments" && $columns_import[$key] != "transfer_ins" && $columns_import[$key] != "transfer_outs" && $columns_import[$key] != "ending_balance"){
					?>
	<option value="<? echo $columns_import[$key]; ?>"><? echo $value; ?></option>
	<?}}?>
				</select>
			</td>
		</tr>
	</table>
<form id="formstep2">
<!-- <form action="<? echo base_url('admin/ImportData/submitStep2') ?>" method="post"> -->
<input type="hidden" name="table" id="table" value="tbl_inventory">
<input type="hidden" name="app" id="app" value="">
<input type="hidden" name="field" id="field" value="">
<input type="hidden" name="file" id="file" value="">
<input type="hidden" name="row" id="row" value="">
<div class="table-responsive" style="max-height: 400px;overflow-y: scroll;">
	<table class="table table-bordered" id="utab">
		<thead>
			<tr id="header"></tr>
			<tr id="sets"></tr>
		</thead>
		<tbody id="map">
		
		</tbody>
	</table>
	<div style="clear:both"></div>
</div>
<div class="row" style="margin-top:20px;">
	<div class="col-md-9">
<div class="alert alert-danger" style="display: none" id="emsg"></div>
<div class="alert alert-success" style="display: none" id="smsg"></div>
<div id="errorTable"></div>
	</div>
	<div class="col-md-1 text-right">
<img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 80%;margin-top:-10px;display:none" id="loader">
	</div>
	<div class="col-md-2 text-right">
	<input type="submit" name="submit" class="btn btn-primary right impsubmit" value="Submit">	
	</div>
</div>
</form>
</div>  



                                            
											</div>

											<div class="tab-pane p-3" id="settings" role="tabpanel">
												<form id="createColumn">
												<input type="hidden" name="appid" value="<? echo $appid; ?>">
												<div class="row">
													<div class="col-md-3">
														Column Name <span style="color:red">*</span>
														<input type="text" name="cName" class="form-control" required="required">
													</div>
													<div class="col-md-3">
														Column Type <span style="color:red">*</span>
														<select name="cType" class="form-control">
															<option value="text">Short Text</option>
															<option value="number">Number</option>
															<option value="date">Date</option>
														</select>
													</div>
													<div class="col-md-2">
														<br/>
														<input type="submit" name="submit" class="btn btn-primary" value="Add Column">
													</div>
													<div class="col-md-4">
														<img src="<? echo base_url();?>assets/images/loader.gif" width="60" height="60" style="margin-top:12px;display: none" id="settings_loader">
														<div class="alert alert-danger" style="margin-top:20px;display: none" id="settings_emsg"></div>
														<div class="alert alert-success" style="margin-top:20px;display: none" id="settings_smsg"></div>
													</div>
												</div>
												</form>
												<hr/>
												<h4>Columns</h4>
												<div class="row">
												<? foreach($fdata["labels"] as $key=>$value){ ?>
												<div class="col-md-3" style="padding:5px">
													<? echo $value; ?>
													<span style="color:red;text-align: right;cursor: pointer" onclick="deleteColumn('<? echo $value; ?>');">
														<i class="fa fa-trash"></i>
													</span>		
												</div>
												<?}?>
												</div>

											</div>
                                     
                                      		<div class="tab-pane p-4" id="create" role="tabpanel">
<!-- <form action="<? echo base_url('admin/apps/addInventory') ?>" method="post"> -->
                                            	<form id="addInventory" method="post">
													<div class="row">

														<? 
														 
														   foreach($fdata["labels"] as $lkey => $fd){
															   
															 if($fdata["columns"][$lkey] == "location"){
																 
														?>		 
																<div class="col-md-3" style="margin-bottom: 20px;">
																	<b><? echo $fd ?></b>
																	<select class="form-control select2 getLocation" name="<? echo $fdata["columns"][$lkey] ?>" required>

																		<option value="">Select Location</option>
																		<? foreach($locations as $loc){ 

																			  echo '<option value="'.$loc->nameid.'">'.$loc->nameid.'</option>';	

																		   } ?>
																	</select>	 
																</div>	 	 	 
																 
														<?		 
															 }elseif($fdata["columns"][$lkey] == "item"){
																 
														?>		 
																<div class="col-md-3" style="margin-bottom: 20px;">
																	<b><? echo $fd ?></b>
																	<select class="form-control select2 additem" name="<? echo $fdata["columns"][$lkey] ?>" id="" disabled required>
																		<option value="">Select Item</option>
																		<?  foreach($sitems as $item){
																				   
																				   echo '<option value="'.$item["item_name"].'">'.$item["item_name"].'</option>';
																				   
																			   } ?>
																	</select>	 
																</div>	 
																
																<input type="hidden" name="<? echo $fdata["columns"][$lkey] ?>" class="additem1">	 	 	 
																 
														<?		 
															 }elseif($fdata["columns"][$lkey] == "starting_balance"){
									  
																echo '<div class="col-md-3" style="margin-bottom: 20px;"><b>'.$fd.'</b><input type="number" name="'.$fdata["columns"][$lkey].'" class="form-control" step="1" required></div>';	  

															 }else{  
														?>
														
															<div class="col-md-3" style="margin-bottom: 20px;">
																<b><? echo $fd ?></b>
<input type="<? echo $fdata["dataType"][$lkey] ?>" name="<? echo $fdata["columns"][$lkey] ?>" class="form-control <? echo (($fdata["columns"][$lkey] == "starting_balance")) ? 'starting_balance' : '' ?><? echo (($fdata["columns"][$lkey] == "loccode")) ? 'loccode' : '' ?><? echo (($fdata["columns"][$lkey] == "loctype")) ? 'loctype' : '' ?><? echo (($fdata["columns"][$lkey] == "notes")) ? 'notes' : '' ?><? echo (($fdata["columns"][$lkey] == "locname")) ? 'locname' : '' ?>" <? echo (($fdata["columns"][$lkey] == "locname") || ($fdata["columns"][$lkey] == "loccode") || ($fdata["columns"][$lkey] == "issues") || ($fdata["columns"][$lkey] == "returns") || ($fdata["columns"][$lkey] == "transfer_ins") || ($fdata["columns"][$lkey] == "transfer_outs") || ($fdata["columns"][$lkey] == "adjustments") || ($fdata["columns"][$lkey] == "ending_balance") || ($fdata["columns"][$lkey] == "loctype")) ? 'readonly' : '' ?> value="<? echo ($fdata['columns'][$lkey] == 'last_report_date') ? date('Y-m-d', time()):'' ?>">
															</div>
															
														<? }} ?>
													</div>

													<div class="row">
														<div class="col-md-9">
															<div class="mloader" style="display:none">
																<center><img src="<? echo base_url('assets/images/loader.gif') ?>" width="80" height="80" ></center>
															</div>
															<div class="merror"></div>
														</div>
														<div class="col-md-3 text-right">
															<input type="hidden" name="appId" value="<? echo $appid; ?>">
															<input type="hidden" name="deleted" value="0">
															<input type="hidden" name="cdate" value="<? echo date('Y-m-d h:i:s', time()); ?>">
															<input type="submit" name="submit" class="btn btn-primary" value="ADD INVENTORY">
														</div>
													</div>
                                           
												</form> 
                                            
											</div>
                                      
                                      		<div class="tab-pane p-3" id="tasks" role="tabpanel">
                                                
                                                <div class="row">
													<div class="col-lg-12">
														<div class="">
															<div class="card-body" style="padding:0px;">
					                                        
					                                        	<div class="row">
					                                        	
					                                        		<div class="col-md-3">
					                                        			
					                                        		</div>
					                                        		
					                                        		<div class="col-md-6">
					                                        			
					                                        			<h3 class="showAddtaskname" style="display: none; text-align: center">Add Task</h3>
					                                        			
					                                        		</div>
					                                        		
					                                        		<div class="col-md-3" align="right" style="margin-bottom: 10px">
					                                        		 
						                                        		<a class="btn btn-primary showAddTask" href="javascript:void(0)"><i class="dripicons-plus"></i> Add Task</a>
						                                        		<a class="btn btn-primary showAlltasks" style="display: none" href="javascript:void(0)">Back</a>

						                                        	</div>
						                                        	
						                                        </div>
										
																<div class="table-responsive allTasks row">
																	<div class="col-md-12">
																		<table class="table mb-0 table-bordered" id="tasksTable" style="width: 100%;">
																			<thead class="thead-light">
																				<tr>
																					<th>S No</th>
																					<th>Task Name</th>
																					<th>Schedule Type</th>
																					<th>Status</th>
																					<th>Next Run Date</th>
																					<th>Action</th>
																				</tr>
																			</thead>
																			<tbody>

																			</tbody>
																			<tfooter>
																				<tr style="background-color: #f1f1f1">
																					<th>S No</th>
																					<th>Task Name</th>
																					<th>Schedule Type</th>
																					<th>Status</th>
																					<th>Next Run Date</th>
																					<th>Action</th>												
																				</tr>
																			</tfooter>
																		</table>
																	</div>	
																</div>
																
																<div class="insTask" style="display: none">	
																   <div class="row">
																	  <section class="col-12">
																		<ul class="nav nav-tabs nav-tabs1 flex-nowrap" role="tablist">
																			<li role="presentation" class="nav-item">
																				<a href="#step1" class="nav-link active" data-toggle="tab" aria-controls="step1" role="tab"> Task </a>
																			</li>
																			<li role="presentation" class="nav-item">
																				<a href="#step2" class="nav-link nav-link1 disabled" data-toggle="tab" aria-controls="step2" role="tab"> Action </a>
																			</li>
																		</ul>
																		<form role="form" method="post" id="cTask">
																			<div class="tab-content py-2">
																				<div class="tab-pane active" role="tabpanel" id="step1">
<!--																					<h3>Step 1</h3>-->
																					
																					<div class="card" style="border-radius: 10px;margin-left:15%;margin-right:15%;box-shadow: 0px 0px 13px 0px rgba(159, 159, 173, 0.44);padding: 20px;">
																					
																						<div class="form-group" style="padding-left: 25%;padding-right: 25%;">
																						<label>Task Name</label>
																							<input type="text" id="task_name" name="task_name" class="form-control" required>
																							
																						</div>
																						
																						<div class="form-group" style="padding-left: 25%;padding-right: 25%;">
																						<label>Schedule</label>
																							<select name="schedule_type" class="form-control" required>
																								
																								<option value="daily">Daily</option>
																								<option value="weekly">Weekly</option>
																								<option value="monthly">Monthly</option>

																								
																							</select>
																							
																						</div>
																						
																						<div class="form-group" style="padding-left: 25%;padding-right: 25%;">
																						<label>Next Run Date</label>
																						
																							<div class="row">
																								
																								<div class="col-md-8">
																								
																									<input type="date" name="next_run_date" value="<? echo date('Y-m-d',time()) ?>" class="form-control">
																									
																								</div>
																								
																								<div class="col-md-4">
																									
																									<select name="next_run_time" class="form-control time">
																										<option value="<? echo date("H:ia") ?>"><? echo date("H:ia") ?></option>
																										<? foreach($times as $time){?>

																										<option value="<? echo $time; ?>"><? echo $time; ?></option>
																										<?}?>
																									</select>
																									
																								</div>
																								
																							</div>
																						
																							
																							
																							
																						</div>
																						
																						<div class="form-group" style="padding-left: 25%;padding-right: 25%;">
																						<label>Status</label>
																							<select name="status" class="form-control" required>
																								
																								<option value="on">ON (Task Is Running)</option>
																								<option value="off">OFF (Task Is Paused)</option>
																								
																							</select>
																							
																						</div>
																						
																						<div align="center">
																						<button type="button" class="btn btn-primary next-step">Next</button>
																						
																					</div>
																					</div>
																					
																					
																					
																				</div>
																				
																				<div class="tab-pane" role="tabpanel" id="step2">
																					
																					<div class="card" style="border-radius: 10px;margin-left:15%;margin-right:15%;box-shadow: 0px 0px 13px 0px rgba(159, 159, 173, 0.44);padding: 20px;">
																					
																					<div class="row">
																						
																						<div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px">
																							<label>Action</label>
																						</div>
																						
																						<div class="col-md-4">
																							<div class="form-group">

																								<select name="action" class="form-control">

																									<option value="update_each_record">Update Each Record</option>

																								</select>

																							</div>
																						</div>
																						
																						<div class="col-md-6"></div>
																						
																					</div>
																					
																					<div class="row lwhenOpen">
																						
																						<div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px">
																							<label>When</label>
																						</div>
																						
																						<div class="col-md-4" style="margin-top: 5px;">
																							
																							<p>Every Record. <a href="javascript:void(0)" class="lwhenCondition"><strong style="font-size: 18px">add criteria</strong></a></p>
																							
																						</div>
																						
																					</div>
																					<div class="row lwhenClose" style="display: none">
																						
																						<div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px">
																							<label>When</label>
																						</div>
																						
																						<div class="col-md-10">
																							<div class="row">
																							
																								<div class="col-md-4">
																									<div class="form-group">

																										<select name="cond_column[]" class="form-control lgetColumn" rid="getwhenRef">

																											<?  
																											   foreach($lcolumns->labels as $key => $labels){										if(($lcolumns->columns[$key] != "location") && ($lcolumns->columns[$key] != "loccode") && ($lcolumns->columns[$key] != "loctype") && ($lcolumns->columns[$key] != "issues") && ($lcolumns->columns[$key] != "returns") && ($lcolumns->columns[$key] != "transfer_ins") && ($lcolumns->columns[$key] != "transfer_outs") && ($lcolumns->columns[$key] != "adjustments")){			
																											?>

																												<option value="<? echo $lcolumns->columns[$key].'-'.$lcolumns->dataType[$key] ?>"><? echo $labels ?></option>

																											<? }} ?>

																										</select>

																									</div>
																								</div>
																								
																								<div class="col-md-3">
																									<div class="form-group opgetwhenRef">

																										<select name="condition[]" class="form-control onchangeCondition">
																										<? $operators = $this->common->getConditionbydatatype(""); 
																										
																											foreach($operators as $op){
																										?>
																											
																											<option value="<? echo $op ?>"><? echo $op ?></option>

																										<? } ?>
																										</select>

																									</div>
																								</div>
																								
																								<div class="col-md-3">
																									<div class="form-group getwhenRef onchangeConditionValue">

																										<? $locations = $this->mongo_db->get_where("tbl_locations",["status"=>"Active"]); ?>
																										
																										<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value[]" required>
																										
																											<? 
																											foreach($locations as $loc){	
																											
																												echo '<option value="'.$loc['locname'].'">'.$loc['locname'].'</option>';		
																											
																											}
																											?>
																										
																										</select>								

																									</div>
																								</div>
																								
																								<div class="col-md-2" align="right">
																								
																									<i class="fa fa-plus-circle addTaskbind" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>
																									
																									<i class="fa fa-times-circle lremoveWhencondition" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>
																									
																								</div>
																								
																							</div>	
																						</div>
																						
																					</div>
																					
																					<div class="addtask_wrapper"></div>
																					
																					<div class="row">
																						
																						<div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px">
																							<label>Values</label>
																						</div>
																						
																						<div class="col-md-10">
																							<div class="row">
																							
																								<div class="col-md-1" style="margin-top: 7px;font-size: 16px;font-weight: 400;">Set</div>
																								
																								<div class="col-md-3">
																									<div class="form-group">

																										<select name="scond_column[]" id="scond_val" class="form-control" wuid="getSetfield">

																											<?  
																											   foreach($lcolumns->labels as $key => $labels){
																												   
																												if(($lcolumns->columns[$key] != "location") && ($lcolumns->columns[$key] != "loccode") && ($lcolumns->columns[$key] != "loctype") && ($lcolumns->columns[$key] != "issues") && ($lcolumns->columns[$key] != "returns") && ($lcolumns->columns[$key] != "transfer_ins") && ($lcolumns->columns[$key] != "transfer_outs") && ($lcolumns->columns[$key] != "adjustments") && ($lcolumns->columns[$key] != "ending_balance")){   
																											?>

																												<option value="<? echo $lcolumns->columns[$key].'-'.$lcolumns->dataType[$key] ?>"><? echo $labels ?></option>

																											<? }} ?>

																										</select>

																									</div>
																								</div>
																								
																								<div class="col-md-3">
																									<div class="form-group">

																										<select name="ssetcondition[]" class="form-control getLocucolumns" uid="getConditionalst">

																											<option value="to a custom value">To a custom value</option>
																											<option value="to a field value">To a field value</option>

																										</select>

																									</div>
																								</div>
																								
																								<div class="col-md-3">
																									<div class="form-group getConditionalst getSetfield">

																										<? $locations = $this->mongo_db->get_where("tbl_locations",["status"=>"Active"]); ?>
																										
																										<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="ssetvalue[]" required>
																										
																											<? 
																											foreach($locations as $loc){	
																											
																												echo '<option value="'.$loc['locname'].'">'.$loc['locname'].'</option>';		
																											
																											}
																											?>
																										
																										</select>

																									</div>
																								</div>
																								
																								<div class="col-md-2" align="right">
																									<i class="fa fa-plus-circle addtask_set" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>
																								</div>
																								
																							</div>	
																						</div>
																						
																					</div>

																					<div class="addtaskset_wrapper"></div>

																						<div class="col-md-12" align="center">
																						
																							<input type="hidden" name="table" value="tbl_inventory">
																							<input type="hidden" name="cond_days[]">
																							
																							<button type="submit" class="btn btn-primary ubSubmit">Save Task</button>
																							<div class="ubloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 50px;height: 50px"></div>
																						</div>
																				</div>
																				
																				<div class="clearfix"></div>
																			</div>
																		</form>
																	  </section>      

																	</div>


																	<div class="row">

																		<div class="col-md-9">

																			<div class="stloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 50px;height: 50px"></div>
																			<div class="sterror"></div>

																		</div>

																		<div class="col-md-3" align="right">

																		</div>

																	</div>

															</div>	
																

															</div>
														</div>
													</div>
												</div>
                                                
                                            </div>     
                                            
                                       		                                       		<div class="tab-pane p-3" id="fields" role="tabpanel">
                                                
                                                <div class="row">
													<div class="col-lg-12">
														<div class="card-body" style="padding:0px;">

															<div class="table-responsive col-md-6" style="overflow-x: auto;max-height: 400px">
																
																<table class="table table-striped">
																	
																	<thead>
																		
																		<tr>
																			
																			<th>Field Type</th>
																			<th>Field Name</th>
																			<th>Action</th>
																			
																		</tr>
																		
																	</thead>
																	
																	<tbody>
																		<? 
																			foreach($lcolumns->labels as $key => $lc){ 
																		
																				/*$vrid = "";
																				
																				$vrules = $this->admin->getRow("",["table"=>"tbl_inventory","field"=>$lcolumns->columns[$key],"appId"=>$aid],[],"$mdb.tbl_validation_rules");
																				

																				if($vrules){

																					$vrid = $vrules->_id;

																				}*/

																				$crid = "";
																				
																				$crules = $this->admin->getRow("",["table"=>"tbl_inventory","field"=>$lcolumns->columns[$key],"appId"=>$aid],[],"$mdb.tbl_conditional_rules");

																				if($crules){

																					$crid = $crules->_id;

																				}
																		?>
																		
																		<tr>
																			
																			<td><? echo ucfirst($lcolumns->dataType[$key]) ?></td>
																			<td><a href="javascript:void(0)" class="<? if(($lcolumns->columns[$key] != "location") && ($lcolumns->columns[$key] != "locname") && ($lcolumns->columns[$key] != "loccode") && ($lcolumns->columns[$key] != "loctype") && ($lcolumns->columns[$key] != "issues") && ($lcolumns->columns[$key] != "returns") && ($lcolumns->columns[$key] != "transfer_ins") && ($lcolumns->columns[$key] != "transfer_outs") && ($lcolumns->columns[$key] != "adjustments") && ($lcolumns->columns[$key] != "ending_balance")){ ?>cmodal<? } ?>" crid="<? echo $crid ?>" colname="<? echo $lc ?>" fname="<? echo $lcolumns->columns[$key] ?>" style="font-size: 16px">
																				<strong><? echo $lc ?> &nbsp;&nbsp;

																					<? //echo ($vrid != "") ? '<i class="fa fa-check" data-toggle="tooltip" title="This field has validation rules"></i>' : '' ?>&nbsp;
																					<? echo ($crid != "") ? '<i class="fa fa-random" data-toggle="tooltip" title="This field has conditional rules"></i>' : '' ?>
																					
																				</strong>
																			</a>
																			</td>
																			<td>
																				 <button id="customDropdown" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-color: transparent; border-radius: 25px">
																				  <i class="fa fa-cog"></i><i class="fa fa-caret"></i>
																				</button>
																				<? if(($lcolumns->columns[$key] != "location") && ($lcolumns->columns[$key] != "locname") && ($lcolumns->columns[$key] != "loccode") && ($lcolumns->columns[$key] != "loctype") && ($lcolumns->columns[$key] != "issues") && ($lcolumns->columns[$key] != "returns") && ($lcolumns->columns[$key] != "transfer_ins") && ($lcolumns->columns[$key] != "transfer_outs") && ($lcolumns->columns[$key] != "adjustments") && ($lcolumns->columns[$key] != "ending_balance")){ ?>
																				<div class="dropdown-menu" aria-labelledby="customDropdown">
																				  <span class="dropdown-menu-arrow"></span>
<!--																				  <a class="dropdown-item vmodal" vrid="<? //echo $vrid ?>" fname="<? //echo $lcolumns->columns[$key] ?>" href="javascript:void(0)">Validation Rules</a>-->
																				  
																				  	<a class="dropdown-item cmodal" href="javascript:void(0)" crid="<? echo $crid ?>" colname="<? echo $lc ?>" fname="<? echo $lcolumns->columns[$key] ?>">Conditional Rules</a>

																				  	

																				</div><? } ?>
																			</td>
																			
																		</tr>
																		
																		<? } ?>
																		
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
						</div>                        
                        
                        
                    </div>
                    <!-- container-fluid -->

                </div>
                
<div class="modal fade" id="pickupModal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" style="width:100%">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #333547;color:#fff">
					<h5 class="modal-title mt-0" id="myLargeModalLabel">Update Inventory</h5>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color:#fff;">×</button>
				</div>
				<div class="modal-body">
<!-- <form action="<? echo base_url('admin/apps/uInventory') ?>" method="post"> -->
					<form id="uInventory" method="post">
						<div class="row">

							<? $fdata = $this->mongo_db->get_where("settings",array("table"=>"tbl_inventory","appId"=>$appid))[0]; 

							   foreach($fdata["labels"] as $lkey => $fd){ 									   
								  if($fdata["columns"][$lkey] == "location"){
							?>		 
									<div class="col-md-3" style="margin-bottom: 20px;">
										<b><? echo $fd ?></b>
										<select class="form-control select2 getuLocation" id="<? echo $fdata["columns"][$lkey] ?>" name="<? echo $fdata["columns"][$lkey] ?>" id="nloc" required>

											<option value="">Select Location</option>
											<? foreach($locations as $loc){ 
									echo '<option value="'.$loc['nameid'].'">'.$loc['nameid'].'</option>';	
											 } ?>
										</select>	 
									</div>	 	 	 

							<?		 
								 }elseif($fdata["columns"][$lkey] == "item"){

							?>		 
									<div class="col-md-3" style="margin-bottom: 20px;">
										<b><? echo $fd ?></b>
										<select class="form-control select2" id="<? echo $fdata["columns"][$lkey] ?>" name="<? echo $fdata["columns"][$lkey] ?>" required>

											<option value="">Select Item</option>
											<?  foreach($sitems as $item){
																				   
												   echo '<option value="'.$item["item_name"].'">'.$item["item_name"].'</option>';

											   } ?>
										</select>	 
									</div>	 	 	 

							<?		 
								 }elseif($fdata["columns"][$lkey] == "starting_balance"){
									  
									echo '<div class="col-md-3" style="margin-bottom: 20px;"><b>'.$fd.'</b><input type="number" name="'.$fdata["columns"][$lkey].'" id="'.$fdata["columns"][$lkey].'" class="form-control" step="1" required></div>';	  
  
									  
								 }else{  
							?>

								<div class="col-md-3" style="margin-bottom: 20px;">
									<b><? echo $fd ?></b>
									<input type="<? echo $fdata["dataType"][$lkey] ?>" id="<? echo $fdata["columns"][$lkey] ?>" name="<? echo $fdata["columns"][$lkey] ?>" class="form-control <? echo (($fdata["columns"][$lkey] == "loccode")) ? 'uloccode' : '' ?><? echo (($fdata["columns"][$lkey] == "loctype")) ? 'uloctype' : '' ?><? echo (($fdata["columns"][$lkey] == "notes")) ? 'unotes' : '' ?><? echo (($fdata["columns"][$lkey] == "locname")) ? 'ulocname' : '' ?>" <? echo (($fdata["columns"][$lkey] == "locname") || ($fdata["columns"][$lkey] == "loccode") || ($fdata["columns"][$lkey] == "issues") || ($fdata["columns"][$lkey] == "returns") || ($fdata["columns"][$lkey] == "adjustments") || ($fdata["columns"][$lkey] == "transfer_ins") || ($fdata["columns"][$lkey] == "transfer_outs") || ($fdata["columns"][$lkey] == "ending_balance") || ($fdata["columns"][$lkey] == "loctype")) ? 'readonly' : '' ?>>
								</div>

							<? }} ?>
						</div>

						<div class="row">
							<div class="col-md-9">
								<div class="uloader" style="display:none">
									<center><img src="<? echo base_url('assets/images/loader.gif') ?>" width="80" height="80" ></center>
								</div>
								<div class="uerror"></div>
							</div>
							<div class="col-md-3 text-right">
								<input type="hidden" name="udate" value="<? echo date('Y-m-d h:i:s', time()); ?>">
								<input type="hidden" name="id" id="lid">
								<input type="submit" name="submit" class="btn btn-primary" value="UPDATE INVENTORY">
							</div>
						</div>

					</form>
					
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div> 

	<!-- Filters Modal Start -->
 <div id="myFilter" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-lg" style="width: 60%">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header" style="display: block">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Add Filters</h4>
		  </div>
		  <div class="modal-body">

<form id="submitFilter" novalidate>
<!-- <form action="<? echo base_url('admin/apps/addFilter') ?>" method="post">
<input type="hidden" name="id" value="<? echo $appid; ?>"> -->
<div id="top">
<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first0">

<div class="row">
	<div class="col-md-1">
		<select name="cause[]" id="cause" class="form-control causeData" style="display: none"><option value="where">where</option></select>
		<p style="margin-top: 5px;font-weight: bold">Where</p>
	</div>
	<div class="col-md-3">
		<select name="field[]" class="form-control getLocdata fieldData loc_filter" id="updLoc1" lopid="updLoc1" lo_id="locgetwhenRef">
		    <option value="">Select</option>
		    <option value="id">Inventory Id</option>
			<option value="locname">Location Name</option>
			<option value="loccode">Location Code</option>
			<option value="loctype">Location Type</option>
			<option value="notes">Notes</option>
			<option value="last_report_date">Last Report Date</option>
			<option value="starting_balance">Starting Balance</option>
			<option value="issues">Shipments</option>
			<option value="returns">Pickups</option>
			<option value="transfer_ins">Transfer Ins</option>
			<option value="transfer_outs">Transfer Outs</option>
			<option value="adjustments">Adjustments</option>
			<option value="ending_balance">Ending Balance</option>
			<option value="audit_count2019">Audit Count</option>
			<option value="audit_date2019">Audit Date</option>
		</select>
	</div>
	<div class="col-md-3 dynlocgetwhenRef">
		<select name="value[]" id="value" class="form-control valueData">
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
		<div id="setDvalue">
			<select name="svalue[]" class="form-control select2 svalueData">
			<option value="">Select</option>
			<? 
				$this->mongo_db->switch_db($this->database);
				$ldata = $this->mongo_db->order_by(["locname"=>'asc'])->get("tbl_locations");
				
				foreach($ldata as $ld){
			
					echo '<option value="'.$ld['locname'].'">'.$ld['locname'].'</option>';	

				}
				
			?>
			
			</select>
		</div>
	</div>
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
	<!-- Filters Modal End -->                
                
 <div class="modal fade modal-fullscreen" id="modal-fullscreen" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="padding-left: 10px !important">
	  <div class="modal-dialog modal-dialog1" style="z-index: 9999">
		<div class="modal-content modal-content1">
		  <div class="modal-header card-header" style="display: block">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle" style="color: white"></i></button>
			<h4 class="modal-title" id="myModalLabel" style="text-align: center;color: white">Update Location Inventory Records</h4>
		  </div>
		  <hr>
		  <div class="modal-body">
		  	
		  	<div class="uberror" align="center"></div>
		  		
			<p style="text-align: center"><strong>Updating <span class="count"></span> records.</strong> Which values do you want to update?</p>
			
			<form method="post" id="updateLocrecords">
			<div class="card" style="border-radius: 10px;margin: 10px;box-shadow: 0px 0px 13px 0px rgba(159, 159, 173, 0.44);padding: 20px;">
			
				<div class="row">

<!--					<div class="col-md-2"></div>-->
				
					<div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>Set</label></div>
					<div class="col-md-3" align="left">

						<div class="form-group">

							<select class="form-control getColumn" name="columns[]">

								<?  
								   foreach($fdata["labels"] as $key => $labels){
									   
									   if(($fdata["columns"][$key] != "location") && ($fdata["columns"][$key] != "loccode") && ($fdata["columns"][$key] != "loctype") && ($fdata["columns"][$key] != "issues") && ($fdata["columns"][$key] != "returns") && ($fdata["columns"][$key] != "transfer_ins") && ($fdata["columns"][$key] != "transfer_outs") && ($fdata["columns"][$key] != "adjustments") && ($fdata["columns"][$key] != "ending_balance")){
								?>

									<option value="<? echo $fdata["columns"][$key]."-".$fdata["dataType"][$key] ?>"><? echo $labels ?></option>

								<? }} ?>
								
							</select>

						</div>

					</div>
					<div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>To</label></div>

					<div class="col-md-3" align="left">

						<div class="form-group bindField">

<!--							<input type="text" class="form-control" name="value[]">-->
							
							<select class="select2 form-control select2-multiple" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" required><option value="">Choose Selection</option><? foreach($locations as $location){ ?><option value="<? echo $location['locname']; ?>"><? echo $location['locname']; ?></option><? } ?></select>

						</div>

					</div>
					
					<div class="col-md-2"><i class="fa fa-plus-circle add_sheading" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div>
					
					
					<div class="col-md-2"></div>
					
			</div>
			
				<div class="field_wrapper"></div>
			
				<div class="col-md-12" align="center">
					<button type="submit" class="btn btn-primary ubSubmit">Update Records</button>
					<div class="ubloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 50px;height: 50px"></div>
				</div>
				
			</form>
			
		  </div>
		  <!--<div class="modal-footer" align="center">
			<button type="button"  class="btn btn-primary">Next</button>
		  </div>-->
		</div>
	  </div>                                           
	</div>
               
                <!-- content --> 	
<? admin_footer(); ?>
	 
<script src="<? echo base_url(); ?>assets/plugins/select2/js/select2.min.js"></script>
<script src="<? echo base_url(); ?>assets/js/cron/cron_inventorytasks.js"></script>
<script src="<? echo base_url(); ?>assets/js/vrules/vrules_inventory.js"></script>
<script src="<? echo base_url(); ?>assets/js/crules/crules_inventory.js"></script>


<!-- Inventory Update History -->


	<!-- Modal -->
 <div id="inventoryHistory" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg" style="width: 60%">

	<!-- Modal content-->
	<div class="modal-content">
	  <div class="modal-header" style="display: block">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Inventory Update History</h4>
	  </div>
	  <div class="modal-body">

		   <div class="row">
			  <section class="col-12">
					<div class="container">

						<table id="example" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
									<th>Started At</th>
									<th>Ended At</th>
									<th>Records Processed</th>
								</tr>
							</thead>
							<tbody>

							<? foreach($invHistory as $inh){ ?>
							
								<tr>
									
									<td><? echo date("m-d-Y H:i:s",strtotime($inh["startTime"])) ?></td>
									<td><? echo date("m-d-Y H:i:s",strtotime($inh["endTme"])) ?></td>
									<td><? echo $inh["count"] ?></td>
									
								</tr>
								
							<? } ?>
						
							</tbody>
						</table>		

					</div>

			  </section>      

			</div>



	  </div>
	</div>

  </div>
</div>





<!--  Edit Fields	-->
	
	 <div id="vmodal" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-lg" style="width: 60%">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header" style="display: block">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Field Options</h4>
		  </div>
		  <div class="modal-body">

			   <div class="row">
				  <section class="col-12">
					<ul class="nav nav-tabs flex-nowrap" role="tablist">
						<li role="presentation" class="nav-item">
							<a href="#vestep1" class="nav-link active" data-toggle="tab" aria-controls="step1" role="tab"> Validation Rules </a>
						</li>
						<!-- <li role="presentation" class="nav-item">
							<a href="#vestep2" class="nav-link" data-toggle="tab" aria-controls="step2" role="tab"> Conditional Rules </a>
						</li> -->	
					</ul>
					<form role="form" method="post" id="cvalidationRules">
						<div class="tab-content py-2">
						
							<div class="tab-pane active" role="tabpanel" id="vstep1">

								<div class="row updvalidationrules" style="margin-left: 20px; margin-top: 10px">

									<div class="col-md-12">
										
										<p>Use rules to validate the <strong class="fieldname"></strong> value.</p>
									
									</div>
									<div class="col-md-12 custom-control custom-checkbox custom-control-inline">
										
										<input type="checkbox" id="customRadioInline1" name="validationrule" class="custom-control-input">
										<label class="custom-control-label" for="customRadioInline1">Add field validation rules</label>
										
									</div>

								</div>
								<div class="validationruleclosed" style="display: none">
								
									<div class="row delSelRule" style="background-color: #ccc; padding: 12px;margin: 5px;border-radius: 5px;">
									
										<div class="col-md-1" align="center" style="margin-top: 5px;font-size: 16px;padding:0px;">
										
											<label>When</label>
											
										</div>

										<div class="col-md-10" style="background-color: #eee;">
										
											<div class="row" style="padding: 10px;margin-bottom: -10px;">

												<div class="col-md-4">
													<div class="form-group">

														<select name="cond_column1[]" class="form-control valLabels" rid="refvalLabels" rCount="1">

															<?  
															   foreach($lcolumns->labels as $key => $labels){										
															?>

															   		<option value="<? echo $lcolumns->columns[$key]."-".$lcolumns->dataType[$key] ?>"><? echo $labels ?></option>

															<? } ?>

														</select>

													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group oprefvalLabels">

														<select name="condition1[]" class="form-control onchangevrulesCondition" rCount="1">
														<? $operators = $this->common->getConditionbydatatype(""); 

															foreach($operators as $op){
														?>

															<option value="<? echo $op ?>"><? echo $op ?></option>

														<? } ?>
														</select>

													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group refvalLabels vrulesConditionValue">

														<input type="text" name="cond_value1[]" class="form-control">	

													</div>
												</div>

												<div class="col-md-2" align="right">
												
													<i class="fa fa-plus-circle addVlabels" rCount="1" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>

<!--												<i class="fa fa-times-circle remVlabels" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>  -->
													
												</div>

											</div>
											
											<div class="addedLabels"></div>		
										</div>
										
										<div class="col-md-1" align="right">
										
											<i class="fa fa-times-circle deleteallRules" delRule="delSelRule" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>
										
										</div>
										
										<div class="col-md-1" align="center" style="margin-top: 5px;font-size: 15px;padding:0px;">

										
											<label>Message</label>
											
										</div>
										
										<div class="col-md-10" style="background-color: #eee;">
											
											<textarea rows="6" cols="10" class="form-control" name="alertMessage1[]" style="margin:10px"></textarea>
											<input type="hidden" name="rulesCount[]" value="1">
										</div>
										
									</div>
									
								</div>

								<div class="addedrules"></div>
								
							
							<div class="clearfix"></div>
						</div>
						<div class="row">
							<div class="col-md-3"><button type="button" class="btn btn-default ubSubmit pull-left addRule" style="display:none; border-color:black">Add Rule</button></div>

							<div class="col-md-6" align="center">

								<input type="hidden" name="vrtable" value="tbl_inventory" id="vTable">
								<input type="hidden" name="appId" value="<? echo $aid ?>" id="vAppid">
								<input type="hidden" name="fieldname" id="fieldname">
								<input type="hidden" name="vid" id="vid">
								<button type="submit" class="btn btn-primary ufields" style="display: none">Update Fields</button>

							</div>										

							<div class="col-md-3"></div>
						</div>
					</form>
				  </section>      

				</div>


				<div class="row">

					<div class="col-md-9">

						<div class="vstloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 50px;height: 50px"></div>
						<div class="vsterror"></div>

					</div>

					<div class="col-md-3" align="right"></div>

				</div>
	
		  </div>
		</div>

	  </div>
	</div>

	<!-- Modal -->
 <div id="editTask" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-lg" style="width: 60%">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header" style="display: block">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Edit Task</h4>
		  </div>
		  <div class="modal-body">

			   <div class="row">
				  <section class="col-12">
					<ul class="nav nav-tabs flex-nowrap" role="tablist">
						<li role="presentation" class="nav-item">
							<a href="#estep1" class="nav-link active" data-toggle="tab" aria-controls="step1" role="tab"> Task </a>
						</li>
						<li role="presentation" class="nav-item">
							<a href="#estep2" class="nav-link" data-toggle="tab" aria-controls="step2" role="tab"> Action </a>
						</li>
						<li role="presentation" class="nav-item">
							<a href="#history" class="nav-link" data-toggle="tab" aria-controls="step2" role="tab"> History </a>
						</li>
						<li role="presentation" class="nav-item">
							<a href="#run" class="nav-link" data-toggle="tab" aria-controls="step2" role="tab"> Run </a>
						</li>
					</ul>
					<form role="form" method="post" id="uTask">
						<div class="tab-content py-2">
							<div class="tab-pane active" role="tabpanel" id="estep1">
			<!--																					<h3>Step 1</h3>-->


									<div class="form-group" style="padding-left: 25%;padding-right: 25%;">
									<label>Task Name</label>
										<input type="text" id="etask_name" name="task_name" class="form-control" required>

									</div>

									<div class="form-group" style="padding-left: 25%;padding-right: 25%;">
									<label>Schedule</label>
										<select name="schedule_type" id="eschedule_type" class="form-control" required>

											<option value="daily">Daily</option>
											<option value="weekly">Weekly</option>
											<option value="monthly">Monthly</option>

										</select>

									</div>

									<div class="form-group" style="padding-left: 25%;padding-right: 25%;">
									<label>Next Run Date</label>

										<div class="row">

											<div class="col-md-8">

												<input type="date" id="enext_run_date" name="next_run_date" value="<? echo date('Y-m-d',time()) ?>" class="form-control">

											</div>

											<div class="col-md-4">

												<select name="next_run_time" id="enext_run_time" class="form-control time">
													<option value="<? echo date("H:ia") ?>"><? echo date("H:ia") ?></option>
													<? foreach($times as $time){?>

													<option value="<? echo $time; ?>"><? echo $time; ?></option>
													<?}?>
												</select>

											</div>

										</div>

									</div>

									<div class="form-group" style="padding-left: 25%;padding-right: 25%;">
									
										<label>Status</label>
										<select name="status" id="estatus" class="form-control" required>

											<option value="on">ON (Task Is Running)</option>
											<option value="off">OFF (Task Is Paused)</option>

										</select>

									</div>

								
							</div>

							<div class="tab-pane" role="tabpanel" id="estep2">


								<div class="row">

									<div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px">
										<label>Action</label>
									</div>

									<div class="col-md-4">
										<div class="form-group">

											<select name="action" class="form-control">

												<option value="update_each_record">Update Each Record</option>

											</select>

										</div>
									</div>

									<div class="col-md-6"></div>

								</div>

									<div class="row elwhenOpen">

										<div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px">
											<label>When</label>
										</div>

										<div class="col-md-4" style="margin-top: 5px;">

											<p>Every Record. <a href="javascript:void(0)" class="elwhenCondition"><strong style="font-size: 18px">add criteria</strong></a></p>

										</div>

									</div>
									<div class="elwhenClose" style="display: none">

										<div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px">
											<label>When</label>
										</div>

										<div class="col-md-10">
											<div class="row">

												<div class="col-md-4">
													<div class="form-group">

														<select name="cond_column[]" class="form-control elgetColumn" rid="egetwhenRef">

															<?  
															   foreach($lcolumns->labels as $key => $labels){													
															?>

																<option value="<? echo $lcolumns->columns[$key]."-".$lcolumns->dataType[$key] ?>"><? echo $labels ?></option>

															<? } ?>

														</select>

													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group eopgetwhenRef">

														<select name="condition[]" class="form-control ">
														<? $operators = $this->common->getConditionbydatatype(""); 

															foreach($operators as $op){
														?>

															<option value="<? echo $op ?>"><? echo $op ?></option>

														<? } ?>
														</select>

													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group egetwhenRef">

														<input type="text" name="cond_value[]" class="form-control">	

													</div>
												</div>

												<div class="col-md-2" align="right">
													<i class="fa fa-plus-circle eaddTaskbind" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>

													<i class="fa fa-times-circle elremoveWhencondition" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>
												</div>

											</div>	
										</div>

									</div>

								
								<div class="eaddtask_wrapper"></div>

								<div class="updatedValues"></div>
								
								<div class="eaddtaskset_wrapper"></div>

							
							<div class="clearfix"></div>
						</div>

							<div class="tab-pane" role="tabpanel" id="history">
			
								<div class="container">
									
									<table id="example" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th>Started At</th>
												<th>Ended At</th>
												<th>Status</th>
												<th>Records Processed</th>
											</tr>
										</thead>
										<tbody id="thistory">
											
										</tbody>
									</table>		
									
								</div>
						
							</div>
											
							<div class="tab-pane" role="tabpanel" id="run">
			
								<div class="container">
									
									<p>Click below to run this task immediately. The task will continue to run on the schedule you've defined.</p>
									
									<div align="left">
										
										<div class="taskLoader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 50px;height: 50px"></div>
										<div id="cerr"></div>
										<a href="javascript:void(0)" class="btn btn-primary" id="runTask">Run this task</a>
										
									</div>
									
								</div>
						
							</div>				
							
							<div class="col-md-12" align="center">

								<input type="hidden" name="table" value="tbl_inventory">
								<input type="hidden" name="task_id" id="task_id">
								<button type="submit" class="btn btn-primary ubSubmit">Update Task</button>
								
							</div>										
					</form>
				  </section>      

				</div>


				<div class="row">

					<div class="col-md-9">

						<div class="ustloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 50px;height: 50px"></div>
						<div class="usterror"></div>

					</div>

					<div class="col-md-3" align="right"></div>

				</div>
	
		  </div>
		</div>

	  </div>
	</div>

<!-- Conditions Modal Start -->
	
 <div id="cmodal" class="modal fade" role="dialog">
  
  
  	  <div class="modal-dialog modal-lg" style="width: 60%">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header" style="display: block">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Field Options</h4>
		  </div>
		  <div class="modal-body">

			   <div class="row">
				  <section class="col-12">
					<ul class="nav nav-tabs flex-nowrap" role="tablist">
						<li role="presentation" class="nav-item">
							<a href="#vestep1" class="nav-link active" data-toggle="tab" aria-controls="step1" role="tab"> Conditional Rules </a>
						</li>
						<!-- <li role="presentation" class="nav-item">
							<a href="#vestep2" class="nav-link" data-toggle="tab" aria-controls="step2" role="tab"> Conditional Rules </a>
						</li> -->	
					</ul>
					<form role="form" method="post" id="cconditionalRules" novalidate>
						<div class="tab-content py-2">
						
							<div class="tab-pane active" role="tabpanel" id="vstep1">

								<div class="row updcvalidationrules" style="margin-left: 20px; margin-top: 10px">

									<div class="col-md-12">
										
									<p>Use rules to set the values of the <b><span class="column_name"></span></b> field based on other record values. </p>
									
									</div>
									<div class="col-md-12 custom-control custom-checkbox custom-control-inline">
										
										<input type="checkbox" id="condRulecheck" name="conditionalrule" class="custom-control-input">
										<label class="custom-control-label" for="condRulecheck">Add field conditional rules</label>
										
									</div>

								</div>
								<div class="conditionruleclosed" style="display: none">
								
									<div class="row delSelCondRule" style="background-color: #ccc; padding: 12px;margin: 5px;border-radius: 5px;">
									
										<div class="col-md-1" align="center" style="margin-top: 5px;font-size: 16px;padding:0px;">
										
											<label>When</label>
											
										</div>

										<div class="col-md-10" style="background-color: #eee;">
										
											<div class="row" style="padding: 10px;margin-bottom: -10px;">

												<div class="col-md-4">
													<div class="form-group">

														<select name="cond_column1[]" class="form-control valConLabels" rid="refconvalLabels" rCount="1">

															<?  
															   foreach($lcolumns->labels as $key => $labels){										
															?>

															   		<option value="<? echo $lcolumns->columns[$key]."-".$lcolumns->dataType[$key] ?>"><? echo $labels ?></option>

															<? } ?>

														</select>

													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group oprefconvalLabels">

														<select name="condition1[]" class="form-control onchangecrulesCondition" rCount="1">
														<? $operators = $this->common->getConditionbydatatype(""); 

															foreach($operators as $op){
														?>

															<option value="<? echo $op ?>"><? echo $op ?></option>

														<? } ?>
														</select>

													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group refconvalLabels crulesConditionValue">

														<input type="text" name="cond_value1[]" class="form-control">	

													</div>
												</div>

												<div class="col-md-2" align="right">
												
													<i class="fa fa-plus-circle addClabels" rCount="1" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>

<!--												<i class="fa fa-times-circle remVlabels" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>  -->
													
												</div>

											</div>
											
											<div class="addedConLabels"></div>		
										</div>

										
										<div class="col-md-1" align="right">
										
											<i class="fa fa-times-circle deleteallCRules" delRule="delSelCondRule" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>
										
										</div>
										
										<div class="col-md-1" align="center" style="margin-top: 5px;font-size: 15px;padding:0px;">

											<label>Values</label>
											
										</div>
										
										<div class="col-md-10" style="background-color: #eee;">
											<div class="row">
																							
												<div class="col-md-1" style="margin-top: 7px;font-size: 16px;font-weight: 400;">Set</div>

												<div class="col-md-3">
													<div class="form-group">

														<select name="ssetcondition1[]" class="form-control getConditionalLabels" uid="getconConditionalst" rcount="1">

															<option value="to a custom value">To a custom value</option>
															<option value="to a field value">To a record value</option>

														</select>

													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group getconConditionalst">

														<input type="text" name="ssetvalue1[]" class="form-control">

													</div>
												</div>

												<div class="col-md-2" align="right">
												</div>

											</div>
											
											<div class="addedConsetLabels"></div>
											
											<input type="hidden" name="rulesCCount[]" value="1">
										</div>
										
									</div>
									
								</div>

								<div class="addedcrules"></div>
								
							
							<div class="clearfix"></div>
						</div>
						<div class="row">
							<div class="col-md-3"><button type="button" class="btn btn-default ubcSubmit pull-left addCRule" style="display:none; border-color:black">Add Rule</button></div>

							<div class="col-md-6" align="center">

								<input type="hidden" name="contable" value="tbl_inventory" id="cTable">
								<input type="hidden" name="appId" value="<? echo $aid ?>" id="cAppid">
								<input type="hidden" name="fieldname" id="confieldname">
								<input type="hidden" name="conid" id="conid">
								<button type="submit" class="btn btn-primary ufields1" style="display: block">Update Field</button>

							</div>										

							<div class="col-md-3"></div>
						</div>
					</form>
				  </section>      

				</div>


				<div class="row">

					<div class="col-md-9">

						<div class="cstloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 50px;height: 50px"></div>
						<div class="csterror"></div>

					</div>

					<div class="col-md-3" align="right"></div>

				</div>
	
		  </div>
		</div>

	  </div>

  	</div>
	
<!-- Conditions Modal End -->	
<input type="hidden" name="base_url" id="base_url" value="<? echo base_url() ?>">
<input type="hidden" name="updatedWhencount" id="updatedWhencount" value="1"> 
<input type="hidden" name="updatedValuescount" id="updatedValuescount" value="1"> 

<script>
	
	$(document).ready(function(){
		
		setInterval(function(){
            $.ajax({
                type : "POST",
				data : {appId:"<? echo $appid ?>"},
                url : "<? echo base_url('admin/cron/getProgresscount') ?>",
                success : function(response){ 
					
					if(response == 0){
					
						$(".invCount").hide();
						return false;
						
					}
					
					if(response == 100){
						
						$(".invCount").html('<div class="alert alert-success">Location Inventory Count Updated Successfully</div>')
						setTimeout(function(){ location.reload() },2000);
						
					}else{
						
						$(".invCount").show();
						$(".invHistory").hide();
						$(".progress-bar-info").width(response+"%");
						$(".progress-bar-info").html("<strong>"+response+"%</strong>");
						
					}
					
//					console.log(response);
					
//                    $("body").html(response);
                }
            });
        },10000);	
		
	})
	
// task start
	
	$("#tasksTable").DataTable({
		
		/*"dom": 'Bfrtip',
		 buttons: [
				'csv', 'excel','pageLength'
			],*/
		 "bProcessing": true,
         "sAjaxSource": "<? echo base_url(); ?>admin/tasks/getAlltasks/tbl_inventory",
         "aoColumns": [
         	   { mData: 'sno'},
         	   { mData: 'task_name'},
               { mData: 'schedule_type' },
               { mData: 'status' } ,
               { mData: 'next_run_date' } ,
               { mData: 'actions' } ,
             ],
             
          "bLengthChange": true,
		
	});
	
	$(".showAddTask").click(function(){
		
		$(".insTask").show();	
		$(".allTasks").hide();	
		$(".showAddTask").hide();	
		$(".showAlltasks").show();	
		$(".showAddtaskname").show();	
		
	});
	
	$(".showAlltasks").click(function(){
		
		$(".insTask").hide();	
		$(".allTasks").show();	
		$(".showAddTask").show();	
		$(".showAlltasks").hide();
		$(".showAddtaskname").hide();
		
	});
	
	$(".getLocation").change(function(){
		
		var location = $(this).val();
		
		$.ajax({
			
			type : "post",
			url : "<? echo base_url('admin/apps/getInventorylocation') ?>",
			data : {location : location},
			dataType : 'json',
			success : function(data){
				
				$(".locname").val(data.locname);
				$(".loccode").val(data.loccode);
				$(".loctype").val(data.type);
				$(".notes").val(data.notes);
				
				console.log(data);
			},
			error : function(data){
				
				console.log(data);
				
			}
			
		});
		
	});
	
	$(".getuLocation").change(function(){
		
		var location = $(this).val();
		
		$.ajax({
			
			type : "post",
			url : "<? echo base_url('admin/apps/getInventorylocation') ?>",
			data : {location : location},
			dataType : 'json',
			success : function(data){
				
				$(".ulocname").val(data.locname);
				$(".uloccode").val(data.loccode);
				$(".uloctype").val(data.type);
				$(".unotes").val(data.notes);
				
				console.log(data);
			},
			error : function(data){
				
				console.log(data);
				
			}
			
		});
		
	});
	
// bulk operation starts
	
	$(".select2").select2();


	$("#updateLocrecords").submit(function(e){
		
		e.preventDefault();
		var fdata = $(this).serialize();
		
		var locations = [];
			$.each($("input[name='lid']:checked"), function(){
			locations. push($(this). val());
		});
		
		
		var form_data = fdata+'&'+$.param({ 'targets': locations,'table' : 'tbl_inventory' })
		
		$.ajax({
			
			type : "post",
			url : "<? echo base_url('admin/locations/updateLocbulkrecords') ?>",
			data : form_data,
			beforeSend : function(){
				
				$(".ubSubmit").hide();	
				$(".ubloader").show();
				

			},
			success : function(data){
				console.log(data);	
				$(".ubloader").hide();
				$(".ubSubmit").show();
				
				if(data == "success"){

					$(".uberror").html('<div class="alert alert-success">selected location inventory updated successfully</div>')
					setTimeout(function(){ location.reload() },2000);

				}else{

					$(".uberror").html('<div class="alert alert-danger">error occured</div>')

				}

			},
			error : function(data){
				
				console.log(data);	
				$(".ubloader").hide();
				$(".ubSubmit").show();

			}
		});
		
	})
	
	$(".getColumn").change(function(){
		
		var value = $(this).val();
		
		var column = value.split("-")[0];
		var datatype = value.split("-")[1];
		
		
		 if(column == "item"){	
			
			$(".bindField").html('<div class="form-group bindField"><select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" required><option value="">Choose Item</option><? foreach($items as $item){ ?><option value="<? echo $item->item_name; ?>"><? echo $item->item_name; ?></option><? } ?></select></div>');
			
		} else if(column == "locname"){
			
			$(".bindField").html('<div class="form-group bindField"><select class="select2 form-control select2-multiple" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" required><option value="">Choose Selection</option><? foreach($locations as $location){ ?><option value="<? echo addslashes($location['locname']); ?>"><? echo addslashes($location['locname']); ?></option><? } ?></select></div>');		
			
		} else if(column == "adjdirection"){
			
			$(".bindField").html('<div class="form-group bindField"><select class="select2 form-control select2-multiple" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" required><option value="">Select ...</option><option value="IN">IN</option><option value="OUT">OUT</option></select></div>');
			
		} else{
			
			$(".bindField").html('<input type="'+datatype+'" class="form-control" name="value[]">');
			
		}
		
		$(".select2").select2();
		
	});
	
	$(".field_wrapper").on("change",".getupdatedColumn",function(){
		
		var ref = ($(this).attr("ref"));
		
		var value = $(this).val();
		
		var column = value.split("-")[0];
		var datatype = value.split("-")[1];
		
		if(column == "item"){	
			
			$("."+ref).html('<div class="form-group '+ref+'"><select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" required><option value="">Choose Item</option><? foreach($items as $item){ ?><option value="<? echo $item->item_name; ?>"><? echo $item->item_name; ?></option><? } ?></select></div>');
			
		} else if(column == "locname"){
			
			$("."+ref).html('<div class="form-group '+ref+'"><select class="select2 form-control select2-multiple" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" required><option value="">Choose Selection</option><? foreach($locations as $location){ ?><option value="<? echo addslashes($location['locname']); ?>"><? echo addslashes($location['locname']); ?></option><? } ?></select></div>');
			
		}else if(column == "adjdirection"){
			
			$("."+ref).html('<div class="form-group '+ref+'"><select class="select2 form-control select2-multiple" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" required><option value="">Select ...</option><option value="IN">IN</option><option value="OUT">OUT</option></select></div>');
			
		}else{
			
			$("."+ref).html('<input type="'+datatype+'" class="form-control" name="value[]">');
			
		}
		
		$(".select2").select2();
		
	});

	$(".deleteBulk").click(function(){
		
		var locations = [];
			$.each($("input[name='lid']:checked"), function(){
			locations. push($(this). val());
		});
		
		
		if(locations.length > 0){
		
			$.ajax({

				type : "post",
				url : "<? echo base_url('admin/locations/deleteBulkdata') ?>",
				data : {locations : locations,table:'tbl_inventory'},
				beforeSend : function(){
					
					$(".bloader").show();
					
				},
				success : function(data){
					console.log(data);	
					$(".bloader").hide();
					if(data == "success"){
						
						$(".berror").html('<div class="alert alert-success">selected location inventory deleted successfully</div>')
						setTimeout(function(){ location.reload() },2000);
							
					}else{
						
						$(".berror").html('<div class="alert alert-danger">error occured</div>')
						
					}

				},
				error : function(data){
					console.log(data);	
					$(".bloader").hide();	

				}

			});
			
		}else{
			
			$(".berror").html('<div class="alert alert-danger">Please select locations to delete</div>')
			
		}
	})
		
	$(document).on("click","#selectAll",function(){
		
		if($(this).prop("checked")) {
			
			$(".check").prop("checked", true);
			$("#bulkActions").show();	
			
		} else {
			
			$(".check").prop("checked", false);
			$("#bulkActions").hide();
			
		}  
		
		var locations = [];
			$.each($("input[name='lid']:checked"), function(){
			locations. push($(this). val());
		});
		
		if(locations.length > 0){
			
			$(".count").html(locations.length);
			
		}
		
	});
	
	$(document).on("click",".check",function(){
		
		var locations = [];
			$.each($("input[name='lid']:checked"), function(){
			locations. push($(this). val());
		});
		
		if(locations.length > 0){
			
			$(".count").html(locations.length);
			$("#bulkActions").show();
			
		}else{
			
			$("#bulkActions").hide();
			
		}
		
	})
	
	$(document).on("click",".filter",function(){
		
		$("#bulkActions").hide();
		$("#selectAll").prop("checked", false);
		
	});
	
	$(document).ready(function(){
		
		/*$('select:not(.normal)').each(function () {
                $(this).select2({
                    dropdownParent: $(this).parent()
                });
            });*/
		var maxField = 20; //Input fields increment limitation
		var addButton = $('.add_sheading'); //Add button selector
		var wrapper = $('.field_wrapper'); //Input field wrapper
		var spoints = $('.sub_points')


		var x = 1; //Initial field counter is 1
		var y = 1;

		//Once add button is clicked
		$(addButton).on("click",function(){
			//Check maximum number of input fields
			if(x < maxField){ 
				x++; //Increment field counter
				$(wrapper).append('<div class="row sub_p_rem'+x+'"><div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>Set</label></div><div class="col-md-3" align="left"><div class="form-group"><select ref="reference'+x+'" class="form-control getupdatedColumn" name="columns[]"><? foreach($fdata["labels"] as $key => $labels){ if(($fdata["columns"][$key] != "location") && ($fdata["columns"][$key] != "loccode") && ($fdata["columns"][$key] != "loctype") && ($fdata["columns"][$key] != "issues") && ($fdata["columns"][$key] != "returns") && ($fdata["columns"][$key] != "transfer_ins") && ($fdata["columns"][$key] != "transfer_outs") && ($fdata["columns"][$key] != "adjustments") && ($fdata["columns"][$key] != "ending_balance")){ ?><option value="<? echo $fdata["columns"][$key]."-".$fdata["dataType"][$key] ?>"><? echo $labels ?></option><? }} ?></select></div></div><div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>To</label></div><div class="col-md-3" align="left"><div class="form-group reference'+x+'"><select class="select2 form-control select2-multiple" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" required><option value="">Choose Selection</option><? foreach($locations as $location){ ?><option value="<? echo addslashes($location['locname']); ?>"><? echo addslashes($location['locname']); ?></option><? } ?></select></div></div><div class="col-md-2"><i class="fa fa-plus-circle addDom fa-2x" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-times-circle remove_button" lid="sub_p_rem'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>'); //Add field html

				y++;
				
				$(".select2").select2();
			}
		});
		
		$(wrapper).on("click",".addDom",function(){
			//Check maximum number of input fields
			if(x < maxField){ 
				x++; //Increment field counter
				$(wrapper).append('<div class="row sub_p_rem'+x+'"><div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>Set</label></div><div class="col-md-3" align="left"><div class="form-group"><select ref="reference'+x+'" class="form-control getupdatedColumn" name="columns[]"><? foreach($fdata["labels"] as $key => $labels){ if(($fdata["columns"][$key] != "location") && ($fdata["columns"][$key] != "loccode") && ($fdata["columns"][$key] != "loctype") && ($fdata["columns"][$key] != "issues") && ($fdata["columns"][$key] != "returns") && ($fdata["columns"][$key] != "transfer_ins") && ($fdata["columns"][$key] != "transfer_outs") && ($fdata["columns"][$key] != "adjustments") && ($fdata["columns"][$key] != "ending_balance")){ ?><option value="<? echo $fdata["columns"][$key]."-".$fdata["dataType"][$key] ?>"><? echo $labels ?></option><? }} ?></select></div></div><div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>To</label></div><div class="col-md-3" align="left"><div class="form-group reference'+x+'"><select class="select2 form-control select2-multiple" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" required><option value="">Choose Selection</option><? foreach($locations as $location){ ?><option value="<? echo addslashes($location['locname']); ?>"><? echo addslashes($location['locname']); ?></option><? } ?></select></div></div><div class="col-md-2"><i class="fa fa-plus-circle addDom fa-2x" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-times-circle remove_button" lid="sub_p_rem'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>'); //Add field html

				y++;
				
				$(".select2").select2();
			}
		});

		//Once remove button is clicked
		$(wrapper).on('click', '.remove_button', function(e){
			e.preventDefault();
			var id =$(this).attr("lid");
			
			$(this).parent('div').remove(); //Remove field html
			$('.'+id).remove();
			x--; //Decrement field counter
			y--;
//			alert(id+"-"+x)
		});
	});
	
// bulk operation ends	
	
	function openFilter(){
	$("#myFilter").modal('show');
}	
	
	$("#addInventory").on("submit",function(e){
		
		e.preventDefault();
		var form_data = $(this).serialize();
		$.ajax({

			type : "POST",
			url : "<? echo base_url('admin/apps/addInventory') ?>",
			data: form_data,
			dataType : "json",
			beforeSend : function(){
			
				$('.mloader').show();
				// $("#iSubmit").hide();
				
			},
			success : function(data){
				
//				console.log(data);
				$('.mloader').hide();
//				$("#iSubmit").show();
				
				if(data.Status == "Success"){
					
					$('.merror').html('<div class="alert alert-success">Successfully Inventory Added</div>');
					setTimeout(function(){
						location.reload()
					},2000);
					
				}else{
					
					$('.merror').html('<div class="alert alert-danger">'+data.Message+'</div>');
					
				}
	
			},
			error : function(jq,txt,error){
				
				$('.mloader').hide();
				console.log(jq);		
//				console.log(txt);		
//				console.log(error);		
				
			}

		});

	});
	
	$("#uInventory").on("submit",function(e){
		
		e.preventDefault();
		var form_data = $(this).serialize();
		$.ajax({

			type : "POST",
			url : "<? echo base_url('admin/apps/uInventory') ?>",
			data: form_data,
			dataType : "json",
			beforeSend : function(){
			
				$('.uloader').show();
				// $("#iSubmit").hide();
				
			},
			success : function(data){
				
//				console.log(data);
				$('.uloader').hide();
//				$("#iSubmit").show();
				
				if(data.Status == "Success"){
					
					$('.uerror').html('<div class="alert alert-success">Successfully Inventory Updated</div>');
					setTimeout(function(){
						location.reload()
					},2000);
					
				}else{
					
					$('.uerror').html('<div class="alert alert-danger">'+data.Message+'</div>');
					
				}
	
			},
			error : function(jq,txt,error){
				
				$('.uloader').hide();
				console.log(jq);		
//				console.log(txt);		
//				console.log(error);		
				
			}

		});

	});

	$(".showAddloc").click(function(){
		
		$(".insLoc").show();	
		$(".allLoc").hide();	
		$(".showAddloc").hide();	
		$(".showAllloc").show();	
		
	});
	
	$(".showAllloc").click(function(){
		
		$(".insLoc").hide();	
		$(".allLoc").show();
		$(".showAddloc").show();	
		$(".showAllloc").hide();
		
	});
	
	$(document).on("click",".editInventory",function(){
				
		var lid = $(this).attr("lid");
		var location = $(this).attr("location");
		var locname = $(this).attr("locname");
		var loccode = $(this).attr("loccode");
		var loctype = $(this).attr("loctype");
		var notes = $(this).attr("notes");
		var last_report_date = $(this).attr("last_report_date");
		var starting_balance = $(this).attr("starting_balance");
		var issues = $(this).attr("issues");
		var returns = $(this).attr("returns");
		var transfer_ins = $(this).attr("transfer_ins");
		var transfer_outs = $(this).attr("transfer_outs");
		var adjustments = $(this).attr("adjustments");
		var ending_balance = $(this).attr("ending_balance");
		var audit_count2019 = $(this).attr("audit_count2019");
		var audit_date2019 = $(this).attr("audit_date2019");
		var item = $(this).attr("item");
		
		
		$("#lid").val(lid)
		$("#location").val(location)
		$("#locname").val(locname)
		$("#loccode").val(loccode)
		$("#loctype").val(loctype)
		$("#notes").val(notes)
		$("#last_report_date").val(last_report_date)
		$("#starting_balance").val(starting_balance)
		$("#issues").val(issues)
		$("#returns").val(returns)
		$("#transfer_ins").val(transfer_ins)
		$("#transfer_outs").val(transfer_outs)
		$("#adjustments").val(adjustments)
		$("#ending_balance").val(ending_balance)
		$("#audit_count2019").val(audit_count2019)
		$("#audit_date2019").val(audit_date2019)
		$("#item").val(item)

		$("#location").select2().select2('data',location);
		$("#item").select2().select2('data',item);
		
		$('#pickupModal').modal("show");
		// initiateSelect2();
		
	});
		
	$(document).ready(function() {
		
		var item = $("#sItem").val();

		$(".additem").val(item);
		$(".additem1").val(item);
		$(".additem").select2().select2('data',item);


		$('#inventoryTable').DataTable({
		  'processing': true,
		  'serverSide': true,
		  'serverMethod': 'post',
		  "dom": 'Bfrtip',	
		  "lengthMenu": [[10, 25, 50,100,500], [10, 25, 50,100,500]],
		  "buttons": [
				'pageLength',
			  	{extend: 'excelHtml5',
		 		title:'Location Inventory',
					exportOptions: {
						columns: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18]
					}
		 		},
		 		{
		 		extend: 'csvHtml5',
		 		title:'Location Inventory',
					exportOptions: {
						columns: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18]
					}
		 		},
		 		{
				   "extend": 'excel',
				   "text": 'Export All',
				   "titleAttr": 'Export All',                               
				   "action": exportAll
			    }
			],
		  'ajax': {
			  type : 'post',
			  'data': {item:item},
			  'url':'<? echo base_url('admin/apps/getInventory') ?>',
			  /*'success' : function(data){
				  
				  console.log(data);
				  
			  },
			  'error' : function(data){
				  
				  console.log(data);
				  
			  }*/
		  },
		  'columns': [
			  	{ data:"check",defaultContent : ""},
			  	{ data:"Actions",defaultContent : ""},
			  	{ data:"id",defaultContent : ""},
			  <? foreach($columns as $key=>$value){
					if($key != "_id"){
						echo '{ data:"'.$key.'",defaultContent : ""},';
					}
				 }
			  ?>
		  ],
		  destroy: true,	
	   });
	
		$("#sItem").change(function(){
		   
		    var item = $(this).val();
			
		    $(".additem").val(item);
		    $(".additem1").val(item);
			$(".additem").select2().select2('data',item);

		    $("#additem").val(item);
			$("#additem").select2().select2('data',item);
		
			$('#inventoryTable').DataTable({
			  'processing': true,
			  'serverSide': true,
			  'serverMethod': 'post',
			  "dom": 'Bfrtip',	
			  "lengthMenu": [[10, 25, 50,100,500], [10, 25, 50,100,500]],
			  "buttons": [
					'pageLength',
				    {extend: 'excelHtml5',
					title:'Location Inventory',
						exportOptions: {
							columns: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18]
						}
					},
					{
					extend: 'csvHtml5',
					title:'Location Inventory',
						exportOptions: {
							columns: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18]
						}
					},
					{
					   "extend": 'excel',
					   "text": 'Export All',
					   "titleAttr": 'Export All',                               
					   "action": exportAll
					}
				],
			  'ajax': {
				  type : 'post',
				  'data': {item:item},
				  'url':'<? echo base_url('admin/apps/getInventory') ?>',
				  /*'success' : function(data){

					  console.log(data);

				  },
				  'error' : function(data){

					  console.log(data);

				  }*/
			  },
			  'columns': [
					{ data:"check",defaultContent : ""},
					{ data:"Actions",defaultContent : ""},
			  	{ data:"id",defaultContent : ""},
				  <? foreach($columns as $key=>$value){
						if($key != "_id"){
							echo '{ data:"'.$key.'",defaultContent : ""},';
						}
					 }
				  ?>
			  ],
			  destroy: true,	
		   });
		   
	   })

	   function exportAll(){
			
		    var sitem = $("#sItem").val();
			window.location.href = '<? echo base_url('admin/apps/exportAll/tbl_inventory/inventory/') ?>'+sitem;

		}	

		$("#createColumn").on('submit', function(e){
			e.preventDefault();
			var fdata = $("#createColumn").serialize();
			$.ajax({
				url:"<? echo base_url('admin/apps/addColumn') ?>",
				data:fdata,
				type:"post",
				dataType:"json",
				beforeSend: function(){
					$("#settings_loader").show();
				},
				success: function(data){
					$("#settings_loader").hide();
					$("#settings_emsg").hide();
					$("#settings_smsg").hide();
					if(data.Status == 'Success'){
					$("#settings_smsg").show();	
					$("#settings_smsg").html(data.Message);
					$("#createColumn")[0].reset();
					setTimeout(function(){
					$("#settings_smsg").hide();
					location.reload();	
					},3000);
					}else{
					$("#settings_emsg").show();	
					$("#settings_emsg").html(data.Message);	
					}
				},
				error: function(jqxhr,txtStatus, error){

				}
			});
		});
	});
	
	$("#uapp").submit(function(e){
	
	e.preventDefault();
	var fdata = $(this).serialize();
	
	$.ajax({
		
		type : "post",
		data : fdata,
		url : "<? echo base_url('admin/apps/updateApp') ?>",
		beforeSend : function(data){
			
			$(".loader").show();
			$(".cSubmit").hide();
			
		},
		success : function(data){
			console.log(data);
			
			$(".loader").hide();
			$(".cSubmit").show();
			
			if(data == "success"){
				
				$(".error").html('<div class="alert alert-success">App Successfully Updated</div>');
				setTimeout(function(){ location.reload() },2000);
				
			}else{
				
				$(".error").html('<div class="alert alert-danger">'+data+'</div>');
				
			}
			
		},
		error : function(data){
			
			$(".loader").hide();
			$(".cSubmit").show();
			
		}
		
	});
	
});
	
	$("#cloc").submit(function(e){
	
		e.preventDefault();
		var fdata = $(this).serialize();

		$.ajax({

			type : "post",
			data : fdata,
			url : "<? echo base_url('admin/locations/insertLocation') ?>",
			beforeSend : function(data){

				$(".loader").show();
				$(".cSubmit").hide();

			},
			success : function(data){
				console.log(data);

				$(".loader").hide();
				$(".cSubmit").show();

				if(data == "success"){

					$(".error").html('<div class="alert alert-success">Location Successfully Added</div>');
					setTimeout(function(){ location.reload() },2000);

				}else{

					$(".error").html('<div class="alert alert-danger">'+data+'</div>');

				}

			},
			error : function(data){

				$(".loader").hide();
				$(".cSubmit").show();

			}

		});

	});	

	$(".lcSubmit").click(function(e){

		var lid = $("#lid").val();
		var lcode = $("#lcode").val();
		var lname = $("#lname").val();
		var zip = $("#zip").val();
		var city = $("#city").val();
		var state = $("#state").val();
		var country = $("#country").val();
		var lat = $("#lat").val();
		var lon = $("#lon").val();
		var status = $("#status").val();
		var address = $("#address").val();
		var loctype = $("#loctype").val();
		
		$.ajax({

			type : "post",
			data : {id:lid,lcode:lcode,city:city,state:state,country:country,lat:lat,lon:lon,status:status,address:address,zip:zip,lname:lname,loctype:loctype},
			url : "<? echo base_url('admin/locations/updateLocation') ?>",
			beforeSend : function(data){

				$(".lloader").show();
				$(".lcSubmit").hide();

			},
			success : function(data){
				console.log(data);

				$(".lloader").hide();
				$(".lcSubmit").show();

				if(data == "success"){

					$(".lerror").html('<div class="alert alert-success">Location Successfully Updated</div>');
					setTimeout(function(){ location.reload() },2000);

				}else{

					$(".lerror").html('<div class="alert alert-danger">'+data+'</div>');

				}

			},
			error : function(data){

				$(".lloader").hide();
				$(".cSubmit").show();

			}

		});

	});
	
	function archiveFunction(id) {
       Swal({
		  title: 'Are you sure?',
		  text: 'You will not be able to recover this selected location!',
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonText: 'Yes, delete it!',
		  cancelButtonText: 'No, keep it'
		}).then((result) => {
		  if (result.value) {

			Swal(
			  'Deleted!',
			  'Your Selected Location has been deleted.',
			  'success'
			)
			$.ajax({
				method: 'POST',
				data: {'id' : id },
				url: '<?php echo base_url() ?>admin/apps/delInventory/'+id,
				success: function(data) {
					location.reload();   
				}
			});

		  } else if (result.dismiss === Swal.DismissReason.cancel) {
			Swal(
			  'Cancelled',
			  'Your Selected Location is safe :)',
			  'success',

			)
		  }
		})
    }

    function deleteColumn(id) {
       Swal({
		  title: 'Are you sure?',
		  text: 'You will not be able to recover this selected column!',
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonText: 'Yes, delete it!',
		  cancelButtonText: 'No, keep it'
		}).then((result) => {
		  if (result.value) {

			Swal(
			  'Deleted!',
			  'Your Selected Column has been deleted.',
			  'success'
			)
			$.ajax({
				method: 'POST',
				data: {'id' : id },
				url: '<?php echo base_url() ?>admin/locations/delColumn/'+id,
				success: function(data) {
					// console.log(data);
					location.reload();   
				}
			});

		  } else if (result.dismiss === Swal.DismissReason.cancel) {
			Swal(
			  'Cancelled',
			  'Your Selected Column is safe :)',
			  'success',

			)
		  }
		})
    }
		

$("#submitFilter").on('submit', function(e){
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
		$(".valueData").each(function(){
			value.push($(this).val());
		});
		$(".svalueData").each(function(){
			svalue.push($(this).val());
		});
		$(".dvalueData").each(function(){
			
			dvalue.push($(this).val());
		});
		var item = $("#sItem").val();
		var fdata = {"cause":cause,"field":field,"value":value,"svalue":svalue,"dvalue":dvalue,"item":item,"id":'<? echo $appid; ?>',"table":"tbl_inventory","filter_from":"form_modal"};
		$("#myFilter").modal("hide");

		var table = $('#inventoryTable').dataTable({
			 //"bProcessing": true,
			 "ajax": {
				"url": "<?php echo base_url("admin/apps/addFilter") ?>",
				"type": "POST",
				"data" : fdata
				
			  },
			 'aoColumns': [
				 { data:"check",defaultContent : ""},
				 { data:"Actions",defaultContent : ""},
				 { data:"inventoryid",defaultContent : ""},
			  <? foreach($columns as $key=>$value){
					if($key != "_id"){
						echo '{ mData:"'.$key.'",defaultContent : ""},';
					}
				 }
			  ?>
		  	],
			  'processing': true,
			  'serverSide': true,
			  'serverMethod': 'post',
			  "destroy" : 'true', 
			  "dom": 'Bfrtip',
			  "lengthMenu": [[10, 25, 50,100,500], [10, 25, 50,100,500]],
			  "buttons": [
				'pageLength',
				{extend: 'excelHtml5',
		 		title:'Location Inventory',
					exportOptions: {
						columns: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18]
					}
		 		},
		 		{
		 		extend: 'csvHtml5',
		 		title:'Location Inventory',
					exportOptions: {
						columns: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18]
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

var i=2;		
function addFilter(){
var n = '<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first'+i+'"><div class="row"><div class="col-md-1"><select name="cause[]" id="cause" class="form-control causeData"><option value="and">and</option><option value="or">or</option></select></div><div class="col-md-3"><select name="field[]" class="form-control getUlocdata fieldData loc_filter_dyn" lid="locid'+i+'" id="updLoc'+i+'" lopid="updLoc'+i+'" lo_id="locgetwhenRef'+i+'" ><option value="">Select</option><option value="id">Inventory Id</option><option value="location">Location</option><option value="locname">Location Name</option><option value="loccode">Location Code</option><option value="loctype">Location Type</option><option value="notes">Notes</option><option value="last_report_date">Last Report Date</option><option value="starting_balance">Starting Balance</option><option value="issues">Shipments</option><option value="returns">Pickups</option><option value="transfer_ins">Transfer Ins</option><option value="transfer_outs">Transfer Outs</option><option value="adjustments">Adjustments</option><option value="ending_balance">Ending Balance</option><option value="audit_count2019">Audit Count</option><option value="audit_date2019">Audit Date</option></select></div><div class="col-md-3 dynlocgetwhenRef'+i+'"><select name="value[]" id="value" class="form-control valueData"><option value="">Select</option><option value="contains">contains</option><option value="does not contain">does not contain</option><option value="is">is</option><option value="is not">is not</option><option value="starts with">starts with</option><option value="ends with">ends with</option><option value="is blank">is blank</option><option value="is not blank">is not blank</option></select></div><div class="col-md-4"><div class="locid'+i+' locgetwhenRef'+i+' updLoc'+i+'"><select name="svalue[]" class="form-control select2 svalueData"><option value="">Select</option><? $this->mongo_db->switch_db($this->database);$ldata = $this->mongo_db->order_by(["locname"=>'asc'])->get("tbl_locations");foreach($ldata as $ld){echo '<option value="'.str_replace("'","",$ld['locname']).'">'.str_replace("'","",$ld['locname']).'</option>';}?></select></div></div><div class="col-md-1"><p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter(\'first'+i+'\');"><i class="fa fa-trash"></i></p></div></div></div>';
$("#top").append(n);
i++;
	
	$(".select2").select2();
	
}

function removeFilter(first){
	// console.log(first);
	$("."+first).remove();
i--;
}	
	
$(document).on("change",".getLocdata",function(){

	var val = $(this).val();

	if(val == "location"){

		$.ajax({

			type : "post",
			url : "<? echo base_url('admin/apps/getLocdata') ?>",
			data : val,
			success : function(data){

				$("#setDvalue").html(data);
				$(".select2").select2();
//				console.log(data);

			},
			error : function(data){

//				console.log(data);

			}

		});	

	}else{

		$("#setDvalue").html('<input type="text" name="svalue[]" id="svalue" class="form-control svalueData">');

	}

});	

$(document).on("change",".getUlocdata",function(){

	var val = $(this).val();
	var target = $(this).attr("lid");

	if(val == "location"){

		$.ajax({

			type : "post",
			url : "<? echo base_url('admin/apps/getLocdata') ?>",
			data : val,
			success : function(data){

				$("."+target).html(data);
				$(".select2").select2();
//				console.log(data);

			},
			error : function(data){

//				console.log(data);

			}

		});	

	}else{

		$("."+target).html('<input type="text" name="svalue[]" id="svalue" class="form-control svalueData">');

	}

});			


function uploadDocument(){
	$("#iSubmit").attr("disabled", false);
}

$("#fileinfo").on("submit",function(e){
		e.preventDefault();
		var form_data = new FormData($("#fileinfo")[0]);
		$.ajax({
			type : "POST",
			url : "<? echo base_url('admin/ImportData/uploadFile') ?>",
			data: form_data,
		    cache: false,
		    contentType: false,
		    enctype: 'multipart/form-data',
		    processData: false,
		    dataType:"json",
			beforeSend : function(){			
				$('.mloader').show();
				$("#iSubmit").hide();
			},
			success : function(data){
			$('.mloader').hide();
			$('.merror').hide();
			$("#iSubmit").show();
			// console.log(data);

			if(data.Status == 'Success'){
				$("#screen1").hide();
				$("#screen2").show();
				
				$("#header").html("");
				$("#sets").html("");

				var head = "";
				var set = "";
				if(data.Headings.length > 4){
					$("#utab").css("width",2000);
				}
				data.Headings.forEach(function(item, index){
					head+='<td>'+item+'</td>';
					set+='<td>'+$("#clmns").html()+'</td>';
				});

				$("#header").append(head);
				$("#sets").append(set);
				$("#map").empty();

				data.Records.forEach(function(item, index){
					var record = "<tr>";
					for(var i=0;i<item.length;i++){
						record+='<td>'+item[i]+'</td>'
					}
					record +='</tr>';
					$("#map").append(record);
				});
				$("#app").val(data.appId);
				$("#field").val(data.field);
				$("#file").val(data.File);
				$("#row").val(data.headers);

			}else{
				$(".merror").html('<div class="alert alert-danger" style="margin-top:10px;">'+data.Message+'</div>');
			}
			
			},
			error : function(jq,txt,error){
				$('.mloader').hide();
				$(".merror").html('<div class="alert alert-danger" style="margin-top:10px;">'+error+'</div>');
			}

		});

	});			

$("#formstep2").on('submit', function(e){
		e.preventDefault();
		var fdata = $("#formstep2").serialize();
		$.ajax({
			url:"<? echo base_url('admin/ImportData/submitStep2') ?>",
			data:fdata,
			type:"post",
			dataType:"json",
			beforeSend: function(){
				$(".impsubmit").hide();
				$("#loader").show();
			},
			success: function(data){
				$(".impsubmit").show();
				$("#loader").hide();
				$("#emsg").hide();
				$("#smsg").hide();
				console.log("Step2 Output:",data);
				if(data.Status == "Success"){
					$("#smsg").show();
					$("#smsg").html(data.Message);
					setTimeout(function(){ location.reload(); },3000);
				}else if(data.Status == 'Dups'){
					if(data.Message.length <= 10){
						
						$("#errorTable").hide();
						
						var append='';
						data.Message.forEach(function(item, index){
							append+=item.Msg+"<br>";
							// console.log(item);
						})
						$("#emsg").show();
						$("#emsg").html(append);
						
					}else{
						
						$("#errorTable").show();
						
						var append='<table class="table errorData table-striped" width="100%"><thead class="thead-light"><tr><th>Error</th><th>Value</th></tr></thead><tbody>';
						data.Message.forEach(function(item, index){
							
							append+='<tr><td>'+item.Msg+'</td><td>'+item.Error+'</td></tr>';
    
							
						})
						
						append += '</tbody></table>';
						$("#errorTable").html(append);
						$(".errorData").dataTable();
					}
				}else{
					$("#emsg").show();
					$("#emsg").html(data.Message);
				}
			},
			error: function(jqxhr,txtStatus,error){
				console.log(jqxhr);
				$(".impsubmit").show();
				$("#loader").hide();
				$("#emsg").show();
				$("#emsg").html(error);
				$("#smsg").hide();


			}
		});

	});

function showScreens(){
	$("#screen2").hide();
	$("#screen1").show();
	$("#ldata").val('');
	$("#iSubmit").attr("disabled", true);
	$("#emsg").hide();
	$("#smsg").hide();
	$("#errorTable").hide();
}
function openConditions(name,column){
	$(".field_name").html(name);
	checkConditionStatus(column);
	$("#myCondition").modal('show');
	$("#collection_field").val(column);
	}

function updateConditionBox(){
	if($("#cyes").is(":checked")){
		$("#mainBox").show();
	}else{
		$("#mainBox").hide();
	}
}

function updateModule(module,index){
	if(module == "1"){
		$("#criteria"+index).hide();
	}else{
		$("#criteria"+index).show();
	}
}

function updateSet(set,index){
	if(set == "1"){
		$(".vvalue"+index).hide();
		$(".uvalue"+index).show();
	}else{
		$(".vvalue"+index).show();
		$(".uvalue"+index).hide();
	}
}
var nIndex=1;
function addRule(){
	var append ='<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first'+nIndex+'"><div class="row" style="margin-bottom:10px;"><div class="col-md-3"><input type="radio" name="setup[]" value="1" onchange="updateModule(this.value,\''+nIndex+'\');" checked="checked">Update every record</div><div class="col-md-3"><input type="radio" name="setup[]" value="2" onchange="updateModule(this.value,\''+nIndex+'\');">Add Criteria</div><div class="col-md-5"></div><div class="col-md-1"><p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeRule(\'first'+nIndex+'\');"><i class="fa fa-trash"></i></p></div></div><div class="row" style="display: none;margin-bottom: 10px;" id="criteria'+nIndex+'"><div class="col-md-1"><select name="cause[]" id="cause" class="form-control causeData" style="display: none"><option value="where">When</option></select><p style="margin-top: 5px;font-weight: bold">When</p></div><div class="col-md-3"><select name="field[]" id="field" class="form-control fieldData"><? echo $m; ?></select></div><div class="col-md-3"><select name="value[]" id="value" class="form-control valueData"><option value="contains">contains</option><option value="does not contain">does not contain</option><option value="is">is</option><option value="is not">is not</option><option value="starts with">starts with</option><option value="ends with">ends with</option><option value="is blank">is blank</option><option value="is not blank">is not blank</option></select></div><div class="col-md-4"><input type="text" name="svalue[]" id="svalue" class="form-control svalueData"></div><div class="col-md-1"></div></div><div class="row"><div class="col-md-1"><p style="margin-top:5px;font-weight: bold">Value</p></div><div class="col-md-5"><div class="form-group row"><label class="col-sm-2 col-form-label">Set</label><div class="col-sm-10"><select name="" class="form-control" onchange="updateSet(this.value,\''+nIndex+'\');"><option value="1">to a custom value</option><option value="2">to a record value</option></select></div></div></div><div class="col-md-3"><input type="text" name="updatedvalue[]" class="form-control uvalue'+nIndex+'"><select name="updatedvalue[]" class="form-control vvalue'+nIndex+'" style="display: none"><? echo $m; ?></select></div></div></div>';
	nIndex++;
	$("#go").append(append);
}

function removeRule(str){
	console.log("This is th output: "+nIndex+" - "+str);
	$("."+str).remove();
	// nIndex--;
}

$("#submitConditions").on('submit', function(e){
	e.preventDefault();
	var fdata = $("#submitConditions").serialize();
	$.ajax({
		url:"<? echo base_url('admin/conditions/getConditions') ?>",
		data:fdata,
		type:"post",
		dataType:"json",
		success: function(data){
			console.log(data);
			if(data.Status == 'Success'){
				$("#csmsg").show();
				$("#csmsg").html("Conditions applied successfully");
				setTimeout(function(){ location.reload(); },2000);
			}else{
				$("#cemsg").show();
				$("#cemsg").html("Something went wrong");
			}
		}
	});
});

function checkConditionStatus(column){

$.ajax({
		url:"<? echo base_url('admin/conditions/checkConditions'); ?>",
		data: {"table":"tbl_inventory","appId":'<? echo $appid; ?>', "column":column},
		type:"post",
		dataType:"json",
		success: function(data){
			console.log(data);
			if(data.Status == 'Success'){
				$("#cyes").prop('checked', true);
				updateConditionBox();
			}else{
				$("#cyes").prop('checked', false);
				updateConditionBox();
			}
		}
	});

}

function updateConditionBoxStatus(){
	var status;
	if($("#cyes").is(":checked")){
		status = 'Active';
	}else{
		status = 'Inactive';
	}

	$.ajax({
		url:"<? echo base_url('admin/conditions/updateStatus') ?>",
		data:{ "status": status, "table":"tbl_inventory","appId":'<? echo $appid; ?>',"column":$("#collection_field").val() },
		type:"post",
		success: function(data){
			console.log(data);
		}
	})
}
$(document).on("change",".loc_filter",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("lo_id");
			var lopid = $(this).attr("lopid");
			var col_val = "";
			$("."+ref).show();
			
			
			if(column == "id"){
				col_val = "id-text";
			}
			if(column == "locname"){
				col_val = "locname-select";
			}
			if(column == "loccode"){
				col_val = "loccode-text";
			}
			if(column == "loctype"){
				col_val = "loctype-select";
			}
			if(column == "notes"){
				col_val = "notes-text";
			}
			if(column == "item"){
				col_val = "item-select";
			}
			if(column == "last_report_date"){
				col_val = "last_report_date-date";
			}
			if(column == "starting_balance"){
				col_val = "starting_balance-number";
			}
			if(column == "issues"){
				col_val = "issues-number";
			}
			if(column == "returns"){
				col_val = "returns-number";
			}
			if(column == "transfer_ins"){
				col_val = "transfer_ins-number";
			}
			if(column == "transfer_outs"){
				col_val = "transfer_outs-number";
			}
			if(column == "adjustments"){
				col_val = "adjustments-number";
			}
			if(column == "ending_balance"){
				col_val = "ending_balance-number";
			}
			if(column == "audit_count2019"){
				col_val = "audit_count2019-number";
			}
			if(column == "audit_date2019"){
				col_val = "audit_date2019-date";
			}
			
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions",
				dataType : 'json',
				data : {column : col_val,table:"tbl_inventory","onchangeColref":"updateonchangeConditionLocation",uopid:lopid},
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
					
//					console.log(data);
					
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
			
			if(column == "id"){
				col_val = "id-text";
			}
			if(column == "locname"){
				col_val = "locname-text";
			}
			if(column == "loccode"){
				col_val = "loccode-text";
			}
			if(column == "loctype"){
				col_val = "loctype-text";
			}
			if(column == "notes"){
				col_val = "notes-text";
			}
			if(column == "item"){
				col_val = "item-select";
			}
			if(column == "last_report_date"){
				col_val = "last_report_date-date";
			}
			if(column == "starting_balance"){
				col_val = "starting_balance-number";
			}
			if(column == "issues"){
				col_val = "issues-number";
			}
			if(column == "returns"){
				col_val = "returns-number";
			}
			if(column == "transfer_ins"){
				col_val = "transfer_ins-number";
			}
			if(column == "transfer_outs"){
				col_val = "transfer_outs-number";
			}
			if(column == "adjustments"){
				col_val = "adjustments-number";
			}
			if(column == "ending_balance"){
				col_val = "ending_balance-number";
			}
			if(column == "audit_count2019"){
				col_val = "audit_count2019-number";
			}
			if(column == "audit_date2019"){
				col_val = "audit_date2019-date";
			}
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions",
				dataType : 'json',
				data : {column : col_val,table:"tbl_inventory","onchangeColref":"updateonchangeConditionLocation",uopid:lopid},
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
			var date = getDate();
			if(selection == "last_report_date"){
				selection = "last_report_date-date";
			}
			if(selection == "audit_date2019"){
				selection = "audit_date2019-date";
			}
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
</script>