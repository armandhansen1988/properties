$(document).ready(function(e) {
	loadProperties(1);
});

function loadProperties(page_number){
	$('#results').html('<center><li class="fa fa-spinner fa-spin" style="font-size: 50px;"></li><br />Loading Properties<br />Please wait...</center>');
	var page_size = $('#page_size').val();
	$.post("properties", {s: page_size, p: page_number}, function(data){
		$('#results').html(data);
	});
}

function confirmDelete(){
	var uuid = $('#delete_uuid').val();
	$.post('remove-property', {uuid: uuid}, function(data){
		if(data.error == 1){
			loadProperties(1);
			$('.modal').modal('hide');
		}else{
			alert("There was a problem removing the property. Please try again");
		}
	}, 'json');
}