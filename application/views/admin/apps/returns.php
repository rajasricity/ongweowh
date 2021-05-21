
<? admin_header(); ?>
       
<? 
$_SESSION['appid'] = $l[0]['appId'];
admin_sidebar(); 
$appid = $l[0]['appId'];
$aid = $this->uri->segment(4);

$mdb = mongodb;
$lcolumns = $this->admin->getRow("",["table"=>"tbl_returns"],[],$this->admin->getAppdb().".settings");

$times = ['12:00am','12:15am','12:30am','12:45am','01:00am','01:15am','01:30am','01:45am','02:00am','02:15am','02:30am','02:45am','03:00am','03:15am','03:30am','03:45am','04:00am','04:15am','04:30am','04:45am','05:00am','05:15am','05:30am','05:45am','06:00am','06:15am','06:30am','06:45am','07:00am','07:15am','07:30am','08:00am','08:15am','08:30am','08:45am','09:00am','09:15am','09:30am','10:00am','10:15am','10:30am','10:45am','11:00am','11:15am','11:30am','11:45am','12:00pm','12:15pm','12:30pm','12:45pm','01:00pm','01:15pm','01:30pm','01:45pm','02:00pm','02:15pm','02:30pm','02:45pm','03:00pm','03:15pm','03:30pm','03:45pm','04:00pm','04:15pm','04:30pm','04:45pm','05:00pm','05:15pm','05:30pm','05:45pm','06:00pm','06:15pm','06:30pm','06:45pm','07:00pm','07:15pm','07:30pm','08:00pm','08:15pm','08:30pm','08:45pm','09:00pm','09:15pm','09:30pm','10:00pm','10:15pm','10:30pm','10:45pm','11:00pm','11:15pm','11:30pm','11:45pm'];

$tasksCount = $this->mongo_db->where(["table"=>"tbl_returns","appId"=>$_SESSION['appid']])->count("tbl_tasks");

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
	.col-md-1,.col-md-3,.col-md-4{
		padding-left: 5px !important;
		padding-right: 5px !important;
	}
	.fa-trash{
		cursor: pointer;
	}
</style>
<link href="<? echo base_url(); ?>assets/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css" rel="stylesheet" type="text/css" media="screen">

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
                                        <li class="breadcrumb-item active">Pickups</li>
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
                                    <div class="card-body" style="padding:0px;">
<?
$mng = $this->admin->Mconfig();
$locations = $this->admin->getArray($mng,["status"=>'Active'],["sort"=>["locname"=>1]],"$database.tbl_locations");
$items = $this->admin->getArray($mng,["status"=>'Active'],[],"$database.tbl_items");
$users = $this->admin->getArray($mng,["status"=>'Active','role'=>['$ne'=>'superadmin'],"appId"=>$appid],[],"$mdb.tbl_auths");
?>
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#messages1" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                                    <span class="d-none d-sm-block"><i class="dripicons-location"></i> Pickups</span>   
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#create" role="tab">
                                               <span class="d-block d-sm-none"><i class="far fa-plus"></i></span>
                                               <span class="d-none d-sm-block"><i class="ti-plus"></i> Add Pickup</span>   
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a class="nav-link " data-toggle="tab" href="#import" role="tab">
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

                                            <div class="tab-pane active p-3" id="messages1" role="tabpanel">
                                                
                                                <div class="row">
													<div class="col-lg-12">
														<div class="">
															<div class="card-body" style="padding:0px;">

<div class="row">
	<div class="col-md-6">
		<a href="#" style="color:red" onclick="openFilter();">Add Filters</a>
	</div>
	<div class="col-md-6 text-right">
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

<div class="table-rep-plugin">
<div class="table-responsive allLoc">
<table class="table mb-0 table-bordered" style="width:1200px" id="returnsTable">
	<thead class="thead-light">
		<tr>
			 <th style="width:10px"data-orderable="false"><input type="checkbox" id="selectAll"></th> 
			<th style="width:30px" class="filter">&nbsp;#&nbsp;</th>
			<th style="width:100px" class="filter">Pickup ID</th>
			<th style="width:100px" class="filter">Vendor Reference</th>
			<th style="width:150px" class="filter">Ongweoweh Reference</th>
			<th style="width:120px" class="filter">Shipment Date</th>
			<th style="width:100px" class="filter">Quantity</th>
			<th style="width:140px" class="filter">Item</th>
			<th style="width:150px" class="filter">To Location</th>
			<th style="width:160px" class="filter">To Location Code</th>
			<th style="width:100px" class="filter">Vendor Process Date</th>
			<th style="width:100px" class="filter">UMI</th>
		</tr>
	</thead>
	<tbody></tbody>
	<tfooter>
		<tr style="background-color: #f1f1f1">
			<th style="width:10px;padding: 5px">&nbsp;#&nbsp;</th>
			 <th style="width:10px;padding: 30px"></th> 
			<th style="width:100px" class="filter">Pickup ID</th>
			<th style="width:100px;padding: 5px">Vendor Reference</th>
			<th style="width:150px;padding: 5px">Ongweoweh Reference</th>
			<th style="width:120px;padding: 5px">Shipment Date</th>
			<th style="width:100px;padding: 5px">Quantity</th>
			<th style="width:140px;padding: 5px">Item</th>
			<th style="width:150px;padding: 5px">To Location</th>
			<th style="width:160px;padding: 5px">To Location Code</th>
			<th style="width:100px;padding: 5px">Vendor Process Date</th>
			<th style="width:100px;padding: 5px">UMI</th>
		</tr>
	</thead>
</table>
</div>
</div>
																
																

															</div>
														</div>
													</div>
												</div>
                                                
                                            </div>
                                            
                                            <div class="tab-pane p-3" id="import" role="tabpanel">
<?
$mng = $this->admin->Mconfig();
$row = $this->admin->getRow($mng,["table"=>"tbl_returns"],[],$database.".settings");
$labels = $row->labels;
$columns = $row->columns;
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
	<? foreach($labels as $key=>$value){?>
	<option value="<? echo $columns[$key]; ?>"><? echo $value; ?></option>
	<?}?>
	<!-- <option value="2">Yes, the headers are on row 2.</option>
	<option value="0">Nope, the spreadsheet doesn't have a headers row</option> -->
</select>
<p>Match a field to a column from your Excel. The import will use this match to search for an existing record to update. A new record will be added if no match exists.</p>

<button class="btn btn-primary arrow-none waves-effect waves-light" type="submit" id="iSubmit" disabled="disabled">
<i class="dripicons-upload"></i> Upload</button>
															
															<div class="mloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 25%"></div>
															<div class="merror"></div>

														 </div>
                                          
                                          
													    <div class="col-md-3">
													    	
													    </div> 
                                          
                                          				<div class="col-md-3 m-t-30" align="right">
                                          					
                              <a href="<? echo base_url('assets/downloads/Pickups.xlsx') ?>" class="btn btn-info">
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
	<? foreach($labels as $key=>$value){?>
	<option value="<? echo $columns[$key]; ?>"><? echo $value; ?></option>
	<?}?>
				</select>
			</td>
		</tr>
	</table>
<form id="formstep2">
<!-- <form action="<? echo base_url('admin/ImportData/submitStep2') ?>" method="post"> -->
<input type="hidden" name="table" id="table" value="tbl_returns">
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

											<div class="tab-pane p-3" id="create" role="tabpanel">
                                            
                                            	<form id="addPickup" method="post">
												   <div class="row">

<div class="col-md-3" style="margin-bottom: 20px;">
<b>Vendor Reference <span style="color: red">*</span></b>
<input type="text" name="chepreference" class="form-control" required>
</div>

<div class="col-md-3" style="margin-bottom: 20px;">
<b>Ongweoweh Reference <span style="color: red">*</span></b>
<input type="text" name="ongreference" class="form-control" required>
</div>

<div class="col-md-3" style="margin-bottom: 20px;">
<b>Shipment Date <span style="color: red">*</span></b>
<input type="date" name="shippmentdate" class="form-control" min="<? echo date("2015-01-01",time()) ?>" max="<? echo date('Y-m-d',time()) ?>" required="required">
</div>

<div class="col-md-3" style="margin-bottom: 20px;">
<b>Quantity</b>
<input type="number" name="quantity" pattern="^[1-9]" id="iqty" class="form-control" required>
</div>

<div class="col-md-3" style="margin-bottom: 20px;">
<b>Item <span style="color: red">*</span></b>
<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="item" required>
<option value="">Choose Item</option>
<? foreach($items as $item){ ?>
<option value="<? echo $item->item_name; ?>"><? echo $item->item_name; ?></option>
<? } ?>
</select>
</div>


<div class="col-md-3" style="margin-bottom: 20px;">
<b>To Location <span style="color: red">*</span></b>
<select class="select2 form-control select2-multiple" style="height: 35px !important;" data-placeholder="Choose ..." name="tlocation" required>
<option value="">Choose Selection</option>
<? foreach($locations as $location){ ?>
<option value="<? echo $location->locname; ?>"><? echo $location->locname." ".$location->loccode; ?></option>
<? } ?>
</select>
</div>


<div class="col-md-3" style="margin-bottom: 20px;">
<b>Vendor process date (Chep process date)</b>
<input type="date" name="chepprocessdate" class="form-control">
</div>
													
													<div class="col-md-3" style="margin-bottom: 20px;">
<b>UMI</b>
<input type="text" name="umi" class="form-control">
</div>

													</div>

													<div class="row">
														<div class="col-md-9">
															<div class="rmloader" style="display:none">
	<center><img src="<? echo base_url('assets/images/loader.gif') ?>" width="80" height="80" ></center>
															</div>
															<div class="rmerror"></div>
														</div>
														<div class="col-md-3 text-right">
<input type="hidden" name="appId" value="<? echo $appid; ?>">
<input type="hidden" name="deleted" value="0">
<input type="hidden" name="cdate" value="<? echo date('Y-m-d h:i:s', time()); ?>">
<input type="submit" name="submit" class="btn btn-primary" value="ADD PICKUP">
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
																											   foreach($lcolumns->labels as $key => $labels){										if($lcolumns->columns[$key] != "tlcoationcode" && $lcolumns->columns[$key] != "umi"){			
																											?>

																												<option value="<? echo $lcolumns->columns[$key]."-".$lcolumns->dataType[$key] ?>"><? echo $labels ?></option>

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
																						
																							<input type="hidden" name="table" value="tbl_returns">
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
																		
																			/*	$vrid = "";
																				
																				$vrules = $this->mongo_db->get_where("tbl_validation_rules",["table"=>"tbl_returns","field"=>$lcolumns->columns[$key],"appId"=>$aid])[0];

																				if($vrules){

																					$vrid = $vrules["_id"]->{'$id'};

																				}*/

																				$crid = "";
																				
																				$crules = $this->mongo_db->get_where("tbl_conditional_rules",["table"=>"tbl_returns","field"=>$lcolumns->columns[$key],"appId"=>$aid])[0];

																				if($crules){

																					$crid = $crules["_id"]->{'$id'};

																				}			

																		?>
																		
																		<tr>
																			
																			<td><? echo ucfirst($lcolumns->dataType[$key]) ?></td>
																			<td><a href="javascript:void(0)" class="<? if($lcolumns->columns[$key] != "tlcoationcode"){ ?>cmodal<? } ?>" crid="<? echo $crid ?>" colname="<? echo $lc ?>" fname="<? echo $lcolumns->columns[$key] ?>" style="font-size: 16px">
																				<strong><? echo $lc ?> &nbsp;&nbsp;

																					<? //echo ($vrid != "") ? '<i class="fa fa-check" data-toggle="tooltip" title="This field has validation rules"></i>' : '' ?>&nbsp;
																					<? echo ($crid != "") ? '<i class="fa fa-random" data-toggle="tooltip" title="This field has conditional rules"></i>' : '' ?>
																					
																				</strong>
																			</a>
																			</td>
																			<td>
																				 <button id="customDropdown" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-color: transparent; border-radius: 25px">
																				  <i class="fa fa-cog"></i><i class="fa fa-caret"></i>
																				</button><? if($lcolumns->columns[$key] != "tlcoationcode"){ ?>
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
                        

<!-- <?

$mng = $this->admin->Mconfig();
//$locations = $this->admin->getArray($mng,[],[],"$database.tbl_locations");
//$items = $this->admin->getArray($mng,[],[],"$database.tbl_items");
$users = $this->admin->getArray($mng,["status"=>'Active','role'=>['$ne'=>'superadmin']],[],"$mdb.tbl_auths");
?> -->
<div class="modal fade" id="transfersModal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" style="width:100%">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #333547;color:#fff">
					<h5 class="modal-title mt-0" id="myLargeModalLabel">Update Pickup</h5>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color:#fff;">×</button>
				</div>
				<div class="modal-body">
					
					<form action="#" id="updateReturns" method="post">
						<input type="hidden" name="id" id="id">
					   <div class="row">

					   		 <div class="col-md-3"> 

								<div class="form-group">
									<label>Vendor Reference <span style="color:red">*</span></label>
					<input type="text" class="form-control" name="chepreference" id="chepreference" required>
								</div>

							 </div>

							  <div class="col-md-3"> 

								<div class="form-group">
									<label>Ongweoweh Reference <span style="color:red">*</span></label>
					<input type="text" class="form-control" name="ongreference" id="ongreference" required>
								</div>

							 </div>

							 <div class="col-md-3"> 

								<div class="form-group">
									<label>Shipment Date <span style="color:red">*</span></label>
					<input type="date" class="form-control" name="shippmentdate" min="<? echo date("2015-01-01",time()) ?>" max="<? echo date('Y-m-d',time()) ?>" id="shippmentdate" required>
								</div>

							 </div>

							 <div class="col-md-3"> 

								<div class="form-group">
									<label>Quantity</label>
			<input type="number" class="form-control" name="quantity" pattern="^[1-9]" id="quantity" required>
								</div>

							 </div>	
							 

							 
							 <div class="col-md-3"> 

								<div class="form-group">
					<label>Item <span style="color:red">*</span></label>
					<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="item" id="item">
				    <? foreach($items as $item){ ?>
				      <option value="<? echo $item->item_name; ?>"><? echo $item->item_name; ?></option>
				    <? } ?>
				    </select>
								</div>

							 </div>
							 

							 <div class="col-md-3"> 

								<div class="form-group">
									<label>To Location <span style="color:red">*</span></label>
				<select class="select2 form-control select2-multiple getfLocation" style="height: 35px !important;" data-placeholder="Choose ..." name="tlocation" id="tlocation1">
				    <? foreach($locations as $location){ ?>
				      <option value="<? echo $location->locname; ?>"><? echo $location->locname; ?></option>
				    <? } ?>
				    </select>
								</div>

							 </div>

							 <div class="col-md-3"> 

								<div class="form-group">
									<label>To Location Code</label>
			<input type="text" class="form-control" name="tlcoationcode" id="tlcoationcode" readonly>
								</div>

							 </div>	

							 

							 <div class="col-md-3"> 

								<div class="form-group">
									<label>Vendor process date (Chep process date)</label>
			<input type="date" class="form-control" name="chepprocessdate" id="chepprocessdate">
								</div>

							 </div>
							 
							<div class="col-md-3" style="margin-bottom: 20px;">
								<b>UMI</b>
								<input type="text" name="umi" id="umi" class="form-control">
							</div>

						</div>


						<div class="row">

							<div class="col-md-9">

								<div id="update_loader" style="display: none">
									<img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 10%">
								</div>
								<div class="rerror"></div>

							</div>

							<div class="col-md-3" align="right">
	<button class="btn btn-default arrow-none waves-effect waves-light" data-dismiss="modal" style="border: 1px solid lightgrey" type="button">Close</button>
	<button class="btn btn-primary arrow-none waves-effect waves-light" type="submit">Update</button>
							</div>

						</div>

					</form>
					
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->   
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
		<select name="field[]" class="form-control fieldData getLocdata loc_filter" id="updLoc1" lopid="updLoc1" lo_id="locgetwhenRef">
		    <option value="">Select</option>
		    <option value="id">Pickup Id</option>
		    <option value="chepreference">Vendor Reference</option>
			<option value="ongreference">Ongweoweh Reference</option>
			<option value="shippmentdate">Shipment Date</option>
			<option value="quantity">Quantity</option>
			<option value="item">Item</option>
			<option value="umi">UMI</option>
			<option value="tlocation">To Location</option>
			<option value="tlcoationcode">To Location Code</option>
			<option value="chepprocessdate">Vendor Process Date</option>
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
                    </div>
                    <!-- container-fluid -->

                </div>
                <!-- content -->
                
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

								<input type="hidden" name="table" value="tbl_returns">
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
                 	 	
<? admin_footer(); ?>
<script src="<? echo base_url(); ?>assets/plugins/select2/js/select2.min.js"></script>
<script src="<? echo base_url(); ?>assets/js/cron/cron_returnstasks.js"></script>
<script src="<? echo base_url(); ?>assets/js/vrules/vrules_returns.js"></script>
<script src="<? echo base_url(); ?>assets/js/crules/crules_returns.js"></script>
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

								<input type="hidden" name="vrtable" value="tbl_returns" id="vTable">
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

								<input type="hidden" name="contable" value="tbl_returns" id="cTable">
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


<div class="modal fade modal-fullscreen" id="modal-fullscreen" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="padding-left: 10px !important">
	  <div class="modal-dialog modal-dialog1" style="z-index: 9999">
		<div class="modal-content modal-content1">
		  <div class="modal-header card-header" style="display: block">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle" style="color: white"></i></button>
			<h4 class="modal-title" id="myModalLabel" style="text-align: center;color: white">Update Pickups Records</h4>
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
									   
									   if($lcolumns->columns[$key] != "tlcoationcode" && $lcolumns->columns[$key] != "umi"){
								?>

									<option value="<? echo $lcolumns->columns[$key]."-".$lcolumns->dataType[$key] ?>"><? echo $labels ?></option>

								<? }} ?>
								
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



	
<input type="hidden" name="base_url" id="base_url" value="<? echo base_url() ?>">
<input type="hidden" name="updatedWhencount" id="updatedWhencount" value="1"> 
<input type="hidden" name="updatedValuescount" id="updatedValuescount" value="1"> 


<script>
		
	$(".getfLocation").change(function(){
		
		var location = $(this).val();
		
		$.ajax({
			
			type : "post",
			url : "<? echo base_url('admin/apps/getlocationcode') ?>",
			data : {location : location},
			dataType : 'json',
			success : function(data){
				
				$("#tlcoationcode").val(data.loccode);
				console.log(data);
			},
			error : function(data){
				
				console.log(data);
				
			}
			
		});
		
	});
		
// task start
	
	$("#tasksTable").DataTable({
		
		/*"dom": 'Bfrtip',
		 buttons: [
				'csv', 'excel','pageLength'
			],*/
		 "bProcessing": true,
         "sAjaxSource": "<? echo base_url(); ?>admin/tasks/getAlltasks/tbl_returns",
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

// bulk operation starts	
	
	$("#updateLocrecords").submit(function(e){
		
		e.preventDefault();
		var fdata = $(this).serialize();
		
		var locations = [];
			$.each($("input[name='lid']:checked"), function(){
			locations. push($(this). val());
		});
		
		
		var form_data = fdata+'&'+$.param({ 'targets': locations,'table' : 'tbl_returns' })
		
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

					$(".uberror").html('<div class="alert alert-success">selected Pickups updated successfully</div>')
					setTimeout(function(){ location.reload() },2000);

				}else{

					$(".uberror").html('<div class="alert alert-danger">'+data+'</div>')

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
			
		} else if(column == "tlocation"){
			
			$(".bindField").html('<div class="form-group bindField"><select class="select2 form-control select2-multiple" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" required><option value="">Choose Selection</option><? foreach($locations as $location){ ?><option value="<? echo str_replace("'"," ",$location->locname); ?>"><? echo str_replace("'"," ",$location->locname); ?></option><? } ?></select></div>');
			
		} else{
			
			if(column == "shippmentdate"){
			
				$(".bindField").html('<input type="'+datatype+'" class="form-control" name="value[]" min="<? echo date("2015-01-01",time()) ?>" max="<? echo date('Y-m-d',time()) ?>">');
			
			}else{
				
				$(".bindField").html('<input type="'+datatype+'" class="form-control" name="value[]">');
				
			}
			
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
			
		} else if(column == "tlocation"){
			
			$("."+ref).html('<div class="form-group '+ref+'"><select class="select2 form-control select2-multiple" style="height: 35px !important;" data-placeholder="Choose ..." name="value[]" required><option value="">Choose Selection</option><? foreach($locations as $location){ ?><option value="<? echo str_replace("'"," ",$location->locname); ?>"><? echo str_replace("'"," ",$location->locname); ?></option><? } ?></select></div>');
			
		}else{
			
			if(column == "shippmentdate"){
			
				$("."+ref).html('<input type="'+datatype+'" class="form-control" name="value[]" min="<? echo date("2015-01-01",time()) ?>" max="<? echo date('Y-m-d',time()) ?>">');
			
			}else{
				
				$("."+ref).html('<input type="'+datatype+'" class="form-control" name="value[]">');
				
			}
			
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
				data : {locations : locations,table:'tbl_returns'},
				beforeSend : function(){
					
					$(".bloader").show();
					
				},
				success : function(data){
					console.log(data);	
					$(".bloader").hide();
					if(data == "success"){
						
						$(".berror").html('<div class="alert alert-success">selected Pickups deleted successfully</div>')
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
				$(wrapper).append('<div class="row sub_p_rem'+x+'"><div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>Set</label></div><div class="col-md-3" align="left"><div class="form-group"><select ref="reference'+x+'" class="form-control getupdatedColumn" name="columns[]"><? foreach($lcolumns->labels as $key => $labels){ if($lcolumns->columns[$key] != "tlcoationcode" && $lcolumns->columns[$key] != "umi"){ ?><option value="<? echo $lcolumns->columns[$key]."-".$lcolumns->dataType[$key] ?>"><? echo $labels ?></option><? }} ?></select></div></div><div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>To</label></div><div class="col-md-3" align="left"><div class="form-group reference'+x+'"><input type="text" class="form-control" name="value[]"></div></div><div class="col-md-2"><i class="fa fa-plus-circle addDom fa-2x" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-times-circle remove_button" lid="sub_p_rem'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>'); //Add field html

				y++;
			}
		});
		
		$(wrapper).on("click",".addDom",function(){
			//Check maximum number of input fields
			if(x < maxField){ 
				x++; //Increment field counter
				$(wrapper).append('<div class="row sub_p_rem'+x+'"><div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>Set</label></div><div class="col-md-3" align="left"><div class="form-group"><select ref="reference'+x+'" class="form-control getupdatedColumn" name="columns[]"><? foreach($lcolumns->labels as $key => $labels){ if($lcolumns->columns[$key] != "tlcoationcode" && $lcolumns->columns[$key] != "umi"){?><option value="<? echo $lcolumns->columns[$key]."-".$lcolumns->dataType[$key] ?>"><? echo $labels ?></option><? }} ?></select></div></div><div class="col-md-1" align="right" style="margin-top: 5px;font-size: 18px"><label>To</label></div><div class="col-md-3" align="left"><div class="form-group reference'+x+'"><input type="text" class="form-control" name="value[]"></div></div><div class="col-md-2"><i class="fa fa-plus-circle addDom fa-2x" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-times-circle remove_button" lid="sub_p_rem'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>'); //Add field html

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
			y--;
//			alert(id+"-"+x)
		});
	});
	
// bulk operation ends	
	
	
$(function(){
$(".select2").select2();
});
</script>
<script>
	function openFilter(){
		$("#myFilter").modal('show');
	}
	


$("#addPickup").on("submit",function(e){
		e.preventDefault();

		var qty = $("#iqty").val();
		
		if(qty == 0){
			
			$('.rmerror').html('<div class="alert alert-danger">Please enter postive or negative integer values in Quantity</div>');
			return false;
			
		}
		var form_data = $("#addPickup").serialize();
		$.ajax({
			type : "POST",
			url : "<? echo base_url('admin/locations/addPickup') ?>",
			data: form_data,
			dataType: "json",
			beforeSend : function(){
				$('.rmloader').show();
			},
			success : function(data){
				$('.rmloader').hide();
				if(data.Status == "Success"){
					$('.rmerror').html('<div class="alert alert-success">Successfully Pickup Added</div>');
					setTimeout(function(){
						location.reload()
					},2000);
				}else{
					
					$('.rmerror').html('<div class="alert alert-danger">'+data.Message+'</div>');
				}

				console.log(data);		
			},
			error : function(jq,txt,error){
				$('.rmloader').hide();
				console.log(jq);		
			}

		});

	});
				
	$(document).ready(function() {
		
		function exportAll(){
		
			window.location.href = '<? echo base_url('admin/apps/exportAll/tbl_returns/pickups') ?>';

		}
		
		$('#returnsTable').DataTable({
		  'processing': true,
		  'serverSide': true,
		  'serverMethod': 'post',
		  "dom": 'Bfrtip',	
			"lengthMenu": [[10, 25, 50,100,500], [10, 25, 50,100,500]],
		  buttons: [
				'pageLength',
				{
				extend: 'excelHtml5',
				title:'Pickups Ongoweoweh',
					exportOptions: {
						columns: [2,3,4,5,6,7,8,9,10,11]
					}
				},
				{
				extend: 'csvHtml5',
				title:'Pickups Ongoweoweh',
					exportOptions: {
						columns: [2,3,4,5,6,7,8,9,10,11]
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
			  'url':'<? echo base_url('admin/apps/getReturns') ?>',
			  /*'success' : function(data){
				  
				  console.log(data);
				  
			  },
			  'error' : function(data){
				  
				  console.log(data);
				  
			  }*/
		  },
		  'columns': [
			   { data: 'check',defaultContent : "" },
			   { data: 'Actions',defaultContent : "" },
			   { data: 'id',defaultContent : "" },
               { data: 'chepreference',defaultContent : ""  },
               { data: 'ongreference',defaultContent : ""  } ,
               { data: 'shippmentdate',defaultContent : ""  },
               { data: 'quantity' ,defaultContent : "" },
               { data: 'item' ,defaultContent : "" },
               { data: 'tlocation',defaultContent : ""  },
               { data: 'tlcoationcode',defaultContent : ""  },
               { data: 'chepprocessdate' ,defaultContent : "" },
               { data: 'umi' ,defaultContent : "" }
		  ]
	   });
		
		
		
$("#updateReturns").submit(function(e){
	e.preventDefault();
	var qty = $("#quantity").val();
		
	if(qty == 0){

		$('.rerror').html('<div class="alert alert-danger">Please enter postive or negative integer values in Quantity</div>');
		return false;

	}
	
	var fdata = $(this).serialize();
	$.ajax({
		type : "post",
		data : fdata,
		dataType:'json',
		url : "<? echo base_url('admin/apps/ureturns') ?>",
		beforeSend : function(data){
			$("#update_loader").show();
		},
		success : function(data){
			console.log(data);
			$("#update_loader").hide();
			
			if(data.Status == "Success"){
				
				$(".rerror").html('<div class="alert alert-success">'+data.Message+'</div>');
				setTimeout(function(){ location.reload() },2000);
				
			}else{
				
				$(".rerror").html('<div class="alert alert-danger">'+data.Message+'</div>');
				
			}
			
		},
		error : function(data){
			
			$("#update_loader").hide();
			$(".cSubmit").show();
			
		}
		
	});
	
});

	} );

	$(document).on("click",".editLocate",function(){
		$(".rerror").html('');
		var keys = ['id','chepreference','ongreference','shippmentdate','quantity','item','tlocation1','tlcoationcode','chepprocessdate','umi'];
		$("#transfersModal").modal("show");
		for(var i=0;i< keys.length;i++){
			
			if(keys[i]=== 'item' || keys[i]=== 'tlocation1'){
				
				var value = $(this).attr(keys[i]);
				
				$("#"+keys[i]).val(value);
				$("#"+keys[i]).select2().select2('data',value);
				
			}else{
				
				var value = $(this).attr(keys[i]);
				$("#"+keys[i]).val(value);
				
			}
//			console.log("#"+keys[i] +' ---- '+arguments[i]);
		}

	});

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
		var fdata = {"cause":cause,"field":field,"value":value,"svalue":svalue,"dvalue":dvalue,"id":'<? echo $appid; ?>',"table":"tbl_returns","filter_from":"form_modal"};
		$("#myFilter").modal("hide");

		var table = $('#returnsTable').dataTable({
			 //"bProcessing": true,
			 "ajax": {
				"url": "<?php echo base_url("admin/apps/addFilter") ?>",
				"type": "POST",
				"data" : fdata
			  },
			 "aoColumns": [
         	   { mData: 'check',defaultContent : "" },
         	   { mData: 'Actions',defaultContent : "" },
         	   { mData: 'returnid',defaultContent : "" },
               { mData: 'chepreference',defaultContent : ""  },
               { mData: 'ongreference',defaultContent : ""  } ,
               { mData: 'shippmentdate',defaultContent : ""  },
               { mData: 'quantity' ,defaultContent : "" },
               { mData: 'item' ,defaultContent : "" },
               { mData: 'tlocation',defaultContent : ""  },
               { mData: 'tlcoationcode',defaultContent : ""  },
               { mData: 'chepprocessdate' ,defaultContent : "" },
               { mData: 'umi' ,defaultContent : "" }
             ], 
			  'processing': true,
			  'serverSide': true,
			  'serverMethod': 'post',
			  "destroy" : 'true', 
			  "dom": 'Bfrtip',
			  "lengthMenu": [[10, 25, 50,100,500], [10, 25, 50,100,500]],
			  "buttons": [
				'pageLength',
				{
		 		extend: 'excelHtml5',
		 		title:'Pickups Ongoweoweh',
					exportOptions: {
						columns: [2,3,4,5,6,7,8,9,10,11]
					}
		 		},
		 		{
		 		extend: 'csvHtml5',
		 		title:'Pickups Ongoweoweh',
					exportOptions: {
						columns: [2,3,4,5,6,7,8,9,10,11]
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
var n = '<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first'+i+'"><div class="row"><div class="col-md-1"><select name="cause[]" id="cause" class="form-control causeData"><option value="and">and</option><option value="or">or</option></select></div><div class="col-md-3"><select name="field[]" class="form-control getUlocdata fieldData loc_filter_dyn" lid="locid'+i+'" id="updLoc'+i+'" lopid="updLoc'+i+'" lo_id="locgetwhenRef'+i+'"><option value="">Select</option><option value="id">Pickup Id</option><option value="chepreference">Vendor Reference</option><option value="ongreference">Ongweoweh Reference</option><option value="shippmentdate">Shipment Date</option><option value="quantity">Quantity</option><option value="item">Item</option><option value="chepumi">UMI</option><option value="tlocation">To Location</option><option value="tlcoationcode">To Location Code</option><option value="chepprocessdate">Vendor Process Date</option></select></div><div class="col-md-3 dynlocgetwhenRef'+i+'"><select name="value[]" id="value" class="form-control valueData"><option value="">Select</option><option value="contains">contains</option><option value="does not contain">does not contain</option><option value="is">is</option><option value="is not">is not</option><option value="starts with">starts with</option><option value="ends with">ends with</option><option value="is blank">is blank</option><option value="is not blank">is not blank</option></select></div><div class="col-md-4"><div class="locid'+i+' locgetwhenRef'+i+' updLoc'+i+'"><input type="text" name="svalue[]" id="svalue" class="form-control svalueData"></div></div><div class="col-md-1"><p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter(\'first'+i+'\');"><i class="fa fa-trash"></i></p></div></div></div>';
$("#top").append(n);
i++;
}

function removeFilter(first){
	// console.log(first);
	$("."+first).remove();
i--;
}	


$(document).on("change",".getLocdata",function(){
	
	var val = $(this).val();
	
	if(val == "tlocation"){
		
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
	
	if(val == "tlocation"){
		
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

function archiveFunction(id) {
       Swal({
		  title: 'Are you sure?',
		  text: 'You will not be able to recover this selected data!',
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonText: 'Yes, delete it!',
		  cancelButtonText: 'No, keep it'
		}).then((result) => {
		  if (result.value) {

			Swal(
			  'Deleted!',
			  'Your Selected data has been deleted.',
			  'success'
			)
			$.ajax({
				method: 'POST',
				data: {'id' : id,"table":"tbl_returns" },
				url: '<?php echo base_url() ?>admin/apps/delReqData/'+id,
				success: function(data) {
					location.reload();   
				}
			});

		  } else if (result.dismiss === Swal.DismissReason.cancel) {
			Swal(
			  'Cancelled',
			  'Your Selected Data is safe :)',
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
				$("#loader").hide();
				$("#emsg").show();
				$("#emsg").html(error);
				$("#smsg").hide();
				$(".impsubmit").show();
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
		data: {"table":"tbl_returns","appId":'<? echo $appid; ?>', "column":column},
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
		data:{ "status": status, "table":"tbl_returns","appId":'<? echo $appid; ?>',"column":$("#collection_field").val() },
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
			if(column == "chepreference"){
				col_val = "chepreference-text";
			}
			if(column == "ongreference"){
				col_val = "ongreference-text";
			}
			if(column == "shippmentdate"){
				col_val = "shippmentdate-date";
			}
			if(column == "quantity"){
				col_val = "quantity-number";
			}
			if(column == "item"){
				col_val = "item-select";
			}
			if(column == "chepumi"){
				col_val = "chepumi-text";
			}
			if(column == "tlocation"){
				col_val = "tlocation-select";
			}
			if(column == "tlcoationcode"){
				col_val = "tlcoationcode-text";
			}
			if(column == "chepprocessdate"){
				col_val = "chepprocessdate-date";
			}
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions",
				dataType : 'json',
				data : {column : col_val,table:"tbl_returns","onchangeColref":"updateonchangeConditionLocation",uopid:lopid},
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
			if(column == "chepreference"){
				col_val = "chepreference-text";
			}
			if(column == "ongreference"){
				col_val = "ongreference-text";
			}
			if(column == "shippmentdate"){
				col_val = "shippmentdate-date";
			}
			if(column == "quantity"){
				col_val = "quantity-number";
			}
			if(column == "item"){
				col_val = "item-select";
			}
			if(column == "chepumi"){
				col_val = "chepumi-text";
			}
			if(column == "tlocation"){
				col_val = "tlocation-select";
			}
			if(column == "tlcoationcode"){
				col_val = "tlcoationcode-text";
			}
			if(column == "chepprocessdate"){
				col_val = "chepprocessdate-date";
			}
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/apps/getDatatypeconditions",
				dataType : 'json',
				data : {column : col_val,table:"tbl_returns","onchangeColref":"updateonchangeConditionLocation",uopid:lopid},
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