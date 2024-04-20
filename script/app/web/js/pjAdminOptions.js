var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	"use strict";
	$(function () {
		var validate = ($.fn.validate !== undefined),
	    	multilang = ($.fn.multilang !== undefined),
	    	$document = $(document),
			$frmNotifications = $('#frmNotifications');
		
		if (multilang && 'pjBaseLocale' in window) {
			$(".multilang").multilang({
				langs: pjBaseLocale.langs,
				flagPath: pjBaseLocale.flagPath,
				tooltip: "",
				select: function (event, ui) {
					locale_id = ui.index;
				}
			});
		}
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

		function onChange() {
			var $box, base_code, new_code,
				$cal = $("#install_calendar"),
				$loc = $("#install_locale"),
				$mon = $("#install_months"),
				$area = $("textarea.textarea_install"),
				cal = $cal.find("option:selected").val(),
				loc = $loc.find("option:selected").val(),
				mon = $mon.find("option:selected").val();
			
			if (cal == 'all') {
				$mon.attr("disabled", "disabled").closest("div.form-group").hide();
				$box = $("#boxAvailability");
				base_code = $box.text();
				new_code = base_code;
			} else {
				$mon.removeAttr("disabled").closest("div.form-group").show();
				$box = $("#boxStandard");
				base_code = $box.text();
				new_code = base_code.replace(/{CID}/g, cal).replace(/{VIEW}/g, mon);
			}

			if (loc.length > 0) {
				new_code = new_code.replace(/{LOCALE}/g, '&locale=' + loc);
			} else {
				new_code = new_code.replace(/{LOCALE}/g, '');
			}
			
			$area.val(new_code);
		}
		
		if ($("#boxStandard").length > 0) {
			onChange.call(null);
		}
		
		$(document).on("focus", ".textarea_install", function (e) {
			var $this = $(this);
			$this.select();
			$this.mouseup(function() {
				$this.unbind("mouseup");
				return false;
			});
		}).on("change", "#install_calendar", function (e) {	
			onChange.call(null);
		}).on("change", "#install_locale", function (e) {
			onChange.call(null);
		}).on("change", "#install_months", function (e) {
			onChange.call(null);
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
			
			$.get("index.php?controller=pjAdminOptions&action=pjActionNotificationsGetMetaData", params).done(function (data) {
				
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
			
			$.get("index.php?controller=pjAdminOptions&action=pjActionNotificationsGetContent", {
				recipient: $('input[name="recipient"]:checked').val(),
				variant: $checked.val(),
				transport: $checked.data("transport")
			}).done(function (data) {
				
				$box.html(data);
				
				myTinyMceDestroy.call(null);
				myTinyMceInit.call(null, 'textarea.mceEditor');
				
				var index = $(".pj-form-langbar-item.btn-primary").data("index");
				if (index !== undefined) {
					$box.find('.pj-multilang-wrap[data-index!="' + index + '"]').hide();
					$box.find('.pj-multilang-wrap[data-index="' + index + '"]').show();
				}
				
				$('.notifyTokens').hide();
				if($checked.val() == 'new_property')
				{
					$('.propertyTokens').show();
				}else if($checked.val() == 'new_user' || $checked.val() == 'password_reminder' || $checked.val() == 'welcome_msg'){
					$('.ownerTokens').show();
				}else if($checked.val() == 'property_payment' ){
					$('.propertyPaymentTokens').show();
				}else{
					$('.reservationTokens').show();
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
				$form = $box.find("form");
			
			if (toggle) {
				postData = $.param({
					is_active: ($form.find("#is_active").is(":checked") ? 1 : 0),
					id: $form.find('input[name="id"]').val()
				});
			} else {
				postData = $form.serialize();
				postData = postData.replace(/&?is_active=(\w+)?/, "");
				
				var l = Ladda.create($form.find(":submit").get(0));
				l.start();
			}
			
			$.post("index.php?controller=pjAdminOptions&action=pjActionNotificationsSetContent", postData).done(function (data) {
				
				if (data && data.status && data.status === "OK") {
					
					notificationsGetMetaData.call(null);
					
				}
				
			});
		}
		
		$("#boxNotificationsWrapper").on("change", 'input[name="recipient"]', function () {
			
			var search = window.location.search,
				recipient = search.match(/&?recipient=(\w+)/),
				variant = search.match(/&?variant=(\w+)/),
				transport = search.match(/&?transport=(\w+)/);
			
			var arr = [];
			arr.push("index.php?controller=pjAdminOptions&action=pjActionNotifications&recipient=");
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
			
			var url = ["index.php?controller=pjAdminOptions&action=pjActionNotifications&recipient=", $('input[name="recipient"]:checked').val(), "&transport=", $this.data("transport"), "&variant=", $this.val()].join("");
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
			
		}).on("submit", "form", function (e) {
			e.preventDefault();
			
			notificationsSetContent.call(null, false);
			
			return false;
		});
		
		$('input[name="recipient"]:checked').trigger("change");
		
		var $topMenu = $("#page-wrapper").children(".row.border-bottom"),
			$iframe = $("#iframeEditor"),
			$body = $("body"),
			$window = $(window);
	
		function resizeIframe() {
			if (!$iframe.length) {
				return;
			}
	
			$iframe.height($window.height() - $topMenu.outerHeight());
		}
	
		if ($iframe.length) {
	
			$iframe.on('load', function () {
			    var body = this.contentWindow.document.body;
			    if (body.getAttribute('data-editor'))
	            {
	                var script = document.createElement('script');
	                script.type = 'text/javascript';
	                script.async = true;
	                script.src = body.getAttribute('data-editor');
	                window.setTimeout(function () {
	                    body.appendChild(script);
	                }, 1200);
	            }
	
				var head = this.contentWindow.document.getElementsByTagName('head')[0],
					style = document.createElement('link');
				style.rel = 'stylesheet';
				style.href = 'third-party/font_awesome/4.7.0/css/font-awesome.min.css';
				head.appendChild(style);
			});
	
			$body.addClass("page-editor");
			resizeIframe.call(null);
	
			$window.on("resize", function () {
				resizeIframe.call(null);
			});
		}
	
		$(document).on('click', '.device-view', function (e) {
			e.preventDefault();
	
			var $this = $(this),
				device = $this.data('device'),
				orientation = $this.data('orientation'),
				$device = $('#iframeDevice'),
				$holder = $('#iframeHolder');
	
			$this.closest('.row').find('.device-view.active').removeClass('active').end().end().addClass('active');
	
			switch (device) {
			case 'desktop':
				$device.addClass('hidden');
				$iframe.insertBefore($device);
				$body.addClass('page-editor');
				$window.trigger('resize');
				break;
			case 'tablet':
			case 'phone':
				$iframe.appendTo($holder);
				$holder.removeClass().addClass(device + '-view-' + orientation);
				$device.removeClass('hidden');
				$body.removeClass('page-editor');
				$('#device_title').html($(['#', device, '_', orientation].join('')).html());
				$('#device_info').html($(['#', device, '_', orientation, '_info'].join('')).html());
				break;
			}
	
			return false;
		}).on('change', '#preview_calendar_id, #preview_months', function (e) {
		    e.preventDefault();
		    
		    var $cid = $('#preview_calendar_id').val(),
		    	$view = $('#preview_months').val();
			$iframe.attr('src', 'preview.php?cid='+$cid+'&view=' + $view);
			$('.open-new-window').attr('href', 'preview.php?cid='+$cid+'&view=' + $view);
		});
		
		$(window).on("popstate", function (e) {
			var state = e.originalEvent.state;
			if (state !== null) {
				//load
			} else {
				//empty
			}
		});
	});
})(jQuery);