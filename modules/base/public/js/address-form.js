/**
 * 
 */


$(document).ready(function() {
	
	$('.form-generator.form-address-form').find('[name=zipcode], [name=street_no]').change(function() {
		address_form_postcodecheck();
	});
});

function address_form_postcodecheck() {
	
	var street = $.trim($('.form-address-form [name=street]').val());
	var city = $.trim($('.form-address-form [name=city]').val());
	
	if (street != '' || city != '') {
		return;
	}
	
	var zipcode = $.trim($('.form-address-form [name=zipcode]').val());
	var nr = $.trim($('.form-address-form [name=street_no]').val());
	
	// no zipcode/nr ?
	if (zipcode == '' || nr == '')
		return;
	
	check_zipcode(zipcode, nr, function( resp ) {
		if (resp.success) {
			$('.form-address-form [name=city]').val( resp.data.city );
			$('.form-address-form [name=street]').val( resp.data.street );
			
		}
	});
	
}

function check_zipcode(zipcode, nr, callback) {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=base&c=connect/zipcode'),
		data: {
			zipcode: zipcode,
			nr: nr
		},
		success: function(data, xhr, response) {
			callback( data );
		}
	});
	
}



