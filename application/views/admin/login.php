<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <title>Ampcus | Logistics Management</title>
        <meta content="Admin Dashboard" name="description" />
        <meta content="" name="author" />
        <link rel="shortcut icon" href="<? echo base_url() ?>assets/logo/favicon.ico">

        <link href="<? echo base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<? echo base_url() ?>assets/css/metismenu.min.css" rel="stylesheet" type="text/css">
        <link href="<? echo base_url() ?>assets/css/icons.css" rel="stylesheet" type="text/css">
        <link href="<? echo base_url() ?>assets/css/style.css" rel="stylesheet" type="text/css">
    </head>

    <body>

        <div class="home-btn d-none d-sm-block">
<!--            <a href="index.html" class="text-white"><i class="fas fa-home h2"></i></a>-->
        </div>
        
        <!-- Begin page -->
        <div class="accountbg"></div>

        <div class="wrapper-page account-page-full">

            <div class="card">
                <div class="card-body">

                    <div class="text-center">
                        <a href="<? echo base_url() ?>" class="logo"><img src="<? echo base_url() ?>assets/logo/home.png" height="120" alt="logo"></a>
                    </div>

                    <div class="p-3" id="loginscreen">
                        <h4 class="font-18 m-b-5 text-center">Welcome Back !</h4>
                        <p class="text-muted text-center">Sign in to continue</p>
                        
                        <div class="error"></div>
                        <div class="serror" style="display: none"><div class="alert alert-success">password updated successfully please login to continue.</div></div>

                        <form class="form-horizontal m-t-30" id="login" method="post" autocomplete="off">

                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="email" class="form-control" name="uname" id="username" placeholder="Enter username" autocomplete="off" required>
                            </div>

                            <div class="form-group">
                                <label for="userpassword">Password</label>
                                <input type="password" class="form-control" name="password" min="4" id="userpassword" autocomplete="off" placeholder="Enter password" required>
                            </div>

                            <div class="form-group row m-t-20">
                                <div class="col-sm-6">
<!--
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customControlInline">
                                        <label class="custom-control-label" for="customControlInline">Remember me</label>
                                    </div>
-->
                                </div>
                                <div class="col-sm-6 text-right">
                                    <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Log In</button>
                                </div>
                            </div>

                            <div class="form-group m-t-10 mb-0 row">
                                <div class="col-12 m-t-20">
                                    <a href="javascript:void(0)" id="forgotpass"><i class="mdi mdi-lock"></i> Forgot your password?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    
                    <div class="p-3" id="forgotscreen" style="display: none">
                        <p class="text-muted text-center">Forgot Password</p>
                        
                        <div class="ferror"></div>

						<div class="form-group">
							<label for="username">Username</label>
							<input type="email" class="form-control" name="uname" id="fusername" autocomplete="off" placeholder="Enter username" required>
						</div>

						<div class="form-group row m-t-20">

							<div class="col-sm-6"></div>
							<div class="col-sm-6 text-right">
								<button class="btn btn-primary w-md waves-effect waves-light" id="forgotvalidation" type="submit">Submit</button>
							</div>

						</div>
                            
					</div>
                
                    <div class="p-3" id="otpscreen" style="display: none">
                        <p class="text-muted text-center">Confirm OTP</p>
                        
                        <div class="oerror"></div>

						<div class="form-group">
							<label for="username">OTP</label>
							<input type="text" class="form-control" name="uname" id="otp" autocomplete="off" placeholder="Enter OTP" required>
						</div>

						<div class="form-group row m-t-20">

							<div class="col-sm-6"></div>
							<div class="col-sm-6 text-right">
								<button class="btn btn-primary w-md waves-effect waves-light" id="otpvalidation" type="submit">Submit</button>
							</div>

						</div>
                            
					</div>  
                
                
                    <div class="p-3" id="resetscreen" style="display: none">
                        <p class="text-muted text-center">Reset Password</p>
                      
                      <form method="post" id="resetvalidation">
  
                        <div class="rerror"></div>

						<div class="form-group">
							<label for="username">Password</label>
							<input type="password" class="form-control" name="pass" id="pass" autocomplete="off"  placeholder="Enter Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 4 or more characters" required>
						</div>
						
						<div class="form-group">
							<label for="username">Confirm Password</label>
							<input type="password" class="form-control" name="cpass" id="cpass" autocomplete="off" placeholder="Enter Confirm Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 4 or more characters" required>
						</div>

						<div class="form-group row m-t-20">
							
							<input type="hidden" name="email" id="remail">

							<div class="col-sm-6"></div>
							<div class="col-sm-6 text-right">
								<button class="btn btn-primary w-md waves-effect waves-light" id="" type="submit">Submit</button>
							</div>

						</div>
                        
					  </form>    
					  
					</div>                            

                </div>
            </div>

<!--
            <div class="m-t-40 text-center">
                <p>Don't have an account ? <a href="pages-register-2.html" class="font-500 text-primary"> Signup now </a> </p>
                <p>© 2019 Veltrix. Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesbrand</p>
            </div>
-->

        </div>
        <!-- end wrapper-page -->

        <!-- jQuery  -->
        <script src="<? echo base_url() ?>assets/js/jquery.min.js"></script>
        <script src="<? echo base_url() ?>assets/js/bootstrap.bundle.min.js"></script>
        <script src="<? echo base_url() ?>assets/js/metisMenu.min.js"></script>
        <script src="<? echo base_url() ?>assets/js/jquery.slimscroll.js"></script>
        <script src="<? echo base_url() ?>assets/js/waves.min.js"></script>

        <!-- App js -->
        <script src="<? echo base_url() ?>assets/js/app.js"></script>

    </body>
    
    
    <script>
		
		$("#forgotpass").click(function(){
			
			$("#loginscreen").hide();
			$("#forgotscreen").show();
			
		});
		
		$("#forgotvalidation").click(function(){
			
			var email = $("#fusername").val();
			
			if(email == ""){
				
				$(".ferror").html('<div class="alert alert-danger">Enter Username</div>');
				return false;
				
			}
			
			$.ajax({
				
				type : "post",
				url : "<? echo base_url('login/forgotpassword') ?>",
				data : {uname : email},
				success : function(data){
					
//					console.log(data);
					
					if(data == "success"){
						
						$(".oerror").html('<div class="alert alert-success">OTP successfully sent to your registered email</div>');
						$("#forgotscreen").hide();
						$("#otpscreen").show();
						
					}else{
						
						$(".ferror").html('<div class="alert alert-danger">'+data+'</div>');
						
					}
					
				},
				error : function(data){
					
					console.log(data);
					$(".ferror").html('<div class="alert alert-danger">Error Occured</div>');
					
				}
			});
			
		})
		
		$("#otpvalidation").click(function(){
			
			var email = $("#fusername").val();
			var otp = $("#otp").val();
			
			if(otp == ""){
				
				$(".ferror").html('<div class="alert alert-danger">Enter OTP</div>');
				return false;
				
			}
			
			$.ajax({
				
				type : "post",
				url : "<? echo base_url('login/checkOtp') ?>",
				data : {email:email,otp : otp},
				success : function(data){
					
					console.log(data);
					
					if(data == "success"){
						
//						$(".oerror").html('<div class="alert alert-success">OTP successfully sent to your registered email</div>');
						$("#otpscreen").hide();
						$("#resetscreen").show();
						$("#remail").val(email);
						
					}else{
						
						$(".oerror").html('<div class="alert alert-danger">'+data+'</div>');
						
					}
					
				},
				error : function(data){
					
					console.log(data);
					$(".oerror").html('<div class="alert alert-danger">Error Occured</div>');

				}
			});
			
		})

		$("#resetvalidation").submit(function(e){
			
			e.preventDefault();
			var email = $("#fusername").val();
			var pass = $("#pass").val();
			var cpass = $("#cpass").val();
			
			if(pass == ""){
				
				$(".rerror").html('<div class="alert alert-danger">Enter Password</div>');
				return false;
				
			}
			
			if(cpass == ""){
				
				$(".rerror").html('<div class="alert alert-danger">Enter Confirm Password</div>');
				return false;
				
			}
			
			
			
			
			$.ajax({
				
				type : "post",
				url : "<? echo base_url('login/resetPassword') ?>",
				data : {email : email,pass:pass,cpass : cpass},
				success : function(data){
					
					console.log(data);
					
					if(data == "success"){
						
						$("#resetscreen").hide();
						$("#loginscreen").show();
						
						$(".serror").show();
						
						setInterval(function(){ $(".serror").fadeOut(); }, 2000)
						
						$("#fusername").val("");
						$("#pass").val("");
						$("#cpass").val("");
						$("#otp").val("");
						
					}else{
						
						$(".rerror").html('<div class="alert alert-danger">'+data+'</div>');
						
					}
					
				},
				error : function(data){
					
					console.log(data);
					$(".rerror").html('<div class="alert alert-danger">Error Occured</div>');

				}
			});
			
		})

		$("#login").submit(function(e){
			
			e.preventDefault();
			var fdata = $(this).serialize();
			
			$.ajax({
				
				type : "post",
				url : "<? echo base_url('login/do_login') ?>",
				data : fdata,
				dataType : "json",
				beforeSend : function(){
					
					
				},
				success : function(data){
					
					console.log(data);
					
					if(data.status == "Success"){
						
						if(data.role == "superadmin"){
							
							window.location.href = "<? echo base_url('admin/dashboard') ?>";
							
						}else if(data.role == "user"){
							
							window.location.href = "<? echo base_url('user/Userdashboard') ?>";
							
						}else if(data.role == "customer_admin"){
						
							window.location.href = "<? echo base_url('main/Admindashboard') ?>";
							
						}
						
						
//						location.reload();
						
					}else{
						
						$(".error").html('<div class="alert alert-danger">'+data.status+'</div>')
						
					}
					
					
					
				},
				error : function(data){
					
					console.log(data);
					$(".error").html('<div class="alert alert-danger">Error Occured</div>');

				}
				
			});
			
		})
	
	</script>
    
    

</html>