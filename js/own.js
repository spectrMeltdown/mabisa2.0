

function saveLocation() {
	// alert('goods');
	$ok = 1;
	$country = $('#country').val();
	$region = $('#region').val();
	$province = $('#province').val();
	$city = $('#city').val();
	$barangay = $('#barangay').val();
	if ($barangay == '') {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Barangay cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($city == '') {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> City cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($province == '') {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Province cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($region == '') {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Region cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($country == '') {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Country cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($ok == 1) {
		$.ajax({
		    type: "POST",
		    url: "../actions/dashboard/location/add.php",
		    async: false,
		    data: {
		      	country: $country,
		      	region: $region,
		      	province: $province,
		      	city: $city,
		      	barangay: $barangay,
		      	location: 1
		    },
		    success:function(result){
		      	// alert(result);
		      	// $('#main_content').html(result);
		      	// window.location = '../patient_appointment/';
		      	$('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert">'+
				  '<strong>Success!</strong> Location Successfully Saved.'+
				  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
				    '<span aria-hidden="true">&times;</span>'+
				  '</button>'+
				'</div>');
				$('#country').val('');
				$('#region').val('');
				$('#province').val('');
				$('#city').val('');
				$('#barangay').val('');
				view_location();
		    }
		});
	}
}
function view_location() {
	$.ajax({
	    type: "POST",
	    url: "../actions/dashboard/location/view.php",
	    async: false,
	    data: {
	      	view: 1
	    },
	    success:function(result){
	      	$('#viewLocation').html(result);
	    }
	});
}

function edit_location(val) {
	$.ajax({
	    type: "POST",
	    url: "../actions/dashboard/location/edit.php",
	    async: false,
	    data: {
	      	val: val,
	      	edit: 1
	    },
	    success:function(result){
	      	$('#editLocationForm').html(result);
	    }
	});
}

function delete_location(val) {
	$.ajax({
	    type: "POST",
	    url: "../actions/dashboard/location/delete.php",
	    async: false,
	    data: {
	      	val: val,
	      	delete: 1
	    },
	    success:function(result){
	    	alert('Successfully Deleted.');
	      	view_location();
	    }
	});
}

function updateLocation() {
	// alert('goods');
	$ok = 1;
	$barangay = $('#barangay2').val();
	code = $('#barangay3').val();
	if ($barangay == '') {
		$('#alert2').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Barangay cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($ok == 1) {
		$.ajax({
		    type: "POST",
		    url: "../actions/dashboard/location/update.php",
		    async: false,
		    data: {
		      	barangay: $barangay,
		      	code: code,
		      	update: 1
		    },
		    success:function(result){
		      	// alert(result);
		      	// $('#main_content').html(result);
		      	// window.location = '../patient_appointment/';
		      	$('#alert2').html('<div class="alert alert-success alert-dismissible fade show" role="alert">'+
				  '<strong>Success!</strong> Location Successfully Updated.'+
				  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
				    '<span aria-hidden="true">&times;</span>'+
				  '</button>'+
				'</div>');
				view_location();
		    }
		});
	}
}