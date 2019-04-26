

$(document).ready(function() {
	

	$.ajax({
		type: 'POST',
		url: appUrl('/?m=moneybird&c=contacts&a=check_import'),
		data: {
			person_id: $('.form-person-form [name=person_id]').val(),
			company_id: $('.form-company-form [name=company_id]').val()
		},
		success: function(data, xhr, textStatus) {
			if (data.moneybirdImport == true) {
				showInlineSecondary('Let op, dit contact is geïmporteerd vanuit Moneybird. Wijzigingen worden overschreven.');
			}
		}
	});
	
	
});
