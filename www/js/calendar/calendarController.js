/**
 * 
 */


function CalendarController(container, opts) {
	
	this.container = container;
	this.opts = opts || {};
	
	this.calendarId = null;
	
	this.currentYear = null;
	this.currentMonth = null;
	this.currentWeek = null;
	
	this.loadTimeout = null;
	
	this.viewMode = 'monthly';
	this.lastResponse = null;
	
	
	this.fetchRenderer = function() {
		
		if (this.viewMode == 'monthly') {
			renderer = new MonthViewRenderer(this);
			return renderer;
		}
		else if (this.viewMode == 'weekly') {
//			this.renderWeekly();
		}
		
		return null;
	}
	
	this.render = function() {
		var renderer = null;
		
		$(this.container).empty();
		
		renderer = this.fetchRenderer();
		
		renderer.render();
	}
	
	this.setYear = function(y) { this.currentYear = y; }
	this.setMonth = function(m) { this.currentMonth = m; }
	
	this.setCalendarId = function(id) { this.calendarId = id; }
	
	this.prevMonth = function() {
		var d = new Date(this.currentYear, this.currentMonth-2, 15);
		this.currentYear = d.getFullYear();
		this.currentMonth = d.getMonth() + 1;
		
		this.loadData();
	}
	
	this.nextMonth = function() {
		var d = new Date(this.currentYear, this.currentMonth, 15);
		this.currentYear = d.getFullYear();
		this.currentMonth = d.getMonth() + 1;
		
		this.loadData();
	}
	
	// calls _loadData but with a timeout of 100msec
	this.loadData = function() {
		var me = this;
		
		// clear old timeout?
		if (this.loadTimeout != null)
			clearTimeout(this.loadTimeout);
		
		this.loadTimeout = setTimeout(function() {
			me._loadData();
		}, 100);
	}
	
	this._loadData = function() {
		var me = this;
		
		var data = {
				calendarId: this.calendarId
		};
		
		// set startDate & endDate
		
		var dates = this.fetchRenderer().datesToRender();
		
		data.startDate = format_date(dates[0]);
		data.endDate = format_date(dates[dates.length-1]);
		
		
		// do request
		$.ajax({
			url: appUrl( '/?m=calendar&c=view&a=request_items' ),
			data: data,
			success: function(resp) {
				if (resp.error)
					return me.handleError(resp);
				
				me.lastResponse = resp;
				me.render();
			},
			error: function() {
				
			}
		});
	}
	
	
	this.editItem = function(item) {
		if ($('.context-popup').length > 0 || $('.item-editor').length > 0) {
			$('.context-popup, .item-editor').remove();
			return;
		}
		
		var me = this;
		
		var data = { };
		
		if (item.isnew) {
			// new item
			data.calendarId = this.calendarId;
			data.startDate = item.date;
			data.calendar_item_id = null;
		} else {
			// existing item
			data.startDate = item.date;
			data.calendar_item_id = item.id;
		}
		
		if (item.recurrent) {
			showContextPopup('', {
				items: [
				        { text: 'Reeks bewerken',     click: function() { me._editItem(data, false); } },
				        { text: 'Exemplaar bewerken', click: function() { me._editItem(data, true); } }
				]
			});
			
			return;
		}
		
		this._editItem(data, false);
	}
	
	this._editItem = function(data, editDerivedItem) {
		$('.context-popup').remove();
		
		var me = this;
		
		data.edit_derived_item = editDerivedItem ? 1 : 0;
		
		var opts = {};
		opts.data = data;
		opts.renderCallback = function() {
			$('.form-calendar-item-form').submit(function() {
				me.saveItem();
				
				return false;
			});
			
			$('.form-calendar-item-form').find('.submit-container').hide();
			
			
			$('.calendar-item-container .submit-calendar-item').click(function() { me.saveItem(); });
			$('.calendar-item-container .delete-calendar-item').click(function() { me.deleteItem(); });
			
			focusFirstField('.calendar-item-container');
		};
		
		show_popup(appUrl('/?m=calendar&c=view&a=edit'), opts);
		
	}
	
	
	this.deleteItem = function() {
		var me = this;
		var data = {};
		
		data.calendar_item_id = $('.form-calendar-item-form [name=calendar_item_id]').val();
		data.edit_derived_item= $('.form-calendar-item-form [name=edit_derived_item]').val();
		data.selected_date    = $('.form-calendar-item-form [name=selected_date]').val();
		
		
		$.ajax({
			type: 'POST',
			url: appUrl( '/?m=calendar&c=view&a=delete' ),
			data: data,
			success: function(data, textStatus, xhr) {
				close_popup();
				
				me._loadData();
			}
		});
	}
	
	this.saveItem = function() {
		var me = this;

		var data = $('form.form-calendar-item-form').serialize();
		
		$.ajax({
			type: 'POST',
			url: appUrl( '/?m=calendar&c=view&a=save' ),
			data: data,
			success: function(data, textStatus, xhr) {
				if (data.success) {
					// close & reload
					close_popup();
					
					me._loadData();
				} else {
					// show errors
					$('.calendar-item-container .error-container').empty();
					$('.calendar-item-container .error-container').addClass('errors');
					
					for(var i in data.errors) {
						var err = $('<div />');
						
						err.text( data.errors[i].label + ' - ' + data.errors[i].message );
						
						$('.calendar-item-container .error-container').append(err);
					}
					
				}
			},
			error: function(xhr, textStatus, errorThrown) {
				console.log(xhr);
				alert(xhr.responseText);
			}
		});
		
	};
	
	
	this.handleError = function(j) {
		alert(j.errorMessage);
	};
	
	this.init = function() {
		var me = this;
		
		this.currentYear = this.opts.year || new Date().getFullYear();
		this.currentMonth = this.opts.month || new Date().getMonth() + 1;		// 0 - 11
	}
	
	this.init();
}




function MonthViewRenderer(controller) {
	this.startWeekDay = 1;
	
	this.monthNames = {
		1: 'Januari',
		2: 'Februari',
		3: 'Maart',
		4: 'April',
		5: 'Mei',
		6: 'Juni',
		7: 'Juli',
		8: 'Augustus',
		9: 'September',
		10: 'Oktober',
		11: 'November',
		12: 'December'
	};
	
	this.dayNames = {
		0: 'Zondag',
		1: 'Maandag',
		2: 'Dinsdag',
		3: 'Woensdag',
		4: 'Donderdag',
		5: 'Vrijdag',
		6: 'Zaterdag'
	};
	
	this.controller = controller;
	this.container = controller.container;
	this.tbl = null;
	
	
	
	this.renderHeaderMonth = function(tbl) {
		var td = $('<td colspan="7" />');
		
		td.append('<a href="javascript:void(0);" onclick="controller.prevMonth()" class="fa fa-chevron-left"></a>');
		td.append(' ');
		td.append('<span class="month-name">' + this.monthNames[this.controller.currentMonth].toLowerCase() + '  ' + this.controller.currentYear + '</span>');
		td.append(' ');
		td.append('<a href="javascript:void(0);" onclick="controller.nextMonth()" class="fa fa-chevron-right"></a>');
		
		
		var thead = $('<thead id="monthNameContainer" />').append('<tr />');
		thead.find('tr').append(td);
		
		tbl.prepend(thead);
	}
	
	
	this.renderHeaderDays = function(tbl) {
		
		tbl.append('<thead id="dayNameContainer"><tr /></thead>');
		tbl.find('#dayNameContainer tr').append('<td class="week-no">Nr</td>');
		for(var x=0; x <= 6; x++) {
			daynr = (x + this.startWeekDay) % 7;
			tbl.find('#dayNameContainer tr').append('<td>' + this.dayNames[daynr] + '</td>');
		}
	}
	
	this.datesToRender = function() {
		var dates = new Array();
		
		var startDate = new Date(this.controller.currentYear, this.controller.currentMonth-1, 1, 12, 0, 0);
		var firstDayCell = (6+(startDate.getDay() - this.startWeekDay)+1) % 7;
		
		var endOfMonth = new Date(this.controller.currentYear, this.controller.currentMonth, 0);
		
		var colno = 0;
		var date;
		for(;colno < firstDayCell; colno++) {
			date = new Date(startDate.getFullYear(), startDate.getMonth(), (firstDayCell-colno-1)*-1, 12, 0, 0);
			
			dates.push( date );
		}
		
		
		for(var x=0; x < endOfMonth.getDate(); x++, colno++) {
			var daynr = x+1;
			
			date = new Date(endOfMonth.getFullYear(), endOfMonth.getMonth(), daynr);
			
			dates.push( date );
		}
		
		while(colno%7 != 0) {
			date = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 1, 12, 0, 0);
			dates.push(date);
			colno++;
		}
		
		return dates;
	}
	
	this.renderDayCells = function(tbl) {
		var me = this;
		
		tbl.append('<tbody id="dayContainer"><tr /></tbody>');
		
		var curDate = new Date();		// today
		var dates = this.datesToRender();
		
		for(var x=0, cellCount=0; x < dates.length; cellCount++) {
			var date = dates[x];
			var strDate = format_date(date);
			
			// new <tr/> row?
			if (cellCount%8 == 0 && cellCount != 0) {
				tbl.find('tbody[id=dayContainer]').append( '<tr/>' );
			}
			
			if (cellCount%8 == 0 || cellCount == 0) {
				var tdWeekNo = $('<td class="week-no" />');
				tdWeekNo.text( moment(date).week() );
				tbl.find('#dayContainer tr').last().append( tdWeekNo );
				continue;
			}
			
			// fetch current row
			var tr = tbl.find('#dayContainer tr').last();
			
			// add <td> cell
			var td = $('<td id="date-' + strDate + '" />');
			
			if (date.getFullYear() == curDate.getFullYear() && date.getMonth() == curDate.getMonth() && date.getDate() == curDate.getDate()) {
				td.addClass('current-date');
			}
			
			if (date.getFullYear() != curDate.getFullYear() || date.getMonth() != curDate.getMonth()) {
				td.addClass('date-other-month');
				// if (cellCount < 8)
				// 	td.addClass('date-previous-month');
				// else if (cellCount > 8)
				// 	td.addClass('date-next-month');
			}
			
			
			tr.append(td);
			td.data('date', strDate);
			td.attr('id', 'date-' + strDate);
			td.click(function(evt) {
				// ignore child targets
				if (evt.target != this) return;
				
				me.controller.editItem({
					isnew: true,
					date: $(this).data('date')
				});
			});
			td.html('<span class="daynr">' + date.getDate() + '</span>');

			x++;
		}
	}
	
	this.insertData = function() {
		var me = this;
		var lastResponse = this.controller.lastResponse;
		
		if (lastResponse == null)
			return;
		
		
		for(var i in lastResponse.events) {
			var i = lastResponse.events[i];
			
			var start = new Date(i.startDate);
			var end = new Date(i.endDate);
			
			var strDate = format_date(start);
			
			var item = $('<div class="item" />');
			
			if (i.cancelled)
				item.addClass('cancelled');
			
			var shortDesc = '';
			if (i.startTime || i.endTime) {
				if (i.startTime)
					shortDesc += i.startTime;
				if (i.endTime)
					shortDesc += ' - ' + i.endTime;
				
				shortDesc += ' ';
			}
			shortDesc += i.description;
			
			item.text(shortDesc)
			item.data('item', i);
			
			item.click(function(evt) {
				if (evt.target != this) return;
				
				var data = $(this).data('item');
				data.date = $(this).closest('td').data('date');
				
				me.controller.editItem( data );
			});
		
			$(this.container).find('#date-' + strDate).append(item);
		}
		
	}
	
	
	this.render = function() {
		this.tbl.empty();
		
		this.renderHeaderMonth(this.tbl);
		
		this.renderHeaderDays(this.tbl);
		
		this.renderDayCells(this.tbl);
		
		this.insertData();
	}
	
	
	this.init = function() {
		this.tbl = $('<table class="calendar calendar-month-view" />');
		
		$(this.container).append(this.tbl);
	};
	
	
	this.init();
}




