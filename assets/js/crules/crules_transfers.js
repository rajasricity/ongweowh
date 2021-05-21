// JavaScript Document
function randomString(length) {
	var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];
    return result;
}

$(".cmodal").click(function(){

	$("#cmodal").modal("show");
	$(".updcvalidationrules").show();
	$("#confieldname").val($(this).attr("fname"));
	$(".column_name").html($(this).attr("colname"));
	$(".conditionruleclosed").html('');
	
	var vrid = $(this).attr("crid");
	var base_url = $("#base_url").val();
	var field = $("#confieldname").val();
	
	if(vrid != ""){
		var appId = $("#cAppid").val();
		var table = $("#cTable").val();

		$.ajax({

			type : 'post',
			url : base_url+'admin/Conditions/editField',
			data : {vrid:vrid},
			dataType : 'json',
			success : function(data){
								
//				console.log(data)
				if(data.status == "on"){
					
					$('.conditionruleclosed').html(data.cond_data);
					$(".conditionruleclosed").show();
					$('input[name="conditionalrule"]')[0].checked = true;
					$("#conid").val(vrid);
					$(".addCRule").show();
					$(".ufields").show();
					
				}
				
				$(".select2").select2();
				
			},
			error : function(data){
				
//				console.log(data);
				
			}

		})
		
	}else{
		
		$(".conditionruleclosed").hide();
		$(".addCRule").hide();
		$(".addedcrules").html('');
		$('input[name="conditionalrule"]')[0].checked = false;
		
			$.ajax({

				type : "post",
				data : {table:"tbl_touts",column:field,ref:1,condref:"conditions"},
				dataType : 'json',
				url : base_url+"admin/tasks/getColumns",
				success : function(data){

//					console.log(data);

					$('.conditionruleclosed').html('<div class="row delSelCondRule" style="background-color: #ccc; padding: 12px;margin: 5px;border-radius: 5px;"><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 16px;padding:0px;"><label>When</label></div><div class="col-md-10" style="background-color: #eee;"><div class="row" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column1[]" class="form-control valConLabels" rid="refconvalLabels" id="updateCOperatorId1" uopid="updateCOperatorId1" rCount="1">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group oprefconvalLabels"><select name="condition1[]" class="form-control onchangecrulesCondition" rCount="1" opid="updateCOperatorId1">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group refconvalLabels crulesConditionValue"><input type="text" name="cond_value1[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-plus-circle addClabels" rCount="1" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div><div class="addedConLabels"></div></div><div class="col-md-1" align="right"><i class="fa fa-times-circle deleteallCRules" delRule="delSelCondRule" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 15px;padding:0px;"><label>Values</label></div><div class="col-md-10" style="background-color: #eee;"><div class="row"><div class="col-md-1" style="margin-top: 7px;font-size: 16px;font-weight: 400;">Set</div><div class="col-md-3"><div class="form-group"><select name="ssetcondition1[]" class="form-control getConditionalLabels" uid="getconConditionalst" rcount="1"><option value="to a custom value">To a custom value</option><option value="to a field value">To a record value</option></select></div></div><div class="col-md-3"><div class="form-group getconConditionalst getconSetfield">'+data.csetvalue+'</div></div><div class="col-md-2" align="right"></div></div><div class="addedConsetLabels"></div><input type="hidden" name="rulesCCount[]" value="1"></div></div></div>'); 

					$(".select2").select2();

				}
			});
		
	}
	

});

$(document).on("click",'#condRulecheck',function(){

	
	if($(this). prop("checked") == true){
		
		$(".conditionruleclosed").show();
		$(".addedcrules").show();
		$(".addCRule").show();
//		$(".ufields").show();
//		$(".updvalidationrules").hide();
		
	}else{
		
		$(".conditionruleclosed").hide();
		$(".addedcrules").hide();
		$(".addCRule").hide();
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

		$(document).on("change",".valConLabels",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("rid");
			var uopid = $(this).attr("uopid");
			var rCount = $(this).attr("rCount");
			
			$(".crulesConditionValue").show();
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/Conditions/getDatatypeconditions",
				dataType : 'json',
				data : {column : column,table:"tbl_touts","onchangeColref":"onchangecrulesCondition","uopid":uopid,"rCount":rCount},
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

		$(document).on("change",".ucvalLabels",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("rid");
			var uopid = $(this).attr("uopid");
			var rCount = $(this).attr("rCount");
			
			$("."+ref).show();
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/Conditions/getDatatypeconditions",
				dataType : 'json',
				data : {column : column,table:"tbl_touts","onchangeColref":"vcoperatorCh","uopid":uopid,"rCount":rCount},
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
			var addButton = $('.addClabels'); //Add button selector
			var wrapper = $('.addedLabels'); //Input field wrapper

			var x = 1; //Initial field counter is 1
			var y = 1;

			//Once add button is clicked
			$(document).on("click",'.addClabels',function(){
				//Check maximum number of input fields
				if(x < maxField){
					
					var rRef = $(this).attr("rCount");
					var base_url = $("#base_url").val();
					x++;
					
					$.ajax({
						
						type : "post",
						data : {table:"tbl_touts",condref:"conditions"},
						dataType : 'json',
						url : base_url+"admin/tasks/getColumns",
						success : function(data){
//							console.log(data)
							
							$('.addedConLabels').append('<div class="row removecLabel'+x+'" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column'+rRef+'[]" class="form-control ucvalLabels" rid="ucrefvalLabels'+x+'" id="updatecopVal'+x+'" rCount="'+rRef+'" uopid="updatecopVal'+x+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group uopucrefvalLabels'+x+'"><select name="condition'+rRef+'[]" class="form-control vcoperatorCh"  rCount="'+rRef+'" opid="updatecopVal'+x+'">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group ucrefvalLabels'+x+' updatecopVal'+x+'"><input type="text" name="cond_value'+rRef+'[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-times-circle remClabels" lid="removecLabel'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;<i class="fa fa-plus-circle adddomClabels"  rCount="'+rRef+'" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>'); 
							
						}
						
					})
					 //Increment field counter
					
					y++;
				}
			});

			$(document).on("click",".adddomClabels",function(){
				//Check maximum number of input fields
				if(x < maxField){ 
					
					var base_url = $("#base_url").val();
					var rRef = $(this).attr("rCount");
					
					x++;
					
					$.ajax({
						
						type : "post",
						data : {table:"tbl_touts",condref:"conditions"},
						dataType : 'json',
						url : base_url+"admin/tasks/getColumns",
						success : function(data){
							console.log(data);
							$('.addedConLabels').append('<div class="row removecLabel'+x+'" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column'+rRef+'[]" class="form-control ucvalLabels" rid="ucrefvalLabels'+x+'" id="updatecopVal'+x+'"  rCount="'+rRef+'" uopid="updatecopVal'+x+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group uopucrefvalLabels'+x+'"><select name="condition'+rRef+'[]"  rCount="'+rRef+'" class="form-control vcoperatorCh" opid="updatecopVal'+x+'">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group ucrefvalLabels'+x+' updatecopVal'+x+'"><input type="text" name="cond_value'+rRef+'[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-times-circle remClabels" lid="removecLabel'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;<i class="fa fa-plus-circle adddomClabels" rCount="'+rRef+'" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>'); //Add field html
							
						},error:function(data){
							
							console.log(data);
						}
						
					})	
					y++;
				}
			});

			//Once remove button is clicked
			$(document).on('click', '.remClabels', function(e){
				e.preventDefault();
				var id =$(this).attr("lid");

				$(this).parent('div').remove(); //Remove field html
				$('.'+id).remove();
				x--; 
			});
		});

		
// on change of condition for 1st value

		$(document).on("change",".onchangecrulesCondition",function(){
			
			var cond = $(this).val();
			var rRef = $(this).attr("rCount");
			var opid = $(this).attr("opid");
			var selection = $("#"+opid).val();
			
			var date = getDate();
			
			if(cond == "is blank" || cond == "is any" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time"){
				
				$(".crulesConditionValue").hide();
				
			}else if(cond == "is during the current"){
				
				$(".crulesConditionValue").html('<select name="cond_value'+rRef+'[]" class="form-control"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$(".crulesConditionValue").show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				
				$(".crulesConditionValue").html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days'+rRef+'[]" class="form-control">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value'+rRef+'[]" class="form-control"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option><option value="rolling years">rolling years</option></select></div></div>');
				
				$(".crulesConditionValue").show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$(".crulesConditionValue").html('<input type="date" class="form-control" name="cond_value'+rRef+'[]" value="'+date+'">');
				
				$(".crulesConditionValue").show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$(".crulesConditionValue").html('<input type="date" class="form-control" name="cond_value'+rRef+'[]" value="'+date+'">');
				
				}
				$(".crulesConditionValue").show();
				
			}else{
				
				$(".crulesConditionValue").show();
				
			}
			
		})
		
		$(document).on("change",".vcoperatorCh",function(){
			
			var cond = $(this).val();
			var bind = $(this).attr("opid");
			var selection = $("#"+bind).val();
			var rRef = $(this).attr("rCount");

			var date = getDate();
			
			if(cond == "is blank" || cond == "is any" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time"){
				
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
		var addButton = $('.addCRule'); //Add button selector
		var wrapper = $('.addedcrules'); //Input field wrapper

		var x = 1; //Initial field counter is 1
		var y = 1;

		//Once add button is clicked
		$(addButton).on("click",function(){
			//Check maximum number of input fields
//			if(x < maxField){

				var base_url = $("#base_url").val();
//				x++;
				var field = $("#confieldname").val();
				var key = randomString(10);

				$.ajax({

					type : "post",
					data : {table:"tbl_touts",column:field,ref:key,condref:"conditions"},
					dataType : 'json',
					url : base_url+"admin/tasks/getColumns",
					success : function(data){

						$(wrapper).append('<div class="row delCSelRule'+key+'" style="background-color: #ccc;padding: 12px;margin: 5px;border-radius: 5px;"><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 16px;padding:0px;"><label>When</label></div><div class="col-md-10" style="background-color: #eee;"><div class="row" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column'+key+'[]" class="form-control changeCLabelArule" rid="refCvalLabels'+key+'" id="getopCruleval'+key+'" uopid="getopCruleval'+key+'"  rCount="'+key+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group oprefCvalLabels'+key+'"><select name="condition'+key+'[]"  rCount="'+key+'" class="form-control changeOperatorCrule" opid="getopCruleval'+key+'">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group refCvalLabels'+key+' getopCruleval'+key+' crulesConditionValue"><input type="text" name="cond_value'+key+'[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-plus-circle addWhencondCrule" crid="addedWhencondCrule'+key+'" rCount="'+key+'" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div><div class="addedWhencondCrule'+key+'"></div></div><div class="col-md-1" align="right"><i class="fa fa-times-circle deleteCRule" delRule="delCSelRule'+key+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 15px;padding:0px;"><label>Values</label></div><div class="col-md-10" style="background-color: #eee;"><div class="row"><div class="col-md-1" style="margin-top: 7px;font-size: 16px;font-weight: 400;">Set</div><div class="col-md-3"><div class="form-group"><select name="ssetcondition'+key+'[]" class="form-control getConditionalLabels" uid="getCconConditionalst'+key+'" rcount="'+key+'"><option value="to a custom value">To a custom value</option><option value="to a field value">To a record value</option></select></div></div><div class="col-md-3"><div class="form-group getCconConditionalst'+key+'">'+data.csetvalue+'</div></div><div class="col-md-2" align="right"></div></div><input type="hidden" name="rulesCCount[]" value="'+key+'"></div></div>'); 

						$(".select2").select2();
					}

				})
				 //Increment field counter

//				y++;

				$('.deleteallCRules').addClass('deleteCRule').removeClass('deleteallCRules');

//			}
		});

		//Once remove button is clicked
		$(document).on('click', '.deleteCRule', function(e){
			e.preventDefault();
			var id =$(this).attr("delRule");

			$(this).parent('div').remove(); //Remove field html
			$('.'+id).remove();
			x--;

			var rulescount = document.getElementsByName('rulesCCount[]');
			
			if(rulescount.length <= 2){

				$('.deleteCRule').addClass('deleteallCRules').removeClass('deleteCRule');

			}else{

				$('.deleteallCRules').addClass('deleteCRule').removeClass('deleteallCRules');

			}

		});

		$(document).on('click','.deleteallCRules', function(e){

			e.preventDefault();
			var id =$(this).attr("delRule");
			var base_url = $("#base_url").val();

			$(this).parent('div').remove(); //Remove field html
			$('.'+id).remove();

			$(".addCRule").hide();
//			$(".ufields").hide();
			$(".conditionruleclosed").hide();
			$(".addedcrules").hide();
			$('input[name="conditionalrule"]')[0].checked = false;
			var field = $("#confieldname").val();

			$.ajax({

				type : "post",
				data : {table:"tbl_touts",column:field,ref:1,condref:"conditions"},
				dataType : 'json',
				url : base_url+"admin/tasks/getColumns",
				success : function(data){

//					console.log(data);

					$('.conditionruleclosed').append('<div class="row delSelCondRule" style="background-color: #ccc; padding: 12px;margin: 5px;border-radius: 5px;"><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 16px;padding:0px;"><label>When</label></div><div class="col-md-10" style="background-color: #eee;"><div class="row" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column1[]" class="form-control valConLabels" rid="refconvalLabels" id="updateCOperatorId1" rCount="1">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group oprefconvalLabels"><select name="condition1[]" class="form-control onchangecrulesCondition" rCount="1" opid="updateCOperatorId1">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group refconvalLabels crulesConditionValue"><input type="text" name="cond_value1[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-plus-circle addClabels" rCount="1" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div><div class="addedConLabels"></div></div><div class="col-md-1" align="right"><i class="fa fa-times-circle deleteallCRules" delRule="delSelCondRule" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 15px;padding:0px;"><label>Values</label></div><div class="col-md-10" style="background-color: #eee;"><div class="row"><div class="col-md-1" style="margin-top: 7px;font-size: 16px;font-weight: 400;">Set</div><div class="col-md-3"><div class="form-group"><select name="ssetcondition1[]" class="form-control getConditionalLabels" uid="getconConditionalst" rcount="1"><option value="to a custom value">To a custom value</option><option value="to a field value">To a record value</option></select></div></div><div class="col-md-3"><div class="form-group getconConditionalst getconSetfield">'+data.csetvalue+'</div></div><div class="col-md-2" align="right"></div></div><div class="addedConsetLabels"></div><input type="hidden" name="rulesCCount[]" value="1"></div></div></div>'); 

					$(".select2").select2();
					
				},
				error : function(data){

					console.log(data);

				}

			})
		});
	});

	$(document).on("change",".changeCLabelArule",function(){
			
		var base_url = $("#base_url").val();
		var column = $(this).val();
		var ref = $(this).attr("rid");
		var uopid = $(this).attr("uopid");
		var rCount = $(this).attr("rCount");

		$("."+ref).show();

		$.ajax({

			type : "post",
			url : base_url+"admin/Conditions/getDatatypeconditions",
			dataType : 'json',
			data : {column : column,table:"tbl_touts","onchangeColref":"changeOperatorCrule","uopid":uopid,"rCount":rCount},
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

	$(document).on("change",".changeOperatorCrule",function(){
			
		var cond = $(this).val();
		var bind = $(this).attr("opid");
		var rRef = $(this).attr("rCount");
		var selection = $("#"+bind).val();

		var date = getDate();

		if(cond == "is blank" || cond == "is any" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time"){

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

	$(document).on("click",".addWhencondCrule",function(){
		//Check maximum number of input fields

			var base_url = $("#base_url").val();
			var appenddiv = $(this).attr("crid");
			var rCount = $(this).attr("rCount");
			var key = randomString(10);

			$.ajax({

				type : "post",
				data : {table:"tbl_touts",condref:"conditions"},
				dataType : 'json',
				url : base_url+"admin/tasks/getColumns",
				success : function(data){
					$('.'+appenddiv).append('<div class="row removeLabel'+key+'" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column'+rCount+'[]" class="form-control ucvalLabels" rid="urefvalLabels'+key+'" rCount="'+rCount+'" id="updateopVal'+key+'" uopid="updateopVal'+key+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group uopurefvalLabels'+key+'"><select name="condition'+rCount+'[]" class="form-control voperatorCh" rCount="'+rCount+'" opid="updateopVal'+key+'">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group urefvalLabels'+key+' updateopVal'+key+'"><input type="text" name="cond_value'+rCount+'[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-times-circle remClabels" lid="removeLabel'+key+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;<i class="fa fa-plus-circle addWhencondCrule" rCount="'+rCount+'" crid="'+appenddiv+'" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>'); //Add field html

				}

			})	
		
	});

// add rule script ends		
		

// create task

	$(document).on("submit","#cconditionalRules",function(e){
		
		e.preventDefault();
		
		var fdata = $(this).serialize();
		var base_url = $("#base_url").val();
		
		$.ajax({
			
			type : "post",
			url : base_url+"admin/Conditions/createFields",
			data : fdata,
//			dataType : 'json',
			beforeSend : function(){
				
				$(".cstloader").show();
				
			},
			success : function(data){
				
				$(".cstloader").hide();
				
				console.log(data);
				if(data == "success"){
				
					$(".csterror").html('<div class="alert alert-success">Successfully Updated</div>');
					setTimeout(function(){location.reload();},3000);
					
				}else{
					
					$(".csterror").html('<div class="alert alert-success">Error Occured</div>');
					
				}
				
				
			},
			error : function(data){
				
				$(".cstloader").hide();
				console.log(data);
				
			}
			
		});
		
	})

	$(document).on("change",".getConditionalLabels",function(){
		
		var val = $(this).val();
		var ref = $(this).attr("uid");
		var rcount = $(this).attr("rcount");
		var base_url = $("#base_url").val();
		
		if(val == "to a field value"){
			
			$.ajax({

				type : "post",
				url : base_url+"admin/conditions/getColumns",
				data : {table:"tbl_touts","rcount":rcount},
	//			dataType : 'json',
				success : function(data){

					$("."+ref).html(data);
					
				},
				error : function(data){

					console.log(data);

				}

			});
			
		}else{
			
			var field = $("#confieldname").val();
	
			$.ajax({

				type : "post",
				data : {table:"tbl_touts",column:field,ref:rcount,condref:"conditions"},
				dataType : 'json',
				url : base_url+"admin/tasks/getColumns",
				success : function(data){

					console.log(data);

					$('.'+ref).html(data.csetvalue); 

					$(".select2").select2();

				},
				error : function(data){
					
					console.log(data);
					
				}
			});
			
		}
		
	})

// locations script ends
	

