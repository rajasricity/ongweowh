
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
                                        <li class="breadcrumb-item active">Users</li>
                                    </ol>

                                </div>
                                <div class="col-sm-6">
                                
                                    <div class="float-right d-none d-md-block">
										<a class="btn btn-primary arrow-none waves-effect waves-light" href="<? echo base_url('admin/users/createUser') ?>">
											 Create
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
<!--                                        <h4 class="mt-0 header-title">All Users</h4>-->
                                        
                                        <div class="table-responsive">
                                            <table class="table mb-0 table-bordered" id="usersTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                       
                                                        <th style="white-space: nowrap">#</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                  
                                                  <? 
													$udata = $this->mongo_db->get_where("tbl_auths",array("deleted"=>0)); 
													$i = 1;
													foreach($udata as $ud){
														
														if($ud["role"] != "superadmin"){
													?>
                                                    
                                                    <tr>
                                                       
                                                        <td><? echo $i ?></td>
                                                        <td><? echo $ud["uname"] ?></td>
                                                        <td><? echo $ud["email"] ?></td>
                                                        <td><? echo $ud["role"] ?></td>
                                                        <td><? echo $ud["status"] ?></td>
                                                        <td>
                                                        	
                                                        	<a href="<? echo base_url('admin/users/editUser/').$ud["_id"]->{'$id'} ?>"><i class="far fa-edit" style="color: cadetblue"></i></a>&nbsp;|&nbsp;
                                                        	<a href="javascript:void(0)" id="<? echo $ud["_id"]->{'$id'} ?>"  onclick="archiveFunction(this.id)"><i class="fas fa-trash-alt" style="color: red"></i></a>
                                                        	
                                                        </td>
                                                        
                                                    </tr>
                                                    
                                                  <? $i++;}} ?>  
                                                  
                                                </tbody>
                                            </table>
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
				
$(document).ready(function() {
    alert();
    $('#usersTable').DataTable({
		
		dom: 'Bfrtip',
		buttons: [
			'csv', 'excel'
		],
		
	});
} );
	
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

 