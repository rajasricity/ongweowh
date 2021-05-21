
<? admin_header(); ?> 

<? admin_sidebar(); 

$mdb = mongodb;

$query = $this->mongo_db->get_where("tbl_auths",array("email"=>$this->session->userdata("admin_email")));
$aData = $this->mongo_db->get_where("tbl_apps",array("appId"=>$query[0]["appid"]));


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
<!--                                    <h4 class="page-title">Form Advanced</h4>-->
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="<? echo base_url('main/Admindashboard') ?>"><? echo $aData[0]["appname"] ?></a></li>
                                        <li class="breadcrumb-item active">Locations</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                <a onclick="window.history.back();" class="btn btn-dark btn-sm float-right">
                                  <i class="fa fa-arrow-left"></i>
                                </a>
                              </div>
                                
                            </div>
                        </div>
                        <!-- end row -->
                        
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
<div>
  <a href="#" onclick="showRequest();" class="btn btn-primary">Request Location Access</a>
  <a href="#" onclick="showLocation();" class="btn btn-primary">Add Location</a>
</div>

<div class="row" style="margin-top:10px;display:none" id="reqloc">
<div class="col-md-12">
<div class="card">
  <div class="card-header" style="color:#fff">Request Location</div>
  <div class="card-body">
<?
$mng = $this->admin->Mconfig();
$user = $this->admin->getRow($mng,['email'=>$this->session->userdata('admin_email')],[],"$mdb.tbl_auths");

$floc = [];
foreach($user->locations as $key=>$location){
    array_push($floc, $location->loccode);
}
$locations = $this->admin->getRows($mng,['$or'=>[['loccode'=>['$nin'=>$floc]]],"status"=>"Active"],[],"$database.tbl_locations");
?>
<form id="requestLocation">
<input type="hidden" name="user" value="<? echo $this->session->userdata('admin_email'); ?>">
<input type="hidden" name="appid" value="<? echo $this->session->userdata('appId'); ?>">
<div class="row">
  <div class="col-md-5">
    Notes
    <textarea rows="3" class="form-control" name="notes"></textarea>
  </div>
  <div class="col-md-4">
    Locations to Request <span style="color:red;">*</span>
    <select class="select2 form-control select2-multiple" multiple="multiple" multiple data-placeholder="Choose ..." name="locations[]" required>
      <option>Select</option>
    <? foreach($locations as $location){ ?>
      <option value="<? echo $location->loccode; ?>"><? echo $location->locname; ?></option>
    <? } ?>
    </select>

  </div>
  <div class="col-md-3">
    <br>
    <input type="submit" name="submit" class="btn btn-primary" style="width: 100%;" value="Submit Request"/>
  </div>
</div>
</form>
<center>
<img src="<? echo base_url('assets/images/loader.gif') ?>" style="display:none" width="75" height="75" id="req_loader">
</center>
<div class="alert alert-success" style="margin-top:10px;display: none" id="req_smsg"></div>

<?
if($this->admin->getCount($mng,"$database.location_requests",["user"=>$this->session->userdata('admin_email')],[]) > 0){
$rlocations = $this->admin->getRows($mng,["user"=>$this->session->userdata('admin_email')],[],"$database.location_requests");
?>
<br/>
<table class="table table-bordered" id="reqTable">
  <thead class="thead-light">
    <tr>
      <th style="padding:5px;font-size:12px;">Req. Date</th>
      <th style="padding:5px;font-size:12px;">Requested Locations</th>
      <th style="padding:5px;font-size:12px;">Notes</th>
      <th style="padding:5px;font-size:12px;">Status</th>
      <th style="padding:5px;font-size:12px;">Updated</th>
    </tr>
  </thead>
  <tbody>
<?
foreach($rlocations as $location){?>
<tr>
  <td><? echo date("M d,D Y H:i:s A", strtotime($location->Created_Date)); ?></td>
  <td>
    <? foreach($location->locations as $loc){
      echo "<span class='badge badge-success'>".$this->admin->getReturn($mng,"$database.tbl_locations",["loccode"=>$loc],[],"locname")."</span> ";
    }
    ?>
  </td>
  <td><? echo $location->notes; ?></td>
  <td>
  <?
    if($location->Status == 'Pending'){
echo "<span class='badge badge-warning'>".$location->Status."</span>";
    }else if($location->Status == 'Rejected'){
echo "<span class='badge badge-danger'>".$location->Status."</span>";
    }else if($location->Status == 'Approved'){
echo "<span class='badge badge-success'>".$location->Status."</span>";
    }
  ?></td>
  <td><? echo $location->Updated_Date; ?></td>
</tr>
<? } ?>
  </tbody>

</table>
<? } ?>
  </div>
</div>
</div>
</div>


<div class="row" style="margin-top:10px;display:none" id="addloc">
<div class="col-md-12">
<div class="card">
  <div class="card-header" style="color:#fff">Add Location</div>
  <div class="card-body">

<form id="addLocation">
  <input type="hidden" name="appid" value="<? echo $this->session->userdata('appId'); ?>">
  <input type="hidden" name="user" value="<? echo $this->session->userdata('admin_email'); ?>">
<div class="row">

<div class="col-md-3"> 

 <div class="form-group">
   <label>Location Name <span style="color:red;">*</span></label>
   <input type="text" class="form-control" name="lname" required>
 </div>

</div>	

<div class="col-md-3"> 

 <div class="form-group">
   <label>City <span style="color:red;">*</span></label>
   <input type="text" class="form-control" name="city" required>
 </div>

</div>    

<div class="col-md-3"> 

 <div class="form-group">
   <label>State <span style="color:red;">*</span></label>
   <input type="text" class="form-control" name="state" required>
 </div>

</div>  

<div class="col-md-3"> 

 <div class="form-group">
   <label>Country <span style="color:red;">*</span></label>
   <input type="text" class="form-control" name="country" required>
 </div>

</div>

<div class="col-md-3"> 

 <div class="form-group">
   <label>Zipcode <span style="color:red;">*</span></label>
   <input type="number" class="form-control" name="zip" required="required">
 </div>

</div>

<div class="col-md-3"> 

 <div class="form-group">
   <label>Latitude</label>
   <input type="text" class="form-control" name="lat">
 </div>

</div>

<div class="col-md-3"> 

 <div class="form-group">
   <label>Longitude</label>
   <input type="text" class="form-control" name="lon">
 </div>

</div>	

<div class="col-md-3"> 

 <div class="form-group">
   <label>Address <span style="color:red;">*</span></label>
   <textarea class="form-control" name="address" rows="2" required></textarea>
 </div>

</div>                  

</div>


<div class="row">

<div class="col-md-9">

 <div class="loader" style="display: none" id="add_loader">

<center>
  <img src="<? echo base_url('assets/images/loader.gif') ?>" width="75" height="75">
</center> 

</div>

<div class="alert alert-success" style="display:none" id="add_smsg"></div>

</div>

<div class="col-md-3" align="right">
 <button class="btn btn-primary arrow-none waves-effect waves-light clocSubmit" type="submit">
  Add Location
 </button>

</div>

</div>
</form>
  </div>
</div>
</div>
</div>
<h5>Locations</h5>
<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
<?
$mng=$this->admin->Mconfig();
$user = $this->admin->getRow($mng,['email'=>$this->session->userdata('admin_email')],[],"$mdb.tbl_auths");
?>
  <table class="table table-bordered" id="adminLocations">
  <thead class="thead-light">
    <tr>
      <th style="padding:5px;font-size:12px;">Location Name</th>
      <th style="padding:5px;font-size:12px;">Location Code</th>
      <th style="padding:5px;font-size:12px;">Address</th>
      <th style="padding:5px;font-size:12px;">City</th>
      <th style="padding:5px;font-size:12px;">State</th>
      <th style="padding:5px;font-size:12px;">Zip</th>
      <th style="padding:5px;font-size:12px;">Country</th>
    </tr>
  </thead>
  <tbody>
  <?
  foreach($user->locations as $key=>$location){ 
  $locdata = $this->admin->getRow($mng,['loccode'=>$location->loccode],[],"$database.tbl_locations");
	  
	  if($locdata->status == "Active"){
	  
    ?>
    <tr>
      <td><? echo $location->LocationName; ?></td>
      <td><? echo $location->loccode; ?></td>
      <td><? echo $locdata->address; ?></td>
      <td><? echo $locdata->city; ?></td>
      <td><? echo $locdata->state; ?></td>
      <td><? echo $locdata->zip; ?></td>
      <td><? echo $locdata->country; ?></td>
    </tr>
  <? }} ?>
  </tbody>
</table>
</div>
  </div>


  </div>
</div>
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
<!-- <script src="<? echo base_url(); ?>assets/pages/form-advanced.js"></script> -->
<script src="<? echo base_url(); ?>assets/plugins/select2/js/select2.min.js"></script>
<!-- <script src="<? echo base_url(); ?>assets/pages/form-advanced.js"></script> -->


<script>
				
$(document).ready(function() {

$(".select2").select2();

$(".select2-limiting").select2({
    maximumSelectionLength: 2
});

$("#reqTable").DataTable();
$("#adminLocations").DataTable({
    dom: 'Bfrtip',
    lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
		buttons: [
			'csv', 'excel','pageLength'
		],
  });
  
  $("#requestLocation").on('submit', function(e){
    e.preventDefault();
    var fdata = $("#requestLocation").serialize();
    $("input[type=submit]").attr("disabled", "disabled");
    $.ajax({
      url:"<? echo base_url() ?>main/inventory/requestLocation",
      data:fdata,
      type:"post",
      dataType:'json',
      beforeSend: function(){
        $("#req_loader").show();
      },
      success: function(data){
        $("input[type=submit]").removeAttr("disabled");
        $("#req_loader").hide();
        console.log(data);
        if(data.Status == 'Success'){
          $("#req_smsg").show();
          $("#req_smsg").html(data.Message);
          setTimeout(function(){
            location.reload();
          },4500);
        }
      },
      error: function(data){
        console.log(data);
      }

    });
  });



  $("#addLocation").on('submit', function(e){
    e.preventDefault();
    var fdata = $("#addLocation").serialize();
    $("input[type=submit]").attr("disabled", "disabled");
    $.ajax({
      url:"<? echo base_url() ?>main/inventory/addLocation",
      data:fdata,
      type:"post",
      dataType:'json',
      beforeSend: function(){
        $("#add_loader").show();
      },
      success: function(data){
        $("input[type=submit]").removeAttr("disabled");
        $("#add_loader").hide();
        console.log(data);
        if(data.Status == 'Success'){
          $("#add_smsg").show();
          $("#add_smsg").html(data.Message);
          setTimeout(function(){
            location.reload();
          },2500);
        }
      },
      error: function(jqxhr, txtStatus, error){
        console.log(error);
      }

    });
  });


  $("#createTout").on('submit', function(e){
    e.preventDefault();
    var fdata = $("#createTout").serialize();
    $("input[type=submit]").attr("disabled", "disabled");
    $.ajax({
      url:"<? echo base_url() ?>main/inventory/saveshipment",
      data:fdata,
      type:"post",
      dataType:'json',
      beforeSend: function(){
        $("#loader").show();
      },
      success: function(data){
        $("input[type=submit]").removeAttr("disabled");
        $("#loader").hide();
        $("#emsg").hide();
        $("#smsg").hide();
        console.log(data);
        if(data.Status == 'Success'){
          $("#smsg").show();
          $("#smsg").html(data.Message);
          setTimeout(function(){
            location.reload();
          },3000);
        }else{
          $("#emsg").show();
          $("#emsg").html(data.Message);
        }
      },
      error: function(jq,txt,error){
        $("#emsg").show();
        $("#emsg").html(error);
        console.log(error);
      }
    });
  });
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
	

    function showRequest(){
      $("#reqloc").slideToggle();
      if($("#addloc").css("display") == 'none'){

      }else{
        $("#addloc").slideToggle();  
      }
    }

    function showLocation(){
      $("#addloc").slideToggle();
      if($("#reqloc").css("display") == 'none'){

      }else{
        $("#reqloc").slideToggle();  
      }
    }
</script>

 