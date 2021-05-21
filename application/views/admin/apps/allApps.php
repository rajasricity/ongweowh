
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
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/dashboard') ?>"><i class="fas fa-home"></i></a></li>
                                        <li class="breadcrumb-item active">Customers</li>
                                    </ol>

                                </div>
                                <div class="col-sm-6">
                                
                                    <div class="float-right d-none d-md-block">
										<a class="btn btn-primary arrow-none waves-effect waves-light" href="<? echo base_url('admin/apps/createApp') ?>">
											 <i class="ion ion-md-person"></i> Create Customer
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
                                                        <th>ID</th>
                                                        <th>Customer ID</th>
                                                        <th>Customer Name</th>
                                                        <th>Short Description</th>
                                                        <th>Status</th>
                                                        <th>Users</th>
                                                        <th>No of Locations</th>
                                                        <th>Action</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                  
                                                  <? 
													$udata = $this->mongo_db->get_where("tbl_apps",array("deleted"=>0)); 
													$i = 1;
													foreach($udata as $ud){

                                                        $db = $this->mongo_db->return_database_name()."_".$ud["appId"];
													?>
                                                    
                                                    <tr>
                                                       
                                                        <td><? echo $i ?></td>
                                                        <td><? echo $ud['id'] ?></td>
                                                        <td><? echo $ud["appId"] ?></td>
                                                        <td>
<a  class="badge badge-primary" href="<? echo base_url('admin/apps/editApp/').$ud["appId"] ?>" style="font-size: 16px;white-space: pre-wrap;"><? echo $ud["appname"] ?>
                                                            </a>
                                                        </td>
                                                        <td><? echo $ud["short_desc"] ?></td>
                                                        <td><? echo ($ud["status"] == "Active") ? '<label class="badge badge-success" style="font-size: 14px">'.$ud["status"].'</label>' : '<label class="badge badge-warning" style="font-size: 14px">'.$ud["status"].'</label>' ?></td>
                                                        <td><? echo count($this->mongo_db->get_where("tbl_auths",array("deleted"=>0,"appid"=>$ud["appId"]))) ?></td>
                                                        <td><? echo $this->admin->getCount("","$db.tbl_locations",[],[]) ?></td>
                                                        <td>
                                                        	
                                                        	<a href="<? echo base_url('admin/apps/editApp/').$ud["appId"]."/edit" ?>"><i class="far fa-edit"></i></a>&nbsp;|&nbsp;
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
    $('#usersTable').DataTable({
		
		dom: 'Bfrtip',
		buttons: [
			'csv', 'excel','pageLength'
		],
		
	});
});		
	
function archiveFunction(id) {
       Swal({
  title: 'Are you sure?',
  text: 'You will not be able to recover this selected customer!',
  type: 'warning',
  showCancelButton: true,
  confirmButtonText: 'Yes, delete it!',
  cancelButtonText: 'No, keep it'
}).then((result) => {
  if (result.value) {

    Swal(
      'Deleted!',
      'Your Selected customer has been deleted.',
      'success'
    )
    $.ajax({
        method: 'POST',
        data: {'id' : id },
        url: '<?php echo base_url() ?>admin/apps/delApp/'+id,
        success: function(data) {
			console.log(data);
            location.reload();   
        }
    });
 
  } else if (result.dismiss === Swal.DismissReason.cancel) {
    Swal(
      'Cancelled',
      'Your Selected customer is safe :)',
      'success',
      
    )
  }
})
    }
	
				
</script>

 