
<? admin_header(); ?> 

           
<? 
admin_sidebar(); 
$mng = $this->admin->Mconfig();
$aid = $this->uri->segment(4);

$mdb = mongodb;

$ruser = $this->admin->getRow($mng,["email"=>$l->user],[],"$mdb.tbl_auths");
$appData = $this->admin->getRow($mng,["appId"=>$l->appid],[],"$mdb.tbl_apps");


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
                                    <h4 class="page-title"><? echo $appData->appname ?></h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/dashboard') ?>"><i class="fas fa-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/apps') ?>">Customers</a></li>
                                        <li class="breadcrumb-item active">Update Customer</li>
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
			<th>Notes</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><? echo $l->Created_Date; ?></td>
			<td><? echo $l->user; ?></td>
			<td><? echo $l->notes; ?></td>
			<td><? echo $l->Status; ?></td>
		</tr>
	</tbody>
</table>
<form id="acceptLocation">
	<input type="hidden" name="reqid" value="<? echo $reqid; ?>">
<div class="row">
<?
$existlocs = [];
foreach($ruser->locations as $local){
	array_push($existlocs, $local->loccode);
}
$i=0;
foreach($l->locations as $location){?>
	<div class="col-md-3">
		<? 
$ldata = $this->admin->getRow($mng,["loccode"=>$location],[],"$database.tbl_locations");
		?>
		<div style="width: 100%;background-color: #e9e9e9;padding: 5px;">
<? if(in_array($location, $existlocs)){?>

<?}else{?>
<input type="checkbox" name="location[]" value="<? echo $ldata->loccode; ?>">
<? $i++; }?>

<h6><? echo $ldata->locname; ?></h6>
<p style="margin:0px;"><? echo $ldata->address; ?></p>
<p style="margin:0px;"><? echo $ldata->state.', '.$ldata->zip.', '.$ldata->country; ?></p>
		</div>
		
	</div>
<? } ?>
</div>
<div class="row" style="margin-top:10px;">
	<div class="col-lg-12">
<? if($i > 0){?>
<input type="submit" name="submit" class="btn btn-primary pull-right" value="ACCEPT REQUEST"/>
	<?}?>

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
			url : "<? echo base_url('admin/apps/updateRequest') ?>",
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

 