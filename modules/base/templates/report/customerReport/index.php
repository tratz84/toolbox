


<div id="report-container"></div>




<script>

var t = new IndexTable('#report-container');

t.setRowClick(function(row, evt) {
	var record = $(row).data('record');
	
	if (record.person_id) {
		window.open(appUrl('/?m=base&c=person&a=edit&person_id=' + record.person_id), '_blank');
	}
	if (record.company_id) {
		window.open(appUrl('/?m=base&c=company&a=edit&company_id=' + record.company_id), '_blank');
	}
});

t.setConnectorUrl( '/?m=base&c=report/customerReport&a=search' );


t.addColumn({
	fieldName: 'customer_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	render: function(record) {
		if (record.person_id) {
			return record.person_id;
		}

		return record.company_id;
	},
	searchable: false
});
t.addColumn({
	fieldName: 'type',
	width: 40,
	fieldDescription: 'type',
	fieldType: 'text',
	render: function(r) {
		if (r.person_id) {
			return 'Particulier';
		}

		return 'Bedrijf';
	},
	searchable: false
});

t.addColumn({
	fieldName: 'name',
	fieldDescription: 'Klant',
	fieldType: 'text',
	render: function(record) {
		return format_customername(record);
	},
	searchable: false
});
t.addColumn({
	fieldName: 'coc_number',
	fieldDescription: 'Kvk nr',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'vat_number',
	fieldDescription: 'Btw nr',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'iban',
	fieldDescription: 'IBAN',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'bic',
	fieldDescription: 'BIC',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'street',
	fieldDescription: 'Straat',
	fieldType: 'text',
	render: function(record) {
		var t = '';
		
		if (record.street)
			t += record.street;
		if (record.street_no)
			t = t + ' ' + record.street_no;
		
		return t;
	},
	searchable: false
});
t.addColumn({
	fieldName: 'zipcode',
	fieldDescription: 'Postcode',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'city',
	fieldDescription: 'Plaats',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'email_address',
	fieldDescription: 'E-mail',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'phonenr',
	fieldDescription: 'Telnr',
	fieldType: 'text',
	searchable: false
});

t.load();

</script>



