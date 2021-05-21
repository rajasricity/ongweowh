
<? admin_header(); ?> 

           
<? admin_sidebar(); ?>            


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
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/dashboard') ?>">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/users') ?>">Users</a></li>
                                        <li class="breadcrumb-item active"><? echo isset($u[0]["_id"]->{'$id'}) ? 'Update' : 'Create' ?> User</li>
                                    </ol>

                                </div>
                                <div class="col-sm-6">
                                
                                    <div class="float-right d-none d-md-block">
										<a class="btn btn-primary arrow-none waves-effect waves-light" href="<? echo base_url('admin/users') ?>">
											 All Users
										</a>
                                    </div>

                                </div>
                                
                            </div>
                        </div>
                        <!-- end row -->
                        
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form action="#" id="<? echo isset($u[0]["_id"]->{'$id'}) ? 'uuser' : 'cuser' ?>" method="post" autocomplete="off">
                                          
                                           <div class="row">
												 <div class="col-md-3"> 

													<div class="form-group">
														<label>Name</label>
<input type="text" class="form-control" name="uname" value="<? echo isset($u[0]['uname']) ? $u[0]['uname'] : '' ?>" required autocomplete="off">
													</div>

												 </div>    

												<div class="col-md-3"> 

													<div class="form-group">
														<label>Email</label>
														<input type="email" class="form-control" name="email" value="<? echo isset($u[0]['email']) ? $u[0]['email'] : '' ?>" required>
													</div>

												 </div>  

												 <div class="col-md-3"> 

													<div class="form-group">
														<label>Password</label>
														<input type="text" class="form-control" name="password" value="<? echo isset($u[0]['password']) ? $this->secure->decrypt($u[0]['password']) : '' ?>" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 4 or more characters" required>
													</div>

												 </div>

												 <div class="col-md-3"> 

													<div class="form-group">
														<label>Role</label>
														<select class="form-control" name="role" required>

															<option value="">Select Role</option>
															<option value="admin" <? echo ($u[0]["role"] == "admin") ? 'selected' : '' ?>>Admin</option>	
															<option value="user" <? echo ($u[0]["role"] == "user") ? 'selected' : '' ?>>User</option>
														</select>
													</div>

												 </div> 
                                               
                                             <? if(isset($u[0]["_id"]->{'$id'})){ ?>                
                                                                           
                                                 <div class="col-md-3"> 

													<div class="form-group">
														<label>Status</label>
														<select class="form-control" name="status" required>

															<option value="">Select Status</option>
															<option value="Active" <? echo ($u[0]["status"] == "Active") ? 'selected' : '' ?>>Active</option>	
															<option value="Inactive" <? echo ($u[0]["status"] == "Inactive") ? 'selected' : '' ?>>Inactive</option>
														</select>
													</div>

												 </div>                       
                                                       
                                             <? } ?>          
                                                       
                                            </div>
                                            
                                            
                                            <div class="row">
                                            	 
                                            	<div class="col-md-9">
                                            		
                                            		<div class="loader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 10%"></div>
                                            		<div class="error"></div>
                                            		
                                            	</div>
                                            	
                                            	<div class="col-md-3" align="right">
                                            		
                                            		<input type="hidden" name="id" value="<? echo isset($u[0]["_id"]->{'$id'}) ? $u[0]["_id"]->{'$id'} : ''  ?>">
                                            		<button class="btn btn-primary arrow-none waves-effect waves-light cSubmit" type="submit"><? echo isset($u[0]["_id"]->{'$id'}) ? 'Update' : 'Create' ?></button>
                                            		
                                            	</div>
                                            	
                                            </div>
                                            
                                        </form>
                                        
                                        <? if(isset($u[0]["_id"]->{'$id'})){ ?>
                                        	<div class="row">
                                            
                                            	<div class="col-md-12">
                                            	
<!--                                            	<h6 class="page-title" style="padding-left: 15px">Locations :</h6>-->
													<fieldset style="border:1px solid #F1F1F1;padding:10px;margin-top: 10px">
													<legend style="padding:3px;background-color: #F1F1F1">Locations</legend>
													<? $uLoc = isset($u[0]["locations"]) ? $u[0]["locations"] : []; ?>
                                           	  	
													
                                            		
                                            		<div class="row">
                                            		
												<?
																   

												$loc = $this->mongo_db->get_where("tbl_locations",array("deleted"=>0,"status"=>"Active")); 
												$i = 1;
												foreach($loc as $l){ 
													
													if(in_array($l["_id"]->{'$id'},$uLoc)){
												?>		
														<div class="col-md-3">
                                           					<input id="loc<? echo $l["_id"]->{'$id'} ?>" type="checkbox" name="location[]" value="<? echo $l["_id"]->{'$id'}  ?>" checked required> <span><label for="loc<? echo $l["_id"]->{'$id'} ?>"><? echo $l["city"] ?></label></span>
                                           				</div>
												<?		
													}else{
														
												?>
                                           				<div class="col-md-3">
                                           					<input id="loc<? echo $l["_id"]->{'$id'} ?>" type="checkbox" name="location[]" value="<? echo $l["_id"]->{'$id'}  ?>" required> <span><label for="loc<? echo $l["_id"]->{'$id'} ?>"><? echo $l["city"] ?></label></span>
                                           				</div>
                                           			<? }} ?>
                                           			
                                           			</div>
                                           			
                                           			<div class="row">
                                           				<div class="col-md-9">
                                           					
                                           					<div class="lerror"></div>	
                                           					
															<div class="lloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 10%"></div>
														</div>
														<div class="col-md-3" align="right">
															<button class="btn btn-primary arrow-none waves-effect waves-light" id="addLoc" type="button">Add / Remove</button>	
                                          				</div>
                                           			</div>
</fieldset>                                            	

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
				
$(document).ready(function() {
    $('#usersTable').DataTable();
} );
	
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
				
				$(".error").html('<div class="alert alert-success">User Successfully Added</div>');
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
	
$("#uuser").submit(function(e){
	
	e.preventDefault();
	var fdata = $(this).serialize();
	
	$.ajax({
		
		type : "post",
		data : fdata,
		url : "<? echo base_url('admin/users/updateUser') ?>",
		beforeSend : function(data){
			
			$(".loader").show();
			$(".cSubmit").hide();
			
		},
		success : function(data){
			console.log(data);
			
			$(".loader").hide();
			$(".cSubmit").show();
			
			if(data == "success"){
				
				$(".error").html('<div class="alert alert-success">User Successfully Updated</div>');
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
	
$("#addLoc").click(function(){
	
	var loc = [];
	$. each($("input[name='location[]']:checked"), function(){
		loc. push($(this). val());
	});
//	if(loc.length == 0){
//		
//		alert()
//		return false;
//	}
	
	$.ajax({
		
		type : "post",
		data : {loc : loc,uid : "<? echo $this->uri->segment(4) ?>"},
		url : "<? echo base_url('admin/users/updateLocation') ?>",
		beforeSend : function(data){
			
			$(".lloader").show();
			$("#addLoc").hide();
			
			
		},
		success : function(data){
			
			$(".lloader").hide();
			$("#addLoc").show();
			
			$(".lerror").html('<div class="alert alert-success">Location Successfully Added</div>');
			setTimeout(function(){ location.reload() },2000);
			
		},
		error : function(data){
			
			
		}
		
	})
	
	
});	
	
				
</script>

 