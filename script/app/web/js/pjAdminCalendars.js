var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var datagrid = ($.fn.datagrid !== undefined),
			validate = ($.fn.validate !== undefined),
			multilang = ($.fn.multilang !== undefined),
			select2 = ($.fn.select2 !== undefined),
			colorpicker = ($.fn.colorpicker !== undefined),
			$frmCreateCalendar = $("#frmCreateCalendar"),
			$frmUpdateCalendar = $("#frmUpdateCalendar"),
			$frmUpdatePayments = $("#frmUpdatePayments"),
			$frmUpdateLimits = $("#frmUpdateLimits"),
			$frmUpdateBookingOptions = $("#frmUpdateBookingOptions"),
			$frmFeeds = $('#frmFeeds'),
			$pjFeedWrapper = $('#pjFeedWrapper'),
			$frmExportReservations = $("#frmExportReservations"),
			$datepick = $(".datepick");

		if (multilang && myLabel.isFlagReady == 1) {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				tooltip: "",
				select: function (event, ui) {
					$("input[name='locale_id']").val(ui.index);					
				}
			});
		}
		
		if ($('.colorpicker-component').length > 0 && colorpicker) {
			$('.colorpicker-component').colorpicker();
		}
		
		if ($(".select-item").length && select2) {
            $(".select-item").select2({
                allowClear: true
            });
        };
        
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
        
        function myTinyMceDestroy() {
			if (window.tinymce === undefined) {
				return;
			}
			
			var iCnt = tinymce.editors.length;
			
			if (!iCnt) {
				return;
			}
			
			for (var i = 0; i < iCnt; i++) {
				tinymce.remove(tinymce.editors[i]);
			}
		}
		
		function myTinyMceInit(pSelector) {			
			if (window.tinymce === undefined) {
				return;
			}
			
			tinymce.init({
				relative_urls : false,
				remove_script_host : false,
				convert_urls : true,
				browser_spellcheck : true,
			    contextmenu: false,
			    selector: pSelector,
			    theme: "modern",
			    height: 480,
			    plugins: [
			         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
			         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			         "save table contextmenu directionality emoticons template paste textcolor"
			    ],
			    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
			    image_advtab: true,
			    menubar: "file edit insert view table tools",
			    setup: function (editor) {
			    	editor.on('change', function (e) {
			    		editor.editorManager.triggerSave();
			    	});
			    }
			});
		}

		if ($('.mceEditor').length > 0) {
			myTinyMceDestroy.call(null);
			myTinyMceInit.call(null, 'textarea.mceEditor');
        }
		
		function limitDatePickerTrigger()
		{
			var $tbody = $("#tblLimits tbody"),
				$datepick = $tbody.find(".datepick");
			if ($datepick.length > 0) {
	        	$datepick.datepicker({autoclose: true}).on('changeDate', function (selected) {
	        		var idx = $(this).attr('data-idx');
	        		var element_id = $(this).attr('id');
	        		if (parseInt(element_id.indexOf("date_from_"), 10) >= 0) {
		        		var $toElement = $("#date_to_" + idx);
	        			if($toElement.length > 0)
	        			{
	        				var $minDate = new Date(selected.date.valueOf()),
	        					date_to_value = $toElement.datepicker("getUTCDate");
	        				if(date_to_value < selected.date)
	    					{
	        					$toElement.val($(this).val());
	    					}
	        				$toElement.datepicker('setStartDate', $minDate);
	        			}
	        		}

	        		if (parseInt(element_id.indexOf("date_to_"), 10) >= 0) {
	        			var $fromElement = $("#date_from_" + idx);
	        			var $toElement = $("#date_to_" + idx);
	        			if($fromElement.length > 0 && $toElement.length > 0)
	        			{
	        				var $maxDate = new Date(selected.date.valueOf()),
	        					date_from_value = $fromElement.datepicker("getUTCDate");
	        				if(date_from_value > selected.date)
	    					{
	        					$fromElement.val($toElement.val());
	    					}
	        				$fromElement.datepicker('setEndDate', $maxDate);
	        			}
	        		}
	            });
	        }
		}
		
		if ($frmCreateCalendar.length > 0 && validate) {
			$frmCreateCalendar.validate({
				rules: {
					"uuid": {
						remote: "index.php?controller=pjAdminCalendars&action=pjActionCheckRefId"
					}
				},
				onkeyup: false
			});
		}
		
		if ($frmUpdateCalendar.length > 0 && validate) {
			$frmUpdateCalendar.validate({
				onkeyup: false
			});
		}
		
		if ($frmUpdateLimits.length > 0 && validate) {
			$frmUpdateLimits.validate({
				onkeyup: false,
				highlight: function(ele, errorClass, validClass) {
	            	var element = $(ele);
	            	element.parent().parent().addClass('has-error');
	            },
	            unhighlight: function(ele, errorClass, validClass) {
	            	var element = $(ele);
	            	element.parent().parent().removeClass('has-error').addClass('has-success');
	            }
			});
		}
		
		if ($frmUpdateBookingOptions.length > 0 && validate) {
			$frmUpdateBookingOptions.validate({
				onkeyup: false
			});
		}

		function formatTitle(val, obj) {
			if (myLabel.has_update)
			{
				return ['<a href="index.php?controller=pjAdmin&action=pjActionRedirect&nextController=pjAdminCalendars&nextAction=pjActionUpdate&calendar_id=', obj.id, '&nextParams=', encodeURIComponent("id=" + obj.id), '">', val, '</a>'].join("");	
			}else{
				return val;
			}
		}
		
		function onBeforeShow (obj) {
			if (parseInt(obj.id, 10) === pjGrid.currentCalendarId || parseInt(obj.id, 10) == 1) {
				return false;
			}
			return true;
		}
		
		function formatReservations(val, obj) {
			var tmp,
				arr = [];
			if (obj.latest_booking !== undefined && obj.latest_booking != null) {
				tmp = obj.latest_booking.split("~:~");
				if (tmp[1] != '') {
					if (myLabel.has_update_booking) {
						arr.push('<a href="index.php?controller=pjAdminReservations&action=pjActionUpdate&id='+tmp[0]+'">'+tmp[1]+'</a>');
					} else {
						arr.push('<a href="javascript:void(0);">'+tmp[1]+'</a>');
					}
				}
				if (tmp[2] != '') {
					arr.push('<a href="mailto:'+tmp[2]+'">'+tmp[2]+'</a>');
				}
				if (myLabel.has_update_booking) {
					arr.push('<a href="index.php?controller=pjAdminReservations&action=pjActionUpdate&id='+tmp[0]+'">'+tmp[3]+'</a>');
				} else {
					arr.push([tmp[3]]);
				}
			}
			return arr.join("<br />");
		}
		
		if ($("#grid").length > 0 && datagrid) {
			var buttonsOpts = [];
			var actionsOpts = [];
			
			if (myLabel.has_update)
			{
				buttonsOpts.push({type: "edit", url: "index.php?controller=pjAdmin&action=pjActionRedirect&nextController=pjAdminCalendars&nextAction=pjActionUpdate&calendar_id={:id}&nextParams=id={:id}&nextTab=calendar"});
			}
			if (myLabel.has_delete)
			{
				buttonsOpts.push({type: "delete", url: "index.php?controller=pjAdminCalendars&action=pjActionDeleteCalendar&id={:id}", beforeShow: onBeforeShow});
			}
			
			buttonsOpts.push({type: "menu", url: "#", text: '', items: [
              {text: myLabel.edit, url: "index.php?controller=pjAdmin&action=pjActionRedirect&nextController=pjAdminCalendars&nextAction=pjActionUpdate&calendar_id={:id}&nextParams=id={:id}&nextTab=calendar"}, 
              {text: myLabel.settings, url: "index.php?controller=pjAdmin&action=pjActionRedirect&nextController=pjAdminCalendars&nextAction=pjActionUpdate&calendar_id={:id}&nextParams=id={:id}&nextTab=general_settings"},
              {text: myLabel.prices, url: "index.php?controller=pjAdmin&action=pjActionRedirect&nextController=pjAdminCalendars&nextAction=pjActionUpdate&calendar_id={:id}&nextParams=id={:id}&nextTab=prices"},
            ]});

			if (myLabel.has_delete_bulk) 
			{
				actionsOpts.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminCalendars&action=pjActionDeleteCalendarBulk", render: true, confirmation: myLabel.delete_confirmation});
			}
			
			var $grid = $("#grid").datagrid({
				buttons: buttonsOpts,
				columns: [
				          {text: myLabel.calendar, type: "text", sortable: true, editable: false, renderer: formatTitle},
				          {text: myLabel.refid, type: "text", sortable: true, editable: false},
				          {text: myLabel.latest_reservation, type: "text", sortable: true, editable: false, renderer: formatReservations}
			          ],
				dataUrl: "index.php?controller=pjAdminCalendars&action=pjActionGetCalendar",
				dataType: "json",
				fields: ['name', 'uuid', 'latest_booking_created'],
				paginator: {
					actions: actionsOpts,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminCalendars&action=pjActionSaveCalendar&id={:id}",
				select: {
					field: "id",
					name: "record[]",
					cellClass: 'cell-width-2'
				}
			});
		}
		
		function formatDateTime (val, obj) {
			return [obj.date_from, obj.date_to].join("<br/>");
		}
		function formatNameEmail (val, obj) {
			return [obj.c_name, '<br/><a href="mailto:',obj.c_email,'">',obj.c_email,'</a>'].join("");
		}
		function formatStatus(val, obj) {
			if(val == 'Confirmed')
			{
				return '<div class="btn bg-confirmed btn-xs no-margin"><i class="fa fa-check"></i> ' + myLabel.confirmed + '</div>';
			}else if(val == 'Cancelled'){
				return '<div class="btn bg-cancelled btn-xs no-margin"><i class="fa fa-times"></i> ' + myLabel.cancelled + '</div>';
			}else if(val == 'Pending'){
				return '<div class="btn bg-pending btn-xs no-margin"><i class="fa fa-exclamation-triangle"></i> ' + myLabel.pending + '</div>';
			}
		}
		function formatAmount (val, obj) {
			return obj.amount_formated;
		}
		
		if ($("#grid_reservations").length > 0 && datagrid) {			
			var buttonOpts = [];
			var columnOpts = [];
			var actionOpts = [];
			var select = false;
			if(pjGrid.hasUpdateBooking)
			{
				buttonOpts.push({type: "edit", url: "index.php?controller=pjAdminReservations&action=pjActionUpdate&id={:id}"});
			}
			if(pjGrid.hasDeleteSingleBooking)
			{
				buttonOpts.push({type: "delete", url: "index.php?controller=pjAdminReservations&action=pjActionDeleteReservation&id={:id}"});
			}
			columnOpts.push({text: myLabel.res_id, type: "text", sortable: true, editable: false});
			columnOpts.push({text: myLabel.date_from_to, type: "text", sortable: true, editable: false, renderer: formatDateTime})
			columnOpts.push({text: myLabel.nights, type: "text", sortable: true, editable: false});
			columnOpts.push({text: myLabel.name_email, type: "text", sortable: true, editable: false, renderer: formatNameEmail});
			columnOpts.push({text: myLabel.status, type: "text", sortable: true, editable: false, renderer: formatStatus});
			
			if(pjGrid.hasDeleteMultiBookings)
			{
				actionOpts.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminReservations&action=pjActionDeleteReservationBulk", render: true, confirmation: myLabel.delete_confirmation});
			}
			
			if (actionOpts.length) {
				select = {
					field: "id",
					name: "record[]",
					cellClass: 'cell-width-2'
				};
			}
			var $grid_reservations = $("#grid_reservations").datagrid({
				buttons: buttonOpts,
				columns: columnOpts,
				dataUrl: "index.php?controller=pjAdminCalendars&action=pjActionGetReservation" + pjGrid.queryString,
				dataType: "json",
				fields: ['uuid', 'date_from', 'nights', 'c_name', 'status'],
				paginator: {
					actions: actionOpts,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: false,
				select: select
			});
		}
		
		if ($("#calendar_grid_reservations").length > 0 && datagrid) {			
			var buttonOpts = [];
			var columnOpts = [];
			var actionOpts = [];
			var select = false;
			if(pjGrid.hasUpdateBooking)
			{
				buttonOpts.push({type: "edit", url: "index.php?controller=pjAdminReservations&action=pjActionUpdate&id={:id}"});
			}
			if(pjGrid.hasDeleteSingleBooking)
			{
				buttonOpts.push({type: "delete", url: "index.php?controller=pjAdminReservations&action=pjActionDeleteReservation&id={:id}"});
			}
			columnOpts.push({text: myLabel.id, type: "text", sortable: true, editable: false});
			columnOpts.push({text: myLabel.name_email, type: "text", sortable: true, editable: false, renderer: formatNameEmail});
			columnOpts.push({text: myLabel.from_to, type: "text", sortable: true, editable: false, renderer: formatDateTime})
			columnOpts.push({text: myLabel.amount, type: "text", sortable: true, editable: false, renderer: formatAmount});
			columnOpts.push({text: myLabel.status, type: "text", sortable: true, editable: false, renderer: formatStatus});
					
			var $calendar_grid_reservations = $("#calendar_grid_reservations").datagrid({
				buttons: buttonOpts,
				columns: columnOpts,
				dataUrl: "index.php?controller=pjAdminCalendars&action=pjActionGetReservation" + pjGrid.queryString,
				dataType: "json",
				fields: ['uuid', 'c_name', 'date_from', 'amount', 'status'],
				paginator: {
					actions: actionOpts,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: false,
				select: select
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
			$grid.datagrid("load", "index.php?controller=pjAdminCalendars&action=pjActionGetCalendar", content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter-reservations", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid_reservations.datagrid("option", "content"),
				cache = $grid_reservations.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val(),
				status: $this.find("select[name='status']").val(),
				"time": ""
			});
			$grid_reservations.datagrid("option", "cache", cache);
			$grid_reservations.datagrid("load", "index.php?controller=pjAdminCalendars&action=pjActionGetReservation" + pjGrid.queryString, content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on("change", "#filter_status", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$('.frm-filter-reservations').trigger('submit');
			return false;
		}).on( 'change', '.onoffswitch-checkbox', function (e) {
			var name = $(this).attr('name'),
				type = $(this).attr('data-type');
			if($(this).is(':checked'))
			{
				$('input[name="value-enum-'+name+'"]').val('1|0::1');
				if (type == 'limits') {
					$(this).closest('td').find('.pjLimitBlockedDays').show();
				}
			}else{
				$('input[name="value-enum-'+name+'"]').val('1|0::0');
				if (type == 'limits') {
					$(this).closest('td').find('.pjLimitBlockedDays').hide();
				}
			}
		}).on( 'click', '.btnCopyOptions', function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var l = Ladda.create($(this).get(0));
			l.start();
			var $parent = $(this).closest('.modal'),
				$copy_calendar_id = parseInt($parent.find('select[name="copy_calendar_id"]').val(), 10),
				$copy_tab_id = parseInt($parent.find('input[name="copy_tab_id"]').val(), 10),
				$copy_tab = $parent.find('input[name="copy_tab"]').val();
			if ($copy_calendar_id > 0) {
				$.post("index.php?controller=pjAdminCalendars&action=pjActionCopy", {calendar_id: $copy_calendar_id, tab_id: $copy_tab_id, copy_tab: $copy_tab}).done(function (data) {
					l.stop();
					if(data.status == 'ERR')
					{
						$parent.modal('hide');
						if (data.code == 100) {
							swal({
				    			title: "",
								text: data.text,
								type: "error",
								confirmButtonColor: "#DD6B55",
								confirmButtonText: myLabel.alert_btn_close,
								closeOnConfirm: false,
								showLoaderOnConfirm: false
							}, function () {
								swal.close();
							});
						} else {
							swal({
								title: "",
								text: data.text,
								type: "warning",
								showCancelButton: true,
								confirmButtonColor: "#DD6B55",
								confirmButtonText: myLabel.alert_btn_yes,
								cancelButtonText: myLabel.alert_btn_no,
								closeOnConfirm: false,
								showLoaderOnConfirm: true
							}, function () {
								$.post("index.php?controller=pjAdminCalendars&action=pjActionCopy", {calendar_id: $copy_calendar_id, tab_id: $copy_tab_id, copy_tab: $copy_tab, confirmed: 1}).done(function (data) {
									window.location.href = "index.php?controller=pjAdminCalendars&action=pjActionUpdate&id=" + data.calendar_id + "&tab=" + data.copy_tab;
								});
							});
						}
					}else{
						window.location.href = "index.php?controller=pjAdminCalendars&action=pjActionUpdate&id=" + data.calendar_id + "&tab=" + data.copy_tab;
					}
				});
			}
		}).on("click", ".paymentLink", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var select_locale_id = null;
			var payment_method = $(this).attr('data-method');
			var calendar_id = $frmUpdatePayments.find('input[name="id"]').val();
			$('.pj-form-langbar-item').each(function(){
				if($(this).hasClass('btn-primary'))
				{
					select_locale_id = $(this).attr('data-index');
				}
			});
			$.get("index.php?controller=pjAdminCalendars&action=pjActionPaymentOptions", {
				"calendar_id": calendar_id,
				"payment_method": payment_method
			}).done(function (data) {
				$('#modalContent').html(data);
				if($('#modalContent').find('.checkbox input[type="checkbox"]').length > 0)
				{
					$('#modalContent').find('.checkbox input[type="checkbox"]').iCheck({
			            checkboxClass: 'icheckbox_square-green'
			        });
				}
				if (multilang && typeof pjLocale != "undefined")
				{
					var $multilangWrap = $('#modalContent').find('.pj-multilang-wrap');
					$multilangWrap.each(function(e){
						var locale_id = $(this).attr('data-index');
						if(locale_id == select_locale_id)
						{
							$(this).show();
						}else{
							$(this).hide();
						}
					})
				}
				$('#paymentModal').modal('show');
			});
			return false;
		}).on("click", "#btnSavePaymentOptions", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var calendar_id = $frmUpdatePayments.find('input[name="id"]').val();
			$.post("index.php?controller=pjAdminCalendars&action=pjActionPaymentOptions&calendar_id=" + calendar_id, $('#frmPaymentOptions').serialize()).done(function (data) {
				$('#paymentModal').modal('hide');
				window.location.href = "index.php?controller=pjAdminCalendars&action=pjActionUpdate&id=" + calendar_id + "&tab=payments";
			});
			return false;
		}).on( 'change', '#enablePayment', function (e) {
			if ($(this).prop('checked')) {
                $('.hidden-area').show();
                $('#payment_is_active').val(1);
                $("#enableTestMode").trigger("change");
            }else {
                $('.hidden-area').hide();
                $('#payment_is_active').val(0);
            }
		}).on("change", "#enableTestMode", function (e) {
			if ($(this).is(":checked")) {
                $(".test-area").show();
                $(".live-area").hide();
                $("#payment_is_test_mode").val(1);
            } else {
                $(".test-area").hide();
                $(".live-area").show();
                $("#payment_is_test_mode").val(0);
            }
		}).on("click", 'a[data-toggle="tab"]', function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var tab = $(this).attr('data-tab');
			if(tab == 'notifications'){
				$('input[name="recipient"]:checked').trigger("change");
			}
			return false;
		}).on("click", ".btnAddLimit", function (e) {
			var clone_text = $("#tblLimitClone tbody").html(),
				index = 'new_' + Math.ceil(Math.random() * 999999);
			clone_text = clone_text.replace(/\{INDEX\}/g, index);
			$('#tblLimits tbody').append(clone_text);
			
			if (select2) {
				$("#tblLimits tr:last").find("#blocked_days_" + index).select2({
	                allowClear: true
	            });
			}
			$("#tblLimits tr:last").find("#min_nights_" + index).TouchSpin({
				min: 0,
				max: 4294967295,
				step: 1,
				verticalbuttons: true,
	            buttondown_class: 'btn btn-white',
	            buttonup_class: 'btn btn-white'
	        });
			$("#tblLimits tr:last").find("#max_nights_" + index).TouchSpin({
				min: 0,
				max: 4294967295,
				step: 1,
				verticalbuttons: true,
	            buttondown_class: 'btn btn-white',
	            buttonup_class: 'btn btn-white'
	        });
			limitDatePickerTrigger();
		}).on("click", ".lnkRemoveLimitRow", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});
			return false;
		}).on("change", ".number", function (e) {
			var v = parseFloat(this.value);
		    if (isNaN(v)) {
		        this.value = '';
		    } else {
		        this.value = v.toFixed(2);
		    }
		    if (parseFloat(this.value) >= 99999999999999.99) {
		    	this.value = '99999999999999.99';
		    }
		}).on("change", "#export_period", function (e) {
			var period = $(this).val();
			if(period == 'last')
			{
				$('#last_label').show();
				$('#next_label').hide();
				$('#range_label').hide();
			}else if(period == 'all'){
				$('#last_label').hide();
				$('#next_label').hide();
				$('#range_label').hide();
			}else if(period == 'range'){
				$('#last_label').hide();
				$('#next_label').hide();
				$('#range_label').show();
			}else{
				$('#last_label').hide();
				$('#next_label').show();
				$('#range_label').hide();
			}
		}).on("click", ".abCalendarLinkMonth", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this);
			$.get("index.php?controller=pjAdminCalendars&action=pjActionGetCal", {
				"cid": $this.data("cid"),
				"year": $this.data("year"),
				"month": $this.data("month")
			}).done(function (data) {
				$("#abCalendar_" + $this.data("cid")).html(data);
			});
			return false;
		}).on("click", ".abCalendarLinkDate", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this).parent(),
				content = $calendar_grid_reservations.datagrid("option", "content"),
				cache = $calendar_grid_reservations.datagrid("option", "cache");
			if($this.hasClass('abCalendarCellInner'))
			{
				$this = $(this).parent().parent();
			}
			$.extend(cache, {
				"time": $this.data("time"),
				"status": "",
				"q": "",
			});
			$calendar_grid_reservations.datagrid("option", "cache", cache);
			$calendar_grid_reservations.datagrid("load", "index.php?controller=pjAdminCalendars&action=pjActionGetReservation" + pjGrid.queryString, content.column, content.direction, content.page, content.rowCount);
			return false;
		});
				
		function notificationsGetMetaData() {
			var $box = $("#boxNotificationsMetaData");
			if (!$box.length) {
				return;
			}
			
			// show preloader
			$box.empty().addClass("ibox-content-notification");
			
			$('<div class="ibox-content-overlay"></div> \
				<div class="sk-spinner sk-spinner-double-bounce"> \
					<div class="sk-double-bounce1"></div> \
					<div class="sk-double-bounce2"></div> \
				</div>').appendTo($box);
			
			$box.find(".ibox-content-overlay, .sk-spinner").show();

			var search = window.location.search,
				variant = search.match(/&?variant=(\w+)/),
				transport = search.match(/&?transport=(\w+)/),
				params = {
					recipient: $('input[name="recipient"]:checked').val()
				};
			
			if (variant !== null && transport !== null) {
				params.variant = variant[1];
				params.transport = transport[1];
			}
			params.calendar_id = $('#calendar_id').val();
			
			$.get("index.php?controller=pjAdminCalendars&action=pjActionNotificationsGetMetaData", params).done(function (data) {
				$box.html(data);				
				if (variant !== null && transport !== null) {
					$box.find(['#variant', transport[1], variant[1]].join("_")).trigger("change");
				} else {
					$box.find('input[name="variant"]:first').trigger("change");
				}
			});
		}
		
		function notificationsGetContent() {
			var $box = $("#boxNotificationsContent");
			if (!$box.length) {
				return;
			}
			
			// show preloader
			$box.empty().addClass("ibox-content-notification");
			
			$('<div class="ibox-content-overlay"></div> \
				<div class="sk-spinner sk-spinner-double-bounce"> \
					<div class="sk-double-bounce1"></div> \
					<div class="sk-double-bounce2"></div> \
				</div>').appendTo($box);
			
			$box.find(".ibox-content-overlay, .sk-spinner").show();
			
			var $checked = $('input[name="variant"]:checked');
			
			$.get("index.php?controller=pjAdminCalendars&action=pjActionNotificationsGetContent", {
				recipient: $('input[name="recipient"]:checked').val(),
				variant: $checked.val(),
				transport: $checked.data("transport"),
				calendar_id: $('#calendar_id').val()
			}).done(function (data) {
				
				$box.html(data);
				
				myTinyMceDestroy.call(null);
				myTinyMceInit.call(null, 'textarea.mceEditor');
				
				var index = $(".pj-form-langbar-item.btn-primary").data("index");
				if (index !== undefined) {
					$box.find('.pj-multilang-wrap[data-index!="' + index + '"]').hide();
					$box.find('.pj-multilang-wrap[data-index="' + index + '"]').show();
				}
			});
		}
		
		function notificationsSetContent(toggle) {
			
			var $box = $("#boxNotificationsContent");
			if (!$box.length) {
				return;
			}
			
			// show preloader
			$box.addClass("notification-box");
			
			$('<div class="ibox-content-overlay"></div> \
				<div class="sk-spinner sk-spinner-double-bounce"> \
					<div class="sk-double-bounce1"></div> \
					<div class="sk-double-bounce2"></div> \
				</div>').appendTo($box);
			
			$box.find(".ibox-content-overlay, .sk-spinner").show();
			
			var postData,
				$form = $('#pjNotificationContent');
			
			if (toggle) {
				postData = $.param({
					is_active: ($form.find("#is_active").is(":checked") ? 1 : 0),
					notify_id: $form.find('input[name="notify_id"]').val()
				});
			} else {
				postData = $form.find('input, textarea').serialize();
				postData = postData.replace(/&?is_active=(\w+)?/, "");
				
				var l = Ladda.create($form.find(".pjRpbBtnSaveNotify").get(0));
				l.start();
			}
			
			$.post("index.php?controller=pjAdminCalendars&action=pjActionNotificationsSetContent", postData).done(function (data) {
				
				if (data && data.status && data.status === "OK") {
					
					notificationsGetMetaData.call(null);
					
				}
				
			});
		}
		
		$("#boxNotificationsWrapper").on("change", 'input[name="recipient"]', function () {
			var calendar_id = $('#calendar_id').val();
			var search = window.location.search,
				recipient = search.match(/&?recipient=(\w+)/),
				variant = search.match(/&?variant=(\w+)/),
				transport = search.match(/&?transport=(\w+)/);
			var arr = [];
			arr.push("index.php?controller=pjAdminCalendars&action=pjActionUpdate&id="+calendar_id+"&tab=notifications&recipient=");
			arr.push(this.value);
			
			if (recipient !== null && recipient[1] === this.value) {
				if (variant !== null && transport !== null) {
					arr.push("&transport=");
					arr.push(transport[1]);
					arr.push("&variant=");
					arr.push(variant[1]);
				}
			}
			var url = arr.join("");
			history.pushState({
				url: url,
				title: null
			}, null, url);
			
			notificationsGetMetaData.call(null);			
		}).on("change", 'input[name="variant"]', function () {			
			var $this = $(this);
			var calendar_id = $('#calendar_id').val();
			var url = ["index.php?controller=pjAdminCalendars&action=pjActionUpdate","&id=", calendar_id, "&tab=notifications", "&recipient=" , $('input[name="recipient"]:checked').val(), "&transport=", $this.data("transport"), "&variant=", $this.val()].join("");
			history.pushState({
				url: url,
				title: null
			}, null, url);
			
			notificationsGetContent.call(null);
			
		}).on("change", '#is_active', function () {			
			notificationsSetContent.call(null, true);			
			var $this = $(this),
				$hidden = $this.closest("form").find(".notification-area");
			
			if ($this.is(":checked")) {
				$hidden.removeClass("hidden");
			} else {
				$hidden.addClass("hidden");
			}			
		}).on("click", ".pjRpbBtnSaveNotify", function (e) {
			e.preventDefault();			
			notificationsSetContent.call(null, false);			
			return false;
		});
		

		var $feed_grid = $("#feed_grid");
		if($feed_grid.length > 0)
		{
			function formatProperties(val, obj) {
	            if (parseInt(obj.calendar_id, 10) > 0) {
	                return ['<a href="index.php?controller=pjAdminCalendars&amp;action=pjActionUpdate&amp;id=', obj.calendar_id,'">', val,'</a>'].join('');
	            } else {
	                return val;
	            }
	        }
	        
	        function formatProvider(str, obj) {
	        	return obj.provider;
	        }
			$feed_grid.datagrid({
				buttons: [
                	{type: "eye", url: "index.php?controller=pjAdminCalendars&action=pjActionViewFeed&id={:id}", target: "_blank"},
                	{type: "refresh", url: "index.php?controller=pjAdminCalendars&action=pjActionRefreshFeed&calendar_id={:calendar_id}&id={:id}"},
                	{type: "edit", url: "index.php?controller=pjAdminCalendars&action=pjActionUpdateFeed&id={:id}"},
                    {type: "delete", url: "index.php?controller=pjAdminCalendars&action=pjActionDeleteFeed&id={:id}"}
                ],
				columns: [
                    {text: myLabel.property, type: "text", sortable: true, editable: false, renderer: formatProperties,},
                    {text: myLabel.provider, type: "text", sortable: true, editable: false, renderer: formatProvider},
                    {text: myLabel.cnt, type: "text", sortable: true, editable: false}
                ],
                dataUrl: "index.php?controller=pjAdminCalendars&action=pjActionGetFeed" + pjGrid.queryString,
                dataFeed: "json",
                fields: ['property', 'provider_id', 'cnt'],
                paginator: {
                    actions: [],
                    gotoPage: true,
                    paginate: true,
                    total: true,
                    rowCount: true
                },
                saveUrl: "index.php?controller=pjAdminCalendars&action=pjActionSaveFeed&id={:id}",
                select: false,
			}).on("click", ".pj-table-icon-refresh", function(e) {    			
            	e.preventDefault();
            	var url = $(this).attr("href");
            	swal({
					title: myLabel.import_title,
					text: myLabel.import_desc,
					type: "warning",
					showCancelButton: true,
					showConfirmButton: true,
					confirmButtonColor: "#29b0e6",
					confirmButtonText: myLabel.alert_btn_import,
					cancelButtonText: myLabel.alert_btn_close,
					closeOnConfirm: false,
					showLoaderOnConfirm: true
				}, function () {
	            	$.get(url, function (data) {
	            		if (data.code == 200) {
		                	swal({
		    					title: myLabel.import_title,
		    					text: myLabel.import_success_desc,
		    					type: "success",
		    					confirmButtonColor: "#29b0e6"
		    				}, function () {
		    					var content = $feed_grid.datagrid("option", "content");
		                		$feed_grid.datagrid("load", $feed_grid.data("datagrid").settings.dataUrl, content.column, content.direction, content.page, content.rowCount);
		    				});
	            		} else {
	            			swal({
		    					title: myLabel.import_title,
		    					text: myLabel.import_error_desc,
		    					type: "warning",
		    					confirmButtonColor: "#29b0e6"
		    				});
	            		}
	                });
				});
            }).on("click", ".pj-table-icon-edit", function(e) {
	        	e.preventDefault();
	        	var url = $(this).attr('href');
	        	$.get(url, function (data) {
            		if (data.code == 200) {
            			$pjFeedWrapper.find('input[name="calendar_id"]').val(data.feed.calendar_id);
            			$pjFeedWrapper.find('input[name="provider_id"]').val(data.feed.provider_id);
            			$pjFeedWrapper.find('input[name="url"]').val(data.feed.url);
            			$pjFeedWrapper.find('input[name="feed_id"]').val(data.feed.id);
            		}
                });
	        });
		}
		
		if($pjFeedWrapper.length > 0)
		{
			if ($frmFeeds.length > 0 && validate) {
				$frmFeeds.validate({
					onkeyup: false
				});
			}
			
			$(document).on('click', '#pjRpbcImportFeed', function(e){
				if($('#feed_url').val() != '')
				{
					var l = Ladda.create( $(this).get(0) );
					l.start();
					$.post('index.php?controller=pjAdminCalendars&action=pjActionSaveFeed', $pjFeedWrapper.find('input').serialize(), function (data) {	                	
	                	if (data && data.status && data.status === "OK") {
	                		var content = $feed_grid.datagrid("option", "content");
	                		$feed_grid.datagrid("load", $feed_grid.data("datagrid").settings.dataUrl, content.column, content.direction, content.page, content.rowCount);	        				
	                		$pjFeedWrapper.find(".form-control").val("");
	                	} else {
	                		swal({
	        					title: myLabel.import_standard_title,
	        					text: data.code == 101 ? data.text : myLabel.import_standard_desc,
	        					type: "warning",
	        					showCancelButton: false,
	        					showConfirmButton: true,
	        					confirmButtonColor: "#29b0e6",
	        					confirmButtonText: myLabel.alert_btn_close
	        				});	 
	                	}                	
	                }).always(function() {
	                	l.stop();
	                });
				}
                return false;
			}).on("change", "#feed_url", function(e) {
	        	e.preventDefault();
	        	var url = $(this).val();
	        	if (url.indexOf('airbnb.com') !== -1) {
	        		$("#provider_id").val(2);
				}
	        	if (url.indexOf('vrbo.com') !== -1) {
	        		$("#provider_id").val(3);
				}
	        	if (url.indexOf('homeaway.com') !== -1) {
	        		$("#provider_id").val(4);
				}
	        	if (url.indexOf('tripadvisor.com') !== -1) {
	        		$("#provider_id").val(5);
				}
	        	if (url.indexOf('booking.com') !== -1) {
	        		$("#provider_id").val(6);
				}
	        });
		}
		
		if ($frmExportReservations.length > 0 && validate) {
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
			if($('.iChecks').length > 0)
			{
				$('.iChecks').iCheck({
		            radioClass: 'iradio_square-green'
		        });
				$('input').on('ifChanged', function (event) { 
					$(event.target).trigger('change');
					if($(this).val() == 'file')
					{
						$('#abSubmitButton').val(myLabel.btn_export);
						$('.abFeedContainer').hide();
						$('.abPassowrdContainer').hide();
						$("#export_period option[value='all']").show();
						$("#export_period option[value='range']").show();
					}else if($(this).val() == 'feed'){
						$('.abPassowrdContainer').show();
						$('#abSubmitButton').val(myLabel.btn_get_url);
						if($('#export_period').val() == 'all' || $('#export_period').val() == 'range')
						{
							$('#export_period').val('next');
							$('#last_label').hide();
							$('#range_label').hide();
							$('#next_label').show();
						}
						$("#export_period option[value='range']").hide();
					}
				});
			}
			$frmExportReservations.validate({
				rules: {
					"password": {
						required: function(){
							if($('#feed').is(':checked'))
							{
								return true;
							}else{
								return false;
							}
						}
					}
				},
				onkeyup: false,
				ignore: ".ignore",
				submitHandler: function (form) {
				    var ladda_buttons = $(form).find('.ladda-button');
				    if(ladda_buttons.length > 0)
                    {
                        var l = ladda_buttons.ladda();
                        l.ladda('start');
                        
                        setTimeout(function() {
                        	l.ladda('stop');
                        }, 1000);
                    }
                    return true;
                }
			});
		}
		
		if ($("#export_grid").length > 0 && datagrid) 
		{
			if($('input[name="type"]:checked').val() == 'feed')
			{
				$("#export_period option[value='range']").hide();
			}
			var $export_grid = $("#export_grid").datagrid({
				buttons: [{type: "eye", url: "index.php?controller=pjFront&action=pjActionExportFeed{:params}", 'target' : "_blank"},
				          {type: "delete", url: "index.php?controller=pjAdminCalendars&action=pjActionDeletePassword&id={:id}"}
				          ],
				columns: [{text: myLabel.format, type: "text", sortable: false, editable: false},
				          {text: myLabel.reservations, type: "text", sortable: false, editable: false},
				          {text: myLabel.period, type: "text", sortable: false, editable: false}
				          ],
				dataUrl: "index.php?controller=pjAdminCalendars&action=pjActionGetPassword" + pjGrid.queryString,
				dataType: "json",
				fields: ['format', 'type', 'period'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminCalendars&action=pjActionDeletePasswordBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminCalendars&action=pjActionSavePassword&id={:id}",
				select: {
					field: "id",
					name: "record[]",
					cellClass: 'cell-width-2'
					
				}
			});
		}
		
		$(document).ready(function() {
			var $tab = $('.tabs-container .nav-tabs').find('li.active a').attr('data-tab');
			if ($tab == 'notifications') {
				$('input[name="recipient"]:checked').trigger("change");
			}
		});
	});
})(jQuery);