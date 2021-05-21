
<? admin_header(); ?> 

           
<? 
$_SESSION['appid'] = $l[0]['appId'];
admin_sidebar(); 

$aid = $this->uri->segment(4);
?>            
<style>
td{
	font-size: 14px;
	padding:5px !important;
}
</style>
<link href="<? echo base_url(); ?>assets/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css" rel="stylesheet" type="text/css" media="screen">

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
                                        <li class="breadcrumb-item active">Transfers</li>
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
                                    <div class="card-body" style="padding:0px;">
                                    
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#messages1" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                                    <span class="d-none d-sm-block"><i class="dripicons-location"></i> Transfers</span>   
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#import" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-download"></i></span>
                                                    <span class="d-none d-sm-block"><i class="ti-import"></i> Import</span>   
                                                </a>
                                            </li>
                                        </ul>
        
                                        <!-- Tab panes -->
                                        <div class="tab-content">

                                            <div class="tab-pane active p-3" id="messages1" role="tabpanel">
                                                
                                                <div class="row">
													<div class="col-lg-12">
														<div class="">
															<div class="card-body" style="padding:0px;">
					                                        
<div class="table-rep-plugin">
<div class="table-responsive allLoc">
<table class="table mb-0 table-bordered" style="width:5200px;z-index: 10000" id="transfersTable">
	<thead class="thead-light">
		<tr>
			<th style="width:10px"></th>
			<th style="width:10px"></th>
			<th style="width:120px">Shipper PO</th>
			<th style="width:150px">Shippement Date</th>
			<th style="width:120px">ProNum</th>
			<th style="width:100px">Reference#3</th>
			<th style="width:140px">Item</th>
			<th style="width:350px">From Location</th>
			<th style="width:160px">FromLocation Code</th>
			<th style="width:100px">To Location</th>
			<th style="width:150px">To Location Code</th>
			<th style="width:150px">ImportToLocation</th>
			<th style="width:190px">ImportToLocation Code</th>
			<th style="width:130px">ImportToAddress</th>
			<th style="width:120px">ImportToCity</th>
			<th style="width:120px">ImportToState</th>
			<th style="width:120px">ImportToZip</th>
			<th style="width:130px">ImportToCountry</th>
			<th style="width:100px">Quantity</th>
			<th style="width:100px">Report Date</th>
			<th style="width:100px">User</th>
			<th style="width:100px">rcvDate</th>
			<th style="width:100px">processDate</th>
			<th style="width:100px">CHEPProcessDate</th>
			<th style="width:100px">CHEPUMI</th>
			<th style="width:100px">UploadedtoCHEP</th>
			<th style="width:100px">ReasonforHold</th>
			<th style="width:100px">locID</th>
			<th style="width:100px">locID_wRecID</th>
			<th style="width:100px">notes_general</th>
			<th style="width:100px">dupID</th>
			<th style="width:100px">Program</th>
			<th style="width:100px">Type</th>
			<th style="width:100px">JNJ_ID</th>
			<th style="width:100px">TransactionID</th>
			<th style="width:100px">CHEPReference</th>
			<th style="width:100px">OngweowehReference</th>
		</tr>
	</thead>
	<tbody></tbody>	
</table>
</div>
</div>
																
																

															</div>
														</div>
													</div>
												</div>
                                                
                                            </div>

                                            
                                            <div class="tab-pane p-3" id="import" role="tabpanel">
                                            
                                            	<form id="fileinfo" method="post" enctype="multipart/form-data">
                                            	
												   <div class="row">

														 <div class="col-md-3"> 

															<div class="form-group">
																<label>Select File</label>
																<input type="file" class="form-control" name="ldata" style="height: 40px"  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
																<small style="color: red; font-size: 14px">Note : Please select <b>.xlsx</b> format</small>
															</div>

														 </div>
                                          
                                          				 <div class="col-md-3 m-t-30">
															
															<input type="hidden" name="appID" value="<? echo $aid ?>">
															<button class="btn btn-primary arrow-none waves-effect waves-light" type="submit" id="iSubmit"><i class="dripicons-upload"></i> Upload</button>
															
															

														</div>
                                          
													    <div class="col-md-3">
													    	
													    </div> 
                                          
                                          				<div class="col-md-3 m-t-30" align="right">
                                          					
                                          					<a href="<? echo base_url('assets/locations/sample.xlsx') ?>" class="btn btn-info"><i class="ion ion-ios-download"></i> Download Sample</a>
                                          					
                                          				</div>
                                           
													</div>

													<div class="row">
														<div class="col-md-12">
															<div class="mloader" style="display:none">
	<center><img src="<? echo base_url('assets/images/loader.gif') ?>" width="80" height="80" ></center>
														</div>
														<div class="merror"></div>
														</div>
													</div>
                                           
												</form> 
                                            
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
<script src="<? echo base_url(); ?>assets/plugins/select2/js/select2.min.js"></script>
<script>
$(document).ready(function(){
});
	

				
	$(document).ready(function() {
		$("#transfersTable").DataTable({
		 "dom": 'Bfrtip',
		 buttons: [
				'csv', 'excel','pageLength'
			],
		 "bProcessing": true,
		 "deferRender": true,
		 "scroller":true,
         "sAjaxSource": "<? echo base_url(); ?>admin/apps/getTransfers",
         "aoColumns": [
         		{ mData: 'Check'},
         	   { mData: 'Actions'},
               { mData: 'shipperpo' },
               { mData: 'shippmentdate' } ,
               { mData: 'pronum' },
               { mData: 'reference' },
               { mData: 'item' },
               { mData: 'flocation' },
               { mData: 'flcoationcode' },
               { mData: 'tlcoation' },
               { mData: 'tlocationcode' },
               { mData: 'importtolocation' },
               { mData: 'importtolocationcode' },
               { mData: 'importtoaddress' },
               { mData: 'importtocity' },
               { mData: 'importtostate' },
               { mData: 'importtozip' },
               { mData: 'importtocountry' } ,
               { mData: 'quantity' },
               { mData: 'reportdate' },
               { mData: 'user' },
               { mData: 'rcvdate' },
               { mData: 'processdate' },
               { mData: 'chepprocessdate' },
               { mData: 'chepumi' },
               { mData: 'uploadedetochep' },
               { mData: 'reasonforhold' },
               { mData: 'locid' },
               { mData: 'locid_wrecid' },
               { mData: 'notes_general' },
               { mData: 'dupid' },
               { mData: 'program' } ,
               { mData: 'type' },
               { mData: 'jnj_id' },
               { mData: 'transactionid' },
               { mData: 'chepreference' },
               { mData: 'ongreference' }
             ],
          "bLengthChange": true,
		});
	} );
	

	
	
				
</script>

 