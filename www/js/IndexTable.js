/**
 * 
 */


function IndexTable( container, opts ) {
	
	this.opts = opts ? opts : {};
	
	this.table = null;
	this.container    = container;
	this.connectorUrl = '';
	this.columns      = [ ];
	
	// no paging & autoload next entries when end is reached?
	if (typeof this.opts.autoloadNext == 'undefined')
		this.opts.autoloadNext = false;
	
	this.ajaxLoadRequest = null;
	this.listResponse = null;					// just contains listResponse-structure
	this.lastResponse = null;					// contains complete last json-response
	
	this.pageNo = 1;
	this.loading = false;
	this.firstLoadCompleted = false;
	this.sortField = null;
	this.sortFieldDirection = null;
	
	this.callback_rowClick = null;
	this.callback_rowDblclick = null;
	
	this.callback_renderRow = null;		// callback after '<tr/>' row is rendered, signature: function(listResponseObject, $(tr));
	
	this.callback_selectColumnsPopup = null;
	
	this.urlOnUnload = '?s'; 
	
	this.callback_filterChanged = null;
	
	this.sortable = false;
	this.callback_sortUpdate = null;
	
	this.callback_renderRows = null;
	
	
	/**
	 * called before this.load()-ajax call is made
	 * 
	 * @param searchOpts - hash with search parameters
	 */
	this.callback_pre_load = null;

	this.init = function() {
		var me = this;
		
		$(this.container).data('IndexTable', this);

		if (!this.opts.autoloadNext) {
			$(window).on('unload', function() {
				var fields = serialize2object(me.opts.searchContainer ? me.opts.searchContainer : me.container);
				fields.pageNo = me.pageNo;
				
				var url = window.location.toString();
				if (endsWith(url, '?s') == false && endsWith(url, '&s') == false) {
					if (url.indexOf('?') != -1) url += '&s';
					else url += '?s';
				}
				
				history.replaceState({
					indexTable : fields,
					sortField: me.sortField
				}, 'page', url);
			});
		}
		
		
		if (this.opts.autoloadNext) {
			$(this.container).css('height', this.opts.tableHeight);
			$(this.container).css('overflow-y', 'auto');
		
			$(this.container).scroll(function(evt) {
				var t = $(this).scrollTop();
				
				var posBottom = $(this).height() + t;
				var tableHeight = $(me.table).height();
				
				if (tableHeight - posBottom < 50) {
					me.loadMore();
				}
				
				if (me.opts.fixedHeader) {
					$(this).find('table thead').css('transform', 'translateY('+t+'px)');
				}
			});
		}
		
	};

	this.restoreState = function() {
		if (history.state && history.state.indexTable) {
			for(var i in history.state.indexTable) {
				var obj = $(this.container).find('[name=' + i + ']');

				
				if (obj.length > 0 && obj.get(0).type) {
					obj.val([ history.state.indexTable[i] ]);
				} else {
					obj.val( history.state.indexTable[i] );
				}
			}
			
			if (history.state.indexTable.pageNo) {
				this.pageNo = history.state.indexTable.pageNo;
			}
		}
		if (history.state && history.state.sortField && history.state.sortField != null) {
			this.sortField = history.state.sortField;
		}
	}


	this.setConnectorUrl = function(url) {
		this.connectorUrl = appUrl(url);
	};

	this.addColumn = function(opts) {
		if (!opts.pos) {
			opts.pos = this.columns.length * 10;
		}

		this.columns.push(opts);
	};
	
	this.insertColumn = function(opts, pos) {
		opts.pos = pos;
		this.columns.push(opts);
		
		this.columns.sort(function(o1, o2) { return o1.pos - o2.pos });
	};
	
	/**
	 * load() - (re)load table
	 * 			opts.force = force reload
	 */
	this.load = function( opts ) {
		opts = opts ? opts : { };
		opts.force = opts.force ? true : false;
		
		// reset IndexTable? (used for queries with search-options..)
		if (opts.reset) {
			if (this.ajaxLoadRequest) {
				this.ajaxLoadRequest.abort();
			}
			this.loading = false;
			this.listResponse = null;
			this.pageNo = 1;
			$(this.table).find('tbody').empty();
			$(this.container).scrollTop(0);
		}
		
		if (this.loading == true) return;
		
		this.loading = true;
		$(this.container).find('.load-more').remove();
		
		
		if (opts.force == false && this.opts.autoloadNext && this.listResponse != null && this.listResponse.objects.length == 0) {
			this.loading = false;
			return;
		}
		
		if (this.listResponse == null) {
			this.render();
		}
		
		var me = this;

		var searchOpts = { };

		// searchopts
		searchOpts = serialize2object(this.opts.searchContainer ? this.opts.searchContainer : this.container);

		searchOpts['pageNo'] = this.pageNo - 1;
		
		if (this.sortField) {
			searchOpts['sortField'] = this.sortField;
		}
		if (this.sortFieldDirection) {
			searchOpts['sortFieldDirection'] = this.sortFieldDirection;
		}
		
		if (this.opts.defaultSearchOpts) {
			for(var sok in this.opts.defaultSearchOpts) {
				searchOpts[ sok ] = this.opts.defaultSearchOpts[ sok ];
			}
		}
		
		console.log( searchOpts );
		
		if (this.callback_pre_load) {
			this.callback_pre_load(searchOpts);
		}
		
		// show loading indicator?
		if (this.opts.loadingIndicator) {
			$(this.table).find('tbody').remove();
			
			var tbodyLoading = $('<tbody class="loading-indicator"><tr><td colspan="'+this.columns.length+'" align="center"><img src="./images/ajax-loader-big.gif" /> Loading...</td></tr></tbody>');
			$(this.table).append( tbodyLoading );
		}
		
		this.ajaxLoadRequest = $.ajax({
			type : 'POST',
			url : this.connectorUrl,
			data : searchOpts,
			success : function(data, textStatus, xhr) {
				if (data.error) {
					showAlert('Error', 'Error: ' + data.message);
				}
				else {
					me.listResponse = data.listResponse;
					me.lastResponse = data;						// complete response
					console.log(me.listResponse);
					me.render();
					
					me.loading = false;
				}
				
				if (me.firstLoadCompleted == false) {
					me.firstLoadCompleted = true;
					$(window).trigger('IndexTable-loaded-first-time', me);
				}
			},
			error: function() {
				showAlert('Error', 'An error occured loading the data..');
			},
			complete: function() {
				me.loading = false;
			}
		});
	};
	
	this.loadMore = function() {
		if (this.loading) return;
		
		this.pageNo++;
		this.load();
	};
	

	this.setRowClick = function(callback) {
		this.callback_rowClick = callback;
	};

	this.setRowDblclick = function(callback) {
		this.callback_rowDblclick = callback;
	};

	this.setSortUpdate = function(callback) {
		this.sortable = true;
		this.callback_sortUpdate = callback;
	};
	
	this.setCallbackFilterChanged = function(callback) {
		this.callback_filterChanged = callback;
	};
	
	
	this.setCallbackRenderRows = function(callback) {
		this.callback_renderRows = callback;
	};
	
	this.setCallbackRenderRow = function(callback) {
		// callback(obj, row);
		this.callback_renderRow = callback;
	};
	
	
	this.setCallbackRenderDone = function(callback) {
		this.callback_renderDone = callback;
	};
	
	this.setCallbackSelectColumnsPopup = function(callback) {
		this.callback_selectColumnsPopup = callback;
	};

	this.getSortField = function() { return this.sortField; };
	this.setSortField = function(field) { this.sortField = field; };


	this.render = function() {

		if (this.table == null) {
			this.table = $('<table class="list-response-table" />');
			if (this.opts.tableClass) {
				$(this.table).addClass( this.opts.tableClass );
			}
			$(this.table).data('IndexTable', this);

			$(this.container).append(this.table);

			this.renderHeader();
			
			this.restoreState();
		}


		this.renderRows();

		this.renderFooter();

		if (this.opts.autoloadNext == false) {
			this.renderPager();
		}
		
		if (this.opts.autoloadNext && this.listResponse && this.listResponse.objects.length >= this.listResponse.pageSize) {
			var me = this;
			var tr = $('<tr class="load-more"><td><a style="display: block;text-align: center;" href="javascript:void(0);">'+_('Load more')+'</a></td></tr>');
			$(tr).find('td').attr('colspan', this.columns.length);
			$(this.table).find('tbody:last-child').append(tr);
			$(tr).find('a').click(function() {
				console.log(me);
				me.loadMore();
			});
		}

		if (this.callback_renderDone) {
			this.callback_renderDone();
		}
	}

	/**
	 * pageNo - starts @ 1...
	 */
	this.setPage = function(pageNo) {
		this.pageNo = parseInt(pageNo);

		this.load();
	};


	this.filterChanged = function() {
		this.pageNo = 1;

		this.listResponse = null;
		
		this.load();
		
		if (this.callback_filterChanged)
			this.callback_filterChanged();
	};


	this.renderHeader = function() {

		var me = this;

		var thead = $('<thead />');

		thead.append($('<tr />'));

		var columnCount = this.columns.length;
		
		// first column contains handle for sorting
		if (this.sortable) {
			thead.find('tr').append('<td />');
		}

		for (var i in this.columns) {
			var col = this.columns[i];

			var td = $('<th />');
			
			td.data('col', col);
			td.addClass('th-' + slugify(col.fieldName));

			// last header column? => add 'x' to clear filters
			if (i == columnCount - 1) {
				var anch = $('<a href="javascript:void(0);" class="glyphicon glyphicon-remove" title="Reset filters" style="float: right;" />');
				anch.click(function() {
					$(me.table).find('thead input, thead select').val('');
					me.load();
				});
				td.append(anch);
			}

			if (typeof col.width != 'undefined') {
				$(td).css('width', col.width);
			}

			if (typeof col.css != 'undefined') {
				$(td).attr('style', col.css);
			}

			if (typeof col.align != 'undefined') {
				$(td).attr('align', col.align);
			}


			if (col.renderHeader) {
				var r = col.renderHeader(col);
				td.append(r);
			}
			// searchable? => add inputfield to header
			else if (col.searchable) {
				if (col.fieldType == 'text') {
					var t = $('<input type="text" />');
					t.attr('name', col.fieldName);
					t.attr('placeholder', col.fieldDescription);
					t.attr('autocomplete', 'off');

					if (typeof col.width != 'undefined') {
						t.css('width', col.width);
					}
					
					var f = getUrlParam(col.fieldName);
					if (f) {
						t.val(f);
					}

					td.append(t);
				}

				if (col.fieldType == 'boolean') {
					var s = $('<select />');
					s.attr('name', col.fieldName);
					s.append('<option class="label" value="">' + col.fieldDescription + '</option');
					s.append('<option value="1">Ja</option');
					s.append('<option value="0">Nee</option');

					if (typeof col.width != 'undefined') {
						s.css('width', col.width);
					}

					td.append(s);
				}
				if (col.fieldType == 'select') {
					var s = $('<select />');
					s.attr('name', col.fieldName);
					for(var i in col.filterOptions) {
						var fo = col.filterOptions[i];
						
						var opt = $('<option />');
						opt.val( fo.value );
						opt.text( fo.text );
						
						if (col.defaultValue && fo.value == col.defaultValue) {
							opt.prop('selected', true);
						}
						
						s.append(opt);
					}
					td.append(s);
				}
			} else {
				var fieldDescription = $('<span class="field-description" />');
				fieldDescription.append(col.fieldDescription);
				
				td.append(fieldDescription);
			}
			
			
			if (col.sortField) {
				$(td).addClass('header-field-sort');
			
				if ($(td).find('span.field-description').length) {
					$(td).find('span.field-description').wrap('<a href="javascript:void(0);" class="field-sort"></a>');
				}
				$(td).append(' <a href="javascript:void(0);" class="fa fa-sort field-sort"></a>');
				
				$(td).find('.field-sort').click(function() {
					var it = $(this).closest('table').data('IndexTable');
					var col = $(this).closest('th').data('col');
					
					var oldSortField = it.getSortField();
					
					if (typeof col.sortField == 'string') {
						it.setSortField( col.sortField );
					} else {
						if (it.sortField == col.sortField[0]) {
							it.setSortField( col.sortField[1] );
						} else {
							it.setSortField( col.sortField[0] );
						}
					}
					
					// inverse sortfield direction
					if (it.getSortField() == oldSortField) {
						it.sortFieldDirection = it.sortFieldDirection == 'DESC' ? 'ASC' : 'DESC';
					}
					else {
						it.sortFieldDirection = null;
					}
					
					it.pageNo = 1;
					it.load();
				});
			}
			
			
			if (col.infoText) {
				var infopopup = $('<div class="info-popup"><span class="fa fa-info"></span><div class="info-popup-text"></div></div>');
				infopopup.css('display', 'inline');
				infopopup.find('.info-popup-text').text( col.infoText );
				$(td).append( infopopup );
			}

			thead.find('tr').append(td);
		}

		$(this.table).find('thead').remove();

		$(this.table).append(thead);

		
		if ( isIE() ) {
			$(this.container).find('input[type=text]').keypress(function(evt) {
				if (evt.keyCode == 13) {
					$(this).trigger('change');
				}
			});
		}
		
		$(this.container).find('input, select').change(function() {
			me.filterChanged();
		});
		
		if (this.opts.searchContainer) {
			$(this.opts.searchContainer).find('input, select').change(function() {
				me.filterChanged();
			});
		}
	};


	this.renderRows = function() {
		var me = this;

		var tbody = $('<tbody />');

		// no listResponse yet? => skip
		if (!this.listResponse)
			return;

		if (this.listResponse.start == 0 && this.listResponse.objects.length == 0) {
			var td = $('<td class="no-results-found" />');
			td.attr('colspan', this.columns.length + (this.sortable?1:0) );
			td.text(toolbox_t('No results found'));

			var tr = $('<tr class="no-results" />');
			tr.append( td );
			
			tbody.append(tr);
		}
		
		
		// reload/table changed? => remove message
		if (this.listResponse.objects.length > 0) {
			$(tbody).find('tr.no-results').remove();
		}
		
		
		// loop through rows
		for (var cnt = 0; cnt < this.listResponse.objects.length; cnt++) {
			var obj = this.listResponse.objects[cnt];

			// render row
			var tr = $('<tr />');
			tr.data('record', obj);

			// row-click callback set?
			if (this.callback_rowClick) {
				tr.addClass('clickable');
				tr.click(function(evt) {
					// skip click-handling for action-cell
					if ($(evt.target).hasClass('actions') || $(evt.target).closest('td.actions').length > 0) {
						return;
					}
					
					if (($(evt.target).is('td') && $(evt.target).data('click-disabled'))
							|| $(evt.target).closest('td').data('click-disabled')) {
						return;
					}
					
					// Anchor clicked, or cell with anchor? => skip
					if ( evt.target.nodeName == 'A' || $(evt.target).find('A').length) {
						return;
					}


					me.callback_rowClick(this, evt);
				});
				
			}
			if (this.callback_rowDblclick) {
				tr.addClass('clickable');
				tr.on('dblclick', function(evt) {
					// skip click-handling for action-cell
					if ($(evt.target).hasClass('actions') || $(evt.target).closest('td.actions').length > 0) {
						return;
					}
					me.callback_rowDblclick(this, evt);
				});
			}
			
			if (this.sortable) {
				tr.append('<td class="td-sort"><span class="fa fa-sort sort-handle"></span></td>');
			}

			for (var colCnt = 0; colCnt < this.columns.length; colCnt++) {
				var col = this.columns[colCnt];

				// render column
				var td = $('<td class="td-'+slugify(col.fieldName)+'" />');

				if (col.fieldType == 'actions')
					td.addClass('actions');

				if (typeof col.css != 'undefined') {
					td.attr('style', col.css);
				}

				if (typeof col.align != 'undefined') {
					td.attr('align', col.align);
				}

				if (col.render) {
					var html = col.render(obj);
					td.append(html);
				} else {
					var fieldText = obj[col.fieldName];

					if (col.fieldType == 'boolean') {
						if (typeof fieldText == 'string')
							fieldText = (fieldText == '0' || fieldText == 'false') ? _('No') : _('Yes');
						else if (typeof fieldText == 'boolean')
							fieldText = fieldText ? _('Yes') : _('No');
						else if (typeof fieldText == 'number')
							fieldText = fieldText == 0 ? _('No') : _('Yes');
					}
					
					if (col.fieldType == 'date') {
						if (typeof fieldText == 'string' && fieldText.match(/^\d{4}-\d{2}-\d{2}$/)) {
							var dateTokens = fieldText.split('-');
							fieldText = dateTokens[2] + '-' + dateTokens[1] + '-' + dateTokens[0];
						} else if (fieldText !== null) {
							dt = str2date(fieldText);
							fieldText = format_date(dt, {dmy: true});
						}
					}

					if (col.fieldType == 'datetime' || col.fieldType == 'datetimesec') {
						if (typeof fieldText == 'undefined') {
							fieldText = '';
						} else {
							dt = text2date(fieldText);
							if (dt != null) {
								var skipSeconds = col.fieldType == 'datetime' ? true : false;
								if (typeof col.skipSeconds != 'undefined') {
									skipSeconds = col.skipSeconds;
								}
								fieldText = format_datetime(dt, {skipSeconds: skipSeconds});
							} else {
								fieldText = '';
							}
						}
					}

					if (col.fieldType == 'price' || col.fieldType == 'currency') {
						fieldText = format_price(fieldText, true, { thousands: '.' });
					}
					
					if (col.fieldType == 'filesize') {
						fieldText = format_filesize(fieldText);
					}

					if (col.fieldType == 'percentage') {
						fieldText = format_percentage(fieldText);
					}

					if (fieldText == null || fieldText == 'null')
						fieldText = '';

					if (col.fieldType == 'html') {
						td.html(fieldText);
					} else {
						td.text(fieldText);
					}
				}
				
				
				// mouseoverText-property support columns
				if (typeof col.mouseoverText != 'undefined') {
					$(td).data('col', col);
					
					if ($(td).find('span').length == 0) {
						var sp = $('<span />');
						sp.text( $(td).text() );
						$(td).empty();
						$(td).append( sp );
					}

					
					$(td).find('span').mouseover(function() {
						var rec = $(this).closest('tr').data('record');
						var col = $(this).closest('td').data('col');
						
						var txt = '';
						if (typeof col.mouseoverText == 'function') {
							txt = col.mouseoverText( rec );
						}
						else if (typeof col.mouseoverText == 'string') {
							txt = col.mouseoverText;
						}
						
						// no text? => skip
						txt = $.trim(txt);
						if (txt == '') {
							return;
						}
						
						
						showInfo(this, txt, {
							top: $(this).offset().top + $(this).height() + 10,
							left: $(this).offset().left + $(this).width() + 10
						});
					});
					$(td).mouseout(function(evt) {
						hideInfo();
					});
				}

				tr.append(td);
				
				if (this.callback_renderRow) {
					this.callback_renderRow(obj, tr);
				}
			}


			tbody.append(tr);
		}

		if (this.opts.autoloadNext) {
			if (this.listResponse.start == 0) {
				$(this.table).find('tbody').remove();
			}
			
		} else {
			$(this.table).find('tbody').remove();
		}
		$(this.table).append(tbody);
		
		if (this.sortable) {
			$(tbody).sortable({
				handle: '.sort-handle',
				update: function(evt) {
					me.callback_sortUpdate.bind(me)(evt);
				}
			})
		}
		
		// column-selection enabled? => update shown/hidden cols
		if (this.opts.columnSelection) {
			this.updateColumnselection();
		}
		
		if (this.callback_renderRows) {
			this.callback_renderRows();
		}
	};

	this.renderFooter = function() {};

	this.renderPager = function() {
		
		// no listResponse yet? => skip
		if (!this.listResponse)
			return;
		
		var pager = $('<div class="pager" />');

		var totalRecords = this.listResponse.rowCount;

		var pageCount = Math.ceil(totalRecords / this.listResponse.pageSize);
		var pageNo = (this.listResponse.start / this.listResponse.pageSize) + 1;
		var me = this;

		// 
		if (isFinite(pageCount) == false || isNaN(pageCount)) {
			return;
		}
		
		if (pageCount > 10) {
			var selectPager = $('<select name="pager" />');
			
			for (var x = 1; x <= pageCount; x++) {
				var opt = $('<option />');
				opt.val( x );
				opt.text( x + ' / ' + pageCount );
				
				if (x == pageNo)
					opt.attr('selected', 'selected');
				
				selectPager.append( opt );
			}
			
			
			pager.append('<a class="pager-back" href="javascript:void(0);">&lt;</a> ');
			pager.append( selectPager );
			pager.append(' <a class="pager-next" href="javascript:void(0);">&gt;</a>');
			
			if (pageNo == 1)
				pager.find('.pager-back').css('visibility', 'hidden');
			if (pageNo >= pageCount)
				pager.find('.pager-next').css('visibility', 'hidden');
			
			selectPager.change(function() {
				me.setPage($(this).val());
			});
			
			pager.find('.pager-back').click(function() {
				var p = me.pageNo - 1;
				if (p > 0)
					me.setPage(p);
			});
			
			
			pager.find('.pager-next').click(function() {
				var p = me.pageNo + 1;
				me.setPage(p);
			});
			
			
		} else {
			for (var x = 1; x <= pageCount; x++) {
				var anch = $('<a href="javascript:void(0);" />');
				anch.data('pageNo', x);
	
				if (x == pageNo) {
					anch.text('[' + x + ']');
				} else {
					anch.text(x);
				}
	
				anch.click(function() {
					me.setPage($(this).data('pageNo'));
				});
	
				pager.append(anch);
			}
		}

		// remove old
		$(this.container).find('.pager').remove();

		// add new
		$(this.container).append(pager);
	};
	
	
	this.createColumnSelection = function(opts) {
		var me = this;
		opts = opts ? opts : {};
		if (typeof opts.forcePopup == 'undefined') opts.forcePopup = false;
		
		if (opts.forcePopup == false && this.columns.length <= 30) {
			for(var i in this.columns) {
				var c = this.columns[i];
				
				if (c.fieldName == '' || c.fieldDescription == '') {
					continue;
				}
				
				var lbl = $('<label class="itcs-item" />');
				
				var inp = $('<input type="checkbox" />');
				inp.attr('name', 'columnSelection['+c.fieldName+']');
				inp.addClass('index-table-column-selector');
				inp.prop('checked', true);
				inp.data('column', c);
				lbl.append(inp);
				
				var spanDesc = $('<span />');
				spanDesc.text( c.fieldDescription );
				lbl.append(spanDesc);
				
				$(this.opts.columnSelection).append(lbl);
				
				inp.change(function() {
					me.updateColumnselection();
					me.saveColumnState();
				});
			}
		} else {
			var anchSelectColumns = $('<a href="javascript:void(0);" />');
			anchSelectColumns.text(_('Select columns'));
			anchSelectColumns.click(function() {
				this.selectColumnsPopup();
			}.bind(this));
			
			$(this.opts.columnSelection).append( '<hr/>' );
			$(this.opts.columnSelection).append( anchSelectColumns );
		}
	};
	
	
	this.selectColumnsPopup = function() {
		var container = $('<div class="index-table-column-selection" />');
		for(var i in this.columns) {
			var c = this.columns[i];
			
			if (c.fieldName == '' || c.fieldDescription == '') {
				continue;
			}
			
			var lbl = $('<label style="width: 300px;" />');
			
			var inp = $('<input type="checkbox" />');
			inp.attr('name', 'columnSelection['+c.fieldName+']');
			inp.addClass('index-table-column-selector');
			
			var isChecked = $('.th-'+slugify(c.fieldName)).is(':visible');
			
			inp.prop('checked', isChecked);
			inp.data('column', c);
			lbl.append(inp);
			
			var spanDesc = $('<span />');
			spanDesc.text( c.fieldDescription );
			lbl.append(spanDesc);
			
			$(container).append( lbl );
		}

		var dialog = showDialog({
			title: 'Column selection',
			html: container,
			callback_ok: function() {
				this.updateColumnselection();
				this.saveColumnState();
			}.bind(this)
		});
		
		dialog.find('.btn-save').val('Ok');
		
		if (this.callback_selectColumnsPopup) {
			this.callback_selectColumnsPopup(this, dialog);
		}
	};
	
	/**
	 * save shown/hidden column state
	 */
	this.saveColumnState = function() {
		if (!this.opts.tableName) {
			console.error('IndexTable.saveColumnState failed, this.opts.tableName not set');
			return;
		}

		var state = {};
		$('.index-table-column-selector').each(function(index, node) {
			var c = $(node).data('column');
			state[ c.fieldName ] = $(node).prop('checked');
		});
		
		saveJsState('indextable-enabled-columns-'+this.opts.tableName, state);
	};
	
	
	this.updateColumnselection = function() {
		$('.index-table-column-selector').each(function(index, node) {
			var c = $(node).data('column');
			
			if ($(this).prop('checked')) {
				c.hidden = false;
			} else {
				c.hidden = true;
			}
		});
		
		for(var i in this.columns) {
			var c = this.columns[i];
			if (c.hidden) {
				$('.th-'+slugify(c.fieldName)).hide();
				$('.td-'+slugify(c.fieldName)).hide();
			}
			else {
				$('.th-'+slugify(c.fieldName)).show();
				$('.td-'+slugify(c.fieldName)).show();
			}
		}

	};


	this.init();

}


