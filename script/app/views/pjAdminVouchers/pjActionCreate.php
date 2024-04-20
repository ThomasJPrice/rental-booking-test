<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionCreate" method="post" id="frmCreateVoucher" class="form pj-form" autocomplete="off">
	<input type="hidden" name="voucher_create" value="1" />
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalDiscountLabel"><?php __('infoAddVoucherTitle');?></h4>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label class="control-label"><?php __('lblVoucherCode'); ?></label>
	
			<input type="text" name="code" id="code" maxlength="100" class="form-control required" data-msg-required="<?php __('pj_field_required');?>" data-msg-remote="<?php __('lblVoucherCodeExist'); ?>">
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
		<?php $apply_on = pjUtil::sortArrayByArray(__('apply_on'), array('room','extra','both'));?>
		<div class="form-group">
			<label class="control-label"><?php __('lblApplyDiscountOn');?></label>
	
			<select class="form-control required" name="apply_on" id="apply_on" data-msg-required="<?php __('pj_field_required');?>">
				<?php
				foreach($apply_on as $k => $v)
				{
					?>
					<option value="<?php echo $k?>" <?php echo $k == 'room' ? 'selected="selected"' : '';?>><?php echo $v;?></option>
					<?php
				} 
				?>
			</select>
		</div>
	
		<label for=""><?php __('lblVoucherCode'); ?></label>
	
		<div class="row">
			<div class="col-sm-4">
				<div class="form-group">
					<input class="touchspin3 form-control required" type="text" value="1" name="used_count" id="used_count" data-msg-required="<?php __('pj_field_required');?>">
				</div>
			</div>
	
			<div class="col-sm-8">
				<label class="control-label m-t-xs"><?php __('reservations');?></label>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnClose')?></button>
		<button type="button" class="ladda-button btn btn-primary btn-phpjabbers-loader btnSaveVoucher" data-style="zoom-in" style="margin-right: 15px;">
			<span class="ladda-label"><?php __('btnSave'); ?></span>
			<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
		</button>
	</div>
</form>