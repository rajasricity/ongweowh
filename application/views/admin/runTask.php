<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
	$.ajax({
		
		url : "<? echo base_url('admin/tasks/run_taskcronjob') ?>",
		success : function(data){
		
			console.log(data);	
			setTimeout(function(){// wait for 5 secs(2)
				   location.reload(); // then reload the page.(3)
			  }, 60000);
			
		},
		error : function(data){
			
			console.log(data);
			
		}
	})
	
});
</script>
</head>

<body>
	
	<p>Task Executed Successfully....</p>

</body>
