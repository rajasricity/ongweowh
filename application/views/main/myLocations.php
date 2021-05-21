
<? admin_header(); ?> 

           
<? admin_sidebar();

$udata = $this->mongo_db->get_where("tbl_auths",array("email"=>$this->session->userdata("admin_email")));
$mng = $this->admin->Mconfig();

$locs = [];
foreach($udata[0]['locations'] as $locval){
	
	if($locval->status == "Active"){	
		array_push($locs,$locval->loccode);
	}
	
}
	// $ldata = isset($udata[0]["locations"]) ? $udata[0]["locations"] : [];
	
	// // print_r($ldata);
	// // exit;																		
	// if(count($ldata) > 0){
	// 	$lid = [];
	// 	foreach($ldata as $ld){

	// 		$lid[]= $ld->LocationId;

	// 	}

	// }else{

	// 	$lid = [];

	// }
	if(count($locs)>0){
		$ldata = $this->admin->getRows("",['loccode'=> ['$nin'=>$locs],"status"=>"Active"],['sort'=>['_id'=>-1]],"$database.tbl_locations");
	}else{
		$ldata = $this->admin->getRows("",["status"=>"Active"],['sort'=>['_id'=>-1]],"$database.tbl_locations");
	}
	


?>            

<style>

	.acr{
		
		width: 10px !important;
		
	}
	
	.dataTables_filter input{
		color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
	
    line-height: 1.8;
    
}
		
	.dataTables_length{ float:left;}
	.dataTables_filter{ float:right;}
	
	.previous{ border: solid 1px #ccc; padding: 10px;}

	.next{ border: solid 1px #ccc; padding: 10px;}
	
	.current{
    background-color: #626ed4!important;
    border-color: #626ed4!important;
		color: #fff !important;
}
	.paginate_button, .ellipsis {
    -webkit-box-shadow: none;
    box-shadow: none;
   
    background-color: #e9ecef;
}
	
	.paginate_button a{color:#626ed4;}
	
	
	.paginate_button:hover {
    z-index: 2;
    color: #0056b3;
    text-decoration: none;
    background-color: #e9ecef;
    border-color: #dee2e6;
		cursor: pointer;
}
	
	.paginate_button, .ellipsis {
    position: relative;
    display: inline-block;
    padding: .5rem .75rem;
    margin-left: -1px;
    line-height: 1.25;
  
    background-color: #fff;
    border: 1px solid #dee2e6;
}
</style>


<link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" rel="stylesheet">
<link rel='stylesheet' id='custom_css1-css'  href='https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.6/css/dataTables.checkboxes.css' type='text/css' media='all'>
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
                                        <li class="breadcrumb-item"><a href="<? echo base_url('main/Admindashboard') ?>"><i class="fas fa-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0)"><? echo $udata[0]["uname"] ?></a></li>
                                        <li class="breadcrumb-item active">My Locations</li>
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
                                    <div class="card-body" style="padding: 0px;">
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                                            
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#import" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-download"></i></span>
                                                    <span class="d-none d-sm-block">Locations</span>   
                                                </a>
                                            </li>
                                            
                                        </ul>
        
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                           
                                           <div class="tab-pane active p-3" id="import" role="tabpanel">
                                            
                                            	<div class="row">
													<div class="col-lg-12">
														<div class="">
															<div class="card-body" style="padding: 0px;">
																
																<div class="row">
																	
																	<div class="col-md-6">
																		
																		<!-- <div class="alerror" style="height: 40px"></div> -->
<!--																		<div class="alloader" style="display: none"><img src="<? //echo base_url('assets/images/loader.gif') ?>" style="width: 10%"><///div>-->
																		
																	</div>
																	
																	
																	<div class="col-md-4">
																		
																	</div>
																	<div class="col-md-2">
																		
<!--																		<button type="button" class="btn btn-success" id="removeLoc">Remove</button>-->
																		
																	</div>
																	
																</div>		
																<div class="table-responsive">
																	<table class="table mb-0 table-bordered" id="usersTable2" style="width: 100%">
																		<thead class="thead-light">
																			<tr>

																				<th class="acr" style="white-space: nowrap !important">#</th>
																				<th class="hidrow" style="display: none">id</th>
																				<th>Location Code</th>
																				<th>Location Name</th>
																				<th>Type</th>
																				<th>Zip</th>
																				<th>Country</th>
																				<th>State</th>
																				<th>City</th>
																				<th>Address</th>
																			</tr>
																		</thead>
																		<tbody>
<?
$i=1;
foreach($udata[0]['locations'] as $location){ 
	
	if($location->status == "Active"){
	
	$row = $this->admin->getRow($mng,["loccode"=>$location->loccode],[],"$database.tbl_locations");
?>
<tr>
	<td><? echo $i; ?></td>
	<td class="hidrow" style="display: none"></td>
	<td><? echo $location->loccode; ?></td>
	<td><? echo $location->LocationName; ?></td>
	<td style="text-transform: capitalize;"><? echo $location->Type." Location"; ?></td>
	<td><? echo $row->zip; ?></td>
	<td><? echo $row->country; ?></td>
	<td><? echo $row->state; ?></td>
	<td><? echo $row->city; ?></td>
	<td><? echo $row->address; ?></td>

</tr>
<? $i++; }} ?>

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
                        
                        
                    </div>
                    <!-- container-fluid -->

                </div>
                <!-- content -->
	             
 	
<? admin_footer(); ?>

<script type='text/javascript' src='https://cdn.datatables.net/v/dt/dt-1.10.12/se-1.2.0/datatables.min.js'></script>
<script type='text/javascript' src='https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.6/js/dataTables.checkboxes.min.js'></script>

<script>
	
				
	$(document).ready(function(){
		
		$('#usersTable').DataTable();
		
		var ravtable = $('#usersTable1').DataTable({
			
//			dom: 'Bfrtip',
			
			rowId: 'id',
		  'columnDefs': [
			 {
				'targets': 0,
				'checkboxes': {
				   'selectRow': true
				}
			 }
		  ],
			'select': {
				 'style': 'multi',
	//			selector: 'td:first-child,td:nth-child(1)'
			  },
			
		});
		
		var ravtable1 = $('#usersTable2').DataTable();
		
		$("#addLoc").click(function(){
		
			var type = $("#type").val();
			
			var val = ravtable.rows( { selected: true } ).data().toArray();
			var count = val.length;
			
			var uid = "<? echo $udata[0]["_id"]->{'$id'} ?>";

		//	   console.log(ravtable);
		//	   alert(count);

			if(type == ""){
				
				$(".alerror").html('<div class="alert alert-danger">Please Select Type</div>');
				return false;
				
			}
			
			if(count > 0){
				var empids = [];
				$.each(val, function (key, value) {
				  empids[key] = value[1];

				});
				var jsempids = JSON.stringify(empids);
		//			  alert(jsempids)
			}else{
				$(".alerror").html('<div class="alert alert-danger">Please Select Locations</div>');
				return false;
			} 
			
			$.ajax({
				
				type : "post",
				data : {loc:empids,type : type,uid : uid},
				url : "<? echo base_url('admin/users/addLocation') ?>",
				beforeSend : function(data){
					
					$(".alloader").show();
					$(".addLoc").hide();
					
				},
				success : function(data){
					
					console.log(data);
					
					$(".alloader").hide();
					$(".addLoc").show();
					$(".alerror").html('<div class="alert alert-success">Locations Successfully Added</div>');
					setTimeout(function(){ location.reload() },2000);
					
				},
				error : function(data){
					
					$(".alloader").hide();
					$(".addLoc").show();
					console.log(data);
					
				}
				
			});

		})
		
	});
	
	$("#uuser").submit(function(e){

		e.preventDefault();	
		var fdata = $(this).serialize();
		
		$.ajax({

			type : "post",
			data : fdata,
			url : "<? echo base_url('admin/users/updateUser') ?>",
			beforeSend : function(data){

				$(".uloader").show();
				$(".cuSubmit").hide();

			},
			success : function(data){
				console.log(data);

				$(".uloader").hide();
				$(".cuSubmit").show();

				if(data == "success"){

					$(".uerror").html('<div class="alert alert-success">User Successfully Updated</div>');
					setTimeout(function(){ location.reload() },2000);

				}else{

					$(".uerror").html('<div class="alert alert-danger">'+data+'</div>');

				}

			},
			error : function(data){
				console.log(data);
				$(".uloader").hide();
				$(".cuSubmit").show();

			}

		});

	});
	
	function archiveFunction(id,uid) {
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
			  'Your Selected location has been deleted.',
			  'success'
			)
			$.ajax({
				method: 'POST',
				data: {'id' : id },
				url: '<?php echo base_url() ?>admin/apps/delLocation/'+id+'/'+uid,
				success: function(data) {
					location.reload(); 
					
					console.log(data);
				},
				error : function(data){
					
					
					console.log(data);
					
				}
			});

		  } else if (result.dismiss === Swal.DismissReason.cancel) {
			Swal(
			  'Cancelled',
			  'Your Selected location is safe :)',
			  'success',

			)
		  }
		})
    }	
			
</script>

 