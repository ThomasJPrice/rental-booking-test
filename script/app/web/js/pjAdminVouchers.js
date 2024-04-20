var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateVoucher = $("#frmCreateVoucher"),
			$frmUpdateCoupon = $("#frmUpdateCoupon"),
			datepicker = ($.fn.datepicker !== undefined),
			select2 = ($.fn.select2 !== undefined),
			multilang = ($.fn.multilang !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if ($('#datePickerOptions').length) {
        	$.fn.datepicker.dates['en'] = {
        		days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    		    daysMin: $('#datePickerOptions').data('days').split("_"),
    		    daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
    		    months: $('#datePickerOptions').data('months').split("_"),
    		    monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    		    format: $('#datePickerOptions').data('format'),
            	weekStart: parseInt($('#datePickerOptions').data('wstart'), 10),
    		};
        };
		
		if ($("#grid").length > 0 && datagrid) {
			function formatDiscountOptions(str, obj) {
				return obj.options_formated;
			}
			var buttonsOpts = [];
			var actionsOpts = [];
			
			if (myLabel.has_update)
			{
				buttonsOpts.push({type: "edit", url: "index.php?controller=pjAdminVouchers&action=pjActionUpdate&id={:id}"});
			}
			if (myLabel.has_delete)
			{
				buttonsOpts.push({type: "delete", url: "index.php?controller=pjAdminVouchers&action=pjActionDeleteVoucher&id={:id}"});
			}

			if (myLabel.has_delete_bulk) 
			{
				actionsOpts.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminVouchers&action=pjActionDeleteVoucherBulk", render: true, confirmation: myLabel.delete_confirmation});
			}
			if (myLabel.has_export)
			{
				actionsOpts.push({text: myLabel.exported, url: "index.php?controller=pjAdminVouchers&action=pjActionExportVoucher", ajax: false});
			}
			var $grid = $("#grid").datagrid({
				buttons: buttonsOpts,
				columns: [{text: myLabel.code, type: "text", sortable: true, editable: myLabel.has_update},
				          {text: myLabel.discount, type: "text", sortable: true, editable: false},
				          {text: myLabel.validity_from, type: "text", sortable: true, editable: false},
				          {text: myLabel.validity_to, type: "text", sortable: true, editable: false},
				          {text: myLabel.applied_on, type: "text", sortable: true, editable: false},
				          {text: myLabel.usage, type: "text", sortable: true, editable: false}],
				dataUrl: "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher" + pjGrid.queryString,
				dataType: "json",
				fields: ['code', 'discount', 'date_from', 'date_to', 'apply_on', 'used_count'],
				paginator: {
					actions: actionsOpts,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminVouchers&action=pjActionSaveVoucher&id={:id}",
				select: {
					field: "id",
					name: "record[]",
					cellClass: 'cell-width-2'
				}
			});
		}
		
		$(document).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher" + pjGrid.queryString, content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on("change", ".number", function (e) {
			var v = parseFloat(this.value);
		    if (isNaN(v)) {
		        this.value = '';
		    } else {
		    	this.value = v.toFixed(2);
		    }
		    if (parseFloat(this.value) >= 99999999999999.99) {
		    	this.value = 99999999999999.99;
		    }
		}).on("click", "#type", function (e) {
			var sign = $('option:selected', this).attr('data-sign');
			$('#icon_type').html(sign);
		}).on("click", ".btnAddVoucher, .pj-table-icon-edit", function (e) {
			var $url = $(this).attr("href");
			$.get($url).done(function (data) {
				$("#myModalVoucher").find(".modal-content").html(data);
				var $form = $("#myModalVoucher").find('form');
				$form.validate({
					rules: {
						"code": {
							required: true,
							remote: "index.php?controller=pjAdminVouchers&action=pjActionCheckCode&id=" + $form.find("input[name='id']").val()
						}
					}
				});
				if($(".touchspin3").length > 0)
				{
					$(".touchspin3").TouchSpin({
						min: 0,
						max: 4294967295,
						step: 1,
						verticalbuttons: true,
			            buttondown_class: 'btn btn-white',
			            buttonup_class: 'btn btn-white'
			        });
				}
				if ($(".select-item").length && select2) {
		            $(".select-item").select2({
		                allowClear: true,
		                dropdownParent: $("#myModalVoucher .modal-content")
		            });
		        };
				if ($('.datepick').length > 0) {
		        	$('.datepick').datepicker({autoclose: true}).on('changeDate', function (selected) {
	            		if($(this).attr('name') == 'date_from')
	            		{
	            			if($('input[name="date_to"]').length > 0)
	            			{
	            				var $to = $('input[name="date_to"]'),
	            					date_to_value = $to.datepicker("getUTCDate"),
	            					$minDate = new Date(selected.date.valueOf());
	            				if(date_to_value < selected.date)
	        					{
	            					$to.val($('input[name="date_from"]').val());
	        					}
	            				$to.datepicker('setStartDate', $minDate);
	            			}
	            		}
	            		
	            		if($(this).attr('name') == 'date_to')
	            		{
	            			if($('input[name="date_from"]').length > 0)
	            			{
	            				var $from = $('input[name="date_from"]'),
	            					date_from_value = $from.datepicker("getUTCDate"),
	            					$maxDate = new Date(selected.date.valueOf());
	            				if(date_from_value > selected.date)
	        					{
	            					$from.val($('input[name="date_to"]').val());
	        					}
	            				$from.datepicker('setEndDate', $maxDate);
	            			}
	            		}
	                });
	            }
				if($('.i-checks').length > 0)
				{
					$('.i-checks').iCheck({
			            checkboxClass: 'icheckbox_square-green',
			            radioClass: 'iradio_square-green'
			        });
				}
				$("#myModalVoucher").modal('show');
			});			
			return false;
		});		
		
		$("#myModalVoucher").on("click", ".btnSaveVoucher", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $form = $(this).closest('form');	    	
			if ($form.valid()) {
				var l = Ladda.create($(this).get(0));
				l.start();
				$.post($form.attr('action'), $form.serialize()).done(function (data) {
					var content = $grid.datagrid("option", "content");
					$grid.datagrid("load", "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher" + pjGrid.queryString, content.column, content.direction, content.page, content.rowCount);
					l.stop();
					$("#myModalVoucher").modal('hide');
				}).always(function () {
					l.stop();
				});
			}
			return false;
		});
	});
})(jQuery);