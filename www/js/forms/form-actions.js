


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
		$(actionsContainer).find('.list-form-widget, .list-edit-form-widget').each(function(index, node) {
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
	
	$(window).on('popup-container-created', function(evt, el) {
		handleFormActions( el );
	});

	
	$(window).trigger('form-actions-set');
}



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

	};
	
	
	this.setCallbackAddRecord = function(callback) { this.callback_addRecord = callback; }
	this.setCallbackDeleteRecord = function(callback) { this.callback_deleteRecord = callback; }
	
	
	this.addRecord = function(callback) {
		var me = this;
		
		$.ajax({
			url: appUrl('/?m=core&c=form/formListEdit'),
			type: 'POST',
			data: {
				formClass: $(me.container).find('.form-class').val()
			},
			success: function(data, xhr, textStatus) {
				var row = $(data);
				
				$(row).find('.row-delete').click(function() {
					me.deleteRow( $(this).closest('tr') );
				});
				
				$(me.container).find('tbody').append( row );
				
				applyWidgetFields( row );
				
				me.handleCounters();
				
				if (callback) {
					callback( row );
				}
				
				if (me.callback_addRecord) {
					me.callback_addRecord( row );
				}
				
				$(me).trigger('list-edit-add-record');
			}
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
	s.find('option').removeAttr('selected');
	
	var options = s.find('option');
	for(var i=0; i < options.length; i++) {
		if (options.get(i).value == selectedOption && i+1 < options.length) {
			s.val( options.get(i+1).value );
			s.trigger('change');
			break;
		}
	}
}



function monthField_prev_option(obj) {
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
function monthField_next_option(obj) {
	var s = $(obj).parent().find('select');
	
	if (s.prop('disabled')) return;
	
	var selectedOption = s.val();
	s.find('option').removeAttr('selected');
	
	var options = s.find('option');
	for(var i=0; i < options.length; i++) {
		if (options.get(i).value == selectedOption && i+1 < options.length) {
			s.val( options.get(i+1).value );
			s.trigger('change');
			break;
		}
	}
}






