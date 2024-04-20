/*!
 * Availability Booking Calendar v4.0
 * http://www.phpjabbers.com/availability-booking-calendar/
 * 
 * Copyright 2013, PHPJabbers
 * http://www.phpjabbers.com/license-agreement.php
 * http://www.phpjabbers.com/licence-explained.php
 * 
 * Date: Fri Aug 02 09:42:32 2013 +0200
 */
(function (window, undefined) {
	"use strict";
	var document = window.document;
	
	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	
	function ABTooltip(opts) {
		if (!(this instanceof ABTooltip)) {
			return new ABTooltip(opts);
		}
		
		this.opts = null;
		this.$tooltip = null;
		this.$tooltipInner = null;
		
		this.init.call(this, opts);
		
		return this;
	}
	
	ABTooltip.prototype = {
		init: function (opts) {
			this.opts = opts;
			
			var pid = 'abCalendarTooltip_' + this.opts.cid;
			pjQ.$("#" + pid).remove();
			this.$tooltip = pjQ.$('<div style="display: none" class="abCalendarTooltip" id="' + pid + '"></div>').appendTo("body");
			this.$tooltipInner = pjQ.$('<div class="abCalendarTooltipInner"></div>').appendTo(this.$tooltip);
		},
		hide: function () {
			this.$tooltip.hide().css({
				"left": 0,
				"top": 0
			});
			this.$tooltipInner.html("");
		},
		show: function (el) {
			var $this = pjQ.$(el),
				offset = $this.offset();
			
			this.$tooltipInner.html($this.find(".abCalendarLinkDate").data("price"));
			this.$tooltip.show().css({
				"left": (offset.left + ($this.outerWidth() - this.$tooltip.outerWidth()) / 2) + "px",
				"top": (offset.top - this.$tooltip.outerHeight()) + "px"
			});
		}	
	};
	
	function ABCancelIcon(opts) {
		if (!(this instanceof ABCancelIcon)) {
			return new ABCancelIcon(opts);
		}
		
		this.opts = null;
		this.$cancel = null;
		
		this.init.call(this, opts);
		
		return this;
	}
	
	ABCancelIcon.prototype = {
		init: function (opts) {
			this.opts = opts;
			
			var pid = 'abCalendarCancel_' + this.opts.cid;
			pjQ.$("#" + pid).remove();
			
			this.$cancel = pjQ.$('<div style="display: none" class="abCalendarCancel" id="' + pid + '"></div>').appendTo("body");
		},
		hide: function () {
			this.$cancel.hide().css({
				"left": 0,
				"top": 0
			});
		},
		show: function (el) {
			var $this = pjQ.$(el),
				offset = $this.offset();
			
			this.$cancel.show().css({
				"left": (offset.left + $this.outerWidth() - this.$cancel.outerWidth()) + "px",
				"top": (offset.top + $this.outerHeight() - this.$cancel.outerHeight()) + "px"
			});
		}	
	};
	
	function ABCalendar(opts) {
		if (!(this instanceof ABCalendar)) {
			return new ABCalendar(opts);
		}
		
		this.selector = ".abCalendarDate, .abCalendarReservedNightsStart, .abCalendarReservedNightsEnd, .abCalendarPendingNightsStart, .abCalendarPendingNightsEnd, .abCalendarNightsReservedPending, .abCalendarNightsPendingReserved, .abCalendarNightsPendingPending, .abCalendarPartial";
		this.opts = null;
		this.$abWrapper = null;
		this.$abCalendar = null;
		this.$abMessage = null;
		this.$abMessageInner = null;
		this.$abLoader = null;
		this.tooltip = null;
		this.cancel = null;
		this.outerHeight = null;
		this.reset.call(this);
		this.init.call(this, opts);
		
		return this;
	}
	
	function log() {
		if (window && window.console && window.console.log) {
			window.console.log.apply(window.console, arguments);
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	ABCalendar.sortByTime = function (a, b) {
		var aTime = Number(a.getAttribute("data-time")),
			bTime = Number(b.getAttribute("data-time")); 
		return ((aTime < bTime) ? -1 : ((aTime > bTime) ? 1 : 0));
	};
	
	ABCalendar.prototype = {
		reset: function () {
			this.selectedTime = [];
			this.selectedClass = [];
			this.month = null;
			this.year = null;
			this.start_dt = null;
			this.end_dt = null;
			this.periods = [];
			this.paintedData = [];
			this.$firstCell = null;
			this.$secondCell = null;
			this.message_type = null;
			
			return this;
		},
		init: function (opts) {
			var mid,
				self = this;
			this.opts = opts;
			this.$abWrapper = pjQ.$("#pjWrapperRPBC_" + this.opts.cid);
			this.$abCalendar = pjQ.$("#abCalendar_" + this.opts.cid);
			this.$abLoader = pjQ.$("#abLoader_" + this.opts.cid);
			
			mid = "abCalendarMessage_" + this.opts.cid;
			pjQ.$("#" + mid).remove();
			this.$abMessage = pjQ.$('<div style="display: none" class="abCalendarMessage" id="' + mid + '"></div>').appendTo("body");
			this.$abMessageInner = pjQ.$('<div class="abCalendarMessageInner"></div>').appendTo(this.$abMessage);
			this.message_type = 'calendar';
			this.loadHandler.call(this);
			this.getCalendar.call(this, this.opts.year, this.opts.month);
			/*this.outerHeight = pjQ.$("#pjWrapperRPBC_" + this.opts.cid).parent().outerHeight();*/
			// Event delegation
			this.$abWrapper.on("click.ab", ".abCalendarLinkMonth", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = pjQ.$(this);
				
				self.message_type = 'calendar';
				self.loadHandler.call(self);
				self.getCalendar.call(self, $this.data('year'), $this.data('month'));
				
				return false;
			}).on("click.ab", ".abSelectorCancel", function () {
				self.start_dt = null;
				self.end_dt = null;
				pjQ.$(this).prepend('<i class="fa fa-repeat fa-spin"></i>&nbsp;');
				pjQ.$(this).attr("disabled", "disabled");
				pjQ.$(this).siblings().attr("disabled", "disabled");
				self.getCalendar.call(self, self.year, self.month);
			}).on("click.ab", ".abSelectorConfirm", function () {
				var $this = pjQ.$(this),
					$back = $this.siblings(".abSelectorReturn");
				$this.attr("disabled", "disabled");
				$back.attr("disabled", "disabled");
				$this.prepend('<i class="fa fa-repeat fa-spin"></i>');
				pjQ.$('.abBookingMsg').html('');
				pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionBookingSave&cid=", self.opts.cid, "&session_id=", self.opts.session_id].join("")).done(function (data) {
					if (data.code === undefined) {
						return;
					}
					if (parseInt(data.code, 10) === 200) 
					{
						self.getPaymentForm.call(self, data);
					} else {
						$this.removeAttr("disabled");
						$this.find(".fa-spin").remove();
						$back.removeAttr("disabled");
						pjQ.$('.abBookingMsg').html(data.text);
					}
				}).fail(function () {
					log("Deferred is rejected");
				});
			}).on("click.ab", ".abSelectorReturn", function () {
				var $this = pjQ.$(this),
					$continue = $this.siblings(".abSelectorConfirm");
				
				$this.attr("disabled", "disabled");
				$continue.attr("disabled", "disabled");
				$this.prepend('<i class="fa fa-repeat fa-spin"></i>&nbsp;');
				self.getBookingForm.call(self);
			}).on("change.ab", "select[name='payment_method']", function () {
				self.$abWrapper.find(".abCcWrap").hide();
				self.$abWrapper.find(".abBankWrap").hide();
				switch (pjQ.$("option:selected", this).val()) {
				case 'creditcard':
					self.$abWrapper.find(".abCcWrap").show();
					break;
				case 'bank':
					self.$abWrapper.find(".abBankWrap").show();
					break;
				}
			}).on("change.ab", "select[name='c_adults'], select[name='c_children']", function () {
				if(self.validateMaxPeople.call(self) == true && self.validateMinPeople.call(self) == true)
				{
					self.calcPrice.call(self);
				}
			}).on("click.ab", ".abSelectorLocale", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = pjQ.$(this);
				$this.addClass("abLocaleFocus").parent().parent().find("a.abSelectorLocale").not(this).removeClass("abLocaleFocus");
				this.message_type = 'calendar';
				self.loadHandler.call(self);
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionLocale", "&session_id=", self.opts.session_id].join(""), {
					"cid": self.opts.cid,
					"locale_id": $this.data("id")
				}).done(function (data) {
					var year = self.year,
						month = self.month;
					self.reset.call(self);
					self.message_type = 'calendar';
					self.loadHandler.call(self);
					self.getCalendar.call(self, year, month);
				}).fail(function () {
					log("Deferred is rejected");
				});
				return false;
			}).on("click.ab", ".abSelectorTerms", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (pjQ.$.fn.liteDialog !== undefined) {
					pjQ.$.liteDialog({
		            	html: self.$abWrapper.find(".abSelectorTermsBody").html(),
		            	className: 'abDialog',
		            	width: '500px'
		            });
				} else {
					pjQ.$.getScript(self.opts.folder + "third-party/pj_litedialog/1.0.0/pjLiteDialog.min.js").done(function () {
						pjQ.$.liteDialog({
			            	html: self.$abWrapper.find(".abSelectorTermsBody").html(),
			            	className: 'abDialog',
			            	width: '500px'
			            });
					});
				}
				return false;
			}).on("mouseenter.ab", ".abCalendarCell:not(.abCalendarPast)", function (e) {
				self.mark.call(self, this);
			}).on("mouseenter.ab", ".abButtonDefault", function (e) {
				pjQ.$(this).addClass("abButtonDefaultHover");
			}).on("mouseenter.ab", ".abButtonCancel", function (e) {
				pjQ.$(this).addClass("abButtonCancelHover");
			}).on("mouseleave.ab", ".abButtonDefault", function (e) {
				pjQ.$(this).removeClass("abButtonDefaultHover");
			}).on("mouseleave.ab", ".abButtonCancel", function (e) {
				pjQ.$(this).removeClass("abButtonCancelHover");
			}).on("click.ab", ".abReturnToAvailability", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.message_type = 'calendars';
				self.loadHandler.call(self);
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionLoadAvail&index=", self.opts.index, "&locale=", self.opts.locale, "&session_id=", self.opts.session_id].join("")).done(function (data) {
					var template = data.template;
					self.$abWrapper.replaceWith(template.replace('{MSG}', self.opts.error_msg.calendars));
					if (ABCalendarAvailability !== undefined) {
						var abName = "ABCalendarAvailability_" + self.opts.index,
						options = {
							server: self.opts.server,
							folder: self.opts.folder,
							index: self.opts.index,
							locale: self.opts.locale,
							session_id: self.opts.session_id,
							year: self.year,
							month: self.month
						};
						window[abName] = new ABCalendarAvailability(options);
					}
				});
				return false;
			}).on("click.ab", ".abReturnToCalendar", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.start_dt = null;
				self.end_dt = null;
				self.message_type = 'calendar';
				self.loadHandler.call(self);
				self.getCalendar.call(self, self.year, self.month);
				return false;
			}).on("click.ab", ".abSelectorChangeDates", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.start_dt = null;
				self.end_dt = null;
				self.message_type = 'calendar';
				self.loadHandler.call(self);
				self.getCalendar.call(self, self.year, self.month);
				return false;
			}).on("click.ab", ".abExtraCheckbox", function (e) {
				var extra_id = pjQ.$(this).attr('data-id');
				if(pjQ.$(this).is(':checked'))
				{
					pjQ.$('#qty_' + extra_id).prop('disabled', false);
				}else{
					pjQ.$('#qty_' + extra_id).val(1).prop('disabled', true);
				}
				if(self.validateMaxPeople.call(self) == true && self.validateMinPeople.call(self) == true)
				{
					self.calcPrice.call(self);
				}
			}).on("keydown.ab", ".abTextQty", function (e) {
				if (e.shiftKey == true) {
	                e.preventDefault();
	            }
				if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 46) {
					if(self.validateMaxPeople.call(self) == true && self.validateMinPeople.call(self) == true)
					{
						self.calcPrice.call(self);
					}else{
						
					}
	            } else {
	            	e.preventDefault();
	            }
			}).on("click.ab", ".abReCalPrices", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(self.validateMaxPeople.call(self) == true && self.validateMinPeople.call(self) == true)
				{
					self.calcPrice.call(self);
				}
			}).on("change.ab", ".pjRpbExtraCount", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(self.validateMaxPeople.call(self) == true && self.validateMinPeople.call(self) == true)
				{
					self.calcPrice.call(self);
				}
			}).on("click.ab", ".abEnterPromo", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('#abPromoWrapper').toggle();
			}).on("click.ab", ".abPromoApply", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var promo_code = pjQ.$('#abPromoWrapper').find('input').val();
				if(promo_code != '')
				{
					if(self.validateMaxPeople.call(self) == true && self.validateMinPeople.call(self) == true)
					{
						self.calcPrice.call(self);
					}
				}
			}).on("click.ab", "#pjAbcCaptchaImage", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$(this).attr("src", pjQ.$(this).attr("src").replace(/(&rand=)\d+/g, '\$1' + Math.ceil(Math.random() * 99999)));
				pjQ.$('#pjAbcCaptchaInput').val("").removeData("previousValue");
				pjQ.$('#pjAbcCaptchaMessage').css('display', 'none');
				return false;
			});

			if (this.opts.accept_bookings) {
				this.$abWrapper.on("click.ab", this.selector, function (e) {
					if(self.start_dt == null)
					{
						pjQ.$(this).addClass('abCalendarFirstSelect');
						self.cancel.show(this);
						pjQ.$('.abCalendarCancel').click(function(e){
							self.selectedTime = [];
							self.selectedClass = [];
							self.start_dt = null;
							self.end_dt = null;
							self.paintedData = [];
							self.$firstCell = null;
							self.$secondCell = null;
							self.$abCalendar.find("td").removeClass("abCalendarSelect abCalendarMark abCalendarFirstSelect");
							pjQ.$(this).hide();
							pjQ.$('.abCalendarMessage').hide();
						});
					}
					self.select.call(self, this);
				}).on("mouseenter.ab", this.selector, function (e) {
					self.paint.call(self, this);
				});
			}
			
			if (this.opts.show_prices && this.opts.price_plugin == 'price') {
				
				this.tooltip = new ABTooltip({
					cid: this.opts.cid
				});
				
				this.$abWrapper.on("mouseenter.ab", this.selector, function (e) {
					self.tooltip.show(this);
				}).on("mouseleave.ab", this.selector, function (e) {
					self.tooltip.hide();
				});
			}
			
			if (this.opts.booking_behavior === 1) {
				pjQ.$(document).on("click.ab", function (e) {
					if (e.target.className.match(/abCalendar/) === null) {
						if (self.start_dt !== null && self.end_dt === null) {
							self.selectedTime = [];
							self.selectedClass = [];
							self.start_dt = null;
							self.end_dt = null;
							self.paintedData = [];
							self.$firstCell = null;
							self.$secondCell = null;
							self.$abCalendar.find("td").removeClass("abCalendarSelect abCalendarMark abCalendarFirstSelect");
							pjQ.$('.abCalendarCancel').hide();
							pjQ.$('.abCalendarMessage').hide();
						}
						self.errorHandler.call(self, "hide");
					}
				});
			}
			
			this.cancel = new ABCancelIcon({
				cid: this.opts.cid
			});
			
			pjQ.$(document).on("click.ab", 'button[data-dismiss="modal"]', function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $modal = pjQ.$(this).closest('.modal');
				if ($modal !== undefined && $modal.length > 0) {
					$modal.modal('hide');
					pjQ.$('body').removeClass('modal-open');
				}
				return false;
			});
		},
		calcPrice: function()
		{
			var self = this;
			var $form = self.$abWrapper.find(".abSelectorBookingForm");
			var disabled = $form.find('input[type=checkbox]:disabled').removeAttr('disabled');
			var serialized = $form.serialize();
			disabled.attr('disabled','disabled');
			pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionGetPrice&cid=", self.opts.cid, "&session_id=", self.opts.session_id].join(""),  serialized).done(function (data) {
				self.$abWrapper.find(".abSelectorPrice").html(data);
				if (self.$abWrapper.find(".abPaymentMethodWrap").length > 0) {
					var $payment_amount = parseFloat($form.find('input[name="payment_amount"]').val(), 10);
					if ($payment_amount > 0) {
						$form.find('select[name="payment_method"]').addClass('required');
						self.$abWrapper.find(".abPaymentMethodWrap").show();
					} else {
						$form.find('select[name="payment_method"]').removeClass('required');
						self.$abWrapper.find(".abPaymentMethodWrap").hide();
					}
				}
			}).fail(function () {
				log("Deferred is rejected");
			});
		},
		validateMaxPeople: function()
		{
			var number_of_adults = 0;
			var number_of_chidlren = 0;
			var max_people = parseInt(pjQ.$('#pjRpcMaxPeople').attr('data-max'));
			if(pjQ.$("select[name='c_adults']").length > 0)
			{
				if(pjQ.$("select[name='c_adults']").val() != '')
				{
					number_of_adults = parseInt(pjQ.$("select[name='c_adults']").val(), 10);
				}
			}
			if(pjQ.$("select[name='c_children']").length > 0)
			{
				number_of_chidlren = parseInt(pjQ.$("select[name='c_children']").val(), 10);
			}
			if(max_people < (number_of_adults + number_of_chidlren) )
			{
				pjQ.$('#pjRpcMaxPeople').show();
				pjQ.$('#pjRpcMinPeople').hide();
				return false;
			}else{
				pjQ.$('#pjRpcMaxPeople').hide();
				return true;
			}
		},
		validateMinPeople: function()
		{
			var number_of_adults = 0;
			var number_of_chidlren = 0;
			var min_people = parseInt(pjQ.$('#pjRpcMinPeople').attr('data-min'));
			if(pjQ.$("select[name='c_adults']").length > 0)
			{
				if(pjQ.$("select[name='c_adults']").val() != '')
				{
					number_of_adults = parseInt(pjQ.$("select[name='c_adults']").val(), 10);
				}
			}
			if(pjQ.$("select[name='c_children']").length > 0)
			{
				number_of_chidlren = parseInt(pjQ.$("select[name='c_children']").val(), 10);
			}
			if(min_people > (number_of_adults + number_of_chidlren) )
			{
				pjQ.$('#pjRpcMinPeople').show();
				pjQ.$('#pjRpcMaxPeople').hide();
				return false;
			}else{
				pjQ.$('#pjRpcMinPeople').hide();
				return true;
			}
		},
		errorGuide: function (click, range, time, el) {
			var str, pattern, i, iCnt,
				from, to,
				stack = [];
			if (click === 1) {
				str = this.opts.error_msg.valid_singular + " ";
				pattern = ["{FROM} ", this.opts.error_msg.till, " {TO}"].join("");
				//iCnt = range.from.length;
				iCnt = this.periods.length;
				if (iCnt > 1) {
					str = this.opts.error_msg.valid_plural + " ";
				}
				/*for (i = 0; i < iCnt; i += 1) {
					stack.push(pattern
						.replace('{FROM}', this.opts.days[new Date(range.from[i] * 1000).getDay()])
						.replace('{TO}', this.opts.days[new Date(range.to[i] * 1000).getDay()])
					);
				}*/
				for (i = 0; i < iCnt; i += 1) {
					if (time < this.periods[i].start_ts || time > this.periods[i].end_ts) {
						continue;
					} 
					stack.push(pattern
						.replace('{FROM}', this.opts.days[this.periods[i].from_day != 7 ? this.periods[i].from_day : 0])
						.replace('{TO}', this.opts.days[this.periods[i].to_day != 7 ? this.periods[i].to_day : 0])
					);
				}
				this.errorHandler.call(this, 'show', str + stack.join("; "), el);
			} else {
				str = this.opts.error_msg.should_click + " ";
				pattern = "{DAY}";

				if(range.from.length == 0)
				{
					range = this.$firstCell.data("range");
				}
				var $range_from = range.from;
				if($range_from.length == 0 && range.to.length > 0)
				{
					$range_from = range.to;
				}
				for (i = 0, iCnt = $range_from.length; i < iCnt; i += 1) {
					if (this.start_dt < time) {
						if ('toWeekDays' in range) {
							stack.push(pattern.replace('{DAY}', range.toWeekDays.join("|")));
						}else if ('fromWeekDays' in range) {
							stack.push(pattern.replace('{DAY}', range.fromWeekDays.join("|")));
						}
					} else if (this.start_dt > time) {
						if ('fromWeekDays' in range) {
							stack.push(pattern.replace('{DAY}', range.fromWeekDays.join("|")));
						}else if ('toWeekDays' in range) {
							stack.push(pattern.replace('{DAY}', range.toWeekDays.join("|")));
						}
					}
				}
				this.errorHandler.call(this, 'show', str + stack.join(" " + this.opts.error_msg.or + " "), el);
			}
		},
		errorHandler: function (type, message, el) {
			if (type === 'show') {
				var $el = pjQ.$(el),
					offset = $el.offset();
			
				this.$abMessageInner.html(message);
				this.$abMessage.show().css({
					"left": (offset.left + ($el.outerWidth() - this.$abMessage.outerWidth()) / 2) + "px",
					"top": (offset.top - (this.$abMessage.outerHeight())) + "px"
				}).show();
			} else {
				this.$abMessage.hide().css({
					"left": 0,
					"top": 0
				});
				this.$abMessageInner.html("");
			}
			
			return this;
		},
		loadHandler: function () {
			pjQ.$('.abCalendarTooltip').hide();
			pjQ.$('.abCalendarCancel').hide();
			this.errorHandler.call(this, 'hide');
			var msg = '',
				error_msg = pjQ.$('.abErrorMessage').data('msg');
			if (typeof error_msg === 'undefined')
			{
				error_msg = this.opts.load_msg;
			}
			if (typeof error_msg === 'undefined')
			{
				error_msg = this.opts.error_msg;
			}
			switch (this.message_type)
			{
				case 'calendar':
					msg = error_msg.calendar;
					break;
				case 'calendars':
					msg = error_msg.calendars;
					break;
				case 'form':
					msg = error_msg.form;
					break;
				case 'summary':
					msg = error_msg.summary;
					break;
				case 'save':
					msg = error_msg.save;
					break;
				case 'paypal':
					msg = error_msg.paypal;
					break;
				case 'authorize':
					msg = error_msg.authorize;
					break;
			}
			if (arguments.length !== 0) {
				msg = arguments[0];
			}
			if(this.message_type != 'paypal' && this.message_type != 'authorize')
			{
				this.$abCalendar.html("");
			}	
			this.$abLoader.find('span.abLoaderMessage').html(msg);
			this.$abLoader.show();

			return this;
		},
		checkDates: function (el) {
			var self = this;
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionCheckDates", "&session_id=", self.opts.session_id].join(""), {
				"cid": this.opts.cid,
				"start_dt": this.start_dt,
				"end_dt": this.end_dt
			}).done(function (data) {
				if (data.status === "OK") {
					self.message_type = 'form';
					self.loadHandler.call(self);
					pjQ.$('.abCalendarCancel').hide();
					self.getBookingForm.call(self);
				} else {
					self.errorHandler.call(self, 'show', self.opts.error_msg.range_na, el);
					return;
				}
			});
		},
		getBookingForm: function () {
			var self = this;
			
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetBookingForm", "&session_id=", self.opts.session_id].join(""), {
				"cid": self.opts.cid,
				"view": self.opts.view,
				"month": self.month,
				"year": self.year,
				"start_dt": self.start_dt,
				"end_dt": self.end_dt,
				"locale": self.opts.locale,
				"index": self.opts.index
			}).done(function (data) {
				self.$abCalendar.html(data);
				var $frmCheckout = self.$abCalendar.find(".abSelectorBookingForm"),
					$reCaptcha = self.$abCalendar.find('#g-recaptcha_' + self.opts.cid);
				if ($reCaptcha.length > 0)
	            {
	                grecaptcha.render($reCaptcha.attr('id'), {
	                    sitekey: $reCaptcha.data('sitekey'),
	                    callback: function(response) {
	                        var elem = pjQ.$("input[name='recaptcha']");
	                        elem.val(response);
	                        elem.valid();
	                    }
	                });
	            }
				
				self.$abLoader.hide();
				self.errorHandler.call(self, 'hide');
				self.$abWrapper.parent().css("height", "auto");
				
				$frmCheckout.validate({
					rules: {
						"captcha" : {
							remote: self.opts.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha&cid="+self.opts.cid+"&session_id=" + self.opts.session_id,
						},
						"recaptcha": {
	                        remote: self.opts.folder + "index.php?controller=pjFront&action=pjActionCheckReCaptcha&cid="+self.opts.cid+"&session_id=" + self.opts.session_id,
	                    },
					},
					ignore: ":hidden:not(.recaptcha)",
					onkeyup: false,
					errorClass: "abError",
					validClass: "abValid",
					//debug: true,
					errorPlacement: function (error, element) {
						error.insertAfter(element.parent());
					},
					submitHandler: function (form) {
						if(self.validateMaxPeople.call(self) == true && self.validateMinPeople.call(self) == true)
						{
							pjQ.$(form).find(":button, :submit").attr("disabled", "disabled");	
							pjQ.$(form).find(":submit").prepend('<i class="fa fa-repeat fa-spin"></i>&nbsp;');
							self.getSummaryForm.call(self, form);
						}
						return false;
					}
				});
			}).fail(function () {
				log("Deferred is rejected");
			});
		},
		_getCalendar: function (year, month) {
			var self = this;
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetCalendar", "&session_id=", self.opts.session_id].join(""), {
				"cid": self.opts.cid,
				"view": self.opts.view,
				"year": year,
				"month": month,
				"locale": self.opts.locale,
				"index": self.opts.index
			}).done(function (data) {
				self.$abWrapper.parent().css("height", self.outerHeight+"px");
				self.$abCalendar.html(data);
				self.$abCalendar.find('.abCalendarTable > tbody > tr').each(function(e){
					var $this = pjQ.$(this),
						empty_row = true;
					$this.find('td').each(function(event){
						if(pjQ.$(this).html() != '&nbsp;')
						{
							empty_row = false;
						}
						var $td = pjQ.$(this);
						var td = this;
						var dt = parseInt($td.attr('data-time'), 10);
						pjQ.$.each(self.paintedData, function(index, item){
							
							if(pjQ.$(item).attr('data-time') == $td.attr('data-time'))
							{
								$td.addClass('abCalendarSelect');
							}
						});
						if(self.start_dt !== null && dt==self.start_dt)
						{
							$td.addClass('abCalendarFirstSelect');
							pjQ.$('.abCalendarCancel').show();
						}
					});
					if(empty_row == true)
					{
						$this.remove();
					}
				});
				self.$abLoader.hide();
				
				pjQ.$(window).resize(res).trigger("resize");
				
				var dt = new Date();
				self.month = month || dt.getMonth() + 1;
				self.year = year || dt.getFullYear();
			
			}).fail(function () {
				log("Deferred is rejected");
			});
		},
		getCalendar: function (year, month) {
			var self = this;
			if (this.opts.price_plugin === 'period') {
				pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetPeriods", "&session_id=", self.opts.session_id].join(""), {
					"cid": self.opts.cid,
					"view": self.opts.view,
					"year": year,
					"month": month,
					"locale": self.opts.locale
				}).done(function (data) {
					self.periods = data;
					self._getCalendar.call(self, year, month);
				}).fail(function () {
					log("Deferred is rejected");
				});
			} else {
				this._getCalendar.call(this, year, month);
			}
		},
		getPaymentForm: function (obj) {
			var self = this;
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetPaymentForm&cid=", 
			           this.opts.cid, "&reservation_id=", obj.reservation_id, "&payment_method=", obj.payment_method, "&invoice_id=", obj.invoice_id, "&multi=", self.opts.multi, "&session_id=", self.opts.session_id].join("")).done(function (data) {
				self.$abCalendar.html(data);
				
				self.paintedData = [];
				var scrollTop = self.$abCalendar.offset().top - 10;
				if(scrollTop <= 0)
				{
					scrollTop = 0;
				}
				pjQ.$('html, body').animate({
			        scrollTop: scrollTop
			    }, 500);
				var $payment_form = self.$abCalendar.find("form[name='pjOnlinePaymentForm']").first();
				if ($payment_form.length > 0) {
					$payment_form.trigger('submit');
				}
			}).fail(function () {
				log("Deferred is rejected");
			});
		},
		getSummaryForm: function (form) {
			var self = this;
			var disabled = self.$abWrapper.find("form.abSelectorBookingForm").find('input[type=checkbox]:disabled').removeAttr('disabled');
			var qs = self.$abWrapper.find("form.abSelectorBookingForm").serialize();
			disabled.attr('disabled','disabled');
			pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionGetSummaryForm&cid=", self.opts.cid, "&view=", self.opts.view, "&locale=", self.opts.locale, "&index=", self.opts.index, "&session_id=", self.opts.session_id].join(""), qs).done(function (data) {
				self.$abCalendar.html(data);
			}).fail(function () {
				log("Deferred is rejected");
				pjQ.$(form).find(":button, :submit").removeAttr("disabled");
			});
		},
		clear: function (start_dt, end_dt) {
			var index, k, kCnt, a_c, a_t, a_p, tmp,
				self = this,
				selectedClass = [],
				selectedTime = [],
				paintedData = [];
			
			if (end_dt < start_dt) {
				tmp = start_dt;
				start_dt = end_dt;
				end_dt = tmp;
			}
			
			for (k = 0, kCnt = this.selectedTime.length; k < kCnt; k += 1) {
				if (this.selectedTime[k] >= start_dt && this.selectedTime[k] <= end_dt) {
					index = pjQ.$.inArray(this.selectedTime[k], this.selectedTime);
					a_c = this.selectedClass.slice(index, index + 1);
					a_t = this.selectedTime.slice(index, index + 1);
					selectedClass.push(a_c[0]);
					selectedTime.push(a_t[0]);
					
					a_p = pjQ.$.grep(this.paintedData, (function (time) {
						return function (el, i) {
							return el !== undefined && el.getAttribute("data-time") == time;
						};
					})(this.selectedTime[k]));
					paintedData.push(a_p[0]);
				}
			}
			
			this.paintedData = paintedData;
			this.selectedClass = selectedClass;
			this.selectedTime = selectedTime;
		},
		paint: function (el) {
			var end_dt, $item, time,
				self = this,
				$el = pjQ.$(el);
			// Ensure that the first click (start date) is already fired/selected
			if (this.start_dt !== null && this.end_dt === null) {
				end_dt = parseInt($el.data("time"), 10);

				this.$abWrapper.find(this.selector).each(function (i, item) {
					$item = pjQ.$(item);
					time = parseInt($item.data("time"), 10);
					if ((self.start_dt > end_dt && time <= self.start_dt && time >= end_dt) || 
						(self.start_dt < end_dt && time >= self.start_dt && time <= end_dt)) {
						
						$item.addClass("abCalendarSelect");
						// Add painted table cell to the stack
						self.paintedData.push($item.get(0));
						self.paintedData = pjQ.$.unique(self.paintedData);
					} else {
						$item.removeClass("abCalendarSelect");
						// Remove table cell from the stack
						self.paintedData = pjQ.$.grep(self.paintedData, function (value) {
							return value != $item.get(0);
						});
					}
				});
			}
		},
		mark: function (el) {
			var end_dt, $item, time, index,
				self = this,
				$el = pjQ.$(el);
			// Ensure that the first click (start date) is already fired/selected
			if (this.start_dt !== null && this.end_dt === null) {
				end_dt = parseInt($el.data("time"), 10);

				this.$abWrapper.find(".abCalendarCell:not(.abCalendarPast)").each(function (i, item) {
					$item = pjQ.$(item);
					time = parseInt($item.data("time"), 10);
					index = pjQ.$.inArray(time, self.selectedTime);
					if ((self.start_dt > end_dt && time <= self.start_dt && time >= end_dt) || 
						(self.start_dt < end_dt && time >= self.start_dt && time <= end_dt)) {
						
						if (index === -1) {
							self.selectedTime.push(time);
							self.selectedClass.push($item.attr("class"));
						}
						
						$item.addClass("abCalendarMark");
					} else {
						if (index !== -1) {
							self.selectedTime.splice(index, 1);
							self.selectedClass.splice(index, 1);
						}
						
						$item.removeClass("abCalendarMark");
					}
				});
			}
		},
		select: function (el) {
			switch (this.opts.price_plugin) {
			case 'price':
				this._price.call(this, el);
				break;
			case 'period':
				this._period.call(this, el);
				break;
			}
		},
		_first: function ($el, time) {
			this.start_dt = time;
			this.$firstCell = $el;
			
			if (this.opts.booking_behavior === 2 && this.opts.price_plugin === "price") {
				// Single booking
				this.end_dt = this.start_dt;
				this.$secondCell = this.$firstCell;
				this.checkDates.call(this, $el.get(0));
				//this.getBookingForm.call(this);
			}
		},
		_second: function ($el, time) {
			this.end_dt = time;
			this.$secondCell = $el;
			this.checkDates.call(this, $el.get(0));
			//this.getBookingForm.call(this);
		},
		_limit: function (end_dt, tdays, el) {
			// Check limits for selected dates
			var index, x, i, iCnt, j, msg,
				start = this.start_dt < end_dt ? this.start_dt : end_dt, 
				end = this.start_dt < end_dt ? end_dt : this.start_dt,
				passDate = [], limitDate = [], 
				passedDate = [], limitedDate = [];
			
			iCnt = this.opts.limits.length;
			if (iCnt > 0) {
				//for (j = start, x = 0; j < end; j += 86400, x += 1) {
				for (j = start, x = 0; j <= end; j += 86400, x += 1) { //Fix for start == end (single day)
					for (i = 0; i < iCnt; i += 1) {
						// Checked date is found in Limits array
						if (this.opts.limits[i].min_nights > 0 && this.opts.limits[i].max_nights >= 0 && j >= this.opts.limits[i].ts_from && j <= this.opts.limits[i].ts_to) {
							//passDate[x] = false;
							//limitDate[x] = this.opts.limits[i];
							// Number of days/nights fit to boundaries
							
							if(this.opts.limits[i].max_nights > 0)
							{
								if (tdays >= this.opts.limits[i].min_nights && tdays <= this.opts.limits[i].max_nights) {
									passedDate.push(true);
								} else {
									passedDate.push(false);
									limitedDate.push(this.opts.limits[i]);
								}
							}else{
								if (tdays >= this.opts.limits[i].min_nights) {
									passedDate.push(true);
								} else {
									passedDate.push(false);
									limitedDate.push(this.opts.limits[i]);
								}
							}
						}
					}
					//break; //Only for Start date. Comment the break statement to apply for all dates between Start and End
				}
			}
			log(passedDate, limitedDate);	
			//if (passDate.length > 0) {
			if (passedDate.length > 0) {
				//index = pjQ.$.inArray(false, passDate);
				index = pjQ.$.inArray(false, passedDate);
				if (index !== -1) {
					if(limitedDate[index].max_nights > 0)
					{
						msg = this.opts.error_msg.limits;
						if(this.opts.price_based_on == 'days')
						{
							msg = this.opts.error_msg.limits_days;
						}
						if (msg.indexOf("{MIN}") === -1 || msg.indexOf("{MAX}") === -1 || msg.indexOf("{YOUR}") === -1) {
							msg = this.opts.error_msg.limit;
						}
					}else{
						if(this.opts.price_based_on == 'nights')
						{
							msg = this.opts.error_msg.min_limits;
						}else{
							msg = this.opts.error_msg.min_limits_days;
						}
					}
					this.errorHandler.call(this, 'show', msg
						//.replace(/{MIN}/g, limitDate[index].min_nights)
						//.replace(/{MAX}/g, limitDate[index].max_nights)
						.replace(/{MIN}/g, limitedDate[index].min_nights)
						.replace(/{MAX}/g, limitedDate[index].max_nights)
						.replace(/{YOUR}/g, tdays),
						el
					);
					return false;
				}
			}
			
			return true;
		},
		_clear: function()
		{
			if (this.start_dt === null && this.end_dt === null) 
			{
				this.selectedTime = [];
				this.selectedClass = [];
				this.start_dt = null;
				this.end_dt = null;
				this.paintedData = [];
				this.$firstCell = null;
				this.$secondCell = null;
				this.$abCalendar.find("td").removeClass("abCalendarSelect abCalendarMark abCalendarFirstSelect");
				pjQ.$('.abCalendarCancel').hide();
			}
		},
		_period: function (el) {
			var tdays, end_dt, i, iCnt, cellRange,
				reverse, crFirst, crLast, 
				passed, $firstRange, firstTime, firstDayOfWeek,
				$el = pjQ.$(el),
				time = parseInt($el.data("time"), 10),
				range = $el.data("range"),
				dayOfWeek = parseInt($el.data("dayofweek"), 10);
			if (this.start_dt === null && this.end_dt === null) {
				// First click (Start date)
				this.paintedData = [];
				pjQ.$('.abCalendarMessage').hide();
				if (range.start === null && range.end === null && range.middle === null) {
					log('Out of range (first click)');
					this.errorHandler.call(this, 'show', this.opts.error_msg.range_out, el);
					this._clear.call(this);
					return;
				}
				
				// weekly booking
				if (range.start === null && range.end === null && range.middle !== null) {
					log('Daily bookings are disabled 1');
					//this.errorHandler.call(this, 'show', "Daily bookings are disabled (first click)", el);
					this.errorGuide.call(this, 1, range, time, el);
					this._clear.call(this);
					return;
				}
				
				if (range.start === null && range.end !== null && range.middle !== null) {
					log('Daily bookings are disabled 2');
					//this.errorHandler.call(this, 'show', "Daily bookings are disabled (first click)", el);
					this.errorGuide.call(this, 1, range, time, el);
					this._clear.call(this);
					return;
				}
				
				if (range.start === null && range.end !== null && range.middle == null) {
					log('Daily bookings are disabled 3');
					//this.errorHandler.call(this, 'show', "Daily bookings are disabled (first click)", el);
					this.errorGuide.call(this, 1, range, time, el);
					this._clear.call(this);
					return;
				}
				
				if (range.in_out.length) {
					passed = false;
					for (i = 0, iCnt = range.in_out.length; i < iCnt; i += 1) {
						if (range.in_out[i].indexOf(dayOfWeek + "-") === 0) {
							passed = true;
							break;
						}
					}
					if (!passed) {
						log('Period is not valid: 1st - 1');
						this.errorGuide.call(this, 1, range, time, el);
						this._clear.call(this);
						return;
					}
				} else {
					log('Period is not in range: 1st - 2');
					this.errorGuide.call(this, 1, range, time, el);
					this._clear.call(this);
					return;
				}
				
				this._first.call(this, $el, time);
				return;
				
			} else {
				// Second click (End date)
				if (this.start_dt === time/* && this.opts.booking_behavior === 1 && this.opts.price_based_on === "nights"*/) {
					log('Single date booking is disabled (second click)');
					this.errorHandler.call(this, 'show', this.opts.error_msg.single_na, el);
					return;
				}
				
				if (range.start === null && range.end === null && range.middle === null) {
					log('Out of range (second click) 1');
					this.errorHandler.call(this, 'show', this.opts.error_msg.range_out, el);
					return;
				}
				
				$firstRange = this.$firstCell.data("range");
				firstTime = this.$firstCell.data("time");
				firstDayOfWeek = this.$firstCell.data("dayofweek");
				reverse = time > this.start_dt ? false : true;
				if (!reverse) {
					
					if (range.end === null && range.middle !== null) {
						log('Daily bookings are disabled 1');
						//this.errorHandler.call(this, 'show', "Daily bookings are disabled (second click)", el);
						this.errorGuide.call(this, 1, range, time, el);
						return;
					}
					if (range.start !== null && range.end === null && range.middle === null) {
						log('Out of range (second click) 2');
						this.errorHandler.call(this, 'show', this.opts.error_msg.range_out, el);
						return;
					}
					if ((range.end !== null && range.middle !== null) || (range.end !== null && range.middle === null)) 
					{
						var isValid = false;
						if ('toWeekDays' in $firstRange) {
							for (i = 0, iCnt = $firstRange.toWeekDays.length; i < iCnt; i += 1) 
							{
								var j,jCnt;
								for (j = 0, jCnt = range.toW.length; j < jCnt; j += 1) 
								{
									if( $firstRange.toWeekDays[i] == range.toW[j])
									{
										isValid = true;
									}
								}
							}
						}
						if(isValid == false)
						{
							log('Daily bookings are disabled 2', $firstRange, range);
							this.errorGuide.call(this, 1, range, time, el);
							return;
						}
					}
					
					if (range.in_out.length) {
						passed = false;
						for (i = 0, iCnt = range.in_out.length; i < iCnt; i += 1) {
							if (range.in_out[i] === [firstDayOfWeek, dayOfWeek].join("-")) {
								passed = true;
								break;
							}
						}
						if (!passed) {
							log('Period is not valid: 2nd - 1');
							this.errorGuide.call(this, 1, range, time, el);
							this._clear.call(this);
							return;
						}
					} else {
						log('Period is not in range: 2nd - 2');
						this.errorGuide.call(this, 1, range, time, el);
						this._clear.call(this);
						return;
					}
					
				} else {
					if (range.start === null && range.middle !== null) {
						log('Reverse! Daily bookings are disabled 1');
						//this.errorHandler.call(this, 'show', "Daily bookings are disabled (second click)", el);
						this.errorGuide.call(this, 1, $firstRange, time, el);
						return;
					}
					if ((range.start !== null && range.middle !== null) || (range.start !== null && range.middle === null)) 
					{						
						var isValid = false;
						if ('toWeekDays' in range) {
							for (i = 0, iCnt = range.toWeekDays.length; i < iCnt; i += 1) 
							{
								var j,jCnt;
								if($firstRange.toW)
								{
									for (j = 0, jCnt = $firstRange.toW.length; j < jCnt; j += 1) 
									{
										if( range.toWeekDays[i] == $firstRange.toW[j])
										{
											isValid = true;
										}
									}
								}
							}
						}
						if(isValid == false)
						{
							log('Reverse! Daily bookings are disabled: ' + range.fromWeekDays[1]);
							//this.errorHandler.call(this, 'show', "Daily bookings are disabled (second click)", el);
							this.errorGuide.call(this, 1, $firstRange, time, el);
							return;
						}
					}
					
					if (range.in_out.length) {
						passed = false;
						for (i = 0, iCnt = range.in_out.length; i < iCnt; i += 1) {
							if (range.in_out[i] === [dayOfWeek, firstDayOfWeek].join("-")) {
								passed = true;
								break;
							}
						}
						if (!passed) {
							log('Period is not valid: 2nd(reverse) - 1');
							this.errorGuide.call(this, 1, $firstRange, time, el);
							this._clear.call(this);
							return;
						}
					} else {
						log('Period is not in range: 2nd(reverse) - 2');
						this.errorGuide.call(this, 1, $firstRange, time, el);
						this._clear.call(this);
						return;
					}
				}
				
				end_dt = time;
				
				tdays = Math.abs(end_dt - this.start_dt) / 86400;
				if (this.opts.price_based_on === "days") {
					tdays += 1;
				}
				
				// Strip all dates that not conform to selected range
				this.clear.call(this, this.start_dt, end_dt);
				
				for (i = 0, iCnt = this.selectedClass.length; i < iCnt; i += 1) {
					if (this.selectedClass[i].match("abCalendarReserved") !== null && this.selectedClass[i].match("abCalendarReservedNights") === null) {
						log('You can not select fully booked days (second click)');
						this.errorHandler.call(this, 'show', this.opts.error_msg.fully_booked, el);
						return;
					}
				}
				
				// Sort TD cells
				this.paintedData.sort(ABCalendar.sortByTime);
				crFirst = pjQ.$(this.paintedData).first().data("range"); 
				crLast = pjQ.$(this.paintedData).last().data("range");
				for (i = 0, iCnt = this.paintedData.length; i < iCnt; i += 1) 
				{
					/*cellRange = pjQ.$(this.paintedData[i]).data("range");
					if (cellRange.start === null && cellRange.end === null && cellRange.middle === null) {
						log('Selected date range not allowed (second click)');
						this.errorHandler.call(this, 'show', this.opts.error_msg.range_na, el);
						return;
					}*/
					/*if (this.opts.price_based_on == "nights" && i > 0 && i < iCnt - 1 && cellRange.end && cellRange.start === null &&
						(crFirst.weekly === null || crLast.weekly === null)
					) {
						log('Period not allowed (second click)');
						this.errorHandler.call(this, 'show', this.opts.error_msg.period_na, el);
						return;
					}*/
				}
				
				// Bug ID: 1293
				/*if ((crFirst.start || crFirst.end) && crFirst.weekly != crLast.weekly) {
					this.errorGuide.call(this, 2, range, time, el);
					log('1');
					return;
				}*/
				if (crFirst && crLast) {
					if (crFirst.weekly == crLast.weekly && (crFirst.start === null || crLast.end === null)) {
						log('Invalid period');
						this.errorGuide.call(this, 1, range, time, el);
						return;
					}
				}
				
				// Check limits for selected dates
				if (!this._limit.call(this, end_dt, tdays, el)) {
					return;
				}
				
				this._second.call(this, $el, end_dt);
				return;
			}
		},
		_price: function (el) {
			var tdays, end_dt, i, iCnt,
				$el = pjQ.$(el),
				nightsStart = 0,
				nightsEnd = 0,
				pStart = false, 
				pEnd = false, 
				pendingReserved = false, 
				reservedPending = false, 
				partial = false,
				time = parseInt($el.data("time"), 10);
	
			if (this.start_dt === null && this.end_dt === null) {
				
				// First click (Start date)
				this._first.call(this, $el, time);
				return;
				
			} else {
				// Second click (End date)
				if (this.start_dt === time && this.opts.booking_behavior === 1 && this.opts.price_based_on === "nights") {
					log('Single date booking is disabled (second click)');
					this.errorHandler.call(this, 'show', this.opts.error_msg.single_na, el);
					return;
				}
				
				end_dt = time;
				
				tdays = Math.abs(end_dt - this.start_dt) / 86400;
				if (this.opts.price_based_on === "days") {
					tdays += 1;
				}

				// Strip all dates that not conform to selected range
				this.clear.call(this, this.start_dt, end_dt);
				
				for (i = 0, iCnt = this.selectedClass.length; i < iCnt; i += 1) {
					if (this.selectedClass[i].match("abCalendarPendingNightsStart") !== null) {
						pStart = true;
					}
					if (this.selectedClass[i].match("abCalendarPendingNightsEnd") !== null) {
						pEnd = true;
					}
					if (this.selectedClass[i].match("abCalendarNightsPendingReserved") !== null) {
						pendingReserved = true;
					}
					if (this.selectedClass[i].match("abCalendarNightsReservedPending") !== null) {
						reservedPending = true;
					}
					if (this.selectedClass[i].match("abCalendarPartial") !== null) {
						partial = true;
					}
				}
				
				if (pendingReserved && reservedPending) {
					log('p&r');
					this.errorHandler.call(this, 'show', this.opts.error_msg.fully_booked, el);
					return;
				}
				
				for (i = 0, iCnt = this.selectedClass.length; i < iCnt; i += 1) {
					if (this.selectedClass[i].match("abCalendarReserved") !== null && this.selectedClass[i].match("abCalendarReservedNights") === null) {
						log('Rvd');
						this.errorHandler.call(this, 'show', this.opts.error_msg.fully_booked, el);
						return;
					}
					
					if (this.selectedClass[i].match("abCalendarReservedNightsStart") !== null) {
						nightsStart += 1;
					}
					
					if (this.selectedClass[i].match("abCalendarReservedNightsEnd") !== null) {
						nightsEnd += 1;
					}

					if (this.selectedClass[i].match("abCalendarPending") !== null && 
						this.selectedClass[i].match("abCalendarPendingNights") === null && 
						//(this.selectedClass[i].match("abCalendarPendingNights") === null || (pStart && pEnd)) &&
						this.selectedClass[i].match("abCalendarPartial") === null) {
						log('Pndg');
						this.errorHandler.call(this, 'show', this.opts.error_msg.fully_booked, el);
						return;
					}
				}
				log(tdays);
				if (!partial && pStart && pEnd && this.opts.price_based_on === "days") {
					log('!ptl&start&end');
					this.errorHandler.call(this, 'show', this.opts.error_msg.fully_booked, el);
					return;
				}
				if (nightsStart > 1 || nightsEnd > 1 || (nightsStart==1 && nightsEnd==1 && tdays >=1 && this.opts.price_based_on === "days")) {
					log('nS&nE');
					this.errorHandler.call(this, 'show', this.opts.error_msg.fully_booked, el);
					return;
				}
				
				// Check limits for selected dates
				if (!this._limit.call(this, end_dt, tdays, el)) {
					return;
				}
				
				this._second.call(this, $el, end_dt);
				return;
			}
		}
	};
	
	// expose
	window.ABCalendar = ABCalendar;
})(window);

function res() {
    
	var _td = pjQ.$(".abCalendarTable td");
	var td_width = _td.width();
	_td.height(td_width);
	_td.css({"line-height": td_width + "px"});
}