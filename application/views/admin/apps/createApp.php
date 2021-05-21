
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
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/dashboard') ?>"><i class="fas fa-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/apps') ?>">Customers</a></li>
                                        <li class="breadcrumb-item active">Create Customer</li>
                                    </ol>

                                </div>
                                <div class="col-sm-6">
                                
                                    <div class="float-right d-none d-md-block">
										<a class="btn btn-primary arrow-none waves-effect waves-light" href="<? echo base_url('admin/apps') ?>">
											 All Customers
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
                                        <form action="#" id="capp" method="post">
                                           <div class="row">
                                           		
                                           		 <div class="col-md-4"> 

													<div class="form-group">
														<label>Customer Name</label>
														<input type="text" class="form-control" name="appname" required>
													</div>

												 </div> 
                                           		    
                                                 <div class="col-md-6"> 

													<div class="form-group">
														<label>Short Description</label>
														<textarea class="form-control" name="sdesc" rows="2" required></textarea>
													</div>

												 </div> 
                                                 <div class="col-md-2"> 

													<div class="form-group">
														<label>Status</label>
														<select class="form-control" name="status" required>

															<option value="">Select Status</option>
															<option value="Active">Active</option>	
															<option value="Inactive">Inactive</option>
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
	
	
$("#capp").submit(function(e){
	
	e.preventDefault();
	var fdata = $(this).serialize();
	
	$.ajax({
		
		type : "post",
		data : fdata,
		url : "<? echo base_url('admin/apps/insertApp') ?>",
		beforeSend : function(data){
			
			$(".loader").show();
			$(".cSubmit").hide();
			
		},
		success : function(data){
			console.log(data);
			
			$(".loader").hide();
			$(".cSubmit").show();
			
			if(data == "success"){
				
				$(".error").html('<div class="alert alert-success">Customer Successfully Created</div>');
				setTimeout(function(){ window.location.href="<? echo base_url('admin/apps') ?>" },2000);
				
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

 