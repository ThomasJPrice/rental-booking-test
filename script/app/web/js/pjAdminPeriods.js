var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		var datepicker = ($.fn.datepicker !== undefined),
			validate = ($.fn.validate !== undefined)
			$frmPeriods = $("#frmPeriods");

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
        
        function initDatePicker() {
        	var $tbody = $("#tblPeriods tbody"),
				$datepick = $tbody.find(".datepick");
        	if ($datepick.length > 0) {
            	$datepick.datepicker({autoclose: true}).on('changeDate', function (selected) {
            		var idx = $(this).closest('.mainPeriod').attr('data-idx');
            		var element_id = $(this).attr('id');
            		
            		if (parseInt(element_id.indexOf("start_date_"), 10) >= 0) {
    	        		var $toElement = $("#end_date_" + idx);
            			if($toElement.length > 0)
            			{
            				var $minDate = new Date(selected.date.valueOf()),
            					end_date_value = $toElement.datepicker("getUTCDate");
            				if(end_date_value < selected.date)
        					{
            					$toElement.val($(this).val());
        					}
            				$toElement.datepicker('setStartDate', $minDate);
            			}
            		}

            		if (parseInt(element_id.indexOf("end_date_"), 10) >= 0) {
            			var $fromElement = $("#start_date_" + idx);
            			var $toElement = $("#end_date_" + idx);
            			if($fromElement.length > 0 && $toElement.length > 0)
            			{
            				var $maxDate = new Date(selected.date.valueOf()),
            					start_date_value = $fromElement.datepicker("getUTCDate");
            				if(start_date_value > selected.date)
        					{
            					$fromElement.val($toElement.val());
        					}
            				$fromElement.datepicker('setEndDate', $maxDate);
            			}
            		}
                });
            }
        }
		
		if($frmPeriods.length > 0)
		{
			$frmPeriods.validate({
				ignore: ".ignore",
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
			initDatePicker();
		}
		
		$(document).on("click", ".btnAddPeriod", function () {
			var $c = $("#periodDefault tbody").clone(),
				r = $c.html().replace(/\{INDEX\}/g, 'new_' + Math.ceil(Math.random() * 99999));
			$("#tblPeriods").find("tbody").append(r);
			initDatePicker();
		}).on("click", ".btnAdultsChildren", function () {
			var $this = $(this),
				name = $this.closest("tr").prevUntil("tr.mainPeriod").last().prev().find("input[name^='start_date']").attr("name"),
				m = name.match(/\[((new_)?\d+)\]/),
				$c = $("#periodAdults").find("tbody").clone(),
				r = $c.html()
					.replace(/\{INDEX\}/g, m !== null ? m[1] : "")
					.replace(/\{RAND\}/g, 'x_' + Math.ceil(Math.random() * 999999));
			
			$this.closest("tr").before(r);			
		}).on("click", ".btnDeletePeriod", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$('#modalDeletePeriod').data("link", $(this)).modal('show');
			return false;
		}).on("click", ".btnConfirmDeletePeriod", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $link = $('#modalDeletePeriod').data("link"),
				$tr = $link.closest("tr").nextUntil(".mainPeriod").addBack(),
				$id = $link.data("id");
			$.post("index.php?controller=pjAdminCalendars&action=pjActionDeletePeriod", {
				"id": $id
			}).done(function (data) {
				if (data.code === undefined) {
					return;
				}
				switch (data.code) {
					case 200:
						$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
							$tr.remove();
							$('#modalDeletePeriod').modal('hide');
						});
						break;
				}
			});
			return false;
		}).on("click", ".btnRemovePeriod", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr").nextUntil(".mainPeriod").addBack();
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});				
			return false;
		}).on("click", ".btnRemoveAdultsChildren", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});			
			return false;
		}).on('submit', '#frmPeriods', function(e){
			var post, num,
				i = 0,
				$form = $(this),
				$tbody = $("#tblPeriods tbody"),
				$tr = $tbody.find("tr"),
				$main = $tr.filter(".mainPeriod"),
				len = $main.length,
				perLoop = 1,
				loops = len > perLoop ? Math.ceil(len / perLoop) : 1,
				l = Ladda.create($form.find(":submit").get(0));
			
			num = loops;
	
			$form.find(":input").not(".pj-button").attr("readonly", "readonly");
			$form.find(".pj-button").attr("disabled", "disabled");			
			$form.find(".bxPeriodStatus").hide();
			$form.find(".bxPeriodStatusStart").show();
			l.start();
			$.post("index.php?controller=pjAdminCalendars&action=pjActionDeletePeriods").done(function () {
				setPrices.call(null);
			});
	
			function setPrices() {
				$.ajaxSetup({async:false});
				post = $tr.filter(".mainPeriod").eq(i * perLoop).nextUntil(".mainPeriod").addBack().find(":input").serialize();
				i++;
				$.post("index.php?controller=pjAdminCalendars&action=pjActionSavePeriods", post, callback);
			}
			
			function callback() {
				num--;
				if (num > 0) {
			        setPrices.call(null);
			    } else {
			    	l.stop();
			    	$form.find(":input").removeAttr("readonly");
			    	$form.find(".pj-button").removeAttr("disabled");
			    	$form.find(".bxPeriodStatusStart").hide();
			    	$form.find(".bxPeriodStatusEnd").show();
			    	setTimeout(function() {
			    		window.location.href = "index.php?controller=pjAdminCalendars&action=pjActionUpdate&id=" + $('#calendar_id').val() + "&tab=periods";
			    	}, 2000);
			        return;
			    }
			}
			return false;
		});
	});
})(jQuery);