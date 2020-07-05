


$(window).data('multiuser_auto', true);
$(document).ready(function() {
	if (!window.sessionStorage) {
		return;
	}
	
	if (!$(window).data('multiuser_auto')) {
		return;
	}
	
	var lock_key = '';
	var valuesSet = true;
	
	var keyFields = $('.key-field');
	if (keyFields.length == 0)
		valuesSet = false;
	
	keyFields.each(function(index, node) {
		var field = $(node).val();
		
		var frm = $(node).closest('form');
		var val = frm.find('[name=' + field + ']').val();
		
		if (!val || val == '')
			valuesSet = false;
		
		if (lock_key != '')
			lock_key += '&';
		lock_key = field + '=' + val;
	});
	
	if (valuesSet) {
		multiuser_handleLock( lock_key );
	} else {
		multiuser_resetLock();
	}
});


var multiuser_warningPopupShown = false;
var multiuser_handleLock_interval = null;

function multiuser_handleLock(key) {
	
	if (typeof multiuser_check_interval == 'undefined')
		multiuser_check_interval = 10;
	
	if (multiuser_handleLock_interval) {
		clearInterval( multiuser_handleLock_interval );
	}
	
	multiuser_handleLock_interval = setInterval(function() {
		multiuser_checkLock(key);
	}, multiuser_check_interval * 1000);
	
	multiuser_checkLock(key);
}

function multiuser_tabuid() {
	var tabUid = window.sessionStorage.getItem('tab-uid');
	
	if (tabUid == window.name) {
		return tabUid;
	} else if (!tabUid || window.opener) {
		tabUid = uuidv4();
		window.name = tabUid;
		window.sessionStorage.setItem('tab-uid', tabUid);
	}
	
	return tabUid;
}

var multiuser_lock_message = 'Let op, andere gebruiker actief in dit scherm: ';

function multiuser_checkLock(key) {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=base&c=multiuser'),
		data: {
			key: key,
			tab: multiuser_tabuid(),
			username: username
		},
		success: function(data, xhr, textStatus) {
			if (data.locks) {
				var users = [];
				
				for(var i in data.locks) {
					if (i == username && data.locks[username] == 1) continue;
					
					users.push(username);
				}
				
				if (users.length) {
					var msg = multiuser_lock_message;
					
					for(var i=0; i < users.length; i++) {
						if (i > 0 && i == users.length-1) {
							msg += ' & ';
						} else if (i > 0) {
							msg += ', ';
						}
						msg += users[i];
					}
					
					showInlineWarning( msg );
					
					if (multiuser_warningPopupShown == false) {
						showAlert('Andere gebruiker actief', msg);
						multiuser_warningPopupShown = true;
					}
				} else {
					$('.js-inline-warning').remove();
					multiuser_warningPopupShown = false;
				}
			}
		}
	});
}

function multiuser_resetLock() {
	if (multiuser_handleLock_interval) {
		clearInterval( multiuser_handleLock_interval );
	}
	
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=base&c=multiuser'),
		data: {
			key: '',
			tab: multiuser_tabuid(),
			username: username
		},
		success: function(data, xhr, textStatus) {
			// ?
		}
	});
	
	$('.js-inline-warning').remove();
	multiuser_warningPopupShown = false;
}



