
// CustomerSelectWidget contains a deleted customer? => remove customer-deleted-class when other customer is selected
$(document).ready(function() {
	$('div.dynamic-select-field-widget.customer-deleted select.select2-widget').on('change', function() {
		$(this).closest('div.widget').removeClass('customer-deleted');
	});
});



function newCustomerPopup_Click( objAnchor, opts ) {
	opts = opts ? opts : {};
	
	var popup_url = appUrl('/?m=customer&c=popup/newCustomer');
	if (opts.customer_type) {
		popup_url = popup_url + '&customer_type=' + opts.customer_type;
	}
	
	show_popup( popup_url, {
		renderCallback: function(popup) {
			$(popup).find('.submit-form').click(function() {
				newCustomerPopup_handleSubmit( objAnchor );
			});
//			applyWidgetFields( popup );
		}
	} );
}


function newCustomerPopup_handleSubmit( objAnchor ) {
	
	var activeTab = $('.popup-container .nav-tabs a.nav-item.active');
	
	if (activeTab.data('tab-name') == 'company') {
		newCustomerPopup_handleCompanySubmit( objAnchor );
	}
	
	if (activeTab.data('tab-name') == 'person') {
		newCustomerPopup_handlePersonSubmit( objAnchor );
	}
	
}

function newCustomerPopup_handleCompanySubmit( objAnchor ) {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=customer&c=popup/newCustomer&a=save_company'),
		data: $('.popup-container .form-company-form').serialize(),
		success: function(data, xhr, textStatus) {
			if (data.error) {
				setPopupFormErrors('.popup-container .tab-name-company', data.errors);
			}
			
			if (data.success) {
				var select = $(objAnchor).closest('div.widget').find('select');
				
				if (select.hasClass('select2-widget')) {
					set_select2_val(select, data.customer_id, data.customer_name);
					close_popup();
				}
				else {
					newCustomerPopup_reloadOptions( select, data );
				}
			}
			
		}
	});
}

function newCustomerPopup_handlePersonSubmit( objAnchor ) {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=customer&c=popup/newCustomer&a=save_person'),
		data: $('.popup-container .form-person-form').serialize(),
		success: function(data, xhr, textStatus) {
			if (data.error) {
				console.log(data);
				setPopupFormErrors('.popup-container .tab-name-person', data.errors);
			}
			
			if (data.success) {
				var select = $(objAnchor).closest('div.widget').find('select');
				
				if (select.hasClass('select2-widget')) {
					set_select2_val( select, data.customer_id, data.customer_name );
					close_popup();
				}
				else {
					newCustomerPopup_reloadOptions( select, data );
				}
			}
			
		}
	});
}


function newCustomerPopup_reloadOptions( objSelect, dataNewCustomer ) {
	showPageLoading();
	
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=customer&c=customer&a=all_customers'),
		data: {
			
		},
		success: function(data, xhr, textStatus) {
			$(objSelect).find('option').each(function(index, node) {
				if ($(node).val() != '') $(node).remove();
			});
			
			for( x in data.customers ) {
				var opt = $('<option />');
				
				opt.val( data.customers[x]['customer_id'] );
				opt.text( data.customers[x]['name'] );
				
				if (dataNewCustomer.customer_id == data.customers[x]['customer_id']) {
					opt.attr('selected', 'selected');
				}
				
				$(objSelect).append( opt );
			}
			
			close_popup();
			hidePageLoading();

		},
		error: function() {
			alert('Er is een fout opgetreden, vernieuw de pagina en probeer het opnieuw');
		}
	});
	
}



