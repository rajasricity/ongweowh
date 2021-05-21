<? admin_header(); ?> 

           
<? 
$_SESSION['appid'] = $l[0]['appId'];
admin_sidebar(); 

$aid = $this->uri->segment(4);

$appid = $l[0]['appId'];

$lcolumns = $this->admin->getRow("",["table"=>"tbl_locations"],[],$this->admin->getAppdb().".settings");
$mdb = mongodb;
$users = $this->admin->getArray("",["status"=>'Active',"appid"=>$aid],[],"$mdb.tbl_auths");
$times = ['12:00am','12:15am','12:30am','12:45am','01:00am','01:15am','01:30am','01:45am','02:00am','02:15am','02:30am','02:45am','03:00am','03:15am','03:30am','03:45am','04:00am','04:15am','04:30am','04:45am','05:00am','05:15am','05:30am','05:45am','06:00am','06:15am','06:30am','06:45am','07:00am','07:15am','07:30am','08:00am','08:15am','08:30am','08:45am','09:00am','09:15am','09:30am','10:00am','10:15am','10:30am','10:45am','11:00am','11:15am','11:30am','11:45am','12:00pm','12:15pm','12:30pm','12:45pm','01:00pm','01:15pm','01:30pm','01:45pm','02:00pm','02:15pm','02:30pm','02:45pm','03:00pm','03:15pm','03:30pm','03:45pm','04:00pm','04:15pm','04:30pm','04:45pm','05:00pm','05:15pm','05:30pm','05:45pm','06:00pm','06:15pm','06:30pm','06:45pm','07:00pm','07:15pm','07:30pm','08:00pm','08:15pm','08:30pm','08:45pm','09:00pm','09:15pm','09:30pm','10:00pm','10:15pm','10:30pm','10:45pm','11:00pm','11:15pm','11:30pm','11:45pm'];

$tasksCount = $this->mongo_db->where(["table"=>"tbl_locations","appId"=>$_SESSION['appid']])->count("tbl_tasks");


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
						
</style>

<style>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
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
                                        <li class="breadcrumb-item active">Update Customer</li>
                                    </ol>

                                </div>
                                <div class="col-sm-6">
                                    <div class="float-right d-none d-md-block">
                                    	<a class="btn btn-primary arrow-none waves-effect waves-light" href="<? echo base_url('admin/apps/users/').$aid ?>">
											 <i class="ion ion-md-people"></i> Users
										</a>
										<a class="btn btn-primary arrow-none waves-effect waves-light" href="<? echo base_url('admin/apps') ?>">
											 <i class="dripicons-user-group"></i> All Customers
										</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body" style="padding:0px;">
                                    
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link <? echo ($this->uri->segment(5) == "edit") ? '' : 'active' ?>" data-toggle="tab" href="#messages1" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                                    <span class="d-none d-sm-block"><i class="dripicons-location"></i> Locations</span>   
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link <? echo ($this->uri->segment(5) == "edit") ? 'active' : '' ?>" data-toggle="tab" href="#home1" role="tab">
                                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                    <span class="d-none d-sm-block"><i class="dripicons-user"></i> Edit Customer</span> 
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#import" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-download"></i></span>
                                                    <span class="d-none d-sm-block"><i class="ti-import"></i> Import</span>   
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

                                            <div class="tab-pane  <? echo ($this->uri->segment(5) == "edit") ? '' : 'active' ?> p-3" id="messages1" role="tabpanel">
                                                
                                                <div class="row">
													<div class="col-lg-12">
														<div class="">
															<div class="card-body" style="padding:0px;">
					                                        
					                                        	<div class="row">
					                                        	
					                                        		<div class="col-md-9">
					                                        			<div class="row" id="bulkActions" style="display: none">
					                                        				<div class="col-md-2"><p style="font-weight: 600; margin-top: 8px">With Checked (<span class="count"></span>)</p></div>	
																			<div class="col-md-4">
																				<a class="btn btn-info updateBulk" data-toggle="modal" data-target="#modal-fullscreen" href="javascript:void(0)"><i class="fa fa-edit"></i> Update</a>
																				<a class="btn btn-danger deleteBulk" href="javascript:void(0)"><i class="fa fa-trash"></i> Delete</a>
			                                        					
				                                        					</div>
				                                        					
				                                        					<div class="col-md-6">
				                                        						
				                                        						<div class="berror"></div>
				                                        						<div class="bloader" style="display:none">
																					<center><img src="<? echo base_url('assets/images/loader.gif') ?>" width="80" height="80" ></center>
																				</div>
				                                        						
				                                        					</div>
					                                        			</div>
					                                        		</div>
					                                        		
					                                        		<div class="col-md-3" align="right" style="margin-bottom: 10px">
					                                        		 
						                                        		<a class="btn btn-primary showAddloc" href="javascript:void(0)"><i class="dripicons-plus"></i> Add Location</a>
						                                        		<a class="btn btn-primary showAllloc" style="display: none" href="javascript:void(0)">Back</a>

						                                        	</div>
						                                        	
						                                        </div>
											
										<div class="table-responsive allLoc">
											<div class="row">
											<div class="col-md-6">
												<a href="#" style="color:red" onclick="openFilter();">Add Filters</a>
											</div>
											<div class="col-md-6 text-right">
												<a href="#" style="color:green" onclick="window.location.reload();">Reset</a>
											</div>
										</div>

											<table class="table mb-0 table-bordered" id="usersTable" style="width: 800px;z-index: 10000">
												<thead class="thead-light">
													<tr>
														<th style="width:10px" data-orderable="false"><input type="checkbox" id="selectAll"></th>
														<th style="width:50px" class="filter">&nbsp;&nbsp;</th>
														<th style="width:150px" class="filter">Location ID</th> 
														 <th style="width:150px" class="filter">Name - ID</th> 
														<th class="filter" style="width:150px">Location Name</th>
														<th style="width:100px" class="filter">Location Code</th>
														<th class="filter">Address</th>
														<th class="filter">City</th>
														<th class="filter">State</th>
														<th class="filter">Zip</th>
														<th class="filter">Country</th>
														<th class="filter">Status</th>
														<th class="filter">Type</th>
														<th class="filter">Import Date</th>
														<th class="filter">Accounts</th>
														<th class="filter">Notes</th>
											<!--			<th>Locations Test</th>-->
													</tr>
												</thead>
												<tbody></tbody>
												<tfooter>
													<tr style="background-color: #f1f1f1">
														<th style="width:10px;padding:5px;"></th>
														<th style="width:50px;padding:5px;"></th>
														<th style="width:150px" class="filter">Location ID</th> 
														 <th style="width:150px;padding:5px;">Name - ID</th> 
														<th style="width:150px;padding:5px;">Location Name</th>
														<th style="width:100px;padding:5px;">Location Code</th>
														<th style="padding:5px;">Address</th>
														<th style="padding:5px;">City</th>
														<th style="padding:5px;">State</th>
														<th style="padding:5px;">Zip</th>
														<th style="padding:5px;">Country</th>
														<th style="padding:5px;">Status</th>
														<th style="padding:5px;">Type</th>
														<th style="padding:5px;">Import Date</th>
														<th style="padding:5px;">Accounts</th>
														<th style="padding:5px;">Notes</th>
											<!--			<th style="padding:5px;">Locations Test</th>-->
													</tr>
												</tfooter>
											</table>
										</div>
																
															<div class="insLoc" style="display: none">	
																<form action="#" id="cloc" method="post">
																   <div class="row">
																		 
																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Location Name <span style="color:red">*</span></label>
																				<input type="text" class="form-control" name="locname" required>
																			</div>

																		 </div>	
																		 						
																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Location Code <span style="color:red">*</span></label>
																				<input type="text" class="form-control" name="loccode" required>
																			</div>

																		 </div> 
																		 
																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Address <span style="color:red">*</span></label>
																				<textarea class="form-control" name="address" rows="2" required></textarea>
																			</div>
																		 </div> 
																		 	
																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>City <span style="color:red">*</span></label>
																				<input type="text" class="form-control" name="city" required>
																			</div>

																		 </div>    

																		<div class="col-md-3"> 

																			<div class="form-group">
																				<label>State <span style="color:red">*</span></label>
																				<input type="text" class="form-control" name="state" required>
																			</div>

																		 </div>  
																		 
																		<div class="col-md-3"> 

																			<div class="form-group">
																				<label>Zipcode</label>
																				<input type="number" class="form-control" name="zip" min="1">
																			</div>

																		 </div>
																		 
																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Country <span style="color:red">*</span></label>
																				<input type="text" class="form-control" name="country" required>
																			</div>

																		 </div>
																		 
																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Status <span style="color:red">*</span></label>
																				<select class="form-control" name="status" required>

<!--																					<option value="">Select Status</option>-->
																					<option value="Active">Active</option>	
																					<option value="Inactive">Inactive</option>
																				</select>
																			</div>

																		 </div>

																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Type</label>
																				<select class="form-control" name="Type" required>

																					<option value="">Select Type</option>
																					<option value="External">External</option>	
																					<option value="Internal">Internal</option>
																				</select>
																			 </div>

																		 </div>
                
																		<div class="col-md-2"> 
																			<div class="form-group">
																				<label>Import Date</label>
<input type="date" class="form-control" name="import_date" value="<? echo date('Y-m-d', time()); ?>">
																			</div>
																		</div>	                

																		<div class="col-md-1" style="padding: 0px"> 
																			<div class="form-group">
																				<label>Time</label>
																				<select name="time" class="form-control" onmousedown="if(this.options.length>8){this.size=8;}"  onchange='this.size=0;' onblur="this.size=0;">
																				 <? foreach($times as $time){?>
																					<option value="<? echo $time; ?>"><? echo $time; ?></option>
																				 <?}?>

																				</select>
																			</div>
																		</div>	                

																		<div class="col-md-3"> 
																		<div class="form-group">
																		
																			<label>Accounts</label>
																			<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="accounts[]" multiple>
																				<option value="">Select User</option>
																				
																				<? foreach($users as $u){ 
																				
																					echo '<option value="'.$u->uname.'">'.$u->uname.'</option>';
																				
																				}?>
																				
																				
																			</select>

																		</div>
																		</div>

																		<div class="col-md-3"> 
																		<div class="form-group">
																			<label>Notes</label>
																			<input type="text" class="form-control" name="notes">
																		</div>
																		</div>	      
    
																	</div>


																	<div class="row">

																		<div class="col-md-9">

																			<div class="loader" style="display: none">
																				<img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 50px;height: 50px"></div>
																			<div class="error"></div>

																		</div>

																		<div class="col-md-3" align="right">

																			<input type="hidden" name="id" value="<? echo isset($loc[0]["_id"]->{'$id'}) ? $loc[0]["_id"]->{'$id'} : ''  ?>">
																			<input type="hidden" name="appId" value="<? echo $aid  ?>">
																			<button class="btn btn-primary arrow-none waves-effect waves-light clocSubmit" type="submit"><? echo isset($loc[0]["_id"]->{'$id'}) ? 'Update' : 'Create' ?></button>

																		</div>

																	</div>

																</form>
															</div>	
																

															</div>
														</div>
													</div>
												</div>
                                                
                                            </div>

                                            <div class="tab-pane  <? echo ($this->uri->segment(5) == "edit") ? 'active' : '' ?> p-3" id="home1" role="tabpanel">
                                                
                                                 <form action="#" id="uapp" method="post">
												   <div class="row">

														 <div class="col-md-4"> 

															<div class="form-group">
																<label>Customer Name</label>
																<input type="text" class="form-control" name="appname" value="<? echo $l[0]["appname"] ?>" required>
															</div>

														 </div> 

														 <div class="col-md-6"> 

															<div class="form-group">
																<label>Short Description</label>
																<textarea class="form-control" name="sdesc" rows="2" required><? echo $l[0]["short_desc"] ?></textarea>
															</div>

														 </div> 
														 <div class="col-md-2"> 

															<div class="form-group">
																<label>Status</label>
																<select class="form-control" name="status" required>

																	<option value="">Select Status</option>
																	<option value="Active" <? echo ($l[0]["status"] == "Active") ? 'selected' : '' ?>>Active</option>	
																	<option value="Inactive" <? echo ($l[0]["status"] == "Inactive") ? 'selected' : '' ?>>Inactive</option>
																</select>
															</div>

														 </div>                       

													</div>


													<div class="row">

														<div class="col-md-9">

															<div class="loader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 50px;height: 50px"></div>
															<div class="error"></div>

														</div>

														<div class="col-md-3" align="right">

															<input type="hidden" name="id" value="<? echo isset($l[0]["_id"]->{'$id'}) ? $l[0]["_id"]->{'$id'} : ''  ?>">
															<button class="btn btn-primary arrow-none waves-effect waves-light cSubmit" type="submit"><? echo isset($l[0]["_id"]->{'$id'}) ? 'Update' : 'Create' ?></button>

														</div>

													</div>

												</form>
                                                
                                            </div>
                                            
                                            <div class="tab-pane p-3" id="import" role="tabpanel">
<?
$mng = $this->admin->Mconfig();
$row = $this->admin->getRow($mng,["table"=>"tbl_locations"],[],$database.".settings");
$labels = $row->labels;
array_unshift($labels, "Name - ID");
$columns = $row->columns;
array_unshift($columns, "nameid");
?>

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
	<option value="0">Default</option>
	<? foreach($labels as $key=>$value){ 
		if($columns[$key] != "nameid"){
	?>
	<option value="<? echo $columns[$key]; ?>"><? echo $value; ?></option>
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
                                          					
                              <a href="<? echo base_url('assets/downloads/Locations.xlsx') ?>" class="btn btn-info">
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
	<h6>Confirm your data</h6>
	<p><b>Map</b> each column to an existing <b>field</b>.</p>
	<table class="table table-bordered" style="display: none">
		<tr>
			<td id="clmns">
				<select name="column[]" class="form-control">
						<option value="0">Default</option>
	<? foreach($labels as $key=>$value){
		if($columns[$key] != "nameid"){				
	?>
	<option value="<? echo $columns[$key]; ?>"><? echo $value; ?></option>
	<?}}?>
				</select>
			</td>
		</tr>
	</table>
<form id="formstep2">
<!-- <form action="<? echo base_url('admin/ImportData/submitStep2') ?>" method="post"> -->
<input type="hidden" name="table" id="table" value="tbl_locations">
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

                                            
                                            	<!-- <form id="fileinfo" method="post" enctype="multipart/form-data">
                                            	
												   <div class="row">

														 <div class="col-md-3"> 

															<div class="form-group">
																<label>Select File</label>
																<input type="file" class="form-control" name="ldata" style="height: 40px"  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
																<small style="color: red; font-size: 14px">Note : Please select <b>.xlsx</b> format</small>
															</div>

														 </div>
                                          
                                          				 <div class="col-md-3 m-t-30">
															
															<input type="hidden" name="appID" value="<? echo $aid ?>">
															<button class="btn btn-primary arrow-none waves-effect waves-light" type="submit" id="iSubmit"><i class="dripicons-upload"></i> Upload</button>
															
															<div class="mloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 25%"></div>
															<div class="merror"></div>

														</div>
                                          
													    <div class="col-md-3">
													    	
													    </div> 
                                          
                                          				<div class="col-md-3 m-t-30" align="right">
                                          					
                                          					<a href="<? echo base_url('assets/downloads/Locations.xlsx') ?>" class="btn btn-info"><i class="ion ion-ios-download"></i> Download Template</a>
                                          					
                                          				</div>
                                           
													</div>
                                           
												</form> --> 
                                            
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
																								
																									<input type="date" name="next_run_date" value="<? echo date('Y-m-d',time()) ?>"  class="form-control">
																									
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
																											   foreach($lcolumns->labels as $key => $labels){													
																											?>

																												<option value="<? echo $lcolumns->columns[$key]."-".$lcolumns->dataType[$key] ?>"><? echo $labels ?></option>

																											<? } ?>

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

																										<input type="text" name="cond_value[]" class="form-control">	

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
																											?>

																												<option value="<? echo $lcolumns->columns[$key]."-".$lcolumns->dataType[$key] ?>"><? echo $labels ?></option>

																											<? } ?>

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

																										<input type="text" name="ssetvalue[]" class="form-control">

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
																						
																							<input type="hidden" name="table" value="tbl_locations">
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
																				
																				$vrules = $this->mongo_db->get_where("tbl_validation_rules",["table"=>"tbl_locations","field"=>$lcolumns->columns[$key],"appId"=>$aid])[0];

																				if($vrules){

																					$vrid = $vrules["_id"]->{'$id'};

																				}*/
																				
																				$crid = "";
																				
																				$crules = $this->mongo_db->get_where("tbl_conditional_rules",["table"=>"tbl_locations","field"=>$lcolumns->columns[$key],"appId"=>$aid])[0];

																				if($crules){

																					$crid = $crules["_id"]->{'$id'};

																				}
																		?>
																		
																		<tr>
																			
																			<td><? echo ucfirst($lcolumns->dataType[$key]) ?></td>
																			<td><a href="javascript:void(0)" class="cmodal" crid="<? echo $crid ?>" colname="<? echo $lc ?>" fname="<? echo $lcolumns->columns[$key] ?>" style="font-size: 16px">
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
																				<div class="dropdown-menu" aria-labelledby="customDropdown">
																				  <span class="dropdown-menu-arrow"></span>
<!--																				  <a class="dropdown-item vmodal" vrid="<? //echo $vrid ?>" fname="<? //echo $lcolumns->columns[$key] ?>" href="javascript:void(0)">Validation Rules</a>-->
																				  <a class="dropdown-item cmodal" href="javascript:void(0)" crid="<? echo $crid ?>" colname="<? echo $lc ?>" fname="<? echo $lcolumns->columns[$key] ?>">Conditional Rules</a>
																				</div>
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
                <!-- content -->
	
	<!--  Modal content for the above example -->
	<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" style="width:100%">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #333547;color:#fff">
					<h5 class="modal-title mt-0" id="myLargeModalLabel">Update Location</h5>
					<button type="button" class="close" data-dismiss="modal"  aria-hidden="true" style="color:#fff;">×</button>
				</div>
				<div class="modal-body">
					
					<form action="#" id="uloc" method="post">
					   <div class="row">

						 	 <div class="col-md-3"> 

								<div class="form-group">
									<label>Location Name <span style="color:red">*</span></label>
									<input type="text" class="form-control" name="locname" id="lname" required>
								</div>

							 </div>
							 
						 
							 <div class="col-md-3"> 

								<div class="form-group">
									<label>Location Code <span style="color:red">*</span></label>
									<input type="text" class="form-control" name="loccode" id="lcode" readonly required>
								</div>

							 </div> 

						 	 <div class="col-md-3"> 

								<div class="form-group">
									<label>Address <span style="color:red">*</span></label>
									<textarea class="form-control" name="address" id="address" rows="2" required></textarea>
								</div>

							 </div>
							 
							 <div class="col-md-3"> 

								<div class="form-group">
									<label>City <span style="color:red">*</span></label>
									<input type="text" class="form-control" name="city" id="city" required>
								</div>

							 </div>    

							 <div class="col-md-3"> 

								<div class="form-group">
									<label>State <span style="color:red">*</span></label>
									<input type="text" class="form-control" name="state" id="state" required>
								</div>

							 </div>  

						 	<div class="col-md-3"> 

								<div class="form-group">
									<label>Zip <!--<span style="color:red">*</span>--></label>
									<input type="number" class="form-control" name="zip" id="zip">
								</div>

							 </div>
						 	
							 <div class="col-md-3"> 

								<div class="form-group">
									<label>Country <span style="color:red">*</span></label>
									<input type="text" class="form-control" name="country" id="country" required>
								</div>

							 </div>
							 
							 <div class="col-md-3"> 

								<div class="form-group">
									<label>Status <span style="color:red">*</span></label>
									<select class="form-control" name="status" id="status" required>

										<option value="">Select Status</option>
										<option value="Active">Active</option>	
										<option value="Inactive">Inactive</option>
									</select>
								</div>

							 </div> 
							  <div class="col-md-3"> 

								<div class="form-group">
									<label>Type <span style="color:red">*</span></label>
									<select class="form-control" name="Type" id="loctype" required>

										<option value="">Select Type</option>
										<option value="External">External</option>	
										<option value="Internal">Internal</option>
									</select>
								</div>

							 </div>
							  
							 <div class="col-md-2"> 

								<div class="form-group">
									<label>Import Date</label>
									<input type="date" class="form-control" name="import_date" id="impdate" required>
								</div>

							 </div>

							 <div class="col-md-1" style="padding:0px;"> 

								<div class="form-group">
									<label>Time</label>
									<select name="time" id="time" class="form-control" onmousedown="if(this.options.length>8){this.size=8;}"  onchange='this.size=0;' onblur="this.size=0;">
										<? foreach($times as $time){?>
											<option value="<? echo $time; ?>"><? echo $time; ?></option>
										<?}?>
										
									</select>
								</div>

							 </div>

							 <div class="col-md-3"> 

								<div class="form-group">
									<label>Accounts <!--<span style="color:red">*</span>--></label>
									<select class="uselect2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="accounts[]" multiple>
<!--										<option value="">Select User</option>-->

										<? foreach($users as $u){ 

											echo '<option value="'.$u->uname.'">'.$u->uname.'</option>';

										}?>
									</select>
								</div>

							 </div>

							 <div class="col-md-3"> 

								<div class="form-group">
									<label>Notes</label>
									<input type="text" class="form-control" name="notes" id="notes">
								</div>

							 </div>

						</div>


						<div class="row">

							<div class="col-md-9">

								<div class="lloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 10%"></div>
								<div class="lerror"></div>

							</div>

							<div class="col-md-3" align="right">

								<input type="hidden" name="id" id="lid">
								<input type="hidden" name="appId" value="<? echo $appid ?>">
								<button class="btn btn-default arrow-none waves-effect waves-light" data-dismiss="modal"  style="border: 1px solid lightgrey" type="button">Close</button>
								<button class="btn btn-primary arrow-none waves-effect waves-light " type="submit">Update</button>

							</div>

						</div>

					</form>
					
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
                                           
	<!-- Modal Fullscreen -->
	<div class="modal fade modal-fullscreen" id="modal-fullscreen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="padding-left: 10px !important">
	  <div class="modal-dialog modal-dialog1" style="z-index: 9999">
		<div class="modal-content modal-content1">
		  <div class="modal-header card-header" style="display: block">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle" style="color: white"></i></button>
			<h4 class="modal-title" id="myModalLabel" style="text-align: center;color: white">Update Location Records</h4>
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
								   foreach($lcolumns->labels as $key => $labels){											
								   	if($labels != 'Location Code'){
								?>

									<option value="<? echo $lcolumns->columns[$key]."-".$lcolumns->dataType[$key] ?>"><? echo $labels ?></option>

								<? } } ?>
								
							</select>

						</div>

					</div>
					<div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>To</label></div>

					<div class="col-md-3" align="left">

						<div class="form-group bindField">

							<input type="text" class="form-control" name="value[]">

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
                                                                                                                         	
 	 	 	
<? admin_footer(); ?>

<input type="hidden" name="base_url" id="base_url" value="<? echo base_url() ?>">

<script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
<script src="<? echo base_url(); ?>assets/plugins/select2/js/select2.min.js"></script>
<script src="<? echo base_url(); ?>assets/js/cron/cron_locationstasks.js"></script>
<script src="<? echo base_url(); ?>assets/js/vrules/vrules_locations.js"></script>
<script src="<? echo base_url(); ?>assets/js/crules/crules_locations.js"></script>
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
					<form role="form" method="post" id="cvalidationRules" novalidate>
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

								<input type="hidden" name="vrtable" value="tbl_locations" id="vTable">
								<input type="hidden" name="appId" value="<? echo $aid ?>" id="vAppid">
								<input type="hidden" name="fieldname" id="fieldname">
								<input type="hidden" name="vid" id="vid">
								<button type="submit" class="btn btn-primary ufields1" style="display: block">Update Fields</button>

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

			<form id="submitFilter">
<!--
			 <form action="<? echo base_url('admin/apps/addFilter') ?>" method="post">
			<input type="hidden" name="id" value="<? echo $appid; ?>"> 
-->
			<div id="top">
			<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first0">

			<div class="row">
				<div class="col-md-1">
					<select name="cause[]" id="cause" class="form-control causeData" style="display: none"><option value="where">where</option></select>
					<p style="margin-top: 5px;font-weight: bold">Where</p>
				</div>
				<div class="col-md-3">
					<select name="field[]" class="form-control fieldData loc_filter" id="updLoc1" lopid="updLoc1" lo_id="locgetwhenRef">
					    <option value="">Select</option>
					    <option value="locid">Location Id</option>
						<option value="locname">Location Name</option>
						<option value="loccode">Location Code</option>
						<option value="address">Address</option>
						<option value="city">City</option>
						<option value="state">State</option>
						<option value="zip">Zip</option>
						<option value="country">Country</option>
						<option value="status">Status</option>
						<option value="Type">Type</option>
						<option value="import_date">Import Date</option>
						<option value="accounts">Accounts</option>
						<option value="notes">Notes</option>
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
					<input type="text" name="svalue[]" id="svalue" class="form-control svalueData">
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

								<input type="hidden" name="contable" value="tbl_locations" id="cTable">
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
	
	
<!-- Edit task -->


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

								<input type="hidden" name="table" value="tbl_locations">
								<input type="hidden" name="task_id" id="task_id">
								<button type="submit" class="btn btn-primary ubSubmit">Update Task</button>
								
							</div>										
					</form>
				  </section>      

				</div>


				<div class="row">

					<div class="col-md-9">

						<div class="estloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 50px;height: 50px"></div>
						<div class="esterror"></div>

					</div>

					<div class="col-md-3" align="right"></div>

				</div>
	
		  </div>
		</div>

	  </div>
	</div>
	
	<input type="hidden" name="updatedWhencount" id="updatedWhencount" value="1"> 
	<input type="hidden" name="updatedValuescount" id="updatedValuescount" value="1"> 
	
<script>
	
	
	function deleteTask(id){
       Swal({
		  title: 'Are you sure?',
		  text: 'You will not be able to recover this selected task!',
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonText: 'Yes, delete it!',
		  cancelButtonText: 'No, keep it'
		}).then((result) => {
		  if (result.value) {

			Swal(
			  'Deleted!',
			  'Your Selected task has been deleted.',
			  'success'
			)
			$.ajax({
				method: 'POST',
				data: {'id' : id },
				url: '<?php echo base_url() ?>admin/tasks/delTask/'+id,
				success: function(data) {
					location.reload();   
				}
			});

		  } else if (result.dismiss === Swal.DismissReason.cancel) {
			Swal(
			  'Cancelled',
			  'Your Selected task is safe :)',
			  'success',

			)
		  }
		})
    }

// task start
	
	$("#tasksTable").DataTable({
		
		/*"dom": 'Bfrtip',
		 buttons: [
				'csv', 'excel','pageLength'
			],*/
		 "bProcessing": true,
         "sAjaxSource": "<? echo base_url(); ?>admin/tasks/getAlltasks/tbl_locations",
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

// task ends	
	
// filter starts

//$(document).ready(function(){	
	
	function openFilter(){
		$("#myFilter").modal('show');
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
		var fdata = {"cause":cause,"field":field,"value":value,"svalue":svalue,"dvalue":dvalue,"id":'<? echo $appid; ?>',"table":"tbl_locations","filter_from":"form_modal"};
		$("#myFilter").modal("hide");

		var table = $('#usersTable').DataTable({
			 //"bProcessing": true,
			 "ajax": {
				"url": "<?php echo base_url("admin/apps/addFilter") ?>",
				"type": "POST",
				"data" : fdata
//				 success : function(data){
//					 
//					 console.log(data);
//					 
//				 },
//				 error : function(data){
//					 
//					 console.log(data);
//					 
//				 }
			  },
			 "aoColumns": [
         	   { mData: 'check',defaultContent : ""},
         	   { mData: 'Actions',defaultContent : ""},
         	   { mData: 'locid',defaultContent : ""},
			   { mData: 'nameid' ,defaultContent : ""},
			   { mData: 'locname' ,defaultContent : ""},
			   { mData: 'loccode' ,defaultContent : ""},
			   { mData: 'address',defaultContent : "" },
			   { mData: 'city',defaultContent : "" },
			   { mData: 'state',defaultContent : "" },
			   { mData: 'zip',defaultContent : "" },
			   { mData: 'country',defaultContent : "" },
			   { mData: 'status',defaultContent : "" },
			   { mData: 'Type',defaultContent : "" },
			   { mData: 'import_date',defaultContent : "" },
			   { mData: 'accounts',defaultContent : "" },
			   { mData: 'notes',defaultContent : "" }
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
				title:'Customers Ongoweoweh',
					exportOptions: {
						columns: [2,3,4,5,6,7,8,9,10,11,12,13,14]
					}
				},
				{
				extend: 'csvHtml5',
				title:'Customers Ongoweoweh',
					exportOptions: {
						columns: [2,3,4,5,6,7,8,9,10,11,12,13,14]
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
		var n = '<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first'+i+'"><div class="row"><div class="col-md-1"><select name="cause[]" id="cause" class="form-control causeData"><option value="and">and</option><option value="or">or</option></select></div><div class="col-md-3"><select name="field[]" class="form-control fieldData loc_filter_dyn" id="updLoc'+i+'" lopid="updLoc'+i+'" lo_id="locgetwhenRef'+i+'"><option value="">Select</option><option value="locid">Location Id</option><option value="locname">Location Name</option><option value="loccode">Location Code</option><option value="address">Address</option><option value="city">City</option><option value="state">State</option><option value="zip">Zip</option><option value="country">Country</option><option value="status">Status</option><option value="Type">Type</option><option value="import_date">Import Date</option><option value="accounts">Accounts</option><option value="notes">Notes</option></select></div><div class="col-md-3 dynlocgetwhenRef'+i+'"><select name="value[]" id="value" class="form-control valueData"><option value="">Select</option><option value="contains">contains</option><option value="does not contain">does not contain</option><option value="is">is</option><option value="is not">is not</option><option value="starts with">starts with</option><option value="ends with">ends with</option><option value="is blank">is blank</option><option value="is not blank">is not blank</option></select></div><div class="col-md-4 locgetwhenRef'+i+' updLoc'+i+'"><input type="text" name="svalue[]" id="svalue" class="form-control svalueData"></div><div class="col-md-1"><p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter(\'first'+i+'\');"><i class="fa fa-trash"></i></p></div></div></div>';
		$("#top").append(n);
		i++;
	}

	function removeFilter(first){
		$("."+first).remove();
		i--;
	}		
	
//});	
	
// filter ends	
	
	$(".select2").select2();
	$(".uselect2").select2();

// bulk operation starts	
	
	$("#updateLocrecords").submit(function(e){
		
		e.preventDefault();
		var fdata = $(this).serialize();
		
		var locations = [];
			$.each($("input[name='lid']:checked"), function(){
			locations. push($(this). val());
		});
		
		
		var form_data = fdata+'&'+$.param({ 'targets': locations,'table' : 'tbl_locations' })
		
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

					$(".uberror").html('<div class="alert alert-success">selected locations updated successfully</div>')
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
		
		if(column == "address"){
			
			$(".bindField").html('<div class="form-group bindField"><textarea class="form-control" name="value[]"></textarea></div>');
			
		} else if(column == "status"){
			
			$(".bindField").html('<div class="form-group bindField"><select class="form-control" name="value[]"><option value="">Select Status</option><option value="Active">Active</option><option value="Inactive">Inactive</option></select></div>');
			
		} else if(column == "Type"){
			
			$(".bindField").html('<div class="form-group bindField"><select class="form-control" name="value[]"><option value="">Select Type</option><option value="External">External</option><option value="Internal">Internal</option></select></div>');
			
		} else if(column == "import_date"){
			
			$(".bindField").html('<div class="form-group bindField"><div class="row"><div class="col-md-7"><input type="date" class="form-control" name="value[]"></div><div class="col-md-5"><select name="value1[]" class="form-control" onmousedown="if(this.options.length>8){this.size=8;}"  onchange="this.size=0;" onblur="this.size=0;"><? foreach($times as $time){?><option value="<? echo $time; ?>"><? echo $time; ?></option><?}?></select></div></div></div>');
			
		} else if(column == "accounts"){
			
			$(".bindField").html('<div class="form-group bindField"><select class="buselect2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" multiple required><option value="">Select User</option><? foreach($users as $u){echo '<option value="'.$u->uname.'">'.$u->uname.'</option>';}?></select></div>');
			
			$(".buselect2").select2();
			
		}else{
			
			$(".bindField").html('<input type="'+datatype+'" class="form-control" name="value[]">');
			
		}
		
	});
	
	$(".field_wrapper").on("change",".getupdatedColumn",function(){
		
		var ref = ($(this).attr("ref"));
		
		var value = $(this).val();
		
		var column = value.split("-")[0];
		var datatype = value.split("-")[1];
		
		if(column == "address"){
			
			$("."+ref).html('<div class="form-group '+ref+'"><textarea class="form-control" name="value[]"></textarea></div>');
			
		} else if(column == "status"){
			
			$("."+ref).html('<div class="form-group '+ref+'"><select class="form-control" name="value[]"><option value="">Select Status</option><option value="Active">Active</option><option value="Inactive">Inactive</option></select></div>');
			
		} else if(column == "Type"){
			
			$("."+ref).html('<div class="form-group '+ref+'"><select class="form-control" name="value[]"><option value="">Select Type</option><option value="External">External</option><option value="Internal">Internal</option></select></div>');
			
		} else if(column == "import_date"){
			
			$("."+ref).html('<div class="form-group '+ref+'"><div class="row"><div class="col-md-7"><input type="date" class="form-control" name="value[]"></div><div class="col-md-5"><select name="value1[]" class="form-control" onmousedown="if(this.options.length>8){this.size=8;}"  onchange="this.size=0;" onblur="this.size=0;"><? foreach($times as $time){?><option value="<? echo $time; ?>"><? echo $time; ?></option><?}?></select></div></div></div>');
			
		} else if(column == "accounts"){
			
			$("."+ref).html('<div class="form-group '+ref+'"><select class="buselect2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" multiple required><option value="">Select User</option><? foreach($users as $u){echo '<option value="'.$u->uname.'">'.$u->uname.'</option>';}?></select></div>');
			
			$(".buselect2").select2();
			
		}else{
			
			$("."+ref).html('<input type="'+datatype+'" class="form-control" name="value[]">');
			
		}
		
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
				data : {locations : locations,table:'tbl_locations'},
				beforeSend : function(){
					
					$(".bloader").show();
					
				},
				success : function(data){
					console.log(data);	
					$(".bloader").hide();
					if(data == "success"){
						
						$(".berror").html('<div class="alert alert-success">selected locations deleted successfully</div>')
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
				$(wrapper).append('<div class="row sub_p_rem'+x+'"><div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>Set</label></div><div class="col-md-3" align="left"><div class="form-group"><select ref="reference'+x+'" class="form-control getupdatedColumn" name="columns[]"><? foreach($lcolumns->labels as $key => $labels){ ?><option value="<? echo $lcolumns->columns[$key]."-".$lcolumns->dataType[$key] ?>"><? echo $labels ?></option><? } ?></select></div></div><div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>To</label></div><div class="col-md-3" align="left"><div class="form-group reference'+x+'"><input type="text" class="form-control" name="value[]"></div></div><div class="col-md-2"><i class="fa fa-plus-circle addDom fa-2x" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-times-circle remove_button" lid="sub_p_rem'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>'); //Add field html

				y++;
			}
		});
		
		$(wrapper).on("click",".addDom",function(){
			//Check maximum number of input fields
			if(x < maxField){ 
				x++; //Increment field counter
				$(wrapper).append('<div class="row sub_p_rem'+x+'"><div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>Set</label></div><div class="col-md-3" align="left"><div class="form-group"><select ref="reference'+x+'" class="form-control getupdatedColumn" name="columns[]"><? foreach($lcolumns->labels as $key => $labels){ ?><option value="<? echo $lcolumns->columns[$key]."-".$lcolumns->dataType[$key] ?>"><? echo $labels ?></option><? } ?></select></div></div><div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>To</label></div><div class="col-md-3" align="left"><div class="form-group reference'+x+'"><input type="text" class="form-control" name="value[]"></div></div><div class="col-md-2"><i class="fa fa-plus-circle addDom fa-2x" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-times-circle remove_button" lid="sub_p_rem'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>'); //Add field html

				y++;
			}
		});

		//Once remove button is clicked
		$(wrapper).on('click', '.remove_button', function(e){
			e.preventDefault();
			var id =$(this).attr("lid");
			
			$(this).parent('div').remove(); //Remove field html
			$('.'+id).remove();
			x--; //Decrement field counter
			
//			alert(id+"-"+x)
		});
	});

	
// bulk operation ends	
				
	$(".showAddloc").click(function(){
		
		$("#selectAll").prop("checked", false);
		$(".check").prop("checked", false);
		$(".insLoc").show();	
		$(".allLoc").hide();	
		$(".showAddloc").hide();	
		$("#bulkActions").hide();	
		$(".showAllloc").show();	
		
	});
	
	$(".showAllloc").click(function(){
		
		$(".insLoc").hide();	
		$(".allLoc").show();
		$(".showAddloc").show();	
		$(".showAllloc").hide();
		
	});
	
	$(document).on("click",".editLocate",function(){
	 	$(".lerror").html('');
	 	$("#lid").val($(this).attr("lid"));
	 	$("#lcode").val($(this).attr("lcode"));
	 	$("#lname").val($(this).attr("lname"));
	 	$("#zip").val($(this).attr("zip"));
	 	$("#city").val($(this).attr("city"));
	 	$("#address").val($(this).attr("address"));
	 	$("#state").val($(this).attr("state"));
	 	$("#country").val($(this).attr("country"));
	 	$("#status").val($(this).attr("status"));
	 	$("#loctype").val($(this).attr("Type"));
	 	$("#impdate").val($(this).attr("impdate"));
//	 	$("#time").val($(this).attr("time"));
//	 	$("#accounts").val($(this).attr("accounts"));
		 
		var time = $(this).attr("time");
		
		$('#time').append('<option value="'+time+'" selected>'+time+'</option>');
			 
	    var accounts = $(this).attr("accounts");
        var accountsArr = accounts.split(",");
		 
		 $('.uselect2').val(accountsArr).trigger('change')
		 
	 	$("#notes").val($(this).attr("notes"));
		
	 });
	
	$(document).ready(function() {
		
		function exportAll(){
		
			window.location.href = '<? echo base_url('admin/apps/exportAll/tbl_locations/locations') ?>';

		}
		
		$('#usersTable').DataTable({
		  'processing': true,
		  'serverSide': true,
		  'serverMethod': 'post',
		  "dom": 'Bfrtip',	
		  "lengthMenu": [[10, 25, 50,100,500], [10, 25, 50,100,500]],
		  buttons: [
				'pageLength',
				{
				extend: 'excelHtml5',
				title:'Customers Ongoweoweh',
					exportOptions: {
						columns: [2,3,4,5,6,7,8,9,10,11,12,13,14]
					}
				},
				{
				extend: 'csvHtml5',
				title:'Customers Ongoweoweh',
					exportOptions: {
						columns: [2,3,4,5,6,7,8,9,10,11,12,13,14]
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
			  'url':'<? echo base_url('admin/apps/getLocations') ?>',
			  /*'success' : function(data){
				  
				  console.log(data);
				  
			  },
			  'error' : function(data){
				  
				  console.log(data);
				  
			  }*/
		  },
		  'columns': [
			   { data: 'check',defaultContent : ""},
			   { data: 'Actions',defaultContent : ""},
         	   { data: 'locid',defaultContent : ""},
			   { data: 'nameid' ,defaultContent : ""},
			   { data: 'locname' ,defaultContent : ""},
			   { data: 'loccode' ,defaultContent : ""},
			   { data: 'address',defaultContent : "" },
			   { data: 'city',defaultContent : "" },
			   { data: 'state',defaultContent : "" },
			   { data: 'zip',defaultContent : "" },
			   { data: 'country',defaultContent : "" },
			   { data: 'status',defaultContent : "" },
			   { data: 'Type',defaultContent : "" },
			   { data: 'import_date',defaultContent : "" },
			   { data: 'accounts',defaultContent : "" },
			   { data: 'notes',defaultContent : "" }
//			   { data: 'locationtest' }
		  ]
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

					$(".error").html('<div class="alert alert-success">Customer Successfully Updated</div>');
					setTimeout(function(){ location.reload() },2000);

				}else{

					$(".error").html('<div class="alert alert-danger">'+data+'</div>');

				}

			},
			error : function(data){
				console.log(data);
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

				console.log(data);
				$(".loader").hide();
				$(".cSubmit").show();

			}

		});

	});	

	$("#uloc").submit(function(e){

		e.preventDefault();
		var fdata = $(this).serialize();
		
		$.ajax({

			type : "post",
			data : fdata,
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
				console.log(data);	
				$(".lloader").hide();
				$(".cSubmit").show();

			}

		});

	});
	
	function archiveFunction(id){
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
				url: '<?php echo base_url() ?>admin/locations/delLocation/'+id,
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
				$("#loader").show();
				$(".impsubmit").hide();
			},
			success: function(data){
				
				$(".impsubmit").show();
				$("#loader").hide();
				$("#emsg").hide();
				$("#smsg").hide();
				console.log("Step2 Output:",data);
				if(data.Status == "Success"){
					$("#errorTable").hide();
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
					$("#errorTable").hide();
					$("#emsg").show();
					$("#emsg").html(data.Message);
				}
			},
			error: function(jqxhr,txtStatus,error){
				$("#errorTable").hide();
				$(".impsubmit").show();
				console.log("Step2 Output:",jqxhr);
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
	$("#errorTable").html("");
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
	var append ='<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first'+nIndex+'"><div class="row" style="margin-bottom:10px;"><div class="col-md-3"><input type="radio" name="setup[]" value="1" onchange="updateModule(this.value,\''+nIndex+'\');" checked="checked">Update every record</div><div class="col-md-3"><input type="radio" name="setup[]" value="2" onchange="updateModule(this.value,\''+nIndex+'\');">Add Criteria</div><div class="col-md-5"></div><div class="col-md-1"><p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeRule(\'first'+nIndex+'\');"><i class="fa fa-trash"></i></p></div></div><div class="row" style="display: none;margin-bottom: 10px;" id="criteria'+nIndex+'"><div class="col-md-1"><select name="cause[]" id="cause" class="form-control causeData" style="display: none"><option value="where">When</option></select><p style="margin-top: 5px;font-weight: bold">When</p></div><div class="col-md-3"><select name="field[]" id="field" class="form-control fieldData"><option value="nameid">Name - ID</option><option value="locname">Location Name</option><option value="loccode">Location Code</option><option value="address">Address</option><option value="city">City</option><option value="state">State</option><option value="zip">Zip</option><option value="country">Country</option><option value="status">Status</option><option value="Type">Type</option><option value="import_date">Import Date</option><option value="accounts">Accounts</option><option value="notes">Notes</option></select></div><div class="col-md-3"><select name="value[]" id="value" class="form-control valueData"><option value="contains">contains</option><option value="does not contain">does not contain</option><option value="is">is</option><option value="is not">is not</option><option value="starts with">starts with</option><option value="ends with">ends with</option><option value="is blank">is blank</option><option value="is not blank">is not blank</option></select></div><div class="col-md-4"><input type="text" name="svalue[]" id="svalue" class="form-control svalueData"></div><div class="col-md-1"></div></div><div class="row"><div class="col-md-1"><p style="margin-top:5px;font-weight: bold">Value</p></div><div class="col-md-5"><div class="form-group row"><label class="col-sm-2 col-form-label">Set</label><div class="col-sm-10"><select name="" class="form-control" onchange="updateSet(this.value,\''+nIndex+'\');"><option value="1">to a custom value</option><option value="2">to a record value</option></select></div></div></div><div class="col-md-3"><input type="text" name="updatedvalue[]" class="form-control uvalue'+nIndex+'"><select name="updatedvalue[]" class="form-control vvalue'+nIndex+'" style="display: none"><option value="nameid">Name - ID</option><option value="locname">Location Name</option><option value="loccode">Location Code</option><option value="address">Address</option><option value="city">City</option><option value="state">State</option><option value="zip">Zip</option><option value="country">Country</option><option value="status">Status</option><option value="Type">Type</option><option value="import_date">Import Date</option><option value="accounts">Accounts</option><option value="notes">Notes</option></select></div></div></div>';
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
		data: {"table":"tbl_locations","appId":'<? echo $appid; ?>', "column":column},
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
		data:{ "status": status, "table":"tbl_locations","appId":'<? echo $appid; ?>',"column":$("#collection_field").val() },
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
			var con_col = "";
			$("."+ref).show();
			
			if(column == "locid"){
				con_col = "locid-text";
			}
			if(column == "locname"){
				con_col = "locname-select";
			}
			if(column == "loccode"){
				con_col = "loccode-text";
			}
			if(column == "address"){
				con_col = "address-textarea";
			}
			if(column == "city"){
				con_col = "city-text";
			}
			if(column == "state"){
				con_col = "state-text";
			}
			if(column == "zip"){
				con_col = "zip-text";
			}
			if(column == "country"){
				con_col = "country-text";
			}
			if(column == "status"){
				con_col = "status-select";
			}
			if(column == "Type"){
				con_col = "Type-select";
			}
			if(column == "import_date"){
				con_col = "import_date-date";
			}
			if(column == "accounts"){
				con_col = "accounts-multiselect";
			}
			if(column == "notes"){
				con_col = "notes-text";
			}
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions",
				dataType : 'json',
				data : {column : con_col,table:"tbl_locations","onchangeColref":"updateonchangeConditionLocation",uopid:lopid},
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
			var con_col = "";
			$("."+ref).show();
			
			if(column == "locid"){
				con_col = "locid-text";
			}
			if(column == "locname"){
				con_col = "locname-select";
			}
			if(column == "loccode"){
				con_col = "loccode-text";
			}
			if(column == "address"){
				con_col = "address-textarea";
			}
			if(column == "city"){
				con_col = "city-text";
			}
			if(column == "state"){
				con_col = "state-text";
			}
			if(column == "zip"){
				con_col = "zip-text";
			}
			if(column == "country"){
				con_col = "country-text";
			}
			if(column == "status"){
				con_col = "status-select";
			}
			if(column == "Type"){
				con_col = "Type-select";
			}
			if(column == "import_date"){
				con_col = "import_date-date";
			}
			if(column == "accounts"){
				con_col = "accounts-multiselect";
			}
			if(column == "notes"){
				con_col = "notes-text";
			}
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions",
				dataType : 'json',
				data : {column : con_col,table:"tbl_locations","onchangeColref":"updateonchangeConditionLocation",uopid:lopid},
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
			if(selection == "import_date"){
				selection = "import_date-date";
			}
			var date = getDate();
			
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

 