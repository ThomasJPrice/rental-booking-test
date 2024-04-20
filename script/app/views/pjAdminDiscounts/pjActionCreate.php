<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDiscounts&amp;action=pjActionCreate" method="post" id="frmCreateDiscount" class="form pj-form" autocomplete="off">
	<input type="hidden" name="discount_create" value="1" />
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalDiscountLabel"><?php __('infoAddDiscountTitle');?></h4>
	</div>
	<div class="modal-body">
		<?php if ($tpl['is_flag_ready']) : ?>
		<div class="row">
			<div class="col-sm-12 btn-group-languages text-right">
				<div class="multilang"></div>
			</div>
		</div><!-- /.row -->
		
		<?php endif; ?>
        <div class="form-group">
            <label class="control-label"><?php __('lblDiscountName'); ?></label>

            <?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group ' : NULL;?>pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
					<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][name]" data-msg-required="<?php __('pj_field_required');?>">	
					<?php if ($tpl['is_flag_ready']) : ?>
					<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
					<?php endif; ?>
				</div>
				<?php 
			}
			?>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <label class="control-label"><?php __('lblDateFrom'); ?></label>
                    
                    <div class="input-group"> 
						<input type="text" name="date_from" id="date_from" class="form-control datepick required" value="<?php echo date($tpl['option_arr']['o_date_format'], time());?>" readonly="readonly" data-msg-required="<?php __('pj_field_required');?>" /> 
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
                </div>

                <div class="col-sm-6 col-xs-12">
                    <label class="control-label"><?php __('lblDateTo'); ?></label>

                    <div class="input-group"> 
						<input type="text" name="date_to" id="date_to" class="form-control datepick required" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime('+7 days'));?>" readonly="readonly" data-msg-required="<?php __('lblFieldRequired');?>" />
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
                </div>
            </div>
        </div>
		<?php $apply_conditions = pjUtil::sortArrayByArray(__('apply_conditions'), array('period','made','both'));?>
        <div class="form-group m-t-md">
        	<?php foreach($apply_conditions as $k => $v) { ?>
	            <div class="m-b-xs">
	            	<label class="control-label"><input type="radio" class="i-checks" name="condition" value="<?php echo $k;?>" <?php echo $k=='period' ? ' checked="checked"' : null;?>> <span class="m-l-xs"><?php echo $v;?></span></label>
	            </div>
			<?php } ?>
        </div>

        <label class="control-label"><?php __('lblDiscount'); ?></label>

        <div class="row">
            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
					<div class="input-group">
						<input class="form-control text-right required number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" type="text" id="discount" name="discount" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('prices_invalid_price', false, true);?>">
						<span class="input-group-addon"><strong id="icon_type"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></strong></span>
					</div>
				</div>
            </div>

            <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <select class="form-control required" name="type" id="type" data-msg-required="<?php __('pj_field_required');?>">
                    	<?php
						foreach (__('voucher_types', true, false) as $k => $v)
						{
							?><option value="<?php echo $k; ?>" data-sign="<?php echo $k == 'amount' ? pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) : '%'; ?>"><?php echo $v; ?></option><?php
						}
						?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label"><?php __('lblCalendars'); ?></label>

            <select name="calendar_id[]" id="calendar_id" class="select-item form-control required" multiple data-msg-required="<?php __('pj_field_required');?>" data-placeholder="-- <?php __('lblChoose');?> --">
                <?php
				foreach ($tpl['calendar_arr'] as $v)
				{
					?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
				}
				?>
            </select>
        </div>

		<?php $discount_options = pjUtil::sortArrayByArray(__('discount_options'), array('early','persons','family', 'duration'));?>
		<div class="form-group">
	        <label class="control-label"><?php __('lblDiscountOptions'); ?></label>
	
	        <select class="form-control required" name="options" id="options" data-msg-required="<?php __('pj_field_required');?>">
	            <option value="">-- <?php __('lblChoose'); ?>--</option>
				<?php
				foreach ($discount_options as $k => $v)
				{
					?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
				}
				?>
	        </select>
       </div>
       
       <div class="form-group optionsBox earlyBox">
        	<label class="control-label"><?php echo $discount_options['early']; ?></label>
        	<input class="touchspin3 form-control abOptions" type="text" id="early_days" name="early_days" data-msg-required="<?php __('pj_field_required');?>" />
       </div>
       
       <div class="form-group optionsBox personsBox">
        	<label class="control-label"><?php echo $discount_options['persons']; ?></label>
        	<div class="row">
        		<div class="col-xs-6">
        			<input class="touchspin3 form-control abOptions" type="text" id="min_persons" name="min_persons" data-msg-required="<?php __('pj_field_required');?>" />
        			<small class="help-block m-b-none"><?php __('lblMinimum');?></small>
        		</div>
        		<div class="col-xs-6">
        			<input class="touchspin3 form-control abOptions" type="text" id="max_persons" name="max_persons" data-msg-required="<?php __('pj_field_required');?>" />
        			<small class="help-block m-b-none"><?php __('lblMaximum');?></small>
        		</div>
        	</div>
       </div>
       
       <div class="form-group optionsBox durationBox">
        	<label class="control-label"><?php echo $discount_options['duration']; ?></label>
        	<div class="row">
        		<div class="col-xs-6">
        			<input class="touchspin3 form-control abOptions" type="text" id="min_duration" name="min_duration" data-msg-required="<?php __('pj_field_required');?>" />
        			<small class="help-block m-b-none"><?php __('lblMinimum');?></small>
        		</div>
        		<div class="col-xs-6">
        			<input class="touchspin3 form-control abOptions" type="text" id="max_duration" name="max_duration" data-msg-required="<?php __('pj_field_required');?>" />
        			<small class="help-block m-b-none"><?php __('lblMaximum');?></small>
        		</div>
        	</div>
       </div>
       
    </div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnClose')?></button>
		<button type="button" class="ladda-button btn btn-primary btn-phpjabbers-loader btnSaveDiscount" data-style="zoom-in" style="margin-right: 15px;">
			<span class="ladda-label"><?php __('btnSave'); ?></span>
			<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
		</button>
	</div>
</form>