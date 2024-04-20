var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	"use strict";
	$(function () {
		var $frmCreateReservation = $("#frmCreateReservation"),
			$frmUpdateReservation = $("#frmUpdateReservation"),
			$frmSchedule = $("#frmSchedule"),
			select2 = ($.fn.select2 !== undefined),
			validator,
			validate = ($.fn.validate !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			datagrid = ($.fn.datagrid !== undefined);

		if ($(".select-item").length && select2) {
            $(".select-item").select2({
                placeholder: '-- ' + myLabel.choose + ' --',
                allowClear: true
            });
        }
		
		if($('.i-checks').length > 0)
		{
			$('.i-checks').iCheck({
	            checkboxClass: 'icheckbox_square-green'
	        });
		}
		
		if($(".pjRpbcAdultsChildSelector").length > 0)
		{
			$(".pjRpbcAdultsChildSelector").TouchSpin({
				min: 0,
				max: $(this).attr('data-max'),
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
        	$('.datepicker-item').datepicker({
	            autoclose: true
	        }).on('changeDate', function (selected) {
	        	var $input = $(this).find('input'),
	        		elementName = $input.attr('name'),
	        		$form = $input.closest('form');
	        	if(elementName == 'date_from')
	        	{
	        		var $toElement = $('#date_to').parent(),
	        			date_to_value = $toElement.datepicker("getUTCDate"),
	        			$minDate = new Date(selected.date.valueOf());
	        		if(date_to_value < selected.date)
					{
    					$toElement.find('input').val($input.val());
					}
	        		$toElement.datepicker('setStartDate', $minDate);
	        	}else if(elementName == 'date_to'){
	        		var $fromElement = $("#date_from").parent(),
	        			$toElement = $('#date_to').parent();
	        		if($fromElement.length > 0 && $toElement.length > 0)
        			{
	        			var $maxDate = new Date(selected.date.valueOf()),
        					date_from_value = $fromElement.datepicker("getUTCDate");
        				if(date_from_value > selected.date)
    					{
        					$fromElement.find('input').val($input.val());
    					}
        				$fromElement.datepicker('setEndDate', $maxDate);
        			}
	        	}
	        	if($frmCreateReservation.length > 0 || $frmUpdateReservation.length > 0)
	        	{
	        		$input.valid();
	        		var cal_id,
	        			res_id = parseInt($form.find("input[name='id']").val(), 10);  
					if(res_id > 0) {
						cal_id = parseInt($form.find("input[name='calendar_id']").val(), 10);
					} else {
						cal_id = parseInt($form.find("select[name='calendar_id']").val(), 10);
					}
					checkDates.call(null, 
						$('#date_from').val(), 
						$('#date_to').val(), 
						cal_id,
						res_id
					);
	        	}
			});
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
			    height: 400,
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
		
		function calcPrices(callback) {
			$.post("index.php?controller=pjAdminReservations&action=pjActionCalcPrice", $(this).closest("form").serialize()).done(function (data) {
				if (data.status === "OK") {
					$("#amount").val(data.amount).valid();
					$("#extra_price").val(data.extra_price).valid();
					$("#discount_amount").val(data.discount_amount).valid();
					$("#promo_amount").val(data.promo_amount).valid();
					$("#promo_type").val(data.promo_type);
					$("#deposit").val(data.deposit).valid();
					$("#security").val(data.security).valid();
					$("#tax").val(data.tax).valid();
					$("#tourist_tax").val(data.tourist_tax).valid();
					$("#total").val(data.total).valid();
				}

				if (callback !== undefined && typeof callback === "function") {
					callback();
				}
			});
		}

		function validateMaxPeople()
		{
			var number_of_adults = 0;
			var number_of_chidlren = 0;
			var max_people = parseInt($('#boxMaxPeopleMsg').attr('data-max'));
			if($("select[name='c_adults']").length > 0)
			{
				if($("select[name='c_adults']").val() != '')
				{
					number_of_adults = parseInt($("select[name='c_adults']").val(), 10);
				}
			}
			if($("select[name='c_children']").length > 0)
			{
				number_of_chidlren = parseInt($("select[name='c_children']").val(), 10);
			}
			if(max_people < (number_of_adults + number_of_chidlren) )
			{
				$('#boxMaxPeopleMsg').show();
				$('#boxMinPeopleMsg').hide();
				return false;
			}else{
				$('#boxMaxPeopleMsg').hide();
				return true;
			}
		}
		function validateMinPeople()
		{
			var number_of_adults = 0;
			var number_of_chidlren = 0;
			var min_people = parseInt($('#boxMinPeopleMsg').attr('data-min'));
			if($("select[name='c_adults']").length > 0)
			{
				if($("select[name='c_adults']").val() != '')
				{
					number_of_adults = parseInt($("select[name='c_adults']").val(), 10);
				}
			}
			if($("select[name='c_children']").length > 0)
			{
				number_of_chidlren = parseInt($("select[name='c_children']").val(), 10);
			}
			if(min_people > (number_of_adults + number_of_chidlren) )
			{
				$('#boxMinPeopleMsg').show();
				$('#boxMaxPeopleMsg').hide();
				return false;
			}else{
				$('#boxMinPeopleMsg').hide();
				return true;
			}
		}
		
		$(document).on("click", ".btnCalculate", function () {
			calcPrices.call(this);
		}).on("click", ".btnResend", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if (dialog && $dialogResend.length > 0) {
				$dialogResend.dialog("open");
			}
			return false;
		}).on("change", "#status", function (e) {
			var $pjRpbcSummaryWrapper = $('#pjRpbcSummaryWrapper');
			var value = ($("#status option:selected").val()).toLowerCase();
			var text = $("#status option:selected").text();
			var bg_class = 'bg-' + value;
			$pjRpbcSummaryWrapper.find('.panel-heading').removeClass("bg-pending").removeClass("bg-cancelled").removeClass("bg-confirmed").addClass(bg_class);
			$pjRpbcSummaryWrapper.find('.status-text').html(text);
		}).on("click", ".widget-client-info", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$('.tab-client-details').trigger('click');
			return false;
		});
		
		if (validate) {
			$.validator.addMethod("validDates", function (value, element) {
				return parseInt(value, 10) === 1; 
			}, myLabel.dateRangeValidation);
		}
		if ($frmCreateReservation.length > 0 && validate) {
			$frmCreateReservation.validate({
				rules: {
					"dates": "validDates",
					"uuid": {
						required: true,
						remote: "index.php?controller=pjAdminReservations&action=pjActionCheckUnique"
					}
				},
				messages:{
					"uuid":{
						remote: myLabel.duplicatedUniqueID
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				ignore: ".ignore",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var $_id = $(validator.errorList[0].element, this).closest("div.tab-pane").attr("id");
				    	$('.tab-'+$_id).trigger("click");
				    };
				},
				submitHandler: function(form)
				{
					if(validateMaxPeople.call(null) == true && validateMinPeople.call(null) == true)
					{
						form.submit()
					}
					return false;
				}
			});
		}
		if ($frmUpdateReservation.length > 0 && validate) {
			
			$frmUpdateReservation.validate({
				rules: {
					"dates": "validDates",
					"uuid": {
						required: true,
						remote: "index.php?controller=pjAdminReservations&action=pjActionCheckUnique&id=" + $frmUpdateReservation.find("input[name='id']").val()
					}
				},
				messages:{
					"uuid":{
						remote: myLabel.duplicatedUniqueID
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				ignore: ".ignore",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var $_id = $(validator.errorList[0].element, this).closest("div.tab-pane").attr("id");
				    	$('.tab-'+$_id).trigger("click");
				    };
				},
				submitHandler: function(form)
				{
					if(validateMaxPeople.call(null) == true && validateMinPeople.call(null) == true)
					{
						form.submit()
					}
					return false;
				}
			});
		}

		function checkDates(date_from, date_to, calendar_id, id) {
			if (calendar_id != '' && date_from != '' && date_to != '') { 
				$.get("index.php?controller=pjAdminReservations&action=pjActionCheckDates", {
					"date_from": date_from,
					"date_to": date_to,
					"calendar_id": calendar_id,
					"id": id
				}).done(function (data) {
					if (data.code === undefined) {
						return;
					}
					switch (data.code) {
					case 200:
						$("input#dates").val('1').valid();
						break;
					case 100:
						$("input#dates").val('0').valid();
						break;
					}
				});
			}
		}
		
		if ($("#grid").length > 0 && datagrid) {
			function formatProperty (val, obj) {
				if (pjGrid.hasUpdateProperty)
				{
					return ['<a href="index.php?controller=pjAdmin&action=pjActionRedirect&nextController=pjAdminCalendars&nextAction=pjActionUpdate&calendar_id=', obj.calendar_id, '&nextParams=', encodeURIComponent("id=" + obj.calendar_id), '">', val, '</a>'].join("");	
				}else{
					return val;
				}
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
			
			var buttonOpts = [];
			var columnOpts = [];
			var actionOpts = [];
			var select = false;
			if(pjGrid.hasUpdate)
			{
				buttonOpts.push({type: "edit", url: "index.php?controller=pjAdminReservations&action=pjActionUpdate&id={:id}"});
			}
			if(pjGrid.hasDeleteSingle)
			{
				buttonOpts.push({type: "delete", url: "index.php?controller=pjAdminReservations&action=pjActionDeleteReservation&id={:id}"});
			}
			columnOpts.push({text: myLabel.calendar, type: "text", sortable: true, editable: false, renderer: formatProperty});
			columnOpts.push({text: myLabel.uuid, type: "text", sortable: true, editable: false});
			columnOpts.push({text: myLabel.date_from_to, type: "text", sortable: true, editable: false, renderer: formatDateTime})
			columnOpts.push({text: myLabel.nights, type: "text", sortable: true, editable: false});
			columnOpts.push({text: myLabel.name_email, type: "text", sortable: true, editable: false, renderer: formatNameEmail});
			columnOpts.push({text: myLabel.status, type: "text", sortable: true, editable: false, renderer: formatStatus});
			
			if (pjGrid.hasExport) {
				actionOpts.push({text: myLabel.exportSelected, url: "index.php?controller=pjAdminReservations&action=pjActionExportReservation", ajax: false});
			}
			if(pjGrid.hasDeleteMulti)
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
			var $grid = $("#grid").datagrid({
				buttons: buttonOpts,
				columns: columnOpts,
				dataUrl: "index.php?controller=pjAdminReservations&action=pjActionGetReservation" + pjGrid.queryString,
				dataType: "json",
				fields: ['calendar', 'uuid', 'date_from', 'nights', 'c_name', 'status'],
				paginator: {
					actions: actionOpts,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminReservations&action=pjActionSaveReservation&id={:id}",
				select: select
			});
			
			$(document).on("submit", ".frm-filter", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					q: $this.find("input[name='q']").val(),
					status: $this.find("option:selected", "select[name='status']").val(),
					c_name: '',
					c_email: '',
					uuid: '',
					calendar_id: '',
					date_from: '',
					date_to: '',
					amount_from: '',
					amount_to: '',
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", content.column, content.direction, content.page, content.rowCount);
				return false;
			}).on("change", "#filter_status", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$('.frm-filter').trigger('submit');
				return false;
			}).on("submit", ".frm-filter-advanced", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var obj = {},
					$this = $(this),
					arr = $this.serializeArray(),
					content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
					obj[arr[i].name] = arr[i].value;
				}
				$.extend(cache, obj);
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", content.column, content.direction, content.page, content.rowCount);
				return false;
			}).on("reset", ".frm-filter-advanced", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $frm = $('.frm-filter-advanced');
				$frm.find("input[name='c_name']").val('');
				$frm.find("input[name='c_email']").val('');
				$frm.find("input[name='uuid']").val('');
				$frm.find("select[name='calendar_id']").val('');
				$frm.find("select[name='status']").val('');				
				$frm.find("input[name='date_from']").val('');
				$frm.find("input[name='date_to']").val('');
				$frm.find("input[name='amount_from']").val('');
				$frm.find("input[name='amount_to']").val('');				
				$(".btn-advance-search").trigger("click");
				$('.frm-filter-advanced').submit();
				return false;
			});
			
		}
		
		$(document).on("change", "#calendar_id", function (e) {
			var $form = $(this).closest("form"),
				from = $("#date_from").val(),
				to = $("#date_to").val();
					
			if($(this).val() != '' && from != '' && to != '')
			{
				checkDates.call(null, 
						from, 
						to, 
						$(this).val(),
						$form.find("input[name='id']").val()
					);
			}
			
			$.get("index.php?controller=pjAdminReservations&action=pjActionGetAdults", {
				"id": $(this).val()
			}).done(function (data) {
				$('#boxAdults').html(data.ob_content);
				if($('#boxAdults').find(".pjRpbcAdultsChildSelector").length > 0)
				{
					$('#boxAdults').find(".pjRpbcAdultsChildSelector").TouchSpin({
						min: 0,
						max: $(this).attr('data-max'),
						step: 1,
						verticalbuttons: true,
			            buttondown_class: 'btn btn-white',
			            buttonup_class: 'btn btn-white'
			        });
				}
				
				$('#boxMaxPeopleMsg').attr('data-max', data.o_max_people);
				$('#boxMaxPeopleMsg').find('.errCustom').html(data.max_message);
				$('#boxMinPeopleMsg').attr('data-min', data.o_min_people);
				$('#boxMinPeopleMsg').find('.errCustom').html(data.min_message);
				validateMaxPeople.call(null);
				validateMinPeople.call(null);
				if(data.o_disable_payments == '1')
				{
					$('#payment_method').removeClass('required').valid();
				}else{
					$('#payment_method').addClass('required');
				}
			});
			
			$.get("index.php?controller=pjAdminReservations&action=pjActionGetChildren", {
				"id": $(this).val()
			}).done(function (data) {
				$('#boxChildren').html(data);
				if($('#boxChildren').find(".pjRpbcAdultsChildSelector").length > 0)
				{
					$('#boxChildren').find(".pjRpbcAdultsChildSelector").TouchSpin({
						min: 0,
						max: $(this).attr('data-max'),
						step: 1,
						verticalbuttons: true,
			            buttondown_class: 'btn btn-white',
			            buttonup_class: 'btn btn-white'
			        });
				}
			});
			
			$.get("index.php?controller=pjAdminReservations&action=pjActionGetExtras", {
				"id": $(this).val()
			}).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					$('#boxExtras').html('').hide();
				}else{
					$('#boxExtras').html(data).show();
					if($('#boxExtras').find('.i-checks').length > 0)
					{
						$('#boxExtras').find('.i-checks').iCheck({
				            checkboxClass: 'icheckbox_square-green'
				        });
					}
				}
			});
			
			$.get("index.php?controller=pjAdminReservations&action=pjActionGetPMs&id=" + $(this).val()).done(function (data) {
				$('#payment-method-wrapper').html(data);
			});
			
			$.get("index.php?controller=pjAdminReservations&action=pjActionGetBookingFields&calendar_id=" + $(this).val()).done(function (data) {
				$.each(data, function(key, val){
			        if(key.indexOf('o_bf_') >= 0)
			        {
			        	var field_id = "#" + key.replace("o_bf_", "c_");
			        	if($(field_id).length > 0)
			        	{
			        		var $parent = $(field_id).closest('.pjBookingFormField');
			        		$parent.show();
			        		if(val == '3')
			        		{
			        			$(field_id).addClass('required');
			        		}else{
			        			$(field_id).removeClass('required');
			        			$(field_id).valid();
			        			if (val == '1') {
			        				$parent.hide();
			        			}
			        		}
			        	}
			        } else if(key == 'o_disable_payments') {
			        	var field_id = "#payment_method";
			        	if($(field_id).length > 0)
			        	{
			        		if(val == '0')
			        		{
			        			$(field_id).addClass('required');
			        		}else{
			        			$(field_id).removeClass('required');
			        			$(field_id).valid();
			        		}
			        	}
			        }
			    });
				
			});
		}).on("change", "#c_adults, #c_children", function (e) {
			validateMaxPeople.call(null);
			validateMinPeople.call(null);
		}).on("click", ".confirmation-email", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var reservation_id = $(this).attr('data-id');
			var document_id = 0;
			var $confirmEmailContentWrapper = $('#confirmEmailContentWrapper');
			
			$('#btnSendEmailConfirm').attr('data-reservation_id', reservation_id);
			
			$confirmEmailContentWrapper.html("");
			$.get("index.php?controller=pjAdminReservations&action=pjActionEmailConfirmation", {
				"reservation_id": reservation_id
			}).done(function (data) {
				$confirmEmailContentWrapper.html(data);
				if(data.indexOf("pjResendAlert") == -1)
				{
					if ($('#mceEditor').length > 0) {
						myTinyMceDestroy.call(null);
						myTinyMceInit.call(null, 'textarea#mceEditor');
			        }
					
					validator = $confirmEmailContentWrapper.find("form").validate({});
					$('#btnSendEmailConfirm').show();
				}else{
					$('#btnSendEmailConfirm').hide();
				}	
				$('#confirmEmailModal').modal('show');
			});
			return false;
		}).on("click", "#btnSendEmailConfirm", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this);
			var $confirmEmailContentWrapper = $('#confirmEmailContentWrapper');
			if (validator.form()) {
				$('#mceEditor').html( tinymce.get('mceEditor').getContent() );
				$(this).attr("disabled", true);
				var l = Ladda.create(this);
			 	l.start();
				$.post("index.php?controller=pjAdminReservations&action=pjActionEmailConfirmation", $confirmEmailContentWrapper.find("form").serialize()).done(function (data) {
					if (data.status == "OK") {
						$('#confirmEmailModal').modal('hide');
					} else {
						$('#confirmEmailModal').modal('hide');
					}
					$this.attr("disabled", false);
					l.stop();
				});
			}
			return false;
		}).on("click", ".cancellation-email", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var reservation_id = $(this).attr('data-id');
			var document_id = 0;
			var $cancellationEmailContentWrapper = $('#cancellationEmailContentWrapper');
			
			$('#btnSendEmailCancellation').attr('data-reservation_id', reservation_id);
			
			$cancellationEmailContentWrapper.html("");
			$.get("index.php?controller=pjAdminReservations&action=pjActionEmailCancellation", {
				"reservation_id": reservation_id
			}).done(function (data) {
				$cancellationEmailContentWrapper.html(data);
				if(data.indexOf("pjResendAlert") == -1)
				{
					if ($('#mceEditor').length > 0) {
						myTinyMceDestroy.call(null);
						myTinyMceInit.call(null, 'textarea#mceEditor');
			        }
					validator = $cancellationEmailContentWrapper.find("form").validate({});
					$('#btnSendEmailCancellation').show();
				}else{
					$('#btnSendEmailCancellation').hide();
				}	
				$('#cancellationEmailModal').modal('show');
			});
			return false;
		}).on("click", "#btnSendEmailCancellation", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this);
			var $cancellationEmailContentWrapper = $('#cancellationEmailContentWrapper');
			if (validator.form()) {
				$('#mceEditor').html( tinymce.get('mceEditor').getContent() );
				$(this).attr("disabled", true);
				var l = Ladda.create(this);
			 	l.start();
				$.post("index.php?controller=pjAdminReservations&action=pjActionEmailCancellation", $cancellationEmailContentWrapper.find("form").serialize()).done(function (data) {
					if (data.status == "OK") {
						$('#cancellationEmailModal').modal('hide');
					} else {
						$('#cancellationEmailModal').modal('hide');
					}
					$this.attr("disabled", false);
					l.stop();
				});
			}
			return false;
		});
			
		if($frmSchedule.length > 0)
		{
			function getSchedule()
			{
				var params = '';
				if($frmSchedule.find('input[name="date"]').length > 0)
				{
					params += "&date=" + $frmSchedule.find('input[name="date"]').val();
					params += "&page=" + $frmSchedule.find('input[name="page"]').val();
					params += "&rowCount=" + $frmSchedule.find('select[name="rowCount"]').val();
				}
				$.get("index.php?controller=pjAdminReservations&action=pjActionGetSchedule" + params).done(function (data) {
					$frmSchedule.html(data);
				});
			}
			
			getSchedule();
			
			$frmSchedule.on('click', '.pjRpbcMonthNav', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var date = $(this).attr('data-date');
				$frmSchedule.find('input[name="date"]').val(date);
				getSchedule();
			}).on('click', '.pj-paginator', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var page = $(this).attr('data-page');
				$frmSchedule.find('input[name="page"]').val(page);
				getSchedule();
			}).on('change', '.pj-selector-row-count', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				getSchedule();
			}).on('change', '.pj-selector-goto', function(e){
				var page = $(this).val();
				page = Number(page);
				var min = parseInt($(this).attr('data-min'), 10);
				var max = parseInt($(this).attr('data-max'), 10);
				if (page >= min && page <= max) {
					getSchedule();
				}
			});
		}
		
		$(document).ready(function() {
			if ($frmCreateReservation.length > 0) {
				if (parseInt($('#calendar_id').val(), 10) > 0) {
					$('#calendar_id').trigger('change');
				}
			}
		});
	});
})(jQuery);