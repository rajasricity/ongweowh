
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
                                        <li class="breadcrumb-item active">Locations</li>
                                    </ol>

                                </div>
                                <div class="col-sm-6">
                                
                                    <div class="float-right d-none d-md-block">
										<a class="btn btn-primary arrow-none waves-effect waves-light" href="<? echo base_url('admin/locations/createLocation') ?>">
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
<!--                                        <h4 class="mt-0 header-title">All Locations</h4>-->
                                        
                                        <div class="table-responsive">
                                            <table class="table mb-0 table-bordered" id="usersTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                       
                                                        <th>#</th>
                                                        <th>Location Code</th>
                                                        <th>City</th>
                                                        <th>Address</th>
                                                        <th>State</th>
                                                        <th>Country</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                  
                                                  <? 
													$udata = $this->mongo_db->get_where("tbl_locations",array("deleted"=>0)); 
													$i = 1;
													foreach($udata as $ud){
													?>
                                                    
                                                    <tr>
                                                       
                                                        <td><? echo $i ?></td>
                                                        <td><? echo $ud["loccode"] ?></td>
                                                        <td><? echo $ud["city"] ?></td>
                                                        <td><? echo $ud["address"] ?></td>
                                                        <td><? echo $ud["state"] ?></td>
                                                        <td><? echo $ud["country"] ?></td>
                                                        <td><? echo $ud["status"] ?></td>
                                                        <td>
                                                        	
                                                        	<a href="<? echo base_url('admin/locations/editLocation/').$ud["_id"]->{'$id'} ?>"><i class="far fa-edit"></i></a>&nbsp;|&nbsp;
                                                        	<a href="javascript:void(0)" id="<? echo $ud["_id"]->{'$id'} ?>" onclick="archiveFunction(this.id)"><i class="fas fa-trash-alt" style="color: red"></i></a>
                                                        	
                                                        </td>
                                                        
                                                    </tr>
                                                    
                                                  <? $i++;} ?>  
                                                  
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
    $('#usersTable').DataTable();
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

 