


jQuery(document).ready(function($) {
	handleFormActions(document.body);
});
function handleFormActions(actionsContainer) {
	var $ = jQuery;
	
	// prevent double handling. Shouldn't happen..
	if ($(actionsContainer).data('handleFormActions-called')) {
		console.error('second time handleFormActions called');
		return;
	}
	$(actionsContainer).data('handleFormActions-called', true);
	
	
	$(actionsContainer).find('.form-generator').submit(function() {
		$(actionsContainer).find('.list-form-widget, .list-edit-form-widget, list-edit-div-form-widget').each(function(index, node) {
			handleCounters( node );
		});
	});

	/**
	 * handle clicks on list-form-widget-table rows
	 */
	$(actionsContainer).find('.form-generator .list-form-widget .sublist tbody tr').click(function(evt) {
		// skip click-handling for action-cell
		if ($(evt.target).hasClass('actions') || $(evt.target).closest('td.actions').length > 0) {
			return;
		}
		
		if ($(this).closest('.form-generator').hasClass('form-readonly'))
			return;

		$(this).find('.row-edit').click();
	});
	
	$(actionsContainer).find('.form-generator .list-form-widget .sublist .actions .row-edit').click(function() {
		row_edit( $(this).closest('tr') );
	});
	
	$(actionsContainer).find('.form-generator .list-form-widget .sublist .actions .row-delete').click(function() {
		row_delete( $(this).closest('tr') );
	});
	
	$(actionsContainer).find('.form-generator .list-form-widget .add-record').click(function() {
		if ($(this).closest('.form-generator').hasClass('form-readonly'))
			return;
		
		var postData = { };

		// set overhead
		postData['formClass'] = $(this).closest('.list-form-widget').find('input.form-class').val();
//		console.log(postData);

		var listFormWidget = $(this).closest('.list-form-widget');
		
		// show_popup
		var opts = {};
		opts.data = postData;
		opts.renderCallback = function(popup, data, xhr, textStatus) {
			console.log('renderCallback');
			
			var saveButton = $(popup).find('input[type=submit]');
			saveButton.hide();
//			saveButton.attr('type', 'button');
			
			$(popup).find('.toolbox .popup-save-link').click(function() {
				validateForm(popup, function() {
					addRecord(listFormWidget, popup);
					close_popup();
				});
			});
			
			$(popup).find('form').submit(function() {
				$(popup).find('.toolbox .popup-save-link').click();
				return false;
			});
			
			// set focus
			focusFirstField(popup);
			
			$(window).trigger('list-form-widget-add-record-show');
		}
		
		show_popup(appUrl('/?m=core&c=form/formPopup'), opts);

	});
	
	function row_edit(tr) {
		var postData = { };
		
		// get fields
		$(tr).find('input[type=hidden]').each(function(index, node) {
			var fieldName = $(node).attr('name').replace(/.*\[/, '').replace(/\]$/, '');
			
			if (fieldName != '')
				postData[fieldName] = $(node).val();
		});
		
		// set overhead
		postData['formClass'] = $(tr).closest('.list-form-widget').find('input.form-class').val();
		
//		console.log( postData );
		
		// show_popup
		var opts = {};
		opts.data = postData;
		opts.renderCallback = function(popup, data, xhr, textStatus) {
			console.log('renderCallback');
			
			var saveButton = $(popup).find('input[type=submit]');
			saveButton.hide();
//			saveButton.attr('type', 'button');
			
			$(popup).find('.toolbox .popup-save-link').click(function() {
				validateForm(popup, function() {
					setPopupFields(tr, popup);
					close_popup();
				});
			});
			
			$(popup).find('form').submit(function() {
				$(popup).find('.toolbox .popup-save-link').click();
				return false;
			});
			
			// set focus
			focusFirstField(popup);
			
			$(window).trigger('list-form-widget-add-record-show');
		}
		
		show_popup(appUrl('/?m=core&c=form/formPopup'), opts);
	}
	
	function validateForm(popup, callback) {
		console.log('validate thing..');
		
		var formdata = $(popup).find('form').serializeArray();
		console.log(formdata);
		var data = {};
		$(formdata).each(function(index, obj){
		    data[obj.name] = obj.value;
		});
		
		data['formClass'] = $(popup).find('.form-generator').data('form-class');

		console.log( data );
		
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=core&c=form/formPopup&a=validate'),
			data: data,
			success: function(data, xhr, textStatus) {
				if (data.result) {
					callback();
				} else {
					if (data.errors) {
						setPopupFormErrors(popup, data.errors);
					} else {
						alert(data);
					}
				}
			}
		});
	}
	
	
	function setPopupFields(tr, popup) {
		// loop through popup fields
		$(popup).find('input, select, textarea').each(function(index, node) {
			
			// set hidden fields in main form
			$(tr).find('input[type=hidden]').each(function(index2, node2) {
				var fieldName = $(node2).attr('name').replace(/.*\[/, '').replace(/\]$/, '');
				
				if ($(node).attr('name') == fieldName) {
					
					if ($(node).is(':checkbox') || $(node).is(':radio')) {
						if ($(node).prop('checked')) {
							$(node2).val( $(node).val() );
						} else {
							$(node2).val( '' );
						}
					} else {
						$(node2).val( $(node).val() );
					}
				}
			});
			
			// set fields that are displayed
			$(tr).find('.field-value').each(function(index2, node2) {
				if ($(node2).data('fieldname') && $(node2).data('fieldname') == $(node).attr('name')) {
					$(node2).text( $(node).val() );
				}
			});
			
		});
		
		$(window).trigger('list-form-updated');
	}
	
	
	function handleCounters(container) {
		var rows = $(container).find('tbody tr');
		
		var listName = $(container).find('.method-object-list').val();
		
		rows.each(function(index, node) {
			$(node).find('input, select').each(function(index2, node2) {
				var elementName = node2.name;
				
				if (elementName.indexOf('[') != -1) {
					elementName = elementName.substr(elementName.lastIndexOf('[')+1);
					elementName = elementName.substr(0, elementName.indexOf(']'));
				}
				
				elementName = listName + '[' + index + '][' + elementName + ']';
				
				node2.name = elementName;
			});
		});
		
		$(this).trigger('handleCountersExecuted');
	}

	
	function addRecord(listFormWidget, popup) {
		
		var fields = $.parseJSON( $(listFormWidget).find('input.fields').val() );
		var publicFields = $.parseJSON( $(listFormWidget).find('input.public-fields').val() );
		
		var allFields = jQuery.unique(fields.concat(publicFields));
		
		var newTr = $('<tr />');
		
		if ($(listFormWidget).find('.sortable-container').length) {
			newTr.append('<td><span class="fa fa-sort handler-sortable"></span></td>');
		}
		
		for(var i=0; i < fields.length; i++) {
			var td = $('<td />');
			
			if (i == 0) {
				// add hidden fields
				var methodObjectList = $(listFormWidget).find('.method-object-list').val();
				
				for(var cnt in allFields) {
					var inputName = allFields[cnt];
					var inp = $('<input type="hidden" />');
					inp.attr('name', inputName);
					
					var obj = popup.find('[name='+allFields[cnt]+']');
					inp.val( obj.val() )
					
					$(td).append(inp);
				}
			}
			
			$(td).addClass(slugify(fields[i]));

			
			var s = $('<span class="field-value" />');
			s.data('fieldname', fields[i]);
			s.text( popup.find('[name='+fields[i]+']').val() );
			td.append(s);
			
			newTr.append(td);
		}
		
		var tdActions = $('<td class="actions" />');
		tdActions.append( '<a class="fa fa-pencil row-edit" href="javascript:void(0);"></a>' );
		tdActions.append( '<a class="fa fa-trash row-delete" href="javascript:void(0);"></a>' );
		tdActions.find('.row-edit').click(function() {
			row_edit( $(this).closest('tr') );
		});
		tdActions.find('.row-delete').click(function() {
			row_delete( $(this).closest('tr') );
		});
		
		newTr.append( tdActions );

		
		$(newTr).click(function(evt) {
			// skip click-handling for action-cell
			if ($(evt.target).hasClass('actions') || $(evt.target).closest('td.actions').length > 0) {
				return;
			}

			$(this).find('.row-edit').click();
		});

		
		$(listFormWidget).find('tbody').append(newTr);
		
		$(listFormWidget).find('tr.empty-list').remove();
		
		handleCounters( listFormWidget );
		
		$(window).trigger('list-form-updated');
	}
	
	
	function row_delete(tr) {
		var tb = $(this).closest('tbody');
		
		tr.remove();
	}

	

	/**
	 * list-edit-form-widget event handling
	 */
	$(actionsContainer).find('.widget.list-edit-form-widget').each(function(index, node) {
		var lefw = new ListEditFormWidget( node );
		node.lefw = lefw;
	});
	
	$(actionsContainer).find('.widget.list-edit-div-form-widget').each(function(index, node) {
		var lefw = new ListEditDivFormWidget( node );
		node.lefw = lefw;
	});
	
	$(window).trigger('form-actions-set');
}

// call handleFormActions when show_popup() is called
$(window).on('popup-container-created', function(evt, el) {
	handleFormActions( el );
});


function ListEditFormWidget(container) {
	
	this.container = container;
	
	this.callback_addRecord = null;
	this.callback_deleteRecord = null;
	
	this.init = function() {
		var me = this;
		
		$(this.container).find('.add-record').click(function() {
			me.addRecord();
		});
		
		$(this.container).find('.row-delete').click(function() {
			me.deleteRow( $(this).closest('tr') );
		});
		
		this.handleCounters( );
		
		this.handleMobileEvents();
		
		this.updateMobileView();
		
		// mobile view sortable
		$(this.container).find('.mobile-list-edit .mobile-list-edit-items').sortable({
			handle: '.draggable',
			stop: function() {
//				let trs = $(this.container).find('table.sublist tbody tr').get();
				let mobitems = $(this.container).find('.mobile-list-item .mobile-item-content');
				
				console.log( mobitems.length );
				
				mobitems.each(function(i, n) {
					let tr = $(n).data('tr');
					console.log(tr);
					$(this.container).find('table.sublist tbody').append( tr );
				}.bind(this));
				
			}.bind(this)
		});
		$(this.container).find('.mobile-list-edit .mobile-list-edit-items').disableSelection();

	};
	
	
	this.handleMobileEvents = function() {
		$(this.container).find('table.sublist tbody tr').each(function(index, node) {
			if ( $(node).data('mobile-events-set') )
				return;
			
			$(node).find('input, select').on('change', function() {
				this.updateMobileView();
			}.bind(this));
			
			
			$(node).data('mobile-events-set', true)
		}.bind(this));
	};
	
	
	this.updateMobileView = function() {
		
		let o = $(this.container).find('.mobile-list-edit');
		
		let tpl = o.attr('template');
		if (!tpl || tpl == '')
			return;
		
		let itemContainer = o.find('.mobile-list-edit-items');
		itemContainer.empty();
		
		$(this.container).find('table.sublist tbody tr').each(function(index, node) {
			let rec = {};
			
			$(node).find('input, select').each(function(i2, n2) {
				let n = $(n2).attr('name');
				n = n.replace(/.*\[(.*)\]$/, '$1');
				
				rec[n] = $(n2).val();
			});
			
			$(node).find('[widget-name]').each(function(i2, n2) {
				let n = $(n2).attr('widget-name');
				
				let t;
				if ($(n2).find('select').length > 0) {
					t = $(n2).find('select option:selected').text();
				} else {
					t = $(n2).text().trim();
				}
				
				n = n.replace('-', '_');
				
				rec[n + '_text'] = t;
			});
			
			
			console.log( rec );
			let t = new EzTemplate();
			t.setVar('record', rec);
			t.loadHtml( tpl );
			let div = t.build();
			
			let li = $('<div class="mobile-list-item" />');
			
			// drag
			li.append( '<div class="drag-sortable"><span class="fa fa-bars draggable"></span></div>' );
			
			// content
			li.append( '<div class="mobile-item-content"></div>' );
			
			// delete rec.
			li.append( '<div class="trash"><span class="fa fa-trash"></span></div>' );
			li.find('div.trash span').on('click', function( evt ) {
				let tr = $(evt.currentTarget).closest('.mobile-list-item').find('.mobile-item-content').data('tr');
				this.deleteRow( tr );
			}.bind(this));
			
			li.find('.mobile-item-content').append( div );
			
			li.find('.mobile-item-content').data( 'tr', node );
			li.find('.mobile-item-content').data( 'record', rec );
			li.find('.mobile-item-content').on('click', function( evt ) {
				this.popupEditRecord( evt.currentTarget );
			}.bind(this));
			
			itemContainer.append( li );
		}.bind(this));
		
	};
	
	this.popupEditRecord = function( obj ) {
		
		let tr = $(obj).data('tr');
		
		let widgets = new Array();
		$(tr).find('.widget').each(function(index, node) {
			let n = node.cloneNode(true);
			widgets.push( n );
		});
		
		let form = $('<div class="list-edit-widget-item-mobile-form" />');
		for(var i in widgets) {
			form.append( widgets[i] );
		}
		
//		console.log( form );
		
		showDialog({
			title: toolbox_t('Edit') + ' - ' + $(this.container).find('.mobile-list-edit-header').text(),
			html: form,
			callback_ok: function(dialog) {
				// update tr-fields
				$(dialog).find('input, select').each(function(index, node) {
					if ( $(node).attr('type') == 'hidden' )
						return;
					
					let n = $(node).attr('name');
					let v = $(node).val();
					
					let o = $(tr).find('[name="' + n + '"]');
					o.val( v );
					o.trigger( 'change' );
				});
				
				// update mobile-view
				this.updateMobileView();
			}.bind(this)
		});
		
//		console.log( obj );
	};
	
	
	
	
	this.setCallbackAddRecord = function(callback) { this.callback_addRecord = callback; }
	this.setCallbackDeleteRecord = function(callback) { this.callback_deleteRecord = callback; }
	
	
	this.addRecord = function(callback) {
		let formClassName = $(this.container).find('.form-class').val();
		
		$.ajax({
			url: appUrl('/?m=core&c=form/formListEdit'),
			type: 'POST',
			data: {
				formClass: formClassName
			},
			success: function(data, xhr, textStatus) {
				var row = $(data);
				
				$(row).find('.row-delete').click(function(evt) {
					this.deleteRow( $(evt.target).closest('tr') );
				}.bind(this));
				
				$(this.container).find('table.sublist > tbody').append( row );
				
				applyWidgetFields( row );
				
				this.handleCounters();
				
				if (callback) {
					callback( row );
				}
				
				if (this.callback_addRecord) {
					this.callback_addRecord( row );
				}
				
				$(this).trigger('list-edit-add-record');
				
				this.handleMobileEvents();
				
				this.updateMobileView();
				
				// mobile? open edit-popup
				if ($(window).width() <= 768) {
					let i = $(this.container).find('.mobile-list-edit-items .mobile-list-item').last();
					i.find('.mobile-item-content').click();
				}
			}.bind(this)
		});
	};
	
	
	this.deleteRow = function(node) {
		$(node).remove();
		
		if (this.callback_deleteRecord) {
			this.callback_deleteRecord( node );
		}
		
		this.updateMobileView();
	};
	
	// set element names for POST
	this.handleCounters = function () {
		var rows = $(this.container).find('tbody tr');
		
		var listName = $(this.container).find('.method-object-list').val();
		
		rows.each(function(index, node) {
			$(node).find('input, select, textarea').each(function(index2, node2) {
				var elementName = node2.name;
				
				if (elementName.indexOf('[') != -1) {
					elementName = elementName.substr(elementName.lastIndexOf('[')+1);
					elementName = elementName.substr(0, elementName.indexOf(']'));
				}
				
				
				elementName = listName + '[' + index + '][' + elementName + ']';
				
				node2.name = elementName;
			});
		});
		
		$(this.container).trigger('handleCountersExecuted');
	};
	
	
	this.init();
}



function ListEditDivFormWidget(container) {
	this.container = container;
	
	this.callback_addRecord = null;
	this.callback_deleteRecord = null;
	
	this.init = function() {
		var me = this;
		
		$(this.container).find('.add-record').click(function() {
			me.addRecord();
		});
		
		$(this.container).find('.row-delete').click(function() {
			me.deleteRow( $(this).closest('.list-edit-div-widget-record') );
		});
		
		this.handleCounters( );
	};
	
	
	
	this.setCallbackAddRecord = function(callback) { this.callback_addRecord = callback; }
	this.setCallbackDeleteRecord = function(callback) { this.callback_deleteRecord = callback; }
	
	
	this.addRecord = function(callback) {
		let formClassName = $(this.container).find('.form-class').val();
		
		$.ajax({
			url: appUrl('/?m=core&c=form/formListEdit'),
			type: 'POST',
			data: {
				formClass: formClassName
			},
			success: function(data, xhr, textStatus) {
				var row = $(data);
				
				$(row).find('.row-delete').click(function(evt) {
					this.deleteRow( $(evt.target).closest('.list-edit-div-widget-record') );
				}.bind(this));
				
				let rowContainer = $('<div class="list-edit-div-widget-record" />');
				rowContainer.append( row );
				
				$(this.container).find('.div-sublist > .list-edit-div-widget-record-container').append( rowContainer );
				
				applyWidgetFields( row );
				
				this.handleCounters();
				
				if (callback) {
					callback( row );
				}
				
				if (this.callback_addRecord) {
					this.callback_addRecord( row );
				}
				
				$(this).trigger('list-edit-add-record');
				
			}.bind(this)
		});
	};
	
	
	this.deleteRow = function(node) {
		$(node).remove();
		
		if (this.callback_deleteRecord) {
			this.callback_deleteRecord( node );
		}
	};
	
	// set element names for POST
	this.handleCounters = function () {
		var rows = $(this.container).find('.list-edit-div-record');
		
		var listName = $(this.container).find('.method-object-list').val();
		
		rows.each(function(index, node) {
			
			node.listCounter = index;
			
			$(node).find('input, select, textarea').each(function(index2, node2) {
				var elementName = node2.name;
				
				if (elementName.indexOf('[') != -1) {
					elementName = elementName.substr(elementName.lastIndexOf('[')+1);
					elementName = elementName.substr(0, elementName.indexOf(']'));
				}
				
				
				elementName = listName + '[' + index + '][' + elementName + ']';
				
				node2.name = elementName;
			});
		});
		
		$(this.container).trigger('handleCountersExecuted');
	};
	
	
	this.init();
}


function handleCountersForSelector( listName, selector ) {
	var rows = $( selector );
	
	rows.each(function(index, node) {
		$(node).find('input, select, textarea').each(function(index2, node2) {
			var elementName = node2.name;
			
			if (elementName.indexOf('[') != -1) {
				elementName = elementName.substr(elementName.lastIndexOf('[')+1);
				elementName = elementName.substr(0, elementName.indexOf(']'));
			}
			
			elementName = listName + '[' + index + '][' + elementName + ']';
			
			node2.name = elementName;
		});
	});
	
}



function setPopupFormErrors(container, errorList) {

	// remove old errors
	var pelc = $(container).find('.popup-error-list-container');
	if (pelc.length == 0) {
		var pelc = $('.popup-error-list-container');
	}
	pelc.html('');
	
	$(container).find('.widget.error').removeClass('error');
	
	// set new errors
	pelc.append('<div class="errors error-list"><ul></ul></div>');
	
	for(var i in errorList) {
		var err = errorList[i];
		
		var li = $('<li />');
		li.text(err.label + ' - ' + err.message);
		
		pelc.find('ul').append( li );
		
		$(container).find('input[name=' + err.field + ']').closest('.widget').addClass('error');
	}
	
}

function uploadFilesFieldDelete_Click(obj) {
	var container = $(obj).closest('.upload-files-field-widget');
	
	container.find('.hidden-id').val( $(obj).data('id') );
	
	$(container).closest('form').submit();
	
}



function weekField_prev_option(obj) {
	var s = $(obj).parent().find('select');
	
	if (s.prop('disabled')) return;
	
	var selectedOption = s.val();
	s.find('option').removeAttr('selected');
	
	var options = s.find('option');
	for(var i=0; i < options.length; i++) {
		if (options.get(i).value == selectedOption && i-1 >= 0) {
			s.val( options.get(i-1).value );
			s.trigger('change');
			break;
		}
	}
}
function weekField_next_option(obj) {
	var s = $(obj).parent().find('select');
	
	if (s.prop('disabled')) return;

	var selectedOption = s.val();
	
	var options = s.find('option');
	for(var i=0; i < options.length; i++) {
		if (options.get(i).value == selectedOption && i+1 < options.length) {
//			s.find('option').removeAttr('selected');
			
			s.val( options.get(i+1).value );
			s.trigger('change');
			break;
		}
	}
}

function yearField_prev_option(obj) {
	return weekField_prev_option(obj);
}

function yearField_next_option(obj) {
	return weekField_next_option(obj);
}



function monthField_prev_option(obj, opts) {
	opts = opts ? opts : {};
	
	let skip = 1;
	if (opts.skip) {
		skip = opts.skip;
	}
	
	var s = $(obj).parent().find('select');
	
	if (s.prop('disabled')) return;
	
	var selectedOption = s.val();
//	s.find('option').removeAttr('selected');
	
	var options = s.find('option');
	for(var i=0; i < options.length; i++) {
		if (options.get(i).value == selectedOption && i-skip >= 0) {
			s.val( options.get(i-skip).value );
			s.trigger('change');
			break;
		}
	}
	
	
}
function monthField_next_option(obj, opts) {
	opts = opts ? opts : {};
	
	let skip = 1;
	if (opts.skip) {
		skip = opts.skip;
	}
	
	
	var s = $(obj).parent().find('select');
	
	if (s.prop('disabled')) return;
	
	var selectedOption = s.val();
//	s.find('option').removeAttr('selected');
	
	var options = s.find('option');
	for(var i=0; i < options.length; i++) {
		if (options.get(i).value == selectedOption && i+skip < options.length) {
			console.log('vaag', (i+skip), options.length);
			s.val( options.get(i+skip).value );
			s.trigger('change');
			hit = true;
			break;
		}
	}
	
}






