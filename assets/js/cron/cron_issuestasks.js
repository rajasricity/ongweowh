// JavaScript Document

$(document).ready(function () {
    //Initialize tooltips
    $('.nav-tabs > li a[title]').tooltip();
    
    //Wizard
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        var $target = $(e.target);
        if ($target.parent().hasClass('disabled')) {
            return false;
        }
    });

    $(".next-step").click(function (e) {
		
		var task_name = $("#task_name").val();
		
		if(task_name == ""){
			
			Swal(
			  'Error!',
			  'Please Enter Task Name.',
			  'error'
			)
			
			return false;

		}
		
		
        var $active = $('.nav-tabs1 li>.active');
        $active.parent().next().find('.nav-link1').removeClass('disabled');
        nextTab($active);
    });
    
    $(".prev-step").click(function (e) {
        var $active = $('.nav-tabs li>a.active');
        prevTab($active);
    });
});

function nextTab(elem) {
    $(elem).parent().next().find('a[data-toggle="tab"]').click();
}

function prevTab(elem) {
    $(elem).parent().prev().find('a[data-toggle="tab"]').click();
}

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

		$(".lwhenCondition").click(function(){
			
			$(".lwhenOpen").hide();
			$(".lwhenClose").show();
			
		});

		$(".lremoveWhencondition").click(function(){
			
			$(".lwhenOpen").show();
			$(".lwhenClose").hide();
			
		});

		$(".lgetColumn").on("change",function(){
			
			$(".onchangeConditionValue").show();
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("rid");
			var uopid = $(this).attr("uopid");
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/tasks/getDatatypeconditions",
				dataType : 'json',
				data : {column : column,table:"tbl_issues","onchangeColref":"onchangeCondition","uopid":uopid},
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
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
		});

		$(document).on("change",".ulgetColumn",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("rid");
			var uopid = $(this).attr("uopid");
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/tasks/getDatatypeconditions",
				dataType : 'json',
				data : {column : column,table:"tbl_issues","onchangeColref":"uoperatorchange","uopid":uopid},
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
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
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
			var addButton = $('.addTaskbind'); //Add button selector
			var wrapper = $('.addtask_wrapper'); //Input field wrapper

			var x = 1; //Initial field counter is 1
			var y = 1;

			//Once add button is clicked
			$(addButton).on("click",function(){
				//Check maximum number of input fields
				if(x < maxField){
					
					var base_url = $("#base_url").val();
					x++;
					
					$.ajax({
						
						type : "post",
						data : {table:"tbl_issues"},
						dataType : 'json',
						url : base_url+"admin/tasks/getColumns",
						success : function(data){
							
							$(wrapper).append('<div class="row sub_column'+x+'"><div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px"><label>When</label></div><div class="col-md-10"><div class="row"><div class="col-md-4"><div class="form-group"><select name="cond_column[]" class="form-control ulgetColumn" rid="getwhenRef'+x+'" uopid="uonchangeCondition'+x+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group opgetwhenRef'+x+'"><select name="condition[]" class="form-control uoperatorchange"  opid="uonchangeCondition'+x+'">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group getwhenRef'+x+' uonchangeCondition'+x+'"><input type="text" name="cond_value[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-plus-circle addsTaskbind" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-times-circle tlremove_button" lid="sub_column'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div></div></div>'); 
							
						}
						
					})
					 //Increment field counter
					
					y++;
				}
			});

			$(wrapper).on("click",".addsTaskbind",function(){
				//Check maximum number of input fields
				if(x < maxField){ 
					
					var base_url = $("#base_url").val();
					x++;
					
					$.ajax({
						
						type : "post",
						data : {table:"tbl_issues"},
						dataType : 'json',
						url : base_url+"admin/tasks/getColumns",
						success : function(data){
							$(wrapper).append('<div class="row sub_column'+x+'"><div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px"><label>When</label></div><div class="col-md-10"><div class="row"><div class="col-md-4"><div class="form-group"><select name="cond_column[]" class="form-control ulgetColumn" rid="getwhenRef'+x+'" uopid="uonchangeCondition'+x+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group opgetwhenRef'+x+'"><select name="condition[]" class="form-control uoperatorchange" opid="uonchangeCondition'+x+'">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group getwhenRef'+x+' uonchangeCondition'+x+'"><input type="text" name="cond_value[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-plus-circle addsTaskbind" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-times-circle tlremove_button" lid="sub_column'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div></div></div>'); //Add field html
							
						}
						
					})	
					y++;
				}
			});

			//Once remove button is clicked
			$(wrapper).on('click', '.tlremove_button', function(e){
				e.preventDefault();
				var id =$(this).attr("lid");

				$(this).parent('div').remove(); //Remove field html
				$('.'+id).remove();
				x--; 
			});
		});

		
// on change of condition for 1st value

		$(document).on("change",".onchangeCondition",function(){
			
			var cond = $(this).val();
			var selection = $(".lgetColumn").val();
			
			var date = getDate();
			
			if(cond == "is blank" || cond == "is any" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time"){
				
				$(".onchangeConditionValue").hide();
				
			}else if(cond == "is during the current"){
				
				$(".onchangeConditionValue").html('<select name="cond_value[]" class="form-control"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$(".onchangeConditionValue").show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				
				$(".onchangeConditionValue").html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days[]" class="form-control">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value[]" class="form-control"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option><option value="rolling years">rolling years</option></select></div></div>');
				
				$(".onchangeConditionValue").show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$(".onchangeConditionValue").html('<input type="date" class="form-control" name="cond_value[]" value="'+date+'">');
				
				$(".onchangeConditionValue").show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$(".onchangeConditionValue").html('<input type="date" class="form-control" name="cond_value[]" value="'+date+'">');
				
				}
				$(".onchangeConditionValue").show();
				
			}else{
				
				$(".onchangeConditionValue").show();
				
			}
			
		})
		
		$(document).on("change",".uoperatorchange",function(){
			
			var cond = $(this).val();
			var bind = $(this).attr("opid");
			var selection = $(".lgetColumn").val();
			
			var date = getDate();
			
			if(cond == "is blank" || cond == "is any" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time"){
				
				$("."+bind).hide();
				
			}else if(cond == "is during the current"){
				
				$("."+bind).html('<select name="cond_value[]" class="form-control"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$("."+bind).show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				$("."+bind).html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days[]" class="form-control">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value[]" class="form-control"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option><option value="rolling years">rolling years</option></select></div></div>');
				
				$("."+bind).show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$("."+bind).html('<input type="date" class="form-control" name="cond_value[]" value="'+date+'">');
				$("."+bind).show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$("."+bind).html('<input type="date" class="form-control" name="cond_value[]" value="'+date+'">');
				
				}
				$("."+bind).show();
				
			}else{
				
				$("."+bind).show();
				
			}
			
		})


	// when condition script ends

	// set value script starts

		$(document).ready(function(){
		
			var maxField = 20; //Input fields increment limitation
			var addButton = $('.eaddtask_set'); //Add button selector
			var wrapper = $('.eaddtaskset_wrapper'); //Input field wrapper

			var x1 = 1; //Initial field counter is 1
			var y = 1;

			//Once add button is clicked
			$(document).on("click",".eaddtask_set",function(){
				//Check maximum number of input fields
				if(x1 < maxField){
					
					var base_url = $("#base_url").val();
					x1++;
					
					$.ajax({
						
						type : "post",
						data : {table:"tbl_issues"},
						dataType : 'json',
						url : base_url+"admin/tasks/getColumns",
						success : function(data){
							
							$(wrapper).append('<div class="row esubset_column'+x1+'"><div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px"><label>Values</label></div><div class="col-md-10"><div class="row"><div class="col-md-1" style="margin-top: 7px;font-size: 16px;font-weight: 400;">Set</div><div class="col-md-3"><div class="form-group"><select name="scond_column[]" class="form-control wsetGetcol" id="getCondVal'+x1+'" wuid="getSetfield'+x1+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group"><select name="ssetcondition[]" class="form-control getLocuptcolumns" id="getSetfield'+x1+'" guid="getCondVal'+x1+'" uid="getConditionalst'+x1+'"><option value="to a custom value">To a custom value</option><option value="to a field value">To a field value</option></select></div></div><div class="col-md-3"><div class="form-group getSetfield'+x1+' getConditionalst'+x1+'"><input type="text" name="ssetvalue[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-times-circle etsetremove_button" lid="esubset_column'+x1+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-plus-circle eaddstask_setbind" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div></div></div>'); 
							
						}
						
					})
					
					 //Increment field counter
					
					y++;
				}
			});

			$(document).on("click",".eaddstask_setbind",function(){
				//Check maximum number of input fields
				if(x1 < maxField){ 
					
					
					var base_url = $("#base_url").val();
					x1++;
					
					$.ajax({
						
						type : "post",
						data : {table:"tbl_issues"},
						dataType : 'json',
						url : base_url+"admin/tasks/getColumns",
						success : function(data){
							$(wrapper).append('<div class="row esubset_column'+x1+'"><div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px"><label>Values</label></div><div class="col-md-10"><div class="row"><div class="col-md-1" style="margin-top: 7px;font-size: 16px;font-weight: 400;">Set</div><div class="col-md-3"><div class="form-group"><select name="scond_column[]" class="form-control wsetGetcol" wuid="getSetfield'+x1+'" id="getCondVal'+x1+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group"><select name="ssetcondition[]" class="form-control getLocuptcolumns" guid="getCondVal'+x1+'" uid="getConditionalst'+x1+'" id="getSetfield'+x1+'"><option value="to a custom value">To a custom value</option><option value="to a field value">To a field value</option></select></div></div><div class="col-md-3"><div class="form-group getSetfield'+x1+' getConditionalst'+x1+'"><input type="text" name="ssetvalue[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-times-circle etsetremove_button" lid="esubset_column'+x1+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-plus-circle eaddstask_setbind" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div></div></div>'); //Add field html
						}
					})	
					y++;
				}
			});

			//Once remove button is clicked
			$(document).on('click', '.elremoveSetcondition', function(e){
				e.preventDefault();
				var id =$(this).attr("lid");

				$(this).parent('div').remove(); //Remove field html
				$('.'+id).remove();
			});
			
			//Once remove button is clicked
			$(document).on('click', '.etsetremove_button', function(e){
				e.preventDefault();
				var id =$(this).attr("lid");

				$(this).parent('div').remove(); //Remove field html
				$('.'+id).remove();
//				x1--; 
			});
		});

		$(document).on("change","#scond_val",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("wuid");
			var scon = $(".getLocucolumns").val();
			
			if(scon == "to a field value"){
				
				$.ajax({
				
					type : "post",
					url : base_url+"admin/tasks/getsetvalfields",
					dataType : 'json',
					data : {table:"tbl_issues"},
					success : function(data){

						$("."+ref).html(data.columns);
//						console.log(data);

					},
					error : function(data){

//						console.log(data);

					}

				});
				
			}else{
			
				$.ajax({
				
				type : "post",
				url : base_url+"admin/tasks/getFields",
				dataType : 'json',
				data : {column : column,table:"tbl_issues"},
				success : function(data){
					
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
						
						$("."+ref).html('<input type="text" name="ssetvalue[]" class="form-control">');
						
					}
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
			}
				
		});

		$(document).on("change",".wsetGetcol",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("wuid");
			var scon = $("#"+ref).val();
			
			if(scon == "to a field value"){
			
				$.ajax({
				
					type : "post",
					url : base_url+"admin/tasks/getsetvalfields",
					dataType : 'json',
					data : {table:"tbl_issues"},
					success : function(data){

						$("."+ref).html(data.columns);
//						console.log(data);

					},
					error : function(data){

//						console.log(data);

					}

				});
				
			}else{
				
				$.ajax({
				
				type : "post",
				url : base_url+"admin/tasks/getFields",
				dataType : 'json',
				data : {column : column,table:"tbl_issues"},
				success : function(data){
					
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
						
						$("."+ref).html('<input type="text" name="ssetvalue[]" class="form-control">');
						
					}
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
			}
		});

		$(document).on("change",".getLocucolumns",function(){
			
			var base_url = $("#base_url").val();
			var val = $(this).val();
			var ref = $(this).attr("uid");
			var column = $("#scond_val").val();
			
			if(val == "to a field value"){
				
				$.ajax({
				
					type : "post",
					url : base_url+"admin/tasks/getsetvalfields",
					dataType : 'json',
					data : {table:"tbl_issues"},
					success : function(data){

						$("."+ref).html(data.columns);
//						console.log(data);

					},
					error : function(data){

//						console.log(data);

					}

				});
				
			}else{
				
				$.ajax({
				
					type : "post",
					url : base_url+"admin/tasks/getFields",
					dataType : 'json',
					data : {column : column,table:"tbl_issues"},
					success : function(data){

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

							$("."+ref).html('<input type="text" name="ssetvalue[]" class="form-control">');

						}
//						console.log(data);

					},
					error : function(data){

//						console.log(data);

					}

				});
				
			}
			
		});

		$(document).on("change",".getLocuptcolumns",function(){
				
			var base_url = $("#base_url").val();
			var val = $(this).val();
			var ref = $(this).attr("uid");
			var uref = $(this).attr("guid");
			
			var column = $("#"+uref).val();
			
			if(val == "to a field value"){
				
				$.ajax({
				
					type : "post",
					url : base_url+"admin/tasks/getsetvalfields",
					dataType : 'json',
					data : {table:"tbl_issues"},
					success : function(data){

						$("."+ref).html(data.columns);
//						console.log(data);

					},
					error : function(data){

//						console.log(data);

					}

				});
				
			}else{
				
				$.ajax({
				
					type : "post",
					url : base_url+"admin/tasks/getFields",
					dataType : 'json',
					data : {column : column,table:"tbl_issues"},
					success : function(data){

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

							$("."+ref).html('<input type="text" name="ssetvalue[]" class="form-control">');

						}
//						console.log(data);

					},
					error : function(data){

//						console.log(data);

					}

				});
				
			}
			
		});

	// set value script ends


// create task

	$("#cTask").submit(function(e){
		
		e.preventDefault();
		
		var fdata = $(this).serialize();
		var base_url = $("#base_url").val();
		
		$.ajax({
			
			type : "post",
			url : base_url+"admin/tasks/createTask",
			data : fdata,
			beforeSend : function(){
				
				$(".stloader").show();
				
			},
			success : function(data){
				
				$(".stloader").hide();
				
//				console.log(data);
				if(data == "success"){
				
					$(".sterror").html('<div class="alert alert-success">Task Successfully Added</div>');
					setTimeout(function(){location.reload();},3000);
					
				}else{
					
					$(".sterror").html('<div class="alert alert-success">Error Occured</div>');
					
				}
				
				
			},
			error : function(data){
				
				$(".stloader").hide();
//				console.log(data);
				
			}
			
		});
		
	})


// locations script ends
	
	
//    ***************************   Task Update script  ************************************ //
	
	
	
	$(document).on("click",".editTask",function(){
		
		var tid = $(this).attr("tid");
		var base_url = $("#base_url").val();
		
		$('#enext_run_time').val("");
		$('.elwhenClose').html("");
		$(".updatedValues").html("");
		$(".eaddtask_wrapper").html("");
		$(".eaddtaskset_wrapper").html("");
		
		
		$("#task_id").val(tid);
		
		$.ajax({
			
			type : "post",
			data : {tid : tid,'table':'tbl_issues'},
			url : base_url+"admin/tasks/editTask",
			dataType : "json",
			success : function(data){
				
				$("#updatedWhencount").val(data.updatedWhencount);
				$("#updatedValuescount").val(data.updatedValuescount);
				
				console.log(data);
				
				$("#etask_name").val(data.task_name);
				$("#estatus").val(data.status);
				$("#eschedule_type").val(data.schedule_type);
				$("#enext_run_date").val(data.next_run_date);
				$('#enext_run_time').append($('<option selected>').val(data.next_run_time).text(data.next_run_time));
				$("#eaction").val(data.action);
				
				if(data.cond_column.length > 0){
					
					$(".elwhenOpen").hide();
					$(".elwhenClose").show();
					$('.elwhenClose').html(data.cond_data);
					
				}else{
					
					$(".elwhenOpen").show();
					$(".elwhenClose").hide();
					
				}
				
				$(".updatedValues").html(data.scond_data);
				$("#thistory").html(data.thistory);
				
				$(".select2").select2();
				
			},
			error : function(data){
				
				console.log(data);
				
			}
			
		});
		
	});
	// when condition script starts

		$(document).on("click",".elwhenCondition",function(){
			
			$(".elwhenOpen").hide();
			$(".elwhenClose").show();
			
		});

		$(document).on("click",".elremoveWhencondition",function(){
			
			$(".elwhenOpen").show();
			$(".elwhenClose").hide();
			
		});

		$(document).on("change",".elgetColumn",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("rid");
			var uopid = $(this).attr("uopid");
			
			$("."+ref).show();
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/tasks/getDatatypeconditions",
				dataType : 'json',
				data : {column : column,table:"tbl_issues","onchangeColref":"uoperatorchange",uopid:uopid},
				success : function(data){
					
					$(".eop"+ref).html(data.operators);
					
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
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
					console.log(data.operators);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
		});

		$(document).on("change",".eulgetColumn",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("rid");
			var uopid = $(this).attr("uopid");
			
			$("."+ref).show();
			
			$.ajax({
				
				type : "post",
				url : base_url+"admin/tasks/getDatatypeconditions",
				dataType : 'json',
				data : {column : column,table:"tbl_issues","onchangeColref":"updateonchangeoperator",uopid:uopid},
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
						
						$("."+ref).html('<input type="text" name="cond_value[]" class="form-control">');
						
					}
					
					
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
		});

		$(document).ready(function(){
		
			var emaxField = 20; //Input fields increment limitation
			var addButton = $('.eaddTaskbind'); //Add button selector
			var wrapper = $('.eaddtask_wrapper'); //Input field wrapper

			var x = 1; //Initial field counter is 1
			var y = 1;
			
			$(document).on('click', '.etlremove_button', function(e){
				
				e.preventDefault();
				var id =$(this).attr("lid");
				
				$(this).parent('div').remove(); //Remove field html
				$('.'+id).remove();
				
			});

			$(document).on('click', '.eutlremove_button', function(e){
				
				e.preventDefault();
				var id =$(this).attr("lid");
				
				$(this).parent('div').remove(); //Remove field html
				$('.'+id).remove();
				
			});
			
			//Once add button is clicked
			$(document).on("click",'.eaddTaskbind',function(){
				//Check maximum number of input fields
					var x = $("#updatedWhencount").val();
					var base_url = $("#base_url").val();
					x++;
					
					$("#updatedWhencount").val(x);
					
					$.ajax({
						
						type : "post",
						data : {table:"tbl_issues"},
						dataType : 'json',
						url : base_url+"admin/tasks/getColumns",
						success : function(data){
							
//							console.log(data);
							
							$('.eaddtask_wrapper').append('<div class="row removeuadd'+x+'" style="margin-left:0px !important;margin-right:0px !important;"><div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px"><label>When</label></div><div class="col-md-10"><div class="row"><div class="col-md-4"><div class="form-group"><select name="cond_column[]" class="form-control eulgetColumn" id="updChnop'+x+'" uopid="updChnop'+x+'" rid="getwhenRef'+x+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group opgetwhenRef'+x+'"><select name="condition[]" class="form-control updateonchangeoperator" opid="updChnop'+x+'">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group getwhenRef'+x+' updChnop'+x+'"><input type="text" name="cond_value[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-plus-circle eaddsTaskbind" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-times-circle eutlremove_button" lid="removeuadd'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div></div></div>'); 
							
						},error: function(data){
							
							console.log(data);
							
						}
						
					})
					
					 //Increment field counter
				
			});

			$(document).on("click",".eaddsTaskbind",function(){
				
					var x = $("#updatedWhencount").val();
					var base_url = $("#base_url").val();
					x++;
					$("#updatedWhencount").val(x);
					
					$.ajax({
						
						type : "post",
						data : {table:"tbl_issues"},
						dataType : 'json',
						url : base_url+"admin/tasks/getColumns",
						success : function(data){
							$(wrapper).append('<div class="row removeuadd'+x+'" style="margin-left:0px !important;margin-right:0px !important;"><div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px"><label>When</label></div><div class="col-md-10"><div class="row"><div class="col-md-4"><div class="form-group"><select name="cond_column[]" class="form-control eulgetColumn" uopid="updChnop'+x+'" id="updChnop'+x+'" rid="getwhenRef'+x+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group opgetwhenRef'+x+'"><select name="condition[]" class="form-control updateonchangeoperator" opid="updChnop'+x+'">'+data.operators+'</select></div></div><div class="col-md-3"><div class="form-group getwhenRef'+x+' updChnop'+x+'"><input type="text" name="cond_value[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-plus-circle eaddsTaskbind" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-times-circle eutlremove_button" lid="removeuadd'+x+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div></div></div>'); //Add field html
						}
						
					})	
				
			});

			
		});



	// when condition script ends

// on change of condition for 1st value

		$(document).on("change",".updateonchangeCondition",function(){
			
			var cond = $(this).val();
			var bind = $(this).attr("opid");
			var selection = $("#"+bind).val();
			
			var date = getDate();
			
			if(cond == "is blank" || cond == "is any" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time"){
				
				$("."+bind).hide();
				
			}else if(cond == "is during the current"){
				
				$("."+bind).html('<select name="cond_value[]" class="form-control"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$("."+bind).show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				
				$("."+bind).html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days[]" class="form-control">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value[]" class="form-control"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option><option value="rolling years">rolling years</option></select></div></div>');
				
				$("."+bind).show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$("."+bind).html('<input type="date" class="form-control" name="cond_value[]" value="'+date+'">');
				
				$("."+bind).show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$("."+bind).html('<input type="date" class="form-control" name="cond_value[]" value="'+date+'">');
				
				}
				$("."+bind).show();
				
			}else{
				
				$("."+bind).show();
				
			}
			
		})
		
		$(document).on("change",".updateonchangeoperator",function(){
			
			var cond = $(this).val();
			var bind = $(this).attr("opid");
			var uchgwhen = $(this).attr("uchgwhen");
			var selection = $("#"+bind).val();
			
			var date = getDate();
			
			if(cond == "is blank" || cond == "is any" || cond == "is not blank" || cond == "is today" || cond == "is today or before" || cond == "is today or after" || cond == "is before today" || cond == "is after today" || cond == "is before current time" || cond == "is after current time"){
				
				$("."+bind).hide();
				
			}else if(cond == "is during the current"){
				
				$("."+bind).html('<select name="cond_value[]" class="form-control"><option value="week">week</option><option value="month">month</option><option value="quarter">quarter</option><option value="year">year</option></select>');
				
				$("."+bind).show();
				
			}else if(cond == "is during the previous" || cond == "is during the next" || cond == "is before the previous" || cond == "is after the next"){
				
				var i;
				var end = 31;
				var days = "";
				for (i = 1; i <= end; i++) { 
				  days += '<option value="'+i+'">'+i+'</option>';
				}
				
				$("."+bind).html('<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days[]" class="form-control">'+days+'</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value[]" class="form-control"><option value="days">days</option><option value="weeks">weeks</option><option value="months">months</option><option value="years">years</option><option value="rolling years">rolling years</option></select></div></div>');
				
				$("."+bind).show();
				
			}else if(cond == "is before" || cond == "is after"){
				
				$("."+bind).html('<input type="date" class="form-control" name="cond_value[]" value="'+date+'">');
				$("."+bind).show();
				
			}else if(cond == "is" || cond == "is not"){
				
				var select = selection.split("-");
				
				if(select[1] == "date"){
				
					$("."+bind).html('<input type="date" class="form-control" name="cond_value[]" value="'+date+'">');
				
				}
				$("."+bind).show();
				
			}else{
				
				$("."+bind).show();
				
			}
			
		})

	// set value script starts

	
		$(document).ready(function(){
		
			var maxField = 20; //Input fields increment limitation
			var addButton = $('.addtask_set'); //Add button selector
			var wrapper = $('.addtaskset_wrapper'); //Input field wrapper

			var x1 = 1; //Initial field counter is 1
			var y = 1;

			//Once add button is clicked
			$(addButton).on("click",function(){
				//Check maximum number of input fields
//				if(x1 < maxField){
					
					var x1 = $("#updatedValuescount").val();
				
					var base_url = $("#base_url").val();
					x1++;
					$("#updatedValuescount").val(x1);
					
					$.ajax({
						
						type : "post",
						data : {table:"tbl_issues",toutref:"dshipperpo"},
						dataType : 'json',
						url : base_url+"admin/tasks/getColumns",
						success : function(data){
							
							$(wrapper).append('<div class="row subset_column'+x1+'"><div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px"><label>Values</label></div><div class="col-md-10"><div class="row"><div class="col-md-1" style="margin-top: 7px;font-size: 16px;font-weight: 400;">Set</div><div class="col-md-3"><div class="form-group"><select name="scond_column[]" class="form-control wsetGetcol" id="getCondVal'+x1+'" wuid="getSetfield'+x1+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group"><select name="ssetcondition[]" class="form-control getLocuptcolumns" id="getSetfield'+x1+'" guid="getCondVal'+x1+'" uid="getConditionalst'+x1+'"><option value="to a custom value">To a custom value</option><option value="to a field value">To a field value</option></select></div></div><div class="col-md-3"><div class="form-group getSetfield'+x1+' getConditionalst'+x1+'"><input type="text" name="ssetvalue[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-times-circle tsetremove_button" lid="subset_column'+x1+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-plus-circle addstask_setbind" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div></div></div>'); 
							
						}
						
					})
					
					 //Increment field counter
					
//					y++;
//				}
			});

			$(wrapper).on("click",".addstask_setbind",function(){
				//Check maximum number of input fields
//				if(x1 < maxField){ 
					
					var x1 = $("#updatedValuescount").val();
					var base_url = $("#base_url").val();
					x1++;
					$("#updatedValuescount").val(x1);
				
					$.ajax({
						
						type : "post",
						data : {table:"tbl_issues",toutref:"dshipperpo"},
						dataType : 'json',
						url : base_url+"admin/tasks/getColumns",
						success : function(data){
							$(wrapper).append('<div class="row subset_column'+x1+'"><div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px"><label>Values</label></div><div class="col-md-10"><div class="row"><div class="col-md-1" style="margin-top: 7px;font-size: 16px;font-weight: 400;">Set</div><div class="col-md-3"><div class="form-group"><select name="scond_column[]" class="form-control wsetGetcol" wuid="getSetfield'+x1+'" id="getCondVal'+x1+'">'+data.columns+'</select></div></div><div class="col-md-3"><div class="form-group"><select name="ssetcondition[]" class="form-control getLocuptcolumns" id="getSetfield'+x1+'" guid="getCondVal'+x1+'" uid="getConditionalst'+x1+'" id="getSetfield'+x1+'"><option value="to a custom value">To a custom value</option><option value="to a field value">To a field value</option></select></div></div><div class="col-md-3"><div class="form-group getSetfield'+x1+' getConditionalst'+x1+'"><input type="text" name="ssetvalue[]" class="form-control"></div></div><div class="col-md-2" align="right"><i class="fa fa-times-circle tsetremove_button" lid="subset_column'+x1+'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;<i class="fa fa-plus-circle addstask_setbind" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div></div></div>'); //Add field html
						}
					})	
//					y++;
//				}
			});

			//Once remove button is clicked
			$(wrapper).on('click', '.tsetremove_button', function(e){
				e.preventDefault();
				var id =$(this).attr("lid");

				$(this).parent('div').remove(); //Remove field html
				$('.'+id).remove();
//				x--; 
			});
		});


		$(document).on("change",".escond_val",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("wuid");
			var scon = $("#"+ref).val();
			
			if(scon == "to a field value"){
				
				$.ajax({
				
					type : "post",
					url : base_url+"admin/tasks/getsetvalfields",
					dataType : 'json',
					data : {table:"tbl_issues"},
					success : function(data){

						$("."+ref).html(data.columns);
//						console.log(data);

					},
					error : function(data){

//						console.log(data);

					}

				});
				
			}else{
			
				$.ajax({
				
				type : "post",
				url : base_url+"admin/tasks/getFields",
				dataType : 'json',
				data : {column : column,table:"tbl_issues"},
				success : function(data){
					
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
						
						$("."+ref).html('<input type="text" name="ssetvalue[]" class="form-control">');
						
					}
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
			}
				
		});

		$(document).on("change",".ewsetGetcol",function(){
			
			var base_url = $("#base_url").val();
			var column = $(this).val();
			var ref = $(this).attr("wuid");
			var scon = $("#"+ref).val();
			
			if(scon == "to a field value"){
			
				$.ajax({
				
					type : "post",
					url : base_url+"admin/tasks/getsetvalfields",
					dataType : 'json',
					data : {table:"tbl_issues"},
					success : function(data){

						$("."+ref).html(data.columns);
//						console.log(data);

					},
					error : function(data){

//						console.log(data);

					}

				});
				
			}else{
				
				$.ajax({
				
				type : "post",
				url : base_url+"admin/tasks/getFields",
				dataType : 'json',
				data : {column : column,table:"tbl_issues"},
				success : function(data){
					
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
						
						$("."+ref).html('<input type="text" name="ssetvalue[]" class="form-control">');
						
					}
//					console.log(data);
					
				},
				error : function(data){
					
//					console.log(data);
					
				}
				
			});
			
			}
		});

		$(document).on("change",".egetLocucolumns",function(){
			
			var base_url = $("#base_url").val();
			var val = $(this).val();
			var ref = $(this).attr("uid");
			var column = $("#"+ref).val();
			
			if(val == "to a field value"){
				
				$.ajax({
				
					type : "post",
					url : base_url+"admin/tasks/getsetvalfields",
					dataType : 'json',
					data : {table:"tbl_issues"},
					success : function(data){

						$("."+ref).html(data.columns);
//						console.log(data);

					},
					error : function(data){

//						console.log(data);

					}

				});
				
			}else{
				
				$.ajax({
				
					type : "post",
					url : base_url+"admin/tasks/getFields",
					dataType : 'json',
					data : {column : column,table:"tbl_issues"},
					success : function(data){

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

							$("."+ref).html('<input type="text" name="ssetvalue[]" class="form-control">');

						}
//						console.log(data);

					},
					error : function(data){

//						console.log(data);

					}

				});
				
			}
			
		});

		$(document).on("change",".egetLocuptcolumns",function(){
				
			var base_url = $("#base_url").val();
			var val = $(this).val();
			var ref = $(this).attr("uid");
			var uref = $(this).attr("guid");
			
			var column = $("#"+uref).val();
			
			if(val == "to a field value"){
				
				$.ajax({
				
					type : "post",
					url : base_url+"admin/tasks/getsetvalfields",
					dataType : 'json',
					data : {table:"tbl_issues"},
					success : function(data){

						$("."+ref).html(data.columns);
//						console.log(data);

					},
					error : function(data){

//						console.log(data);

					}

				});
				
			}else{
				
				$.ajax({
				
					type : "post",
					url : base_url+"admin/tasks/getFields",
					dataType : 'json',
					data : {column : column,table:"tbl_issues"},
					success : function(data){

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

							$("."+ref).html('<input type="text" name="ssetvalue[]" class="form-control">');

						}
//						console.log(data);

					},
					error : function(data){

//						console.log(data);

					}

				});
				
			}
			
		});

// update task

	$(document).on("submit","#uTask",function(e){
		
		e.preventDefault();
		
		var fdata = $(this).serialize();
		var base_url = $("#base_url").val();
		
		$.ajax({
			
			type : "post",
			url : base_url+"admin/tasks/updateTask",
			data : fdata,
			beforeSend : function(){
				
				$(".ustloader").show();
				
			},
			success : function(data){
				
				$(".ustloader").hide();
				
				console.log(data);
				if(data == "success"){
				
					$(".usterror").html('<div class="alert alert-success">Task Successfully Updated</div>');
					setTimeout(function(){location.reload();},3000);
					
				}else{
					
					$(".usterror").html('<div class="alert alert-success">Error Occured</div>');
					
				}
				
				
			},
			error : function(data){
				
				$(".ustloader").hide();
				console.log(data);
				
			}
			
		});
		
	})

	// set value script ends
	
	
//  Run task
	
	$(document).on("click","#runTask",function(){
		
		var tid = $("#task_id").val();
		var base_url = $("#base_url").val();
		
		$.ajax({
			
			type : "post",
			data : {tid : tid,"table":"tbl_issues"},
			url : base_url+"admin/tasks/executeQuery",
			beforeSend : function(){
			
				$("#runTask").hide();
				$(".taskLoader").show();
				
			},
			success : function(data){
				
				$(".taskLoader").hide();
				$("#runTask").show();
				console.log(data);
				
				if(data == "success"){
					
					$("#cerr").html('<div class="alert alert-success">Task Executed Successfully</div>');
					setTimeout(function(){location.reload();},3000); 
					
				}
				
//				location.reload()
			},
			error : function(data){
				$(".taskLoader").hide();
				$("#runTask").show();
				console.log(data);
			}
			
		})
		
	})
	
	function deleteTask(id){
		
	   var base_url = $("#base_url").val();	
       Swal({
		  title: 'Are you sure?',
		  text: 'You will not be able to recover this selected task!',
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonText: 'Yes, delete it!',
		  cancelButtonText: 'No, keep it'
		}).then((result) => {
		  if (result.value) {

			Swal(
			  'Deleted!',
			  'Your Selected task has been deleted.',
			  'success'
			)
			$.ajax({
				method: 'POST',
				data: {'id' : id },
				url: base_url+'admin/tasks/delTask/'+id,
				success: function(data) {
					console.log(data);
					location.reload();   
				}
			});

		  } else if (result.dismiss === Swal.DismissReason.cancel) {
			Swal(
			  'Cancelled',
			  'Your Selected task is safe :)',
			  'success',

			)
		  }
		})
    }

