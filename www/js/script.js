


$(document).ready(function() {
	
	
	// prevent session timeouts
	if (typeof contextName != 'undefined' && contextName != 'admin') setInterval(function() {
		$.ajax({
			url: appUrl('/?m=base&c=ping')
		});
	}, 60 * 1000);
	
	
	$('.nav-side-menu .menu-list .menu-item > a.nav-link').on('touchend click', function() {
		
		if ($(window).width( ) < 767) {
			// no child menu's? -> nothing to expand
			if ($(this).closest('.menu-item').find('.child-menu').length == 0) {
				return true;
			}

			var mi = $(this).closest('li.menu-item');
			var isOpen = mi.hasClass('menu-item-open');
			
			$(this).closest('div.menu-list').find('li.menu-item-open').removeClass('menu-item-open');
			
			if (isOpen == false) {
				mi.addClass('menu-item-open');
			}
			
			return false;
		}
	});

	
	
	applyWidgetFields(document.body);
	
	autoformat_fields( $('.main-content form') );
	
	focusFirstField( $('body.action-index table.list-response-table th, .autofocus-first-field th, .main-content form') );
	
	var toolbox = $('.page-header .toolbox');
	if (toolbox.length) {
		if (toolbox.find('.fa.fa-chevron-circle-left').attr('title') != '')
			toolbox.find('.fa.fa-chevron-circle-left').attr('title', toolbox_t('Back'));
		if (toolbox.find('.fa.fa-send').attr('title') != '')
			toolbox.find('.fa.fa-send').attr('title', toolbox_t('Send by mail'));
		if (toolbox.find('.fa.fa-print').attr('title') != '')
			toolbox.find('.fa.fa-print').attr('title', toolbox_t('Print'));
		if (toolbox.find('.fa.fa-save').attr('title') != '')
			toolbox.find('.fa.fa-save').attr('title', toolbox_t('Save'));
		if (toolbox.find('.fa.fa-plus').attr('title') != '')
			toolbox.find('.fa.fa-plus').attr('title', toolbox_t('Add'));
		if (toolbox.find('.fa.fa-cog').attr('title') == '')
			toolbox.find('.fa.fa-cog').attr('title', toolbox_t('Settings'));
	}
	
	$(document).ajaxSend(function(evt, xhr, opts) {
		// don't show progress on ping
		if (typeof opts != 'undefined' && opts.url) {
			if (opts.url.indexOf('/?m=base&c=ping') != -1)
				return;
			if (opts.url.indexOf('/?m=base&c=multiuser') != -1)
				return;
		}
		
		NProgress.start();
	});
	$(document).ajaxComplete(function() {
		NProgress.done();
	});

	
	setTimeout(function() {
		$('.alert.alert-success').not('.called-from-js').slideUp();
	}, 3500);
	
});



function applyWidgetFields(container) {
	
	// form locked? => remove submit-button & disable submit-event
	if ($(container).find('.object-locked').val() == '1') {
		$(container).find('.submit-container').remove();
		
		$(container).find('form').find('input, select, textarea').attr('disabled', 'disabled');
		$(container).find('form').find('input, select, textarea').css('color', '#000');
		$(container).find('form').find('input, select, textarea').css('background-color', '#fff');
		$(container).find('form').find('input, select, textarea').css('border-color', '#ccc');
		$(container).find('form').find('input, select, textarea').css('border-width', '1px');
		$(container).find('form').find('input, select, textarea').css('border-style', 'solid');

		$(container).find('.td-sortable').css('visibility', 'hidden');

		$(container).find('form').find('.row-delete').remove();
		$(container).find('form').find('.add-entry-container.action-box').remove();
		
		$(container).find('form').submit(function() {
			return false;
		});
	}

	
	
	if (typeof $(document).pickadate == 'function') {
		$(container).find('.input-pickadate').pickadate({
			format: 'dd-mm-yyyy',
			firstDay: 1
		});
	}
	if (typeof $(document).pickatime == 'function') {
		$(container).find('.input-pickatime').pickatime({
			format: 'HH:i',
			interval: 10
//			firstDay: 1
		});
	}

	if ($(document).datetimepicker) {
		$(container).find('.input-pickadate').each(function(index, node) {
			$(node).attr("autocomplete", "off");
			$( $(node).parent() ).css('position', 'relative');
			$( $(node).parent() ).css('overflow', 'visible');
			
			var pickerSettings = {
				locale: 'nl',
				format: 'DD-MM-YYYY',
				useCurrent: false
			};

			if ($(node).data('show-weeks') ) pickerSettings.calendarWeeks = true;

			$(node).datetimepicker( pickerSettings );
			
			$(node).on('dp.show', function() {
				$(this).data('date-value', this.value);
			});
			$(node).on('dp.hide', function() {
				var v = $(this).data('date-value');
				
				if (v != this.value)
					$(this).trigger('change');
			});
		});
		
		$(container).find('.input-pickatime').each(function(index, node) {
			$(node).attr("autocomplete", "off");
			$( $(node).parent() ).css('position', 'relative');
			$( $(node).parent() ).css('overflow', 'visible');
			$(node).datetimepicker({
				locale: 'nl',
				format: 'HH:mm',
				useCurrent: false
			});
		});
		

		$(container).find('.input-pickadatetime').each(function(index, node) {
			$(node).attr("autocomplete", "off");
			$( $(node).parent() ).css('position', 'relative');
			$( $(node).parent() ).css('overflow', 'visible');
			$(node).datetimepicker({
				locale: 'nl',
				format: 'DD-MM-YYYY HH:mm',
				sideBySide: true,
				useCurrent: false
			});
		});

	}

	$('.select-image-text-field-widget').each(function(index, node) {
		
		$(node).find('select').select2({
			templateResult: function(state) {
				
				var c = $('<span />');
				
				if ($(state.element).data('image')) {
					var img = $('<img />');
					img.attr('src', $(state.element).data('image'));
					img.attr('height', 20);
					img.attr('width', 'auto');
					c.append(img);
				}
				
				var spanText = $('<span class="text" />');
				spanText.text( state.text );
				c.append(spanText);
				
				return c;
			}
		});
	});
	
	if ($.fn.ColorPicker) {
		$('.color-picker-field-widget input[type=text]').each(function(index, node) {
			$(node).ColorPicker({
				onChange: function (hsb, hex, rgb) {
					$(this).val( hex );
					$(this).closest('.widget').find('.color-picker-color-sample').css('background-color', '#' + hex);
				}.bind(this)
			});
			$(node).change(function() {
				var hex = $(this).val();
				if (hex.match(/^#{0,1}[0-9a-fA-f]{3}$/) || hex.match(/^#{0,1}[0-9a-fA-f]{6}$/)) {
					if (hex.indexOf('#') === 0)
						hex = hex.substr(1);
					
					$(this).closest('.widget').find('.color-picker-color-sample').css('background-color', '#' + hex);
					$(this).val(hex);
					$(this).css('border-color', '');
				} else {
					$(this).css('border-color', '#f00');
				}
			});
		});
	}
	
	$(container).find('.dynamic-select-field-widget .select2-widget').each(function(index, node) {
		var url = $(node).data('url');
		
		$(node).select2({
			ajax: {
				url: appUrl(url),
				type: 'POST',
				data: function(params) {
					var d = {};
	
					d.name = params.term;
					
					return d;
				}
			}
		});
	});
	
	if (typeof $(document).tinymce == 'function') {
		$('.input-tinymce').tinymce({
			plugins: 'paste,code,link,fullpage,media,paste,table,textcolor,wordcount,contextmenu,colorpicker',
			paste_data_images: true
		});
	}
	
	$(container).find('.sortable-container').each(function(index, node) {
		var opts = {};
		
		if ($(node).find('.handler-sortable').length) {
			opts.handle = '.handler-sortable';
		}
		
		$(node).sortable(opts);
	});

	
	$(container).find('.stretch-to-bottom').each(function(index, node) {
		$(node).addClass('stretch-to-bottom-autoresize');
		
		// inside a tab-pane? => use boundary of tab-content holder
		var wh;
		if ($(node).closest('.tab-pane').length) {
			wh = $(window).height() - $(node).closest('.tab-content').offset().top;
		}
		else {
			wh = $(window).height() - $(node).offset().top;
		}
		
		$(node).css('height', wh);
	});
	$(window).resize(function() {
		$(container).find('.stretch-to-bottom-autoresize').each(function(index, node) {
			var h = $(window).height() - $(node).offset().top;
			$(node).css('height', h);
		});
	});
	
	
	$(container).find('input[type=text][autocomplete-url]').each(function(index, node) {
		$(node).autocomplete({
			delay: 500,
			appendTo: $(node).parent(),
			response: function() {
				NProgress.done();
			},
			source: $(node).attr('autocomplete-url')
		});

	});
	
	if ($('.js-menu-container').length)
		$(container).find('.js-menu-container > ul').menu();
	
	
	$('.image-selector-field').each(function(index, node) {
		$(node).find('li').click(function() {
			var val = $(this).data('value');
			$(this).closest('div.widget').find('input.hidden-value').val( val );
			
			$(this).closest('ul').find('li').removeClass('selected');
			$(this).addClass('selected');
			
			$(this).closest('div.widget').find('input.hidden-value').trigger( 'change' );
		});
		
	});
	
	// event for hooking modules
	$(window).trigger( 'applyWidgetFields' );

	handle_resetFieldButton( container );
}



function handle_resetFieldButton(objParent) {
	$(objParent).find('.reset-field-button').on('keyup keypress blur change', function evt_resetFieldButton(evt) {
		var v = $(this).val();
		if (v != '') {
			$(this).addClass('reset-field-cross');
		} else {
			$(this).removeClass('reset-field-cross');
		}
	});
	$(objParent).find('.reset-field-button').each(function(index, node) {
		if ($(node).val() != '') $(node).addClass('reset-field-cross');
	});
	$(objParent).find('.reset-field-button').mousemove(function(evt) {
		if ($(this).hasClass('reset-field-cross') && evt.offsetX >= $(this).width())
			$(this).css('cursor', 'pointer');
		else
			$(this).css('cursor', '');
	});
	$(objParent).find('.reset-field-button').click(function(evt) {
		if (!$(this).hasClass('reset-field-cross'))
			return;
		
		if ($(this).prop('readonly'))
			return;
		
		if (evt.offsetX >= $(this).width()) {
			$(this).val('');
			$(this).removeClass('reset-field-cross');
			
			$(this).trigger('change');
			
			if ($(this).hasClass('date-picker')) {
				$(this).trigger('dp.change');
				$(this).trigger('blur');
			}
		}
	});
}



// development-environment only
$(window).on('IndexTable-loaded-first-time', function(a, b) {
	if (typeof less != 'undefined') {
		less.pageLoadFinished.then(function() {
			// form-page?
			focusFirstField( $('body.action-index table.list-response-table th, .autofocus-first-field th, .main-content form') );
		});
	}
});
if (typeof less != 'undefined') {
	less.pageLoadFinished.then(function() {
		// form-page?
		focusFirstField( $('body.action-index table.list-response-table th, .autofocus-first-field th, .main-content form') );
	});
}




/**
 * event handling submit-form-link rechtsboven formulier-pagina's
 */
$(document).ready(function() {
	
	var submitForm = $('.page-header .toolbox .submit-form');
	
	if (submitForm.length == 0)
		return;
	var form = $('.main-content form');
	
	if (form.length != 1) {
		alert('Error: submit-form button found, but number of forms on current page != 1');
		return;
	}
	
	form.find('input[type=submit]').hide();
	
	submitForm.click(function() {
		console.log(form);
		form.submit();
	});
});



function navSideMenu_toggle() {
	if ($('body').hasClass('small-nav-side-menu')) {
		$('body').removeClass('small-nav-side-menu');
		$('.nav-side-menu').css('display', 'block');
		
		saveJsState('small-nav-side-menu', '0');
	} else {
		$('body').addClass('small-nav-side-menu');
		
		saveJsState('small-nav-side-menu', '1');
	}
}





function format_customername(record) {
	
	// company_id set, but no name? => return 'company-<ID>'
	var company_id = 0;
	if (typeof record.company_id != 'undefined') {
		company_id = parseInt(record.company_id);
		if (isNaN(company_id) == false && company_id > 0 && (typeof record.company_name == 'undefined' || record.company_name == '' || record.company_name == null)) {
			return 'company-' + record.company_id;
		}
	}
	
	
	if (typeof record.company_name != 'undefined' && record.company_name) {
		return record.company_name;
	} else {
		var t = '';
		
		if (typeof record.lastname != 'undefined' && record.lastname) {
			t += record.lastname;
		}
		if (typeof record.insert_lastname != 'undefined' && record.insert_lastname) {
			t += ', ' + record.insert_lastname;
		}
		
		if (typeof record.firstname != 'undefined' && record.firstname) {
			t += ' ' + record.firstname;
		}
		
		// empty name? => return 'person-<ID>'
		if (typeof record.person_id != 'undefined' && t == '') {
			if (record.person_id == '0') {
				return '';
			} else {
				return 'person-' + record.person_id;
			}
		}

		
		return t;
	}
	
}




function appUrl(u) {
	return appSettings.appRootUrl + u.substr(1);
}

function formpost(url, data, opts) {
	data = data || {};
	opts = opts || {};
	
	
	var frm = $('<form method="post" />');
	if (url != '') {
		frm.attr('action', appUrl(url));
	}
	
	if (opts.target) {
		frm.attr('target', opts.target);
	}
	
	var keys = Object.keys(data);
	for(var i in keys) {
		var key = keys[i];
		
		var inp = $('<input type="hidden" />');
		inp.attr('name', key);
		inp.val(data[key]);
		
		frm.append(inp);
	}
	
	var inpSubmit = $('<input type="submit" />');
	frm.append(inpSubmit);
	
	$(document.body).append(frm);
	frm.submit();
	
	inpSubmit.remove();
}


function focusFirstField(container) {
	var inputs = $(container).find('input[type=text], input[type=number], input[type=tel], input[type=email]');
	
	if (inputs.length) {
		
		// pickadate opens calendar on focus, which can be irritating
		if ($(inputs.get(0)).hasClass('input-pickadate') || $(inputs.get(0)).hasClass('input-pickadatetime') || $(inputs.get(0)).hasClass('input-pickatime'))
			return;
		
		// no-auto-focus class set on widget?
		if ($(inputs.get(0)).closest('div.widget.no-auto-focus').length > 0) {
			return;
		}
		
		inputs.get(0).focus();
	}
}



function serialize2object( container ) {
	var obj = {};
	
	$(container).find('input, select, textarea').each(function(index, node) {
		
		if (node.type == 'radio' && $(node).prop('checked') == false)
			return;
		
		if (node.type == 'checkbox' && $(node).prop('checked') == false)
			return;
		
		if (endsWith(node.name, '_submit'))
			return;
		
		if (node.name && node.name != '') {
			obj[node.name] = node.value;
		}
	});
	
	return obj;
}

function handle_deleteConfirmation() {
	$('a.delete').click( handle_deleteConfirmation_event );
}
function handle_deleteConfirmation_event(evt) {
	var me = this;
	
	
	var deleteText = toolbox_t('Are you sure to delete this record?');
	if ($(this).data('confirmationMessage')) {
		deleteText = $(this).data('confirmationMessage');
	} else if ($(this).data('description')) {
		deleteText = toolbox_t('Are you sure to delete this record:')+' "'+$(this).data('description')+'" ?';
	}
	
	
	showConfirmation(toolbox_t('Are you sure?'), deleteText, function() {
		window.location = $(me).attr('href');
	});
	
	evt.preventDefault();
	return false;
}

function confirmationClickHandler(obj, title, text) {
	
	showConfirmation(title, text, function() {
		window.open($(obj).attr('href'), '_self');
	});
	
	return false;
}


function endsWith(haystack, str) {
	var i = haystack.lastIndexOf(str);
	
	return i != -1 && i == haystack.length - str.length;
}



function showConfirmation(title, body, callback_ok) {
	
	var html = '<div class="confirmation-dialog modal fade" tabindex="-1" role="dialog">';
	html += '<div class="modal-dialog">';
	html += '    <div class="modal-content">';
	html += '      <div class="modal-header">';
	html += '        <h4 class="modal-title"></h4>';
	html += '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
	html += '      </div>';
	html += '      <div class="modal-body">';
//	html += '        <p>One fine body&hellip;</p>';
	html += '      </div>';
	html += '      <div class="modal-footer">';
	html += '        <button type="button" class="btn btn-default" data-bs-dismiss="modal">'+toolbox_t('Cancel')+'</button>';
	html += '        <button type="button" class="btn btn-primary">Ok</button>';
	html += '      </div>';
	html += '    </div>';	// <!-- /.modal-content -->
	html += '  </div>';		// <!-- /.modal-dialog -->
	html += '</div>';		// <!-- /.modal -->

	$('.confirmation-dialog').remove();
	
	var d = $(html);
	$(document.body).prepend(d);
	
	$('.confirmation-dialog .modal-title').html(title);
	$('.confirmation-dialog .modal-body').append(body);
	$('.confirmation-dialog .btn-primary').click(function() {
		var r = callback_ok();
		
		// don't close if 'false' is returned
		if (typeof r == 'boolean' && r === false)
			return;
		
		$('.confirmation-dialog').modal('hide');
	});

	var myModal = new bootstrap.Modal( $('.confirmation-dialog').get(0), {
		show: true,
		keyboard: true
	});

	myModal.show();

	$('.confirmation-dialog').on('shown.bs.modal', function() {
		$('.confirmation-dialog').find('input[type="text"], input[type="password"]').first().focus();
	});
}

function showAlert(title, body, callback_ok) {
	
	var html = '<div class="confirmation-dialog modal fade" tabindex="-1" role="dialog">';
	html += '<div class="modal-dialog">';
	html += '    <div class="modal-content">';
	html += '      <div class="modal-header">';
	html += '        <h4 class="modal-title"></h4>';
	html += '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
	html += '      </div>';
	html += '      <div class="modal-body">';
	html += '      </div>';
	html += '      <div class="modal-footer">';
	html += '        <button type="button" class="btn btn-primary">Ok</button>';
	html += '      </div>';
	html += '    </div>';	// <!-- /.modal-content -->
	html += '  </div>';		// <!-- /.modal-dialog -->
	html += '</div>';		// <!-- /.modal -->

	// remove old dialog = backdrop
	$('.confirmation-dialog').remove();
	$('.modal-backdrop').remove();
	
	var d = $(html);
	$(document.body).prepend(d);
	
	$('.confirmation-dialog .modal-title').html(title);
	$('.confirmation-dialog .modal-body').append(body);
	$('.confirmation-dialog .btn-primary').click(function() {
		if (callback_ok)
			callback_ok();
		
		$('.confirmation-dialog').modal('hide');
	});
	
	var myModal = new bootstrap.Modal( $('.confirmation-dialog').get(0), {
		show: true,
		keyboard: true
	});

	myModal.show();
}


function showInlineWarning(message, opts) {
	opts = opts ? opts : {};
	
	$('.js-inline-warning').remove();
	
	var html = $('<div  class="js-inline-warning alert alert-warning" />');
	html.append(message);
	
	$('.main-content').prepend(html);
	
	if (opts.timeout) {
		setTimeout(function() {
			$(html).slideUp(function() { $(this).remove(); });
		}, opts.timeout);
	}
}

function showInlineSecondary(message) {
	var html = $('<div  class="js-inline-notice alert alert alert-secondary" />');
	html.append(message);
	
	$('.main-content').prepend(html);
}


var showInfoByUrl_timeout = null;
var showInfoByUrl_xhr = null;
function showInfoByUrl(obj, url, opts) {
	opts = opts ? opts : {};
	
	if (showInfoByUrl_timeout) {
		clearTimeout(showInfoByUrl_timeout);
	}
	
	showInfoByUrl_timeout = setTimeout(function() {
		if (showInfoByUrl_xhr) {
			showInfoByUrl_xhr.abort();
		}
		
		showInfoByUrl_xhr = $.ajax({
			url: url,
			type: 'POST',
			data: opts.data ? opts.data : {},
			success: function(data, xhr, textStatus) {
				showInfo(obj, data, opts);
			}
		});
	}, opts.timeout ? opts.timeout : 1);
}

function hideInfoByUrl() {
	if (showInfoByUrl_timeout)
		clearTimeout(showInfoByUrl_timeout);
	if (showInfoByUrl_xhr)
		showInfoByUrl_xhr.abort();
	
	$('.show-info-container').remove();
}


function hideInfo() {
	hideInfoByUrl();
}

function showInfo(obj, html, opts) {
	opts = opts ? opts : {};
	
	hideInfo();
	
	var offset = $(obj).offset();
	
	var d = $('<div class="show-info-container" />');
	d.html( html );
	d.css('position', 'absolute');
	d.css('z-index', '99000');
	d.css('background-color', '#fff');
	d.css('padding', '5px 5px');
	d.css('box-shadow', '0px 0px 5px #000')
	d.css('white-space', 'nowrap');
	
	$(document.body).prepend(d);

	if (opts.top) {
		opts.top -= ($(d).outerHeight(true)+3);
		d.css('top', opts.top);
	} else {
		// align above
		d.css('top', offset.top - ($(d).outerHeight(true)+3));
	}
	
	// center
	if (opts.middle) {
		opts.middle -= $(d).outerWidth(true) / 2;
		d.css('left', opts.middle);
	} else 	if (opts.left) {
		d.css('left', opts.left);
	} else {
		var center = offset.left + ($(obj).outerWidth(true)/2);
		center = center - $(d).outerWidth(true) / 2;
		
		d.css('left', center);
	}
}


var show_popup_ajx = null;
function show_popup(url, opts) {
	// previous call active?
	if (show_popup_ajx != null) {
		return;
	}
	
	opts = opts || {};
	console.log(opts);
	
	hideInfo();
	
	show_popup_ajx = $.ajax({
		type: 'POST',
		url: url,
		data: opts.data,
		success: function(data, xhr, textStatus) {
			
			if ($('.popup-background').length == 0) {
				var bg = $('<div class="popup-element popup-background" />');
				bg.click(function() {
					close_popup();
				});
				$(document.body).append(bg);
			}
			
			$(document.body).addClass('popup-container-visible');
			
			var popupNo = 1;
			$('.popup-container').each(function(index, node) {
				if (parseInt($(node).data('popup-no')) >= popupNo) {
					popupNo = parseInt($(node).data('popup-no'))+1;
				}
			});
			
			var popup = $('<div class="popup-element popup-container" />');
			popup.addClass('popup-container-'+popupNo);
			popup.data('popup-no', popupNo);
			popup.html( data );
			
			$(document.body).append(popup);
			
			$(popup).find('.popup-close-link').click(function() { close_popup(); });
			
			autoformat_fields( popup );
			applyWidgetFields( popup );
			
			
			// popup indicator
			if ($('.popup-element.popup-tab').length == 0) {
				var popupTabs = $('<div class="popup-element popup-tab" />');
				$(document.body).append( popupTabs );
			}
			var pti = $('<div class="popup-tab-item" />');
			pti.text( $.trim($(popup).find('.page-header').text()) );
			pti.data('popup', popup.get(0));
			$('.popup-element.popup-tab').append( pti );
			
			
			
			
			$(window).trigger('popup-container-created', popup);
			
			focusFirstField( popup );
			
			if (opts.renderCallback)
				opts.renderCallback(popup, data, xhr, textStatus);
		},
		error: function(err) {
			alert('Error: ' + err);
		},
		complete: function() {
			show_popup_ajx = null;
		}
	});
}

$(document).keydown(function(evt) {
	if (evt.keyCode == 27) {
		close_popup();
	}
});

function close_popup() {
	// no popup? => skip
	if ($('.popup-element').length == 0) {
		return;
	}
	
	// remove only last opened
	var popupContainers = $('.popup-container');
	var lastPopupContainer = popupContainers.get( popupContainers.length - 1 );
	
	$('.popup-element.popup-tab .popup-tab-item').each(function(index, node) {
		if ($(node).data('popup') == lastPopupContainer) {
			$(node).remove();
		}
	});
	
	$(lastPopupContainer).remove();
	
	
	if ($('.popup-container').length == 0) {
		$('.popup-element').remove();
		$(document.body).removeClass('popup-container-visible');
		
		$(window).trigger('close-popup');
	}
}



/**
 * 
 * @param title       - title dialog
 * @param html        - content
 * @param callback_ok - function(objDialog) { ... }
 * 
 * opts: {
 * 		title: '',
 * 		html: '',
 * 		callback_ok: function() { ... },
 * 		width: ..,
 * 		height: ..,
 * }
 */
function showDialog(opts) {
	
	opts = opts || {};
	if (typeof opts.showCancelSave == 'undefined') opts.showCancelSave = true;
	
	closeDialog();
	
	// titlebar
	var titleBar = $('<div class="page-header" />');
	var titleText = $('<h1 />');
	if (opts.title)
		titleText.text(opts.title);
	
	var toolbox = $('<div class="toolbox" />');
	toolbox.append('<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link" />');
	toolbox.find('.popup-close-link').click(function() { closeDialog(); });
	
	titleBar.append(toolbox);
	titleBar.append(titleText);
	
	// content
	var content = $('<div class="pwdialog-content" />');
	content.html(opts.html);
	
	// cancel/save buttons
	if (opts.showCancelSave) {
		var btnCancel = $('<input type="button" class="btn-cancel" value="'+toolbox_t('Cancel')+'" />');
		btnCancel.click(function() { closeDialog(); });
		var btnOk = $('<input type="button" class="btn-save" value="'+toolbox_t('Save')+'" />');
		btnOk.click(function() {
			if (opts.callback_ok) {
				var objDialog = $('.pwdialog-container');
				var r = opts.callback_ok(objDialog);
				
				// don't close dialog if 'false' is returned
				if (typeof r == 'boolean' && r == false) {
					return;
				}
				
				closeDialog();
			}
		});
		var containerControls = $('<div class="pwdialog-controls" />');
		containerControls.append(btnCancel);
		containerControls.append(btnOk);
	}
	
	$(document.body).addClass('popup-container-visible');
	
	// container
	var container = $('<div class="popup-element popup-container" />');
	container.css('overflow', 'auto');
	container.append(titleBar);
	container.append(content)
	container.append(containerControls);
	
	applyWidgetFields( content );
	
	// background
	var bg = $('<div class="popup-element popup-background" />');
	
	bg.click(function() {
		close_popup();
	});
	
	
	
	
	$(document.body).prepend(container);
	$(document.body).prepend(bg);
	
	
	// set focus to first inputfield
	var inputFields = $(container).find('input[type="text"]');
	if (inputFields.length > 0) {
		inputFields.get(0).focus();
	}
	
	return container;
}

function closeDialog() {
	close_popup();
}


function show_user_message(msg, opts) {
	opts = opts ? opts : {};
	
	if (typeof opts.timeout == 'undefined') {
		opts.timeout = 2000;
	}
	
	var d = $('<div class="global-message alert alert-success called-from-js"><div class="msg" /></div>');
	
	d.find('.msg').text(msg);
	
	$('.main-content, .blank-main-content').prepend(d);
	
	setTimeout(function() {
		$(d).slideUp(function() {
			$(this).remove();
		});
	}, opts.timeout);
}

function show_user_warning(msg, opts) {
	opts = opts ? opts : {};
	
	if (typeof opts.timeout == 'undefined') {
		opts.timeout = 2000;
	}
	
	var d = $('<div class="global-message alert alert-warning called-from-js"><div class="msg" /></div>');
	
	d.find('.msg').text(msg);
	
	$('.main-content').prepend(d);
	
	setTimeout(function() {
		$(d).slideUp(function() {
			$(this).remove();
		});
	}, opts.timeout);
}


function show_user_error(msg) {
	var d = $('<div class="global-message alert alert-danger"><div class="msg" /></div>');
	
	d.find('.msg').text(msg);
	
	$('.main-content').prepend(d);
	
	setTimeout(function() {
		$(d).slideUp(function() {
			$(this).remove();
		});
	}, 2000);
}

function closeFullscreenPopup() {
	$('.confirmation-dialog, .popup-element.popup-background').remove();
	$(document.body).removeClass('popup-container-visible');
}

function fullscreenPopup(title, body) {
	closeFullscreenPopup();
	
	var html = '<div class="confirmation-dialog modal fade" tabindex="-1" role="dialog">';
	html += '<div class="modal-dialog">';
	html += '    <div class="modal-content">';
	html += '      <div class="modal-header">';
	html += '        <h4 class="modal-title"></h4>';
	html += '      </div>';
	html += '      <div class="modal-body">';
	html += '      </div>';
	html += '    </div>';	// <!-- /.modal-content -->
	html += '  </div>';		// <!-- /.modal-dialog -->
	html += '</div>';		// <!-- /.modal -->

	$('.confirmation-dialog').remove();
	
	var d = $(html);
	$(document.body).prepend(d);
	
	$(document.body).prepend('<div class="popup-element popup-background" />');
	
	$('.confirmation-dialog .modal-title').html(title);
	$('.confirmation-dialog .modal-body').append(body);

	var myModal = new bootstrap.Modal( $('.confirmation-dialog').get(0), {
		show: true,
		keyboard: true,
		backdrop: false
	});

	myModal.show();
	
}


function showContextPopup(content, opts) {
	$('.context-popup').remove();
	
	opts = opts || {};
	
	
	var p = $('<div class="context-popup" />');
	
	p.html(content);
	p.css('position', 'absolute');
	p.css('left', window.event.clientX);
	p.css('top', window.event.clientY);
	p.css('background-color', '#fff');
	p.css('padding', '3px 5px');
	p.css('border', '1px solid #aaa');
	
	if (opts.items) {
		for(var i in opts.items) {
			var item = opts.items[i];
			
			var itemContainer = $('<div />');
			var itemAnchor = $('<a href="javascript:void(0);" />');
			itemAnchor.text(item.text);
			itemAnchor.click(item.click);
			
			itemContainer.append(itemAnchor);
			
			p.append(itemContainer);
		}
	}
	
	
	$(document.body).append(p);
}



function autoformat_fields(container) {
	
	container.find('input[name=vat_number], input[name=iban], input[name=bic], input[name=zipcode]').change(function() {
		this.value = this.value.toUpperCase();
	});
	
	container.find('input[name=street], input[name=city], input[name=firstname], input[name=lastname]').change(function() {
		if (this.value.length >= 2) {
			this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);
		}
	});
	
	container.find('.autoformat-percentage').change(function() {
		this.value = format_percentage( this.value );
	});
	
}


function format_price(val, currency, opts) {
	
	opts = opts ? opts : {};
	if (!opts.thousands) opts.thousands = ' ';
	
	if (val == null) return '';
	
	var s = val.toString();
//	s = s.replace('.', ',');
	s = s.replace(',', '.');
	
	var d = strtodouble(s, 0);
	s = d.toFixed(2).toString();
	
	var pos_decimal = s.indexOf('.');
	
	var s2 = s.substr(pos_decimal);
	for(var x=1; x <= pos_decimal; x++) {
		if (s.charAt(0) == '-' && x == pos_decimal) {
			// negative number & end reached? => never add thousands-char
		}
		else if ((x-1) % 3 == 0 && x != 1) {
			s2 = opts.thousands + s2;
		}
		
		s2 = s.charAt(pos_decimal - x) + s2;
	}
	
	if (currency)
		s2 = appSettings.currency_symbol + ' ' + s2;
	
	s2 = s2.replace('- ', '-');
	var dotPos = s2.lastIndexOf('.');
	if (dotPos != -1) {
		s2 = s2.substr(0, dotPos) + ',' + s2.substr(dotPos+1);
	}
	
	return s2;
}

function format_percentage(val) {
	if (typeof val == 'undefined')
		val = '';
	
	var s = val.toString();
	
	var percentage = strtodouble( s.replace('.', ',') );
	
	var str = roundNumber(percentage, 2);
	
	return str + ' %';
}

function strtoint(str, default_val) {
	var s = str.replace(',', '.');
	s = s.replace(/[^\d-]/, '');
	
	s = $.trim(s);
	
	var i = parseInt(s);
	
	if (isNaN(i) && typeof(default_val) != 'undefined')
		return default_val;
	else
		return i;
}

function strtodouble(str, default_val) {
	if (str === null) return 0;
	
	var pow_negative = -1;
	
	if (str.indexOf('e-') != -1)
		pow_negative = str.substr(str.indexOf('e-')+2);
	
	s = str.replace(/[^\d\.\-,]/g, '');
	
	s = $.trim(s);
	
	if (s.indexOf(',') != -1 && s.indexOf('.') != -1) {
		if (s.indexOf(',') < s.indexOf('.'))
			s = s.replace(',', '');
		else
			s = s.replace('.', '');
	}
	
	if (s.indexOf(',') != -1)
		s = s.replace(',', '.');
	
	var d = parseFloat(s);
	
	if (pow_negative != -1) {
		d = d / Math.pow(10, pow_negative);
	}
	
	
	if (isNaN(d) && typeof(default_val) != 'undefined')
		return default_val;
	else
		return d;
}


function is_numeric(val) {
	if (typeof val == 'undefined')
		return false;
	
	if (typeof val == 'number')
		return true;
	
	if (typeof val == 'string') {
		return val.length && val.match(/^\d+$/) != null;
	}
	
	return false;
}



function roundNumber(number, decimals) {
	// round same way as php
	var n = strtodouble(number.toString());
	var i = Math.round(n * 100);
	i = i / 100;
	
	return i;
	
	// round same way as php (old)
//	var n = strtodouble(number.toString());
//	var i = parseInt(n * 1000);
//	i = i / 1000;
//	return i.toFixed(2);
	
	
	// 
//	n = n * Math.pow(10, decimals);
//	n = Math.round(n);
//	n = n / Math.pow(10, decimals);
//	
//	return n;
}

function format_number(number, opts) {
	var nr = roundNumber(number, 2);
	
	return format_price(number, false, opts);
}


function format_filesize(size) {
	
	size = parseInt(size);
	
	if (isNaN(size)) {
		return "";
	}
	
	
	if (size <= 1024) {
		return size + " b";
	}
	
	if (size <= 1024 * 1024) {
		return (parseInt(size / 1024.0 * 10) / 10) + " kb";
	}
	
	if (size <= 1024 * 1024 * 1024) {
		return (parseInt(size / (1024.0*1024.0) * 10) / 10) + " mb";
	}
	
	if (size<= 1024 * 1024 * 1024 * 1024) {
		return (parseInt(size / (1024.0*1024.0*1024.0) * 10) / 10) + " gb";
	}
	
	return (parseInt(size / (1024.0*1024.0*1024.0*1024.0) * 10) / 10) + " tb";
}


function validate_email(mail)  {
	// credits to https://www.w3resource.com/javascript/form/email-validation.php
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
		return true;
	} else {
		return false;
	}
}



function valid_date(value) {
	if (typeof value == 'undefined' || value === null) {
		return false;
	}

	if (value == '00-00-0000' || value == '0000-00-00')
		return false;
	if (value.match(/^\d{2}-\d{2}-\d{4}$/))
		return true;
	if (value.match(/^\d{4}-\d{2}-\d{2}$/))
		return true;

	return false;
}

function valid_datetime(value) {
	if (typeof value == 'undefined' || value === null) {
		return false;
	}
	
	if (value == '00-00-0000 00:00:00' || value == '0000-00-00  00:00:00')
		return false;
	if (value.match(/^\d{2}-\d{2}-\d{4} \d{2}:\d{2}:\d{2}$/))
		return true;
	if (value.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/))
		return true;

	return false;
}

function str2datetime(str) {
	if (valid_datetime(str) == false)
		return null;
	
	var dateTime = str.split(' ');
	
	var tokensYear = dateTime[0].split('-');
	var tokensTime = dateTime[1].split(':');
	
	
	var y;
	var m;
	var d;
	var hour = tokensTime[0];
	var min = tokensTime[1];
	var sec = tokensTime[2];
	
	if (tokensYear[0].length == 4) {
		y = parseInt(tokensYear[0]);
		m = parseInt(tokensYear[1]);
		d = parseInt(tokensYear[2]);
	} else {
		y = parseInt(tokensYear[2]);
		m = parseInt(tokensYear[1]);
		d = parseInt(tokensYear[0]);
	}
	
	return new Date(y, m-1, d, hour, min, sec);
}

function str2date(str) {
	if (valid_date(str)) {
		// perfecto
	} else if (valid_datetime(str)) {
		var yearTime = str.split(' ');
		str = yearTime[0];
	} else {
		return null;
	}
	
	var tokens = str.split('-');
	
	if (tokens[0].length == 4) {
		var y = parseInt(tokens[0]);
		var m = parseInt(tokens[1]);
		var d = parseInt(tokens[2]);
		
		return new Date(y, m-1, d);
	} else {
		var y = parseInt(tokens[2]);
		var m = parseInt(tokens[1]);
		var d = parseInt(tokens[0]);
		
		return new Date(y, m-1, d);
	}
}

function previous_month(date, no) {
	return next_month(date, no*-1);
}

function next_month(date, no) {
	if (typeof date == 'string') {
		date = str2date( date );
	}

	var calcDate = new Date( date.getFullYear(), date.getMonth() + no, 15, 12 );

	var dim = days_in_month( date );
	var calcDim = days_in_month( calcDate );
	
	if (date.getDate() == dim || date.getDate() > calcDim) {
		calcDate.setDate( calcDim );
	} else {
		calcDate.setDate( date.getDate() );
	}
	
	return format_date( calcDate );
}

function days_in_month( date ) { 
	return new Date(date.getFullYear(), date.getMonth()+1, 0, 12).getDate(); 
}

function days_between(d1, d2) {
	if (typeof d1 == 'string') {
		d1 = str2date( d1 );
	}
	if (typeof d2 == 'string') {
		d2 = str2date( d2 );
	}

	var msinday = 1000 * 60 * 60 * 24;

	var s = Date.UTC(d1.getFullYear(), d1.getMonth(), d1.getDate());
	var e = Date.UTC(d2.getFullYear(), d2.getMonth(), d2.getDate());

	return (e - s) / msinday;
}

function months_between(d1, d2) {
	if (typeof d1 == 'string') {
		d1 = str2date( d1 );
	}
	if (typeof d2 == 'string') {
		d2 = str2date( d2 );
	}
	
	var months = (d2.getFullYear() - d1.getFullYear()) * 12;
	
	months = months + (d2.getMonth() - d1.getMonth());
	
	return months;
}


function next_day(date, no) {
	if (typeof no == 'undefined') no = 1;
	
	if (typeof date == 'string') {
		date = str2date( date );
	}

	var c = new Date( date.getFullYear(), date.getMonth(), date.getDate()+no, 12);

	return format_date( c );
}

function date2number(date) {
	if (typeof date == 'string') {
		date = str2date( date );
	}
	
	// year
	var year = date.getFullYear();
	
	// month
	var month = '';
	if (date.getMonth() < 9) {					// month = 0-11
		month = '0' + (date.getMonth()+1);
	} else {
		month = (date.getMonth()+1);
	}
	
	// day
	var day = '';
	if (date.getDate() < 10) {
		day = '0' + date.getDate();
	} else {
		day = date.getDate();
	}

	return parseInt( year + '' + month + '' + day );
}


function format_date(date, opts) {
	// only Date-objects can be formatted
	if ((date instanceof Date) == false) {
		console.error('format_date(), invalid object: ' + date);
		return '';
	}

	var t = '';
	
	opts = opts ? opts : {};
	
	// year
	var year = t + date.getFullYear();
	
	// month
	var month = '';
	if (date.getMonth() < 9) {					// month = 0-11
		month = '0' + (date.getMonth()+1);
	} else {
		month = (date.getMonth()+1);
	}
	
	// day
	var day = '';
	if (date.getDate() < 10) {
		day = '0' + date.getDate();
	} else {
		day = date.getDate();
	}
	
	if (isNaN(day) || isNaN(month) || isNaN(year))
		return '';
	
	if (opts.dmy) {
		return day + '-' + month + '-' + year;
	} else if (opts.ymdnumeric) {
		var generatedEndDate = parseInt(year) * 10000;
		generatedEndDate = generatedEndDate + (parseInt(month)*100);
		generatedEndDate = generatedEndDate + parseInt(day);
		return generatedEndDate;
	} else {
		return year + '-' + month + '-' + day;
	}
}


function format_datetime(date, opts) {
	// only Date-objects can be formatted
	if ((date instanceof Date) == false) {
		console.error('format_datetime(), invalid object: ' + date);
		return '';
	}
	
	opts = opts ? opts : {};
	if (typeof opts.skipSeconds == 'undefined') opts.skipSeconds = false;
	
	var t = '';
	
	// day
	if (date.getDate() < 10) {
		t = t + '0' + date.getDate();
	} else {
		t = t + date.getDate();
	}
	
	// month
	t += '-';
	if (date.getMonth() < 9) {					// month = 0-11
		t = t + '0' + (date.getMonth()+1);
	} else {
		t = t + (date.getMonth()+1);
	}
	
	// year
	t += '-';
	t = t + date.getFullYear();
	

	
	// hour
	t += ' ';
	if (date.getHours() < 10) {
		t = t + '0' + date.getHours();
	} else {
		t = t + date.getHours();
	}
	
	// minutes
	t += ':';
	if (date.getMinutes() < 10) {
		t = t + '0' + date.getMinutes();
	} else {
		t = t + date.getMinutes();
	}
	
	// seconds
	if (opts.skipSeconds == false) {
		t += ':';
		if (date.getSeconds() < 10) {
			t = t + '0' + date.getSeconds();
		} else {
			t = t + date.getSeconds();
		}
	}
	
	return t;
}

function format_datetime_minuts(date) {
	var d = format_datetime(date);
	return d.replace(/:\d\d$/, '');
}


function slugify(str) {
	str = str.toLowerCase();
	str = str.replace(/[^a-z0-9 \\-\\_]/g, '');
	str = str.replace(/\s+/g, '-');
	str = str.replace(/_/g, '-');
	
	str = str.replace(/-+/g, '-');
	str = str.replace(/^-+/, '');
	str = str.replace(/-+$/, '');
	
	return str;
}


function text2date(str) {
	var year = -1;
	var month = -1;
	var day = -1;
	
	var hour = 12;
	var minuts = 0;
	var seconds = 0;
	
	if (str == null)
		return null;
	
	if (str.match(/^\d{4}-\d{2}-\d{2}$/)) {
		var tokens = str.split('-');
		year  = parseInt(tokens[0]);
		month = parseInt(tokens[1])-1;
		day   = parseInt(tokens[2]);
	}
	if (str.match(/^\d{2}-\d{2}-\d{4}$/)) {
		var tokens = str.split('-');
		day   = parseInt(tokens[0]);
		month = parseInt(tokens[1])-1;
		year  = parseInt(tokens[2]);
	}
	if (str.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/)) {
		var yearTime = str.split(' ');
		
		var tokens = yearTime[0].split('-');
		year  = parseInt(tokens[0]);
		month = parseInt(tokens[1])-1;
		day   = parseInt(tokens[2]);

		var tokens2 = yearTime[1].split(':');
		hour    = parseInt(tokens2[0]);
		minuts  = parseInt(tokens2[1]);
		seconds = parseInt(tokens2[2]);
	}

	if (year != -1) {
		return new Date(year, month, day, hour, minuts, seconds, 0);
	} else {
		return new Date(str);
	}
}



function trim(o) {
	return o.replace(/^\s+/,'').replace(/\s+$/,'');
}

function getAjxParams() {
	var l = window.location.toString();
	
	if (l.indexOf('#') == -1)
		return '';
	
	l = l.substr(l.indexOf('#')+1);
	
	// set params in array
	var p = [];
	var tokens = l.split('\&');
	for(var x=0; x < tokens.length; x++) {
		var key = tokens[x].substr(0, tokens[x].indexOf('='));
		var data = unescape(tokens[x].substr(tokens[x].indexOf('=')+1));
		
		p[key] = data;
	}
	
	return p;
}
function getAjxParam(name) {
	var params = getAjxParams();
	
	return params[name];
}

function getUrlParams() {
	// get param string
	var l = window.location.toString();
	if (l.indexOf('?') == -1)
		return '';
	l = l.substr(l.indexOf('?')+1);
	if (l.indexOf('#') > -1)
		l = l.substr(0, l.indexOf('#'));
	
	// set params in array
	var p = [];
	var tokens = l.split('\&');
	for(var x=0; x < tokens.length; x++) {
		var key = tokens[x].substr(0, tokens[x].indexOf('='));
		var data = unescape(tokens[x].substr(tokens[x].indexOf('=')+1));
		
		p[key] = data;
	}
	
	return p;
}

function getUrlParam(name) {
	var params = getUrlParams();
	
	if (params[name])
		return params[name];
	else
		return null;
}


function isIE() {
	var ua = navigator.userAgent.toString();
	
	if (ua.indexOf('Windows') != -1 && ua.indexOf('Trident') != -1 && ua.indexOf('Edge') == -1 && ua.indexOf('Chrome') == -1) {
		return true;
	}
	
	return false;
}



function limit_text(str, maxlen, suffix) {
	if (!suffix)
		suffix = '...';
	
	if (str.length > maxlen + suffix.length) {
		str = str.substr(0, maxlen) + suffix;
	}
	
	return str;
}




/**
 * uuidv4() - generates uuid
 * 
 * credits @ https://stackoverflow.com/questions/105034/create-guid-uuid-in-javascript
 */
function uuidv4() {
	return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
		var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
		return v.toString(16);
	});
}


/**
 * fill_form() - fills a form by given object
 * 
 */
function fill_form(form, obj) {
	var form = $(form);
	
	form.get(0).reset();
	
	for(var i in obj) {
		var inp = form.find('[name=' + i + ']');
		
		// input is <select>? make sure <option> is visible
		if (inp.is('select')) {
			inp.find('option').each(function(index, node) {
				// remove 'selected'-tag. if set, chrome 84 does weird things when setting value on <select>
				$(node).removeAttr('selected', '');
				
				if ($(node).attr('value') == obj[i]) {
					$(node).css('display', '');
				}
			});
		}

		if (inp.is(':checkbox')) {
			var bln = false;
			if (obj[i] || obj[i] == 't' || obj[i] == 'T' || obj[i] == 'y' || obj[i] == 'Y' || obj[i] == '1') {
				bln = true;
			}
			
			inp.prop('checked', bln);
		} else if (inp.hasClass('input-pickadate')) {
			if (typeof obj[i] == 'string' && obj[i].match(/\d{4}-\d{2}-\d{2}$/)) {
				var toks = obj[i].split('-');
				
				inp.val(toks[2] + '-' + toks[1] + '-' + toks[0]);
				
				if (inp.prop('readonly')) {
					// don't add class
				} else {
					inp.addClass('reset-field-cross');
				}
			} else if (obj[i] == '') {
				inp.removeClass('reset-field-cross');
				inp.val('');
			} else {
				// ??
				inp.val(obj[i]);
			}
		} else {
			inp.val(obj[i]);
		}
	}
}



function set_select2_val(selector, val, text) {
	var opt = $('<option />').val( val ).text( text );
	$( selector ).html( opt );
	$( selector ).val( val ).trigger('change');
}



function clickableRow_Click( objRow ) {
	var evt = window.event;
	
	if ($(evt.target).hasClass('no-click') || $(evt.target).closest('.no-click').length > 0) {
		return;
	}
	
	if ($(objRow).find('a.anch-edit').length) {
		window.location = $(objRow).find('a.anch-edit').attr('href');
	}
	else if ($(objRow).find('a.fa-pencil').length) {
		window.location = $(objRow).find('a.fa-pencil').attr('href');
	}
	else {
		// TODO: report error?
		alert('No edit-link found. Please report');
	}
	
}



function link_input2text(src, dst) {
	var obj1 = $(src);
	
	obj1.on('keyup change', function() {
		if (typeof dst == 'function') {
			dst( );
		} else {
			var t = '';
			if (this.nodeName == 'SELECT') {
				t = $(this).find('option:selected').text();
				t = $.trim( t );
			} else {
				t = this.value;
			}
			
			$(dst).text( t );
		}
	});
}


function saveJsState(key, val) {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=base&c=js/state'),
		data: {
			a: 'save',
			key: key,
			value: val
		},
		success: function(data, xhr, textStatus) {
			
		},
		error: function(xhr, textStatus, errorThrown) {
			
		}
	});
}

function getJsState(key, callback) {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=base&c=js/state'),
		data: {
			a: 'get',
			key: key
		},
		success: function(data, xhr, textStatus) {
			if (data.success) {
				callback( data.value );
			} else {
				callback( null );
			}
		},
		error: function(xhr, textStatus, errorThrown) {
			callback( null );
		}
	});
}




