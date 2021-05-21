
<? admin_header(); 

$mdb = mongodb;

$query = $this->mongo_db->get_where("tbl_auths",array("email"=>$this->session->userdata("admin_email")));
$locations = $query[0]["locations"];
$aData = $this->mongo_db->get_where("tbl_apps",array("appId"=>$query[0]["appid"]));
$user = $this->admin->getRow($mng,['email'=>$this->session->userdata('admin_email')],[],"$mdb.tbl_auths");
?> 

<? admin_sidebar(); ?>            

<link href="<? echo base_url(); ?>assets/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css" rel="stylesheet" type="text/css" media="screen">
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
                                        <li class="breadcrumb-item"><a href="<? echo base_url('main/Admindashboard') ?>"><? echo $aData[0]["appname"] ?></a></li>
                                        <li class="breadcrumb-item active">Location Inventories</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                <a onclick="window.history.back();" class="btn btn-dark btn-sm float-right">
                                  <i class="fa fa-arrow-left"></i>
                                </a>
                              </div>
                                
                            </div>
                        </div>
                        <!-- end row -->
                        
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
<h5>Location Inventories</h5>
Date Range for this data is June 1st 2017 to Current
<div class="row">
  <div class="col-md-12">
    <div class="table-rep-plugin">
    
    
					<div id="accordion">

				<? $items = $this->admin->getArray("",["status"=>"Active"],[],"$database.tbl_items");
				   $id = 0;			
                   $j=1;
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
                            <div class="row">
								<div class="col-md-6">
									<a href="#" style="color:red" onclick="openFilter('<? echo $item->item_name ?>','<?php echo$j; ?>');">Add Filters</a>
								</div>
								<div class="col-md-6 text-right">
									<a href="#" style="color:green" onclick="window.location.reload();">Reset</a>
								</div>
							</div>
							<div class="table-responsive">

							  <table id="tech-companies-<? //echo $id ?>" class="table table-bordered" style="width:100% !important;border:1px solid #f1f1f1">
							  <thead class="thead-light">
								<tr>
								  <th style="padding:5px;font-size:12px;width:350px;">Location Inventory Details</th>
<!--								  <th style="padding:5px;font-size:12px;">Last Report Date</th>-->
								  <th style="padding:5px;font-size:12px;">Shipments</th>
								  <th style="padding:5px;font-size:12px;">Pickups</th>
								  <th style="padding:5px;font-size:12px;">Transfer Ins</th>
								  <th style="padding:5px;font-size:12px;width:100px;">Transfer Outs</th>
								  <th style="padding:5px;font-size:12px;">Adjustments</th>
								  <th style="padding:5px;font-size:12px;">Ending Balance</th>
								  <th style="padding:5px;font-size:12px;">Audit Count</th>
								  <th style="padding:5px;font-size:12px;">Audit Date</th>
								</tr>
							  </thead>
							  <tbody class="dyn_tr_<?php echo $j; ?>">
							 	<?

							$iss = [];
							$ret = [];
							$tin = [];
							$tou = [];
							$adj = [];
							$ebs = [];
							$ac219 = [];
					
								foreach($locations as $location){
									
									$loccode = $location->loccode;
									$locdata = $this->admin->getRow("",['loccode'=>$loccode],[],"$database.tbl_locations");

									if($locdata->status == "Active"){
									
										$appid = $user->appid;

										$locinvdata = $this->admin->getRow("",['appId'=>$appid,"item.item_name"=>$item->item_name,"loccode"=>$loccode],[],"$database.tbl_inventory");

//										echo '<pre>';
//										print_r($locinvdata);
										

										$issues=($this->common->getInventorycount($database,"tbl_issues",$appid,$loccode,"tlcoationcode",$item->item_name));
										$returns=($this->common->getInventorycount($database,"tbl_returns",$appid,$loccode,"tlcoationcode",$item->item_name));
										$tins=($this->common->getInventorycount($database,"tbl_touts",$appid,$loccode,"tlocationcode",$item->item_name));
										$touts=($this->common->getInventorycount($database,"tbl_touts",$appid,$loccode,"flcoationcode",$item->item_name));
										$adjusts=($this->common->getInventorycount($database,"tbl_adjustments",$appid,$loccode,"tlcoationcode",$item->item_name));


										$eb = ($locinvdata->starting_balance+$issues+$returns+$tins-$touts+$adjusts);

										$iss[] = $issues;
										$ret[] = $returns;
										$tin[] = $tins;
										$tou[] = $touts;
										$adj[] = $adjusts;
										$ebs[] = $eb;
										$ac219[] = $locinvdata->audit_count2019;
								?>
								
						<tr>
							<td>
								<a href="<? echo base_url('main/inventory/location/').$location->loccode ?>/off/<? echo $item->item_name ?>">
								<span class="badge badge-primary" style="font-size: 14px;white-space: unset !important;"><? echo $location->LocationName." - ".$loccode; ?></span>
								</a>
							</td>
<!--							<td><? //echo ($locinvdata->starting_balance != "") ? $locinvdata->starting_balance : 0; ?></td>-->
							<td><? echo ($issues); ?></td>
							<td><? echo ($returns); ?></td>
							<td align="right"><? echo ($tins); ?></td>
							<td align="right"><? echo ($touts); ?></td>
							<td align="right"><? echo ($adjusts); ?></td>
							<td align="right"><? echo ($eb); ?></td>
							<td align="right"><? echo (intval($locinvdata->audit_count2019) != "") ? intval($locinvdata->audit_count2019) : 0; ?></td>
							<td align="right"><? echo (intval($locinvdata->audit_date2019) != "") ? date("m-d-Y",strtotime($locinvdata->audit_date2019)) : ""; ?></td>
						</tr>
								<?}}?> 
							  
							 <tr>
								<td></td>
<!--								<td></td>-->
								<td style="font-weight: bold;text-align: right"><span id="issues_count"><? echo array_sum($iss); ?></span></td>
								<td style="font-weight: bold;text-align: right"><span id="returns_count"><? echo array_sum($ret); ?></span></td>
								<td style="font-weight: bold;text-align: right"><span id="tins_count"><? echo array_sum($tin); ?></span></td>
								<td style="font-weight: bold;text-align: right"><span id="touts_count"><? echo array_sum($tou); ?></span></td>
								<td style="font-weight: bold;text-align: right"><span id="adjustments_count"><? echo array_sum($adj); ?></span></td>
								<td style="font-weight: bold;text-align: right"><span id="ebal_count"><? echo array_sum($ebs); ?></span></td>
								<td style="font-weight: bold;text-align: right"><span id="audit19_count"><? echo array_sum($ac219) ?></span></td>
								<td style="font-weight: bold;text-align: right"></td>
							  </tr> 
							  
							  
					</tbody>
							  
					<? //$fdata = $this->common->getInventoryChepAdminConsolidated($item->item_name)[0]; 
//					   if(($fdata["issues"] != 0) && ($fdata["returns"] != 0) && ($fdata["tins"] != 0) && ($fdata["touts"] != 0) && ($fdata["adjustments"] != 0) && ($fdata["ebal"] != 0) && ($fdata["acount2019"] != 0)){			  
					?>
							  
							  
							  
					<? //} ?>		  
							</table>
						</div>
					</div>
				</div>
			</div>
			
			<? 
				   $id++;
				   $j++; } ?> 
				   
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
							<option value="locname">Location Name</option>
							<option value="loccode">Location Code</option>
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
							<input type="text" name="svalue[]" id="svalue" class="form-control svalueData">
						</div>
					</div>
					<div class="col-md-1">
						<p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter('first0');"><i class="fa fa-trash"></i></p>
					</div>
					<input type="hidden" id="myText1">
					<input type="hidden" id="myText2">
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
							
                        </div>
                    
                    				

                    </div>
                    <!-- container-fluid -->

                </div>
                <!-- content -->



<? admin_footer(); ?>
<input type="hidden" name="base_url" id="base_url" value="<? echo base_url() ?>">
<script src="<? echo base_url(); ?>assets/plugins/select2/js/select2.min.js"></script>
<script src="<? echo base_url(); ?>assets/plugins/RWD-Table-Patterns/dist/js/rwd-table.min.js"></script>
<script>
$('.table-responsive').responsiveTable();
$(".focus-btn-group").hide();				
$(document).ready(function() {

<? //$cid = 0;			

	//foreach($items as $it){ ?>	
	
		/*$("#tech-companies-<? //echo $cid ?>").DataTable({

			'processing': true,
			'serverSide': true,
			'serverMethod': 'post',
			"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		    "dom": '<"top"Bflp<"clear">>',
			"buttons": [
					'csv', 'excel','pageLength'
			],
			  'ajax': {
				  'url':'<? //echo base_url(); ?>main/inventory/getInventoryChepAdmin/<? //echo $it->item_name ?>',
				  'success' : function(data){

					  console.log(data);

				  },
				  'error' : function(data){

					  console.log(data);

				  }
			  },
			 "columns": [
				   { data: 'location' ,defaultContent: ""},
				   { data: 'last_report_date' ,defaultContent: "" },
				   { data: 'issues' ,defaultContent: "" },
				   { data: 'returns' ,defaultContent: "" },
				   { data: 'transfer_ins' ,defaultContent: "" },
				   { data: 'transfer_outs' ,defaultContent: "" },
				   { data: 'adjustments' ,defaultContent: "" },
				   { data: 'ending_balance' ,defaultContent: ""},
				   { data: 'audit_count2019' ,defaultContent: ""},
				 ],


		});
*/
<? //$cid++;} ?>	
	
/*  $.ajax({
    url:"<? //echo base_url(); ?>main/inventory/getInventoryChepAdminConsolidated",
    dataType:"json",
    success: function(data){
      console.log(data[0]);
      $("#issues_count").html(data[0].issues);
      $("#returns_count").html(data[0].returns);
      $("#tins_count").html(data[0].tins);
      $("#touts_count").html(data[0].touts);
      $("#adjustments_count").html(data[0].adjustments);
      $("#ebal_count").html(data[0].ebal);
      $("#audit17_count").html(data[0].acount2017);
      $("#audit18_count").html(data[0].acount2018);
      $("#audit19_count").html(data[0].acount2019);
      $("#variance_count").html(data[0].varieance);
    }
  })*/

});	
function openFilter(e,f){
	$('#updLoc1').val('');
	$('.valueData').val('');
	$('.svalueData').val('');
	$("#myFilter").modal('show');
	document.getElementById("myText1").value = e;
	document.getElementById("myText2").value = f;
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
var n = '<div style="background-color: #f1f1f1;padding:10px;margin-bottom:5px;" class="first'+i+'"><div class="row"><div class="col-md-1"><select name="cause[]" id="cause" class="form-control causeData"><option value="and">and</option><option value="or">or</option></select></div><div class="col-md-3"><select name="field[]" class="form-control getUlocdata fieldData loc_filter_dyn" lid="locid'+i+'" id="updLoc'+i+'" lopid="updLoc'+i+'" lo_id="locgetwhenRef'+i+'" ><option value="locname">Location Name</option><option value="loccode">Location Code</option><option value="issues">Shipments</option><option value="returns">Pickups</option><option value="transfer_ins">Transfer Ins</option><option value="transfer_outs">Transfer Outs</option><option value="adjustments">Adjustments</option><option value="ending_balance">Ending Balance</option><option value="audit_count2019">2019 Audit Count</option></select></div><div class="col-md-3 dynlocgetwhenRef'+i+'"><select name="value[]" id="value" class="form-control valueData"><option value="contains">contains</option><option value="does not contain">does not contain</option><option value="is">is</option><option value="is not">is not</option><option value="starts with">starts with</option><option value="ends with">ends with</option><option value="is blank">is blank</option><option value="is not blank">is not blank</option></select></div><div class="col-md-4"><div class="locid'+i+' locgetwhenRef'+i+' updLoc'+i+'"><select name="svalue[]" class="form-control select2 svalueData"><? $this->mongo_db->switch_db($this->database);$ldata = $this->mongo_db->order_by(["locname"=>'asc'])->get("tbl_locations");foreach($ldata as $ld){echo '<option value="'.str_replace("'","",$ld['locname']).'">'.str_replace("'","",$ld['locname']).'</option>';}?></select></div></div><div class="col-md-1"><p style="margin-top: 8px;font-weight: bold;color:red" onclick="removeFilter(\'first'+i+'\');"><i class="fa fa-trash"></i></p></div></div></div>';
$("#top").append(n);
i++;
	
	$(".select2").select2();
	
}

function removeFilter(first){
	// console.log(first);
	$("."+first).remove();
i--;
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
		var item = $("#myText1").val();
		var tab = $("#myText2").val();
		var fdata = {"cause":cause,"field":field,"value":value,"svalue":svalue,"dvalue":dvalue,"item":item,"id":'<? echo $appid; ?>',"table":"tbl_inventory" };
		$("#myFilter").modal("hide");

		//var table = $('#inventoryTable').dataTable({
			 //"bProcessing": true,
			 /* "ajax": {
				"url": "<?php echo base_url("admin/apps/addFilter") ?>",
				"type": "POST",
				"data" : fdata,
				success : function(data){
					 
					 console.log(data);
					 
				 },
				 error : function(data){
					 
					 console.log(data);
					 
				 }
				
			  } */
			  
		    $.ajax({
			
			type : "post",
			url : "<? echo base_url('admin/apps/loc_filter') ?>",
			data : fdata,
			success : function(data){
				
				console.log(data);
				$('.dyn_tr_'+tab).html(data);
			},
			error : function(data){
				
				console.log(data);
				
			}
			
		});
			  
			 /* 'aoColumns': [
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
		 		title:'Pickups Ongoweoweh',
	                exportOptions: {
	                    columns: [1,2,3,4,5,6,7,8]
	                }
		 		},
		 		{
		 		extend: 'csvHtml5',
		 		title:'Pickups Ongoweoweh',
	                exportOptions: {
	                    columns: [1,2,3,4,5,6,7,8]
	                }
		 		},
		 		{
				   "extend": 'excel',
				   "text": 'Export All',
				   "titleAttr": 'Export All',                               
				   "action": exportAll
			    }
			]
		  }); */

	});
</script>

 