

<div class="page-header">

	<h1>Overzicht offertes</h1>
</div>


<div class="offer-container last-reports ajax-report-container">

</div>



<script>

var contextNames = <?= json_encode($contextNames) ?>;

var timeout = 0;

for(var i in contextNames) {
	let cn = contextNames[i];
	
	setTimeout(function() {
		loadContext( cn );
	}, timeout);
	
	timeout += 1000;
}

function loadContext(name) {
	$.ajax({
		type: 'post',
		url: appUrl( '/?m=admin&c=report&a=requestOffers' ),
		data: {
			contextName: name
		},
		success: function(data, xhr, textStatus) {
			if (typeof data == 'object') {
				renderOffers(name, data);
			} else {
				alert('Fout opgetreden bij het laden van offerts voor ' + name);
			}
		}
	});
}

function renderOffers(name, data) {
	var d = $('<div class="admin-report-container" />');

	var anch = $('<a class="widget-title" href="javascript:void(0);">Laatst gewijzigde offertes ' + name + '</a>');
	anch.click(function() {showLoginContext(name); });
	d.append(anch);

	var tbl = $('<table class="tbl-offers list-widget" />');
	tbl.append('<thead><tr><th>Naam</th><th>Omschrijving</th><th>Status</th><th>Gewijzigd op</th></tr></thead>');
	if (data.objects.length == 0) {
		tbl.append('<tbody><tr><td colspan="4" style="text-align: center; font-style: italic;">Nog geen offertes aangemaakt</td></tr></tbody>');
	}
	
	for(var i in data.objects) {
		var o = data.objects[i];
		
		var tr = $('<tr />');
		var tdCustomer = $('<td />');
		if (o.company_name) {
			tdCustomer.text( o.company_name );
		} else {
			var t = '';
			if (o.firstname) {
				t += o.firstname;
			}
			if (o.insert_name) {
				t += ' ' + o.insert_name;
			}
			if (o.lastname) {
				t += ' ' + o.lastname;
			}
			tdCustomer.text( t );
		}
		
		var tdDescription = $('<td />');
		tdDescription.text( o.subject );
		
		var tdStatus = $('<td />');
		tdStatus.text( o.offer_status_description );

		var tdCreated = $('<td />');
		tdCreated.text( format_datetime(text2date(o.created)) );
		

		tr.append(tdCustomer);
		tr.append(tdDescription);
		tr.append(tdStatus);
		tr.append(tdCreated);
		tbl.append( tr );
	}

	d.append(tbl);

	$('.offer-container').append( d );
}

function showLoginContext(contextName) {
	show_popup(appUrl('/?m=admin&c=customer&a=popup_users'), {
		data: {
			contextName: contextName
		}
	});
}


</script>




