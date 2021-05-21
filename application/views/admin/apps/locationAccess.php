
<? admin_header(); ?> 

           
<? admin_sidebar(); ?>            

<?
$mng = $this->admin->Mconfig();
?>
 <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="page-title-box">
                            <div class="row align-items-center">
                                
                                <div class="col-sm-6">
                                    <h4 class="page-title"><? echo $l[0]["appname"] ?></h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/dashboard') ?>"><i class="fas fa-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="<? echo base_url('admin/apps') ?>">Customers</a></li>
                                        <li class="breadcrumb-item active">Location Access</li>
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
                                    <div class="card-body">
                                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                                            <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#message1" role="tab">
                                        <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
        <span class="d-none d-sm-block"><i class="mdi mdi-arrow-collapse-horizontal"></i> Requested Locations</span>   
                                        </a>
                                            </li>
                                            <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#import" role="tab">
                                        <span class="d-block d-sm-none"><i class="far fa-download"></i></span>
                                        <span class="d-none d-sm-block"><i class="ti-plus"></i> Added Locations</span>   
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
<!-- Request Locations -->
<div class="tab-pane active p-3" id="message1" role="tabpanel">

<div class="table-responsive">
                                            <table class="table mb-0 table-bordered" id="usersTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                       
                                                        <th style="white-space: nowrap;width:1px;">#</th>
                                                        <th>Date</th>
                                                        <th>User</th>
                                                        <th>Locations</th>
                                                        <th>Notes</th>
                                                        <th>Status</th>
                                                        <th style="white-space: nowrap;width:1px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                  
                                                  <? 
//    $udata = $this->mongo_db->get_where("location_requests",array("appid"=>$_SESSION['appid'])); 
                                                    $i = 1;
                                                    foreach($location_requests as $ud){
                                                    ?>
                                                    
                                                    <tr>
                                                       
                                                        <td><? echo $i ?></td>
                                                        <td style="white-space: nowrap;width:1px;"><? echo date("m-d-Y",strtotime($ud->Created_Date)) ?></td>
                                                        <td style="white-space: nowrap;width:1px;">
                                                            <? echo $ud->user ?>
                                                        </td>
                                                        <td>
                                                        <? 
                                                        foreach($ud->locations as $location){
        echo '<span class="badge badge-primary" style="font-size: 14px;">'.$this->admin->getReturn($mng,"$database.tbl_locations",["loccode"=>$location],[],"locname").'</span>'; 
                                                        }
                                                        ?>
                                                        </td>
                                                        <td><? echo $ud->notes ?></td>
                                                        <td style="white-space: nowrap;width:1px;"><? echo $ud->Status ?></td>
                                                        <td>
                                                            
<a href="<? echo base_url('admin/apps/editRequest/').$ud->_id ?>"><i class="far fa-edit"></i></a>
&nbsp;|&nbsp;
<a href="javascript:void(0)" id="<? echo $ud->_id ?>" onclick="archiveFunction(this.id)"><i class="fas fa-trash-alt" style="color: red"></i></a>
                                                            
                                                        </td>
                                                        
                                                    </tr>
                                                    
                                                  <? $i++;} ?>  
                                                  
                                                </tbody>
                                                <tfooter>
                                                    <tr style="background-color: #f1f1f1">
                                                    <th style="white-space: nowrap;width:1px;">#</th>
                                                    <th>Date</th>
                                                    <th>User</th>
                                                    <th>Locations</th>
                                                    <th>Notes</th>
                                                    <th>Status</th>
                                                    <th style="white-space: nowrap;width:1px;">Action</th>
                                                    </tr>
                                                </tfooter>
                                            </table>
                                        </div>

</div>
<!-- Request Locations Close -->
<!-- Added Locations -->
<div class="tab-pane p-3" id="import" role="tabpanel">

<div class="table-responsive">
                                            <table class="table mb-0 table-bordered" id="usersTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                       
                                                        <th style="white-space: nowrap;width:1px;">#</th>
                                                        <th>Date</th>
                                                        <th>User</th>
                                                        <th>Locations</th>
                                                        <th>Address</th>
                                                        <th>Status</th>
                                                        <th style="white-space: nowrap;width:1px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                  
                                                  <? 
//    $sdata = $this->mongo_db->get_where("location_submits",array("appid"=>$_SESSION['appid'])); 
                                                    $i = 1;
                                                    foreach($location_submits as $ud){
                                                    ?>
                                                    
                                                    <tr>
                                                       
                                                        <td><? echo $i ?></td>
                                                        <td style="white-space: nowrap;width:1px;"><? echo date("m-d-Y",strtotime($ud->Created_Date)) ?></td>
                                                        <td style="white-space: nowrap;width:1px;">
                                                            <? echo $ud->user ?>
                                                        </td>
                                                       <td><? echo $ud->lname.", ".$ud->city.", ".$ud->state; ?></td>
                                                       <td><? echo $ud->address ?></td>
                                                <td style="white-space: nowrap;width:1px;"><? echo $ud->Status ?></td>
<td style="white-space: nowrap;width:1px;">                                                            
<a href="<? echo base_url('admin/apps/editSubmit/').$ud->_id ?>"><i class="far fa-edit"></i></a>
<!-- &nbsp;|&nbsp;
<a href="javascript:void(0)" id="<? echo $ud->_id ?>" onclick="archiveFunction(this.id)"><i class="fas fa-trash-alt" style="color: red"></i></a> -->
                                                            
                                                        </td>
                                                        
                                                    </tr>
                                                    
                                                  <? $i++;} ?>  
                                                  
                                                </tbody>
                                                <tfooter>
                                                    <tr style="background-color: #f1f1f1">
                                                    <th style="white-space: nowrap;width:1px;">#</th>
                                                    <th>Date</th>
                                                    <th>User</th>
                                                    <th>Locations</th>
                                                    <th>Address</th>
                                                    <th>Status</th>
                                                    <th style="white-space: nowrap;width:1px;">Action</th>
                                                    </tr>
                                                </tfooter>
                                            </table>
                                        </div>

</div>
<!-- Added Locations Close -->
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
        "buttons": [
			'pageLength',
			{
			extend: 'excelHtml5',
			title:'Items Ongoweoweh',
				exportOptions: {
					columns: [1,2,3,4,5,6]
				}
			},
			{
			extend: 'csvHtml5',
			title:'Customers Ongoweoweh',
				exportOptions: {
					columns: [1,2,3,4,5,6]
				}
			}
		],
        
    });
});     
    
function archiveFunction(id) {
       Swal({
  title: 'Are you sure?',
  text: 'You will not be able to recover this selected location request!',
  type: 'warning',
  showCancelButton: true,
  confirmButtonText: 'Yes, delete it!',
  cancelButtonText: 'No, keep it'
}).then((result) => {
  if (result.value) {
    $.ajax({
        method: 'POST',
        data: {'id' : id },
        url: '<?php echo base_url() ?>admin/apps/delRequest/',
        success: function(data) {
            if(data == 'success'){
                Swal(
      'Deleted!',
      'Your Selected location request has been deleted.',
      'success'
                )

        location.reload();
            }else{
        Swal(
      'Oops!',
      'Something went wrong',
      'error'
                )
            }
        }
    });
 
  } else if (result.dismiss === Swal.DismissReason.cancel) {
    Swal(
      'Cancelled',
      'Your Selected location request is safe :)',
      'success',
      
    )
  }
})
    }
    
                
</script>