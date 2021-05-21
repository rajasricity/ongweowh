
<? admin_header(); ?> 

           
<? 
admin_sidebar(); 
$mng = $this->admin->Mconfig();
$aid = $this->uri->segment(4);
$times = ['12:00am','12:15am','12:30am','12:45am','01:00am','01:15am','01:30am','01:45am','02:00am','02:15am','02:30am','02:45am','03:00am','03:15am','03:30am','03:45am','04:00am','04:15am','04:30am','04:45am','05:00am','05:15am','05:30am','05:45am','06:00am','06:15am','06:30am','06:45am','07:00am','07:15am','07:30am','08:00am','08:15am','08:30am','08:45am','09:00am','09:15am','09:30am','10:00am','10:15am','10:30am','10:45am','11:00am','11:15am','11:30am','11:45am','12:00pm','12:15pm','12:30pm','12:45pm','01:00pm','01:15pm','01:30pm','01:45pm','02:00pm','02:15pm','02:30pm','02:45pm','03:00pm','03:15pm','03:30pm','03:45pm','04:00pm','04:15pm','04:30pm','04:45pm','05:00pm','05:15pm','05:30pm','05:45pm','06:00pm','06:15pm','06:30pm','06:45pm','07:00pm','07:15pm','07:30pm','08:00pm','08:15pm','08:30pm','08:45pm','09:00pm','09:15pm','09:30pm','10:00pm','10:15pm','10:30pm','10:45pm','11:00pm','11:15pm','11:30pm','11:45pm'];

$mdb = mongodb;
?>            


 <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="page-title-box">
                            <div class="row align-items-center">
<?
$appid = $l->appid;
?>
                                <div class="col-sm-6">
<h4 class="page-title"><? echo $this->admin->getReturn($mng,"$mdb.tbl_apps",["appId"=>$appid],[],"appname"); ?></h4>
                                    <ol class="breadcrumb">
<li class="breadcrumb-item"><a href="<? echo base_url('admin/dashboard') ?>"><i class="fas fa-home"></i></a></li>
<li class="breadcrumb-item"><a href="<? echo base_url('admin/apps') ?>">Locations</a></li>
<li class="breadcrumb-item active">Update Submit Location</li>
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
<?
// echo $reqid;
?>
<table class="table table-bordered">
	<thead class="thead-light">
		<tr>
			<th>Requested Date.</th>
			<th>Requested By</th>
			<th>Address</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><? echo $l->Created_Date; ?></td>
			<td><? echo $l->user; ?></td>
			<td><? echo $l->address; ?></td>
			<td><? echo $l->Status; ?></td>
		</tr>
	</tbody>
</table>
<hr/>
<?
if($l->Status != 'Approved'){
?>
<form id="acceptLocation">
	<input type="hidden" name="reqid" value="<? echo $reqid; ?>">
<div class="row">

																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Location Code</label>
																				<input type="text" class="form-control" name="loccode" required>
																			</div>

																		 </div> 

																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Location Name</label>
																				<input type="text" class="form-control" name="locname" value="<? echo $l->lname; ?>" required>
																			</div>

																		 </div>	

																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>City</label>
																				<input type="text" class="form-control" name="city" value="<? echo $l->city; ?>" required>
																			</div>

																		 </div>    

																		<div class="col-md-3"> 

																			<div class="form-group">
																				<label>State</label>
																				<input type="text" class="form-control" name="state" value="<? echo $l->state; ?>" required>
																			</div>

																		 </div>  

																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Country</label>
																				<input type="text" class="form-control" name="country" value="<? echo $l->country; ?>" required>
																			</div>

																		 </div>

																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Zipcode</label>
																				<input type="number" class="form-control" name="zip" value="<? echo $l->zip; ?>">
																			</div>

																		 </div>

																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Latitude</label>
																				<input type="text" class="form-control" name="lat" value="<? echo $l->lat; ?>">
																			</div>

																		 </div>

																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Longitude</label>
																				<input type="text" class="form-control" name="lon" value="<? echo $l->lon; ?>">
																			</div>

																		 </div>	

																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Location Type</label>
																				<select class="form-control" name="Type" required>

																					<option value="">Select Location Type</option>
																					<option value="External">External</option>	
																					<option value="Internal">Internal</option>
																				</select>
																			</div>

																		 </div>

																		 <div class="col-md-3"> 

																			<div class="form-group">
																				<label>Address</label>
																				<textarea class="form-control" name="address" rows="2" required><? echo $l->address; ?></textarea>
																			</div>

																		 </div>
<div class="col-md-2"> 
<div class="form-group">
<label>Import Date</label>
<input type="date" class="form-control" name="import_date">
</div>
</div>	                

<div class="col-md-1"> 
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
<label>Accounts</label>
<input type="text" class="form-control" name="accounts">
</div>
</div>

<div class="col-md-3"> 
<div class="form-group">
<label>Notes</label>
<input type="text" class="form-control" name="notes">
</div>
</div>	      

<div class="col-md-3"> 
<div class="form-group">
<label>Location Test</label>
<input type="text" class="form-control" name="locationtest">
</div>
</div>        

																	</div>

<div class="row" style="margin-top:10px;">
	<div class="col-lg-12 text-right">
<input type="hidden" name="appId" value="<? echo $appid; ?>">
<input type="hidden" name="deleted" value="0">
<input type="hidden" name="cdate" value="<? echo date('Y-m-d h:i:s', time()); ?>">
<input type="submit" name="submit" class="btn btn-primary" value="ADD LOCTION">
	</div>
</div>
</form>
<div class="row" style="margin-top:10px;">
	<div class="col-lg-12">
<center>
	<img src="<? echo base_url(); ?>assets/images/loader.gif" width="75" height="75" style="display:none" id="mloader">
</center>
<div class="alert alert-success" style="display: none" id="smsg"></div>
	</div>
</div>
<? } ?>
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
	
	$("#acceptLocation").on("submit",function(e){
		
		e.preventDefault();
		
		var fdata = $("#acceptLocation").serialize();
		$.ajax({

			type : "POST",
			url : "<? echo base_url('admin/apps/updateSubmit') ?>",
			data: fdata,
			dataType:'json',
			beforeSend : function(){	
				$('#mloader').show();
				
				
			},
			success : function(data){
				console.log(data);
				$('#mloader').hide();
			
				 if(data.Status == "Success"){
					
				 	$('#smsg').show();
				 	$('#smsg').html(data.Message);
				 	setTimeout(function(){
				 		location.reload();
				 	},2000);
					
				 }

						
			},
			error : function(jq,txt,error){
				
				// $('.mloader').hide();
				console.log(jq);		
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
	
	// $(".editLocate").click(function(){
	// 	console.log("I am in");
	// 	$("#lid").val($(this).attr("lid"));
	// 	$("#lcode").val($(this).attr("lcode"));
	// 	$("#lname").val($(this).attr("lname"));
	// 	$("#zip").val($(this).attr("zip"));
	// 	$("#city").val($(this).attr("city"));
	// 	$("#address").val($(this).attr("address"));
	// 	$("#state").val($(this).attr("state"));
	// 	$("#country").val($(this).attr("country"));
	// 	$("#status").val($(this).attr("status"));
	// 	$("#lat").val($(this).attr("lat"));
	// 	$("#lon").val($(this).attr("lon"));
	// 	$("#loctype").val($(this).attr("loctype"));
		
	// })

	function checkClick(lid,lcode,lname,zip,city,address,state,country,status,loctype,lat,lon){
		$("#lid").val(lid);
		$("#lcode").val(lcode);
		$("#lname").val(lname);
		$("#zip").val(zip);
		$("#city").val(city);
		$("#address").val(address);
		$("#state").val(state);
		$("#country").val(country);
		$("#status").val(status);
		$("#lat").val(lat);
		$("#lon").val(lon);
		$("#loctype").val(loctype);
	}
				
	$(document).ready(function() {
		$("#usersTable").DataTable({
		 "dom": 'Bfrtip',
		 buttons: [
				'csv', 'excel','pageLength'
			],
		 "bProcessing": true,
         "sAjaxSource": "<? echo base_url(); ?>admin/apps/getLocations",
         "aoColumns": [
               { mData: 'Sno' },
               { mData: 'loccode' } ,
               { mData: 'locname' },
               { mData: 'city' },
               { mData: 'address' },
               { mData: 'state' },
               { mData: 'country' },
               { mData: 'status' },
               { mData: 'Actions'}
             ],
             
          "bLengthChange": true,
		});
	} );
	
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
		
				
</script>