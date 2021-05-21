
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
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/locations') ?>">Locations</a></li>
                                        <li class="breadcrumb-item active"><? echo isset($l[0]["_id"]->{'$id'}) ? 'Update' : 'Create' ?> Location</li>
                                    </ol>

                                </div>
                                <div class="col-sm-6">
                                
                                    <div class="float-right d-none d-md-block">
										<a class="btn btn-primary arrow-none waves-effect waves-light" href="<? echo base_url('admin/locations') ?>">
											 All Locations
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
                                        <form action="#" id="<? echo isset($l[0]["_id"]->{'$id'}) ? 'uuser' : 'cuser' ?>" method="post">
                                           <div class="row">
                                           		
                                           		 <div class="col-md-3"> 

													<div class="form-group">
														<label>Location Code</label>
														<input type="text" class="form-control" name="lcode" value="<? echo isset($l[0]['loccode']) ? $l[0]['loccode'] : '' ?>" required>
													</div>

												 </div> 
                                           		 
                                           
												 <div class="col-md-3"> 

													<div class="form-group">
														<label>City</label>
														<input type="text" class="form-control" name="city" value="<? echo isset($l[0]['city']) ? $l[0]['city'] : '' ?>" required>
													</div>

												 </div>    

												<div class="col-md-3"> 

													<div class="form-group">
														<label>State</label>
														<input type="text" class="form-control" name="state" value="<? echo isset($l[0]['state']) ? $l[0]['state'] : '' ?>" required>
													</div>

												 </div>  

												 <div class="col-md-3"> 

													<div class="form-group">
														<label>Country</label>
														<input type="text" class="form-control" name="country" value="<? echo isset($l[0]['country']) ? $l[0]['country'] : '' ?>" required>
													</div>

												 </div>
												 
												 <div class="col-md-3"> 

													<div class="form-group">
														<label>Latitude</label>
														<input type="text" class="form-control" name="lat" value="<? echo isset($l[0]['lat']) ? $l[0]['lat'] : '' ?>">
													</div>

												 </div>
												 
         										 <div class="col-md-3"> 

													<div class="form-group">
														<label>Longitude</label>
														<input type="text" class="form-control" name="lon" value="<? echo isset($l[0]['lon']) ? $l[0]['lon'] : '' ?>">
													</div>

												 </div>	
                                                    
                                                      
                                             <? if(isset($l[0]["_id"]->{'$id'})){ ?>                
                                                                           
                                                 <div class="col-md-3"> 

													<div class="form-group">
														<label>Status</label>
														<select class="form-control" name="status" required>

															<option value="">Select Status</option>
															<option value="Active" <? echo ($l[0]["status"] == "Active") ? 'selected' : '' ?>>Active</option>	
															<option value="Inactive" <? echo ($l[0]["status"] == "Inactive") ? 'selected' : '' ?>>Inactive</option>
														</select>
													</div>

												 </div>                       
                                                       
                                             <? } ?> 
                                                      
                                                 <div class="col-md-3"> 

													<div class="form-group">
														<label>Address</label>
														<textarea class="form-control" name="address" rows="2" required><? echo isset($l[0]['address']) ? $l[0]['address'] : '' ?></textarea>
													</div>

												 </div>                  
                                                       
                                            </div>
                                            
                                            
                                            <div class="row">
                                            	 
                                            	<div class="col-md-9">
                                            		
                                            		<div class="loader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 10%"></div>
                                            		<div class="error"></div>
                                            		
                                            	</div>
                                            	
                                            	<div class="col-md-3" align="right">
                                            		
                                            		<input type="hidden" name="id" value="<? echo isset($l[0]["_id"]->{'$id'}) ? $l[0]["_id"]->{'$id'} : ''  ?>">
                                            		<button class="btn btn-primary arrow-none waves-effect waves-light cSubmit" type="submit"><? echo isset($l[0]["_id"]->{'$id'}) ? 'Update' : 'Create' ?></button>
                                            		
                                            	</div>
                                            	
                                            </div>
        
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
	
$("#uuser").submit(function(e){
	
	e.preventDefault();
	var fdata = $(this).serialize();
	
	$.ajax({
		
		type : "post",
		data : fdata,
		url : "<? echo base_url('admin/locations/updateLocation') ?>",
		beforeSend : function(data){
			
			$(".loader").show();
			$(".cSubmit").hide();
			
		},
		success : function(data){
			console.log(data);
			
			$(".loader").hide();
			$(".cSubmit").show();
			
			if(data == "success"){
				
				$(".error").html('<div class="alert alert-success">Location Successfully Updated</div>');
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
	
				
</script>

 