
<? admin_header(); ?> 

           
<? admin_sidebar(); 

$udata = $this->admin->get_admin();

?>            

<style>
	table{
		width:100%;
	}
	td{
		padding:6px !important;
	}
	.apexcharts-canvas {
    position: relative;
    user-select: none;
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
                                    <h4 class="page-title">Profile</h4>
<!--
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item active">Welcome to Ongweoweh Dashboard</li>
                                    </ol>
-->

                                </div>
                                
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- container-fluid -->
                    
					<div class="row">
						<div class="col-lg-6">
							<div class="user-pic" align="center">
							
							<? 
								$pic = ($udata["profile_pic"] != "") ? $udata["profile_pic"] : 'assets/images/users/superAdmin.jpg';
								
							?>
							
								<img src="<? echo base_url().$pic ?>" alt="users" class="rounded-circle img-fluid" style="height: 150px; width: 150px;">

							</div>
							<div class="card-title" align="center" style="padding-top: 20px">
								<p style="font-size: 20px"><strong><? echo $udata["uname"] ?></strong>
								</p>

							</div>
							<div class="" align="center">
								<button class="btn waves-effect waves-light btn-rounded btn-primary" id="updatePro">Update Profile</button>
								<button class="btn waves-effect waves-light btn-rounded btn-info" id="updatePass">Update Password</button>
							</div>

						</div>


						<div class="col-lg-6">

							<div class="card-body">
								<div id="uppro" style="display: none;">
									<form class="form p-t-20" id="updatePic" method="post" enctype="multipart/form-data">
										
										<div class="form-group">
											<label for="exampleInputEmail1">Profile Image</label>
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text" id="basic-addon22"><i class="fa fa-user"></i></span>
												</div>
												<input type="file" class="form-control" aria-label="profile" name="profile_pic" aria-describedby="basic-addon22" style="height: 40px">

											</div>
										</div>

										<button type="submit" class="btn btn-success m-r-10" id="iSubmit">Update</button>
										<div class="mloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 10%"></div>
										<div class="merror"></div>
									</form>
									<!-- <hr> -->
								</div>
								<div id="uppass" style="display: none;">
									<form class="form p-t-20" method="post" id="changePassword">

										<div class="form-group">
											<label>Old Password</label>
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text" id="basic-addon33"><i class="ti-lock"></i></span>
												</div>
												<input type="password" class="form-control" placeholder="Password" aria-label="Password" name="opass" aria-describedby="basic-addon33" required="">
											</div>
										</div>

										<div class="form-group">
											<label>New Password</label>
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text" id="basic-addon33"><i class="ti-lock"></i></span>
												</div>
												<input type="password" class="form-control" placeholder="Password" aria-label="Password" name="npass" aria-describedby="basic-addon33" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 4 or more characters" required="">
											</div>
										</div>
										<div class="form-group">
											<label>Confirm Password</label>
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text" id="basic-addon4"><i class="ti-lock"></i></span>
												</div>
												<input type="password" class="form-control" name="cpass" placeholder="Confirm Password" aria-label="Password" aria-describedby="basic-addon4" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 4 or more characters" required="">
											</div>
										</div>
										<button type="submit" class="btn btn-success m-r-10" id="cPass">Update</button>
										<div class="cloader" style="display: none"><img src="<? echo base_url('assets/images/loader.gif') ?>" style="width: 10%"></div>
										<div class="cerror"></div>

									</form>
								</div>
							</div>

						</div>
					</div>

            </div>
          
<? admin_footer(); ?>

<script>
				
	$("#updatePro").click(function(){
        
        $("#uppro").toggle();
        $("#uppass").hide();
        
    });
    $("#updatePass").click(function(){
        
        $("#uppass").toggle();
        $("#uppro").hide();
        
    });
	
	$("#updatePic").on("submit",function(e){
		
		e.preventDefault();
		
		var form_data = new FormData($(this)[0]);
			
		$.ajax({

			type : "POST",
			url : "<? echo base_url('admin/dashboard/updateProfilepic') ?>",
			data: form_data,
//		    async: false,
		    cache: false,
		    contentType: false,
		    enctype: 'multipart/form-data',
		    processData: false,
			beforeSend : function(){
			
				$('.mloader').show();
				$("#iSubmit").hide();
				
			},
			success : function(data){
				
				$('.mloader').hide();
//				$("#iSubmit").show();
				
				if(data == "success"){
					
					$('.merror').html('<div class="alert alert-success">Successfully Profile Updated</div>');
					setTimeout(function(){
						location.reload()
					},2000);
					
				}else{
					
					$('.merror').html('<div class="alert alert-danger">Please select valid image</div>');
					
				}

				console.log(data);		
			},
			error : function(jq,txt,error){
				
				$('.mloader').hide();
				$('.merror').html('<div class="alert alert-danger">Error Occured</div>');
				console.log(jq);		
//				console.log(txt);		
//				console.log(error);		
				
			}

		});

	});
	
	$("#changePassword").submit(function(e){
		
		e.preventDefault();
		var fdata = $(this).serialize();
		
		$.ajax({
			
			type : "post",
			url : '<? echo base_url() ?>admin/dashboard/changePassword',
			data : fdata,
			beforeSend : function(data){
				
				$(".cloader").show();
				$("#cPass").hide();
				
			},
			success : function(data){
				
				$(".cloader").hide();
				$("#cPass").show();
				
				if(data == 'oldwrong'){
					
					$('.cerror').html('<div class="alert alert-danger">Old password is wrong</div>')
					return false;
					
				}
				if(data == 'notmatched'){
					
					$('.cerror').html('<div class="alert alert-danger">Passwords not matched</div>')
					return false;
					
				}
				
				if(data == 'success'){
					
					$('.cerror').html('<div class="alert alert-success">Password Updated Successfully</div>')
					setTimeout(function(){
						location.reload()
					},2000);
					return false;
					
				}
				
			},
			error : function(data){
				
				console.log(data);
				
				$(".cloader").hide();
				$("#cPass").show();
				
				$('.cerror').html('<div class="alert alert-danger">Error Occured</div>')
				
			}
			
		});
		
	});

				
</script>
 