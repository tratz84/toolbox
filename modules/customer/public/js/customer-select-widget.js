




function newCustomerPopup_Click() {
	
	show_popup( appUrl('/?m=customer&c=popup/newCustomer'), {
		renderCallback: function(popup) {
			$(popup).find('.submit-form').click(function() {
				newCustomerPopup_handleSubmit();
			});
//			applyWidgetFields( popup );
		}
	} );
}


function newCustomerPopup_handleSubmit() {
	
	var activeTab = $('.popup-container .nav-tabs a.nav-item.active');
	
	if (activeTab.data('tab-name') == 'company') {
		newCustomerPopup_handleCompanySubmit();
	}
	
	if (activeTab.data('tab-name') == 'person') {
		newCustomerPopup_handlePersonSubmit();
	}
	
}

function newCustomerPopup_handleCompanySubmit() {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=customer&c=popup/newCustomer&a=save_company'),
		data: $('.popup-container .form-company-form').serialize(),
		success: function(data, xhr, textStatus) {
			if (data.error) {
				setPopupFormErrors('.popup-container .form-company-form', data.errors);
			}
			
			if (data.success) {
				set_select2_val('select[name=customer_id]', data.customer_id, data.customer_name);
				
				close_popup();
			}
			
		}
	});
}

function newCustomerPopup_handlePersonSubmit() {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=customer&c=popup/newCustomer&a=save_person'),
		data: $('.popup-container .form-person-form').serialize(),
		success: function(data, xhr, textStatus) {
			if (data.error) {
				console.log(data);
				setPopupFormErrors('.popup-container .form-person-form', data.errors);
			}
			
			if (data.success) {
				set_select2_val('select[name=customer_id]', data.customer_id, data.customer_name);
				
				close_popup();
			}
			
		}
	});
}

