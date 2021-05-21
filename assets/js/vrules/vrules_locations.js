// JavaScript Document
function randomString(length) {
	var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];
    return result;
}

$(".vmodal").click(function(){

	$("#vmodal").modal("show");
	$(".updvalidationrules").show();
	$("#fieldname").val($(this).attr("fname"));
	$(".validationruleclosed").html('');
	
	var vrid = $(this).attr("vrid");
	var base_url = $("#base_url").val();
	
	if(vrid != ""){
		var field = $("#fieldname").val();
		var appId = $("#vAppid").val();
		var table = $("#vTable").val();

		$.ajax({

			type : 'post',
			url : base_url+'admin/fields/editField',
			data : {vrid:vrid},
			dataType : 'json',
			success : function(data){
								
//				console.log(data)
				if(data.status == "on"){
					
					$('.validationruleclosed').html(data.cond_data);
					$(".select2").select2();
					$(".validationruleclosed").show();
					$('input[name="validationrule"]')[0].checked = true;
					$("#vid").val(vrid);
					$(".addRule").show();
					$(".ufields").show();
					
				}
				
			},
			error : function(data){
				
//				console.log(data);
				
			}

		})
		
	}else{
		
		$(".validationruleclosed").hide();
		$(".addRule").hide();
		$(".addedrules").html('');
		$('input[name="validationrule"]')[0].checked = false;
		
			$.ajax({

				type : "post",
				data : {table:"tbl_locations"},
				dataType : 'json',
				url : base_url+"admin/tasks/getColumns",
				success : function(data){

//					console.log(data);

					$('.validationruleclosed').html('<div class="row delSelRule" style="background-color: #ccc;padding: 12px;margin: 5px;border-radius: 5px;"><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 16px;padding:0px;"><label>When</label></div><div class="col-md-10" style="background-color: #eee;"><div class="row" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column1[]" class="form-control valLabels" rid="refvalLabels" rCount="1">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group oprefvalLabels"><select name="condition1[]" class="form-control onchangevrulesCondition" rCount="1">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group refvalLabels vrulesConditionValue"><input type="text" name="cond_value1[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-plus-circle addVlabels" rCount="1" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div><div class="addedLabels"></div></div><div class="col-md-1" align="right"><i class="fa fa-times-circle deleteallRules" delRule="delSelRule" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 15px;padding:0px;"><label>Message</label></div><div class="col-md-10" style="background-color: #eee;"><textarea rows="6" cols="10" class="form-control" name="alertMessage1[]" style="margin:10px"></textarea><input type="hidden" name="rulesCount[]" value="1"></div></div>'); 


				}
			});
		
	}
	

});

$(document).on("click",'#customRadioInline1',function(){
	
	if($(this). prop("checked") == true){
		
		$(".validationruleclosed").show();
		$(".addRule").show();
		$(".ufields").show();
//		$(".updvalidationrules").hide();
		
	}else{
		
		$(".validationruleclosed").hide();
		$(".addRule").hide();
		$(".ufields").hide();
		
	}
	
});

$(document).ready(function () {
    //Initialize tooltips
    $('.nav-tabs > li a[title]').tooltip();
    
});


// locations script starts

	// when condition script starts

		function getDate(){
			
			var now = new Date();
			var month = (now.getMonth() + 1);               
			var day = now.getDate();
			if (month < 10) 
				month = "0" + month;
			if (day < 10) 
				day = "0" + day;
			var today = now.getFullYear() + '-' + month + '-' + day;
			
			return today;
			
		}

		$(document).on("change",".valLabels",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("rid");
			var uopid = $(this).attr("uopid");
			var rCount = $(this).attr("rCount");
			
			$(".vrulesConditionValue").show();
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/tasks/getDatatypeconditions",
				dataType : 'json',
				data : {column : column,table:"tbl_locations","onchangeColref":"onchangevrulesCondition","uopid":uopid,"rCount":rCount},
				success : function(data){
					
					$(".op"+ref).html(data.operators);
					
					if(data.fields.locnames != null){
						
						$("."+ref).html(data.fields.locnames);
						$(".select2").select2();
						
					}else if(data.fields.status != null){
						
						$("."+ref).html(data.fields.status);
						
					}else if(data.fields.location_type != null){
						
						$("."+ref).html(data.fields.location_type);
						
					}else if(data.fields.import_date != null){
						
						$("."+ref).html(data.fields.import_date);
						
					}else if(data.fields.accounts != null){
						
						$("."+ref).html(data.fields.accounts);
						$(".select2").select2();
						
					}else{
						
						$("."+ref).html('<input type="text" name="cond_value'+rCount+'[]" class="form-control">');
						
					}
					
					
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
		});

		$(document).on("change",".uvalLabels",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("rid");
			var uopid = $(this).attr("uopid");
			var rCount = $(this).attr("rCount");
			
			$("."+ref).show();
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/tasks/getDatatypeconditions",
				dataType : 'json',
				data : {column : column,table:"tbl_locations","onchangeColref":"voperatorCh","uopid":uopid,"rCount":rCount},
				success : function(data){
					
					$(".uop"+ref).html(data.operators);
					
					if(data.fields.locnames != null){
						
						$("."+ref).html(data.fields.locnames);
						$(".select2").select2();
						
					}else if(data.fields.status != null){
						
						$("."+ref).html(data.fields.status);
						
					}else if(data.fields.location_type != null){
						
						$("."+ref).html(data.fields.location_type);
						
					}else if(data.fields.import_date != null){
						
						$("."+ref).html(data.fields.import_date);
						
					}else if(data.fields.accounts != null){
						
						$("."+ref).html(data.fields.accounts);
						$(".select2").select2();
						
					}else{
						
						$("."+ref).html('<input type="text" name="cond_value'+rCount+'[]" class="form-control">');
						
					}
					
					
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
		});

		$(document).ready(function(){
		
			var maxField = 20; //Input fields increment limitation
			var addButton = $('.addVlabels'); //Add button selector
			var wrapper = $('.addedLabels'); //Input field wrapper

			var x = 1; //Initial field counter is 1
			var y = 1;

			//Once add button is clicked
			$(document).on("click",'.addVlabels',function(){
				//Check maximum number of input fields
				if(x < maxField){
					
					var rRef = $(this).attr("rCount");
					var base_url = $("#base_url").val();
					x++;
					
					$.ajax({
						
						type : "post",
						data : {table:"tbl_locations"},
						dataType : 'json',
						url : base_url+"admin/tasks/getColumns",
						success : function(data){
//							console.log(data)
							
							$('.addedLabels').append('<div class="row removeLabel'+x+'" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column'+rRef+'[]" class="form-control uvalLabels" rid="urefvalLabels'+x+'" rCount="'+rRef+'" uopid="updateopVal'+x+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group uopurefvalLabels'+x+'"><select name="condition'+rRef+'[]" class="form-control voperatorCh"  rCount="'+rRef+'" opid="updateopVal'+x+'">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group urefvalLabels'+x+' updateopVal'+x+'"><input type="text" name="cond_value'+rRef+'[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-times-circle remVlabels" lid="removeLabel'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;<i class="fa fa-plus-circle adddomVlabels"  rCount="'+rRef+'" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>'); 
							
						}
						
					})
					 //Increment field counter
					
					y++;
				}
			});

			$(document).on("click",".adddomVlabels",function(){
				//Check maximum number of input fields
				if(x < maxField){ 
					
					var base_url = $("#base_url").val();
					var rRef = $(this).attr("rCount");
					
					x++;
					
					$.ajax({
						
						type : "post",
						data : {table:"tbl_locations"},
						dataType : 'json',
						url : base_url+"admin/tasks/getColumns",
						success : function(data){
							console.log(data);
							$('.addedLabels').append('<div class="row removeLabel'+x+'" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column'+rRef+'[]" class="form-control uvalLabels" rid="urefvalLabels'+x+'"  rCount="'+rRef+'" uopid="updateopVal'+x+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group uopurefvalLabels'+x+'"><select name="condition'+rRef+'[]"  rCount="'+rRef+'" class="form-control voperatorCh" opid="updateopVal'+x+'">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group urefvalLabels'+x+' updateopVal'+x+'"><input type="text" name="cond_value'+rRef+'[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-times-circle remVlabels" lid="removeLabel'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;<i class="fa fa-plus-circle adddomVlabels" rCount="'+rRef+'" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>'); //Add field html
							
						},error:function(data){
							
							console.log(data);
						}
						
					})	
					y++;
				}
			});

			//Once remove button is clicked
			$(document).on('click', '.remVlabels', function(e){
				e.preventDefault();
				var id =$(this).attr("lid");

				$(this).parent('div').remove(); //Remove field html
				$('.'+id).remove();
				x--; 
			});
		});

		
// on change of condition for 1st value

		$(document).on("change",".onchangevrulesCondition",function(){
			
			var cond = $(this).val();
			var rRef = $(this).attr("rCount");
			var selection = $(".valLabels").val();
			
			var date = getDate();
			
			if(cond == "is blank" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time"){
				
				$(".vrulesConditionValue").hide();
				
			}else if(cond == "is during the current"){
				
				$(".vrulesConditionValue").html('<select name="cond_value'+rRef+'[]" class="form-control"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$(".vrulesConditionValue").show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				
				$(".vrulesConditionValue").html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days'+rRef+'[]" class="form-control">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value'+rRef+'[]" class="form-control"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option><option value="rolling years">rolling years</option></select></div></div>');
				
				$(".vrulesConditionValue").show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$(".vrulesConditionValue").html('<input type="date" class="form-control" name="cond_value'+rRef+'[]" value="'+date+'">');
				
				$(".vrulesConditionValue").show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$(".vrulesConditionValue").html('<input type="date" class="form-control" name="cond_value'+rRef+'[]" value="'+date+'">');
				
				}
				$(".vrulesConditionValue").show();
				
			}else{
				
				$(".vrulesConditionValue").show();
				
			}
			
		})
		
		$(document).on("change",".voperatorCh",function(){
			
			var cond = $(this).val();
			var bind = $(this).attr("opid");
			var selection = $(".lgetColumn").val();
			var rRef = $(this).attr("rCount");

			var date = getDate();
			
			if(cond == "is blank" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time"){
				
				$("."+bind).hide();
				
			}else if(cond == "is during the current"){
				
				$("."+bind).html('<select name="cond_value'+rRef+'[]" class="form-control"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$("."+bind).show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				$("."+bind).html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days'+rRef+'[]" class="form-control">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value'+rRef+'[]" class="form-control"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option><option value="rolling years">rolling years</option></select></div></div>');
				
				$("."+bind).show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$("."+bind).html('<input type="date" class="form-control" name="cond_value'+rRef+'[]" value="'+date+'">');
				$("."+bind).show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$("."+bind).html('<input type="date" class="form-control" name="cond_value'+rRef+'[]" value="'+date+'">');
				
				}
				$("."+bind).show();
				
			}else{
				
				$("."+bind).show();
				
			}
			
		})


// when condition script ends


// add rule script starts
		
		
	$(document).ready(function(){
		
		var maxField = 50; //Input fields increment limitation
		var addButton = $('.addRule'); //Add button selector
		var wrapper = $('.addedrules'); //Input field wrapper

		var x = 1; //Initial field counter is 1
		var y = 1;

		//Once add button is clicked
		$(addButton).on("click",function(){
			//Check maximum number of input fields
//			if(x < maxField){

				var base_url = $("#base_url").val();
//				x++;

				var key = randomString(10);

				$.ajax({

					type : "post",
					data : {table:"tbl_locations"},
					dataType : 'json',
					url : base_url+"admin/tasks/getColumns",
					success : function(data){

						$(wrapper).append('<div class="row delSelRule'+key+'" style="background-color: #ccc;padding: 12px;margin: 5px;border-radius: 5px;"><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 16px;padding:0px;"><label>When</label></div><div class="col-md-10" style="background-color: #eee;"><div class="row" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column'+key+'[]" class="form-control changeLabelArule" rid="refvalLabels'+key+'" uopid="getopAruleval'+key+'"  rCount="'+key+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group oprefvalLabels'+key+'"><select name="condition'+key+'[]"  rCount="'+key+'" class="form-control changeOperatorArule" opid="getopAruleval'+key+'">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group refvalLabels'+key+' getopAruleval'+key+' vrulesConditionValue"><input type="text" name="cond_value'+key+'[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-plus-circle addWhencondArule" crid="addedWhencondArule'+key+'" rCount="'+key+'" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div><div class="addedWhencondArule'+key+'"></div></div><div class="col-md-1" align="right"><i class="fa fa-times-circle deleteRule" delRule="delSelRule'+key+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 15px;padding:0px;"><label>Message</label></div><div class="col-md-10" style="background-color: #eee;"><textarea rows="6" cols="10" class="form-control" name="alertMessage'+key+'[]" style="margin:10px"></textarea><input type="hidden" name="rulesCount[]" value="'+key+'"></div></div>'); 

					}

				})
				 //Increment field counter

//				y++;

				$('.deleteallRules').addClass('deleteRule').removeClass('deleteallRules');

//			}
		});

		//Once remove button is clicked
		$(document).on('click', '.deleteRule', function(e){
			e.preventDefault();
			var id =$(this).attr("delRule");

			$(this).parent('div').remove(); //Remove field html
			$('.'+id).remove();
			x--;

			var rulescount = document.getElementsByName('rulesCount[]');

			if(rulescount.length == 1){

				$('.deleteRule').addClass('deleteallRules').removeClass('deleteRule');

			}else{

				$('.deleteallRules').addClass('deleteRule').removeClass('deleteallRules');

			}

		});

		$(document).on('click','.deleteallRules', function(e){

			e.preventDefault();
			var id =$(this).attr("delRule");
			var base_url = $("#base_url").val();

			$(this).parent('div').remove(); //Remove field html
			$('.'+id).remove();

			$(".addRule").hide();
			$(".ufields").hide();
			$(".validationruleclosed").hide();
			$('input[name="validationrule"]')[0].checked = false;


			$.ajax({

				type : "post",
				data : {table:"tbl_locations"},
				dataType : 'json',
				url : base_url+"admin/tasks/getColumns",
				success : function(data){

//					console.log(data);

					$('.validationruleclosed').append('<div class="row delSelRule" style="background-color: #ccc;padding: 12px;margin: 5px;border-radius: 5px;"><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 16px;padding:0px;"><label>When</label></div><div class="col-md-10" style="background-color: #eee;"><div class="row" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column1[]" class="form-control valLabels" rid="refvalLabels" rCount="1">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group oprefvalLabels"><select name="condition1[]" class="form-control onchangevrulesCondition" rCount="1">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group refvalLabels vrulesConditionValue"><input type="text" name="cond_value1[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-plus-circle addVlabels" rCount="1" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div><div class="addedLabels"></div></div><div class="col-md-1" align="right"><i class="fa fa-times-circle deleteallRules" delRule="delSelRule" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 15px;padding:0px;"><label>Message</label></div><div class="col-md-10" style="background-color: #eee;"><textarea rows="6" cols="10" class="form-control" name="alertMessage1[]" style="margin:10px"></textarea><input type="hidden" name="rulesCount[]" value="1"></div></div>'); 


				},
				error : function(data){

					console.log(data);

				}

			})
		});
	});

	$(document).on("change",".changeLabelArule",function(){
			
		var base_url = $("#base_url").val();
		var column = $(this).val();
		var ref = $(this).attr("rid");
		var uopid = $(this).attr("uopid");
		var rCount = $(this).attr("rCount");

		$("."+ref).show();

		$.ajax({

			type : "post",
			url : base_url+"admin/tasks/getDatatypeconditions",
			dataType : 'json',
			data : {column : column,table:"tbl_locations","onchangeColref":"changeOperatorArule","uopid":uopid,"rCount":rCount},
			success : function(data){

				$(".op"+ref).html(data.operators);

				if(data.fields.locnames != null){

					$("."+ref).html(data.fields.locnames);
					$(".select2").select2();

				}else if(data.fields.status != null){

					$("."+ref).html(data.fields.status);

				}else if(data.fields.location_type != null){

					$("."+ref).html(data.fields.location_type);

				}else if(data.fields.import_date != null){

					$("."+ref).html(data.fields.import_date);

				}else if(data.fields.accounts != null){

					$("."+ref).html(data.fields.accounts);
					$(".select2").select2();

				}else{

					$("."+ref).html('<input type="text" name="cond_value'+rCount+'[]" class="form-control">');

				}


//					console.log(data);

			},
			error : function(data){

//					console.log(data);

			}

		});

	});

	$(document).on("change",".changeOperatorArule",function(){
			
		var cond = $(this).val();
		var bind = $(this).attr("opid");
		var rRef = $(this).attr("rCount");
		var selection = $(".lgetColumn").val();

		var date = getDate();

		if(cond == "is blank" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time"){

			$("."+bind).hide();

		}else if(cond == "is during the current"){

			$("."+bind).html('<select name="cond_value'+rRef+'[]" class="form-control"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');

			$("."+bind).show();

		}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){

			var i;
			var end = 31;
			var days = "";
			for (i = 1; i <= end; i++) { 
			  days += '<option value="'+i+'">'+i+'</option>';
			}

			$("."+bind).html('<div class="row"><div class="col-md-5" style="padding:0px"><select name="cond_days'+rRef+'[]" class="form-control">'+days+'</select></div><div class="col-md-7" style="padding:0px"><select name="cond_value'+rRef+'[]" class="form-control"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option><option value="rolling years">rolling years</option></select></div></div>');

			$("."+bind).show();

		}else if(cond == "is before" || cond == "is after"){

			$("."+bind).html('<input type="date" class="form-control" name="cond_value'+rRef+'[]" value="'+date+'">');
			$("."+bind).show();

		}else if(cond == "is" || cond == "is not"){

			var select = selection.split("-");

			if(select[1] == "date"){

				$("."+bind).html('<input type="date" class="form-control" name="cond_value'+rRef+'[]" value="'+date+'">');

			}
			$("."+bind).show();

		}else{

			$("."+bind).show();

		}

	})

	$(document).on("click",".addWhencondArule",function(){
		//Check maximum number of input fields

			var base_url = $("#base_url").val();
			var appenddiv = $(this).attr("crid");
			var rCount = $(this).attr("rCount");
			var key = randomString(10);

			$.ajax({

				type : "post",
				data : {table:"tbl_locations"},
				dataType : 'json',
				url : base_url+"admin/tasks/getColumns",
				success : function(data){
					$('.'+appenddiv).append('<div class="row removeLabel'+key+'" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column'+rCount+'[]" class="form-control uvalLabels" rid="urefvalLabels'+key+'" rCount="'+rCount+'" uopid="updateopVal'+key+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group uopurefvalLabels'+key+'"><select name="condition'+rCount+'[]" class="form-control voperatorCh" rCount="'+rCount+'" opid="updateopVal'+key+'">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group urefvalLabels'+key+' updateopVal'+key+'"><input type="text" name="cond_value'+rCount+'[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-times-circle remVlabels" lid="removeLabel'+key+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;<i class="fa fa-plus-circle addWhencondArule" rCount="'+rCount+'" crid="'+appenddiv+'" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>'); //Add field html

				}

			})	
		
	});

// add rule script ends		
		

// create task

	$(document).on("submit","#cvalidationRules",function(e){
		
		e.preventDefault();
		
		var fdata = $(this).serialize();
		var base_url = $("#base_url").val();
		
		$.ajax({
			
			type : "post",
			url : base_url+"admin/fields/createFields",
			data : fdata,
//			dataType : 'json',
			beforeSend : function(){
				
				$(".vstloader").show();
				
			},
			success : function(data){
				
				$(".vstloader").hide();
				
				console.log(data);
				if(data == "success"){
				
					$(".vsterror").html('<div class="alert alert-success">Successfully Updated</div>');
					setTimeout(function(){location.reload();},3000);
					
				}else{
					
					$(".vsterror").html('<div class="alert alert-success">Error Occured</div>');
					
				}
				
				
			},
			error : function(data){
				
				$(".vstloader").hide();
				console.log(data);
				
			}
			
		});
		
	})
	$(document).on("change",".updateonchangeConditionUpdated",function(){
		
			var cond = $(this).val();
			var bind = $(this).attr("opid");
			var rcount = $(this).attr("rcount");
			var selection = $("#"+bind).val();
			//alert(cond);
			
			var date = getDate();
			
			if(cond == "is blank" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time"){
				
				$("."+bind).hide();
				
			}else if(cond == "is during the current"){
				
				$("."+bind).html('<select name="cond_value'+rcount+'[]" class="form-control"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$("."+bind).show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				
				$("."+bind).html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days'+rcount+'[]" class="form-control">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value'+rcount+'[]" class="form-control"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option><option value="rolling years">rolling years</option></select></div></div>');
				
				$("."+bind).show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$("."+bind).html('<input type="date" class="form-control" name="cond_value'+rcount+'[]" value="'+date+'">');
				
				$("."+bind).show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$("."+bind).html('<input type="date" class="form-control" name="cond_value'+rcount+'[]" value="'+date+'">');
				
				}
				$("."+bind).show();
				
			}else{
				//alert("else");
				$(".vrulesConditionValue").show();
				
			}
			
		})


// locations script ends
	

