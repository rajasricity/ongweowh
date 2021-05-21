
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
                                        <li class="breadcrumb-item"><a href="<? echo base_url('user/Userdashboard') ?>">Inventory</a></li>
                                        <li class="breadcrumb-item active">Location Summary</li>
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
                                  <div class="card-header">
<span style="color:#fff;font-weight: bold">Add Transfer Out</span>  
                                  </div>
                                    <div class="card-body">
<?
$mng=$this->admin->Mconfig();
$user = $this->admin->getRow($mng,['email'=>$this->session->userdata('admin_email')],[],"ongweoweh.tbl_auths");
$floc = [];
$tloc = [];
foreach($user->locations as $key=>$location){
  if($location->Type == 'from'){
    array_push($floc, $location);
  }else{
    array_push($tloc, $location);
  }
}
$ldata = $this->admin->getRow($mng,['loccode'=>$id],[],"ongweoweh.tbl_locations");
?>
<form id="createTout" autocomplete="off">
  <input type="hidden" name="userid" value="<? echo $user->_id; ?>"/>
  <input type="hidden" name="appid" value="<? echo $user->appid; ?>"/>
  <input type="hidden" name="locationid" value="<? echo $id; ?>"/>
<div class="row">
  <div class="col-md-3">
    Shipper PO <span style="color:red">*</span>
    <input type="text" name="shipperpo" id="shipperpo" class="form-control" required tabindex="1">
  </div>
  <div class="col-md-3">
    ProNum <span style="color:red">*</span>
    <input type="text" name="pronum" id="pronum" class="form-control" required tabindex="2">
  </div>
  <div class="col-md-3">
    Reference #3
    <input type="text" name="reference" id="reference" class="form-control" tabindex="3">
  </div>
  <div class="col-md-3">
    Shipment Date (mm/dd/yyyy) <span style="color:red">*</span>
    <input type="date" name="shipdate" id="shipdate"  class="form-control" value="<?php echo date('Y-m-d'); ?>" required tabindex="4">
  </div>
</div>


<div class="row" style="margin-top:20px;">
  <div class="col-md-3">
    To Location <span style="color:red">*</span>
    <select name="tlocation" class="form-control" required tabindex="5">
      <option value="">Select To Location</option>
      <? foreach($tloc as $value){?>
        <option value="<? echo $value->loccode; ?>"><? echo $value->LocationName; ?></option>
        <?}?>
    </select>
  </div>
  <div class="col-md-3">
    From Location <span style="color:red">*</span>
    <select name="flocation" class="form-control" required tabindex="6">
      <option value="">Select From Location</option>
      <? foreach($floc as $value){?>
        <option value="<? echo $value->loccode; ?>"><? echo $value->LocationName; ?></option>
        <?}?>
    </select>
  </div>
  <div class="col-md-3">
    Quantity <span style="color:red">*</span>
    <input type="number" min="1" name="quantity" id="quantity" class="form-control" tabindex="7">
  </div>
  <div class="col-md-3">
    Item <span style="color:red">*</span>
    <select name="item" class="form-control" required tabindex="8">
      <option value="">Select Item</option>
      <option value="Chep 48x40 Block Pallet">Chep 48x40 Block Pallet</option>
    </select>
  </div>
</div>
<div class="row" style="margin-top:20px;">
  <div class="col-md-7">
    <div class="alert alert-success" style="display:none" id="smsg"></div>
    <div class="alert alert-success" style="display:none" id="emsg"></div>
  </div>
  <div class="col-md-1">
    <img src="<? echo base_url('assets/images/loader.gif') ?>" width="60" height="60" style="margin-top:-15px;text-align: right;display:none" id="loader">
  </div>
  <div class="col-md-4">
    <input type="submit" name="submit" class="btn btn-primary float-right"
  </div>
</div>
</form>

<div style="padding:15px;width:100%">
<h5>Location Summary</h5>
Date Range for this data is June 1st 2017 to Current
<div class="row">
  <div class="col-md-12">
  <table class="table table-bordered">
  <thead class="thead-light">
    <tr>
      <th>Location</th><th>Issues</th><th>Returns</th><th>Transfer Ins</th>
      <th>Transfer Outs</th><th>Adjustments</th><th>Ending Balance</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><? echo $ldata->locname; ?></td>
      <td></td>
      <td></td>
      <td align="right">
	<? 
	echo $this->admin->getCount($mng,"ongweoweh.tbl_touts",["appid"=>$user->appid,"userid"=>(string)$user->_id,"tlocation"=>$id],[]);
		?>
	</td>
      <td align="right">
	<? 
	echo $this->admin->getCount($mng,"ongweoweh.tbl_touts",["appid"=>$user->appid,"userid"=>(string)$user->_id,"locationid"=>$id],[]);
		?>
	</td>
      <td></td>
      <td></td>
    </tr>
  </tbody>
</table>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
  <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#tout" role="tab">
                                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                    <span class="d-none d-sm-block">Transfer Outs</span>    
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#tin" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                    <span class="d-none d-sm-block">Transfer Ins</span>    
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#issues" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                                    <span class="d-none d-sm-block">Issues</span>    
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#returns" role="tab">
                                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                    <span class="d-none d-sm-block">Returns</span>    
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#adjustments" role="tab">
                                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                    <span class="d-none d-sm-block">Adjustments</span>    
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
        <div class="tab-pane active" id="tout" role="tabpanel" style="margin-top:10px;">
<?
$touts = $this->admin->getRows($mng,["appid"=>$user->appid,"userid"=>(string)$user->_id,"locationid"=>$id],['sort'=>['_id'=>-1]],"ongweoweh.tbl_touts");
?>
<table class="table table-bordered table-striped" id="toutTable">
  <thead style="background-color:antiquewhite;">
    <tr>
      <th style="padding:6px">Shipper PO</th>
      <th style="padding:6px">Shipement Date</th>
      <th style="padding:6px">Pro Num</th>
      <th style="padding:6px">To Location</th>
      <th style="padding:6px">Quantity</th>
      <th style="padding:6px">Report Date</th>
    </tr>
  </thead>
  <tbody>
<?
foreach($touts as $tout1){?>
<tr>
  <td style="padding:6px"><? echo $tout1->shipperpo; ?></td>
  <td style="padding:6px"><? echo $tout1->shipdate; ?></td>
  <td style="padding:6px"><? echo $tout1->pronum; ?></td>
  <td style="padding:6px"><? echo $this->admin->getReturn($mng,'ongweoweh.tbl_locations',["loccode"=>$tout1->tlocation],[],"locname"); ?></td>
  <td style="padding:6px"><? echo $tout1->quantity; ?></td>
  <td style="padding:6px"><? echo $tout1->Created_Date; ?></td>
</tr>
<?} ?>
  </tbody>
</table>
                                            </div>
<div class="tab-pane" id="tin" role="tabpanel" style="margin-top:10px;">

<table class="table table-bordered table-striped" id="tinTable">
  <thead style="background-color:antiquewhite;">
    <tr>
      <th style="padding:6px">Shipper PO</th>
      <th style="padding:6px">Shipement Date</th>
      <th style="padding:6px">Pro Num</th>
      <th style="padding:6px">From Location</th>
      <th style="padding:6px">Quantity</th>
      <th style="padding:6px">Report Date</th>
    </tr>
  </thead>
  <tbody>
  <?
$tins = $this->admin->getRows($mng,["appid"=>$user->appid,"userid"=>(string)$user->_id,"tlocation"=>$id],['sort'=>['_id'=>-1]],"ongweoweh.tbl_touts");
?>

<?
foreach($tins as $tin){?>
<tr>
  <td style="padding:6px"><? echo $tout1->shipperpo; ?></td>
  <td style="padding:6px"><? echo $tout1->shipdate; ?></td>
  <td style="padding:6px"><? echo $tout1->pronum; ?></td>
  <td style="padding:6px"><? echo $this->admin->getReturn($mng,'ongweoweh.tbl_locations',["loccode"=>$tout1->flocation],[],"locname"); ?></td>
  <td style="padding:6px"><? echo $tout1->quantity; ?></td>
  <td style="padding:6px"><? echo $tout1->Created_Date; ?></td>
</tr>
<?} ?>

  </tbody>
</table>

</div>
<div class="tab-pane" id="issues" role="tabpanel" style="margin-top:10px;">

<table class="table table-bordered table-striped" id="issuesTable">
  <thead style="background-color:antiquewhite;">
    <tr>
      <th style="padding:6px">CHEP Reference</th>
      <th style="padding:6px">Ongweoweh Reference</th>
      <th style="padding:6px">Shipment Date</th>
      <th style="padding:6px">Location</th>
      <th style="padding:6px">Quantity</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>

</div>
<div class="tab-pane" id="returns" role="tabpanel" style="margin-top:10px;">

<table class="table table-bordered table-striped" id="returnsTable">
  <thead style="background-color:antiquewhite;">
  <tr>
      <th style="padding:6px">CHEP Reference</th>
      <th style="padding:6px">Ongweoweh Reference</th>
      <th style="padding:6px">Shipment Date</th>
      <th style="padding:6px">Location</th>
      <th style="padding:6px">Quantity</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>

</div>
<div class="tab-pane" id="adjustments" role="tabpanel" style="margin-top:10px;">

<table class="table table-bordered table-striped" id="adjustmentsTable">
  <thead style="background-color:antiquewhite;">
  <tr>
      <th style="padding:6px">CHEP Reference</th>
      <th style="padding:6px">Ongweoweh Reference</th>
      <th style="padding:6px">Shipment Date</th>
      <th style="padding:6px">Location</th>
      <th style="padding:6px">Quantity</th>
    </tr>
  </thead>
  <tbody>
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

<script>
				
$(document).ready(function() {
$("#toutTable").DataTable({
    dom: 'Bfrtip',
    lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
		buttons: [
			'csv', 'excel','pageLength'
		],
  });
  
  $("#tinTable").DataTable({
    dom: 'Bfrtip',
    lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
		buttons: [
			'csv', 'excel','pageLength'
		],
  });
  
  $("#issuesTable").DataTable({
    dom: 'Bfrtip',
    lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
		buttons: [
			'csv', 'excel','pageLength'
		],
  });
  
  $("#returnsTable").DataTable({
    dom: 'Bfrtip',
    lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
		buttons: [
			'csv', 'excel','pageLength'
		],
  });
  
  $("#adjustmentsTable").DataTable({
    dom: 'Bfrtip',
    lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
		buttons: [
			'csv', 'excel','pageLength'
		],
	});
  $("#createTout").on('submit', function(e){
    e.preventDefault();
    var fdata = $("#createTout").serialize();
    $.ajax({
      url:"<? echo base_url() ?>main/inventory/saveshipment",
      data:fdata,
      type:"post",
      dataType:'json',
      beforeSend: function(){
        $("#loader").show();
      },
      success: function(data){
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
	
				
</script>