var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		var select2 = ($.fn.select2 !== undefined),
			multilang = ($.fn.multilang !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if ($("#grid").length > 0 && datagrid) {
			var buttonsOpts = [];
			var actionsOpts = [];
			
			if (myLabel.has_update)
			{
				buttonsOpts.push({type: "edit", url: "index.php?controller=pjAdminExtras&action=pjActionUpdate&id={:id}"});
			}
			if (myLabel.has_delete)
			{
				buttonsOpts.push({type: "delete", url: "index.php?controller=pjAdminExtras&action=pjActionDeleteExtra&id={:id}"});
			}

			if (myLabel.has_delete_bulk) 
			{
				actionsOpts.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminExtras&action=pjActionDeleteExtraBulk", render: true, confirmation: myLabel.delete_confirmation});
			}
			
			var $grid = $("#grid").datagrid({
				buttons: buttonsOpts,
				columns: [
				          {text: myLabel.name, type: "text", sortable: true, editable: myLabel.has_update},
				          {text: myLabel.price, type: "text", sortable: true, editable: false},
				          {text: myLabel.max_count, type: "text", sortable: true, editable: false},
				          {text: myLabel.status, type: "toggle", sortable: true, editable: myLabel.has_update, positiveLabel: myLabel.active, positiveValue: "T", negativeLabel: myLabel.inactive, negativeValue: "F"}
			          ],
				dataUrl: "index.php?controller=pjAdminExtras&action=pjActionGetExtra" + pjGrid.queryString,
				dataType: "json",
				fields: ['name', 'price', 'max_count', 'status'],
				paginator: {
					actions: actionsOpts,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminExtras&action=pjActionSaveExtra&id={:id}",
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
			$grid.datagrid("load", "index.php?controller=pjAdminExtras&action=pjActionGetExtra" + pjGrid.queryString, content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("active").siblings(".btn").removeClass("active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminExtras&action=pjActionGetExtra" + pjGrid.queryString, content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.removeClass("btn-default").addClass("active").addClass("btn-primary").siblings(".btn").removeClass("active").removeClass("btn-primary").addClass("btn-default");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminExtras&action=pjActionGetExtra" + pjGrid.queryString, content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on("change", "#price_type", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var price_type = $(this).val();
			if(price_type == 'count' || price_type == 'count_night')
			{
				$('#multi').prop('checked', true);
				$('.pjExtraSetMultiQty').hide();
			}else{
				$('#multi').prop('checked', false);
				if (price_type != 'one_time') {
					$('.pjExtraSetMultiQty').show();
				} else {
					$('.pjExtraSetMultiQty').hide();
				}
			}
			showMaxCount();
		}).on("click", "#multi", function (e) {
			showMaxCount();
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
		    $(this).valid();
		}).on("click", ".btnAddExtra, .pj-table-icon-edit", function (e) {
			var $url = $(this).attr("href");
			$("#myModalExtra").data('href', $url).modal('show');
			return false;
		});
		
		function showMaxCount()
		{
			var price_type = $('#price_type').val();
			if(price_type == 'count' || price_type == 'count_night')
			{
				$('#pjRpbMaxCount').css('display', 'block');
				$('#apply_max_count').val(1); 
			}else{
				if(!$('#multi').is(':checked'))
				{
					$('#pjRpbMaxCount').css('display', 'none');
					$('#apply_max_count').val(0);
				}else{
					$('#pjRpbMaxCount').css('display', 'block');
					$('#apply_max_count').val(1);
				}
			}
		}
		
		$("#myModalExtra").on("show.bs.modal", function(e) {
		    var $href = $("#myModalExtra").data('href');
		    $(this).find(".modal-content").load($href, function() {
		    	if (multilang && myLabel.isFlagReady == 1) {
					$(".multilang").multilang({
						langs: pjLocale.langs,
						flagPath: pjLocale.flagPath,
						tooltip: "",
						select: function (event, ui) {
							$("input[name='locale_id']").val(ui.index);					
						}
					});
					
					var $multilangItem = $('#myModalExtra').find('.pj-form-langbar-item');
					$multilangItem.each(function(e){
						var locale_id = $(this).attr('data-index');
						if(locale_id == myLabel.localeId)
						{
							$(this).trigger('click');
						}
					})
				}
		    	$('.tooltip-demo').tooltip({
			        selector: "[data-toggle=tooltip]"
			    });

		    	if ($(".select-item").length && select2) {
		            $(".select-item").select2({
		                allowClear: true,
		                dropdownParent: $("#myModalExtra .modal-content")
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
		        
		        if ($('#frmUpdateExtra').length > 0) {
		        	showMaxCount();
		        }
		    });
		}).on("click", ".btnSaveExtra", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $form = $(this).closest('form');
			if ($form.valid()) {
				var l = Ladda.create($(this).get(0));
				l.start();
				$.post($form.attr('action'), $form.serialize()).done(function (data) {
					var content = $grid.datagrid("option", "content");
					$grid.datagrid("load", "index.php?controller=pjAdminExtras&action=pjActionGetExtra" + pjGrid.queryString, content.column, content.direction, content.page, content.rowCount);
					l.stop();
					$("#myModalExtra").modal('hide');
				}).always(function () {
					l.stop();
				});
			}
			return false;
		});		
	});
})(jQuery);