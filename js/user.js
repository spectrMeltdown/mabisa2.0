
// function onchange_country(val) {
// 	$.ajax({
// 	    type: "POST",
// 	    url: "../actions/dashboard/user/onchange.php",
// 	    async: false,
// 	    data: {
// 	    	val: val,
// 	      	country: 1
// 	    },
// 	    success:function(result){
// 	      	$('#region').html(result);
// 	    }
// 	});
// }

// function onchange_region(val) {
// 	$.ajax({
// 	    type: "POST",
// 	    url: "../actions/dashboard/user/onchange.php",
// 	    async: false,
// 	    data: {
// 	    	val: val,
// 	      	region: 1
// 	    },
// 	    success:function(result){
// 	      	$('#province').html(result);
// 	    }
// 	});
// }

// function onchange_province(val) {
// 	$.ajax({
// 	    type: "POST",
// 	    url: "../actions/dashboard/user/onchange.php",
// 	    async: false,
// 	    data: {
// 	    	val: val,
// 	      	province: 1
// 	    },
// 	    success:function(result){
// 	      	$('#city').html(result);
// 	    }
// 	});
// }

// function onchange_city(val) {
// 	$.ajax({
// 	    type: "POST",
// 	    url: "../actions/dashboard/user/onchange.php",
// 	    async: false,
// 	    data: {
// 	    	val: val,
// 	      	city: 1
// 	    },
// 	    success:function(result){
// 	      	$('#barangay').html(result);
// 	    }
// 	});
// }

function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function delete_user(val) {
	// alert(val);
	$.ajax({
	    type: "POST",
	    url: "../api/delete_user.php",
	    async: false,
	    data: {
	    	val: val,
	      delete: 1
	    },
	    success:function(result){
	      	// $('#user_dataTable').html(result);
	      	view_user();
	    }
	});
}

function edit_user(val) {
	// alert(val);
	$.ajax({
	    type: "POST",
	    url: "../api/edit_use.php",
	    async: false,
	    data: {
	    	val: val,
	      edit: 1
	    },
	    success:function(result){
	      	$('#display_edit').html(result);
	      	// view_user();
	    }
	});
}

function view_user() {
	$.ajax({
	    type: "POST",
	    url: "../api/view_user.php",
	    async: false,
	    data: {
	      	view: 1
	    },
	    success:function(result){
	      	$('#user_dataTable').html(result);
	    }
	});
}

function saveUser() {
	$ok = 1;
	$country = $('#country').val();
	$region = $('#region').val();
	$province = $('#province').val();
	$city = $('#city').val();
	$barangay = $('#barangay').val();
	$username = $('#username_brgy').val();
	$password = $('#password').val();
	$email = $('#new_email').val();
	phone = $('#phone').val();
	$account_type = $('#account_type').val();

	// alert($username);
	if ($account_type == '' || $account_type == null) {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Account Type cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if (phone[0] != '+' || phone[1] != '6' || phone[2] != '3' || phone[3] != '9') {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Invalid Phone Number Format.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if (validateEmail($email) == false) {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Invalid Email format.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($email == '' || $email == null) {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Email cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($password.length < 8) {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Password must atleast 8 letters or numbers.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($password == '' || $password == null) {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Password cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($username == '' || $username == null) {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Username cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($barangay == '' || $barangay == null) {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Barangay cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($city == '' || $city == null) {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> City cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($province == '' || $province == null) {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Province cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($region == '' || $region == null) {
		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Region cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($country == '' || $country == null) {
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
		    url: "../actions/dashboard/user/save_user.php",
		    async: false,
		    data: {
		    	country: $country,
		    	region: $region,
		    	province: $province,
		    	city: $city,
		    	barangay: $barangay,
		    	username: $username,
		    	password: $password,
		    	email: $email,
		    	account_type: $account_type,
		    	phone: phone,
		      	save: 1
		    },
		    success:function(result){
		      	// alert(result);
		      	if (result == 1) {
		      		$('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert">'+
					  '<strong>Success!</strong> User Successfully Saved.'+
					  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
					    '<span aria-hidden="true">&times;</span>'+
					  '</button>'+
					'</div>');
		      		view_user();
		      	}else{
		      		$('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
					  '<strong>Error!</strong> You can Only Create 1(one) Account per Barangay.'+
					  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
					    '<span aria-hidden="true">&times;</span>'+
					  '</button>'+
					'</div>');
		      	}
		    }
		});
	}
}

function updateUser() {
	// alert('goods');
	$ok = 1;
	$country = $('#ecountry').val();
	$region = $('#eregion').val();
	$province = $('#eprovince').val();
	$city = $('#ecity').val();
	$barangay = $('#ebarangay').val();
	$id = $('#id').val();
	$username = $('#eusername').val();
	$password = $('#epassword').val();
	$email = $('#eemail').val();
	ephone = $('#ephone').val();
	$account_type = $('#eaccount_type').val();
	if ($account_type == '' || $account_type == null) {
		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Account Type cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if (ephone[0] != '+' || ephone[1] != '6' || ephone[2] != '3' || ephone[3] != '9') {
		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Invalid Phone Number Format.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if (validateEmail($email) == false) {
		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Invalid Email format.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($email == '' || $email == null) {
		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Email cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($password < 8) {
		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Password must atleast 8 letters or numbers.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($password == '' || $password == null) {
		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Password cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($username == '' || $username == null) {
		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Username cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($barangay == '' || $barangay == null) {
		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Barangay cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($city == '' || $city == null) {
		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> City cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($province == '' || $province == null) {
		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Province cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($region == '' || $region == null) {
		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
		  '<strong>Error!</strong> Region cannot be empty.'+
		  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
		    '<span aria-hidden="true">&times;</span>'+
		  '</button>'+
		'</div>');
		$ok = 0;
	}
	if ($country == '' || $country == null) {
		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
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
		    url: "../actions/dashboard/user/update.php",
		    async: false,
		    data: {
		    	country: $country,
		    	region: $region,
		    	province: $province,
		    	city: $city,
		    	barangay: $barangay,
		    	id: $id,
		    	username: $username,
		    	password: $password,
		    	email: $email,
		    	phone: ephone,
		    	account_type: $account_type,
		      	update: 1
		    },
		    success:function(result){
		      	// alert(result);
		      	if (result == 1) {
		      		$('#ealert').html('<div class="alert alert-success alert-dismissible fade show" role="alert">'+
					  '<strong>Success!</strong> Update Successfully.'+
					  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
					    '<span aria-hidden="true">&times;</span>'+
					  '</button>'+
					'</div>');
		      		view_user();
		      	}else{
		      		$('#ealert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
					  '<strong>Error!</strong> Update failed.'+
					  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
					    '<span aria-hidden="true">&times;</span>'+
					  '</button>'+
					'</div>');
		      	}
		    }
		});
	}
}