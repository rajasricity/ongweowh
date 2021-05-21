
<? admin_header(); ?> 

           
<? admin_sidebar(); 

$aid = $this->uri->segment(4);


$adata = $this->mongo_db->get_where("tbl_apps",array("appId"=>$aid));

?> 
 
<style>
.acr{
		
	width: 10px !important;

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
<!--                                    <h4 class="page-title"><? //echo $adata[0]["appname"] ?></h4>-->
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/dashboard') ?>"><i class="fas fa-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/apps/editApp/').$aid ?>"><label class="badge badge-primary" style="font-size: 14px"><? echo $adata[0]["appname"] ?></label></a></li>
                                        <li class="breadcrumb-item active">Users</li>
                                    </ol>

                                </div>
                                <div class="col-sm-6">
                                
                                   

                                </div>
                                
                            </div>
                        </div>
                        <!-- end row -->
                        
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <!-- <div class="card">
                                    <div class="card-body"> -->
        
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
<!--
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#home1" role="tab">
                                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                    <span class="d-none d-sm-block">Create User</span> 
                                                </a>
                                            </li>
-->
                                            
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#messages1" role="tab">
                                                    <span class="d-none d-sm-block"><i class="mdi mdi-account-multiple-outline
"></i> Users</span>   
                                                </a>
                                            </li>
                                            

                                            
                                        </ul>
        
                                        <!-- Tab panes -->
                                        <div class="tab-content" style="background-color: #fff;">
                                           
                                            <div class="tab-pane active p-3" id="messages1" role="tabpanel">
                                                
                                                <div class="row">
													<div class="col-lg-12">
														<div class="">
															<div class="card-body" style="padding:0px;">
																<div class="row">
					                                        		<div class="col-md-9"></div>
					                                        		<div class="col-md-3" align="right" style="margin-bottom: 10px"> 
						                                        		<a class="btn btn-primary showAddloc" href="javascript:void(0)"><i class="mdi mdi-account-multiple-plus-outline"></i> Create User</a>
						                                        		<a class="btn btn-primary showAllloc" style="display: none" href="javascript:void(0)">Back</a>

						                                        	</div>
						                                        </div>

																<div class="table-responsive allLoc">
																	<table class="table mb-0 table-bordered" id="usersTable">
																		<thead class="thead-light">
																			<tr>

																				<th class="acr" style="white-space: nowrap !important;">#</th>
																				<th>Name</th>
																				<th>Email</th>
																				<th>Role</th>
																				<th>From Locations</th>
																				<th>To Locations</th>
																				<th>Status</th>
																				<th class="acr" style="white-space: nowrap !important;">Action</th>

																			</tr>
																		</thead>
																		<tbody>

																		  <? 
																			$udata = $this->mongo_db->get_where("tbl_auths",array("deleted"=>0,"appid"=>$aid)); 
																			$i = 1;
																			foreach($udata as $ud){
																				
																				$locations = $ud['locations'];
																				
																			?>

																			<tr>

																				<td><? echo $i ?></td>
																				<td><? echo $ud["uname"] ?></td>
																				<td><? echo $ud["email"] ?></td>
																				<td><span class="badge badge-success" style="font-size:14px;"><?  
																				if($ud["role"] == 'customer_admin'){
																					echo "Main Admin";
																				}else if($ud["role"] == 'user'){
																					echo "Reporting User";
																				}
																				?></span></td>
																				
																				<td>
																					
																					<? foreach($locations as $loc){
																					
																							if($loc->Type == "from" && $loc->status == "Active"){
																								
																								echo $loc->LocationName."<br>";
																								
																							}
																					
																						} ?>
																					
																				</td>
																				
																				<td>
																					
																					<? foreach($locations as $loc){
																					
																							if($loc->Type == "to" && $loc->status == "Active"){
																								
																								echo $loc->LocationName."<br>";
																								
																							}
																					
																						} ?>
																					
																				</td>
																				
																				<td><? echo $ud["status"] ?></td>
																				<td>

																					<a href="<? echo base_url('admin/apps/editUser/').$ud["_id"]->{'$id'}."/".$aid ?>"><i class="far fa-edit"></i></a>&nbsp;|&nbsp;
																					<a href="javascript:void(0)" id="<? echo $ud["_id"]->{'$id'} ?>" onclick="archiveFunction(this.id)"><i class="fas fa-trash-alt" style="color: red"></i></a>

																				</td>

																			</tr>

																		  <? $i++;} ?>  

																		</tbody>
																	</table>
																</div>
																
																
																
																
																<div class="insLoc" style="display: none">
                                                	
																	<form action="#" id="cuser" method="post">

																	   <div class="row">
																			 <div class="col-md-3"> 

																				<div class="form-group">
																					<label>Name</label>
																					<input type="text" class="form-control" name="uname" required>
																				</div>

																			 </div>    

																			<div class="col-md-3"> 

																				<div class="form-group">
																					<label>Email</label>
																					<input type="email" class="form-control" name="email" required>
																				</div>

																			 </div>  

																			 <div class="col-md-3"> 

																				<div class="form-group">
																					<label>Password</label>
																					<input type="password" class="form-control" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 4 or more characters" required>
																				</div>

																			 </div>

																			 <div class="col-md-3"> 

																				<div class="form-group">
																					<label>Role</label>
																					<select class="form-control" name="role" required>

																						<option value="">Select Role</option>
																						<option value="customer_admin">Main Admin</option>	
																						<option value="user">Reporting User</option>
																					</select>
																				</div>

																			 </div>                     


																		</div>


																		<div class="row">

																			<div class="col-md-9">

																				<div class="loader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" width="60" height="60"></div>
																				<div class="error"></div>

																			</div>

																			<div class="col-md-3" align="right">

																				<input type="hidden" name="appid" value="<? echo $aid ?>">
																				<button class="btn btn-primary arrow-none waves-effect waves-light cSubmit" type="submit">Create</button>

																			</div>

																		</div>

																	</form>

																</div>


															</div>
														</div>
													</div>
												</div>
                                               
                                            </div>
                                            
                                            
                                        </div>
        
                                    <!-- </div>
                                </div> -->
                            </div>
						</div>                        
                        
                        
                    </div>
                    <!-- container-fluid -->

                </div>
                <!-- content -->
	
	<!--  Modal content for the above example -->
                                            
 	
<? admin_footer(); ?>


<script>
	
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
	
	$(".editUser").click(function(){
		
		$("#uid").val($(this).attr("uid"));
		$("#uname").val($(this).attr("uname"));
		$("#password").val($(this).attr("password"));
		$("#email").val($(this).attr("email"));
		$("#role").val($(this).attr("role"));
		$("#status").val($(this).attr("status"));
		
	})
				
	$(document).ready(function(){
		$('#usersTable').DataTable({
			
			dom: 'Bfrtip',
			buttons: [
				'csv', 'excel','pageLength'
			],
			
		});
		var ravtable = $('#usersTable1').DataTable({
			
			dom: 'Bfrtip',
			buttons: [
				'csv', 'excel'
			],
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
		
		$("#addLoc").click(function(){
		
			var type = $("#type").val();
			
			var val = ravtable.rows( { selected: true } ).data().toArray();
			var count = val.length;

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
				data : {loc:empids,type : type},
				url : "<? echo base_url('admin/users/addLocation') ?>",
				beforeSend : function(data){
					
					$(".alloader").show();
					$(".addLoc").hide();
					
				},
				success : function(data){
					
					$(".alloader").hide();
					$(".addLoc").show();
					console.log(data);
					
				},
				error : function(data){
					
					$(".alloader").hide();
					$(".addLoc").show();
					console.log(data);
					
				}
				
			});

		})
		
	});
	
	
	$("#cuser").submit(function(e){
	
		e.preventDefault();
		var fdata = $(this).serialize();

		$.ajax({

			type : "post",
			data : fdata,
			url : "<? echo base_url('admin/users/insertUser') ?>",
			beforeSend : function(data){

				$(".loader").show();
				$(".cSubmit").hide();

			},
			success : function(data){
				console.log(data);

				$(".loader").hide();
				$(".cSubmit").show();

				if(data == "success"){

					$(".error").html('<div class="alert alert-success">User Successfully Created</div>');
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
	
	$(".cuSubmit").click(function(e){

		var uid = $("#uid").val();
		var uname = $("#uname").val();
		var password = $("#password").val();
		var email = $("#email").val();
		var role = $("#role").val();
		var status = $("#status").val();
		
		$.ajax({

			type : "post",
			data : {id:uid,uname:uname,password:password,email:email,role:role,status:status},
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

				$(".uloader").hide();
				$(".cuSubmit").show();

			}

		});

	});
	
	function archiveFunction(id) {
       Swal({
		  title: 'Are you sure?',
		  text: 'You will not be able to recover this selected user!',
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonText: 'Yes, delete it!',
		  cancelButtonText: 'No, keep it'
		}).then((result) => {
		  if (result.value) {

			Swal(
			  'Deleted!',
			  'Your Selected user has been deleted.',
			  'success'
			)
			$.ajax({
				method: 'POST',
				data: {'id' : id },
				url: '<?php echo base_url() ?>admin/users/delUser/'+id,
				success: function(data) {
					location.reload();   
				}
			});

		  } else if (result.dismiss === Swal.DismissReason.cancel) {
			Swal(
			  'Cancelled',
			  'Your Selected user is safe :)',
			  'success',

			)
		  }
		})
    }	
				
</script>

 