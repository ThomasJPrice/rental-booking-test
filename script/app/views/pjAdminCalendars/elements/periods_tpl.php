<tr class="mainPeriod" data-idx="{INDEX}">
	<td>
		<div class="input-group"> 
			<input type="text" name="start_date[{INDEX}]" id="start_date_{INDEX}" value="<?php echo date($tpl['option_arr']['o_date_format'], time());?>" class="form-control datepick required" readonly="readonly" data-msg-required="<?php __('pj_field_required');?>" /> 
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
		</div>
	</td>
	<td>
		<div class="input-group"> 
			<input type="text" name="end_date[{INDEX}]" id="end_date_{INDEX}" value="<?php echo date($tpl['option_arr']['o_date_format'], time());?>" class="form-control datepick required" readonly="readonly" data-msg-required="<?php __('pj_field_required');?>" /> 
			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
		</div>
	</td>
	<td>
		<select name="from_day[{INDEX}]" class="form-control">
		<?php
		foreach ($days as $index => $day)
		{
			?><option value="<?php echo $index; ?>"><?php echo $day; ?></option><?php
		}
		?>
		</select>
	</td>
	<td>
		<select name="to_day[{INDEX}]" class="form-control">
		<?php
		foreach ($days as $index => $day)
		{
			?><option value="<?php echo $index; ?>"><?php echo $day; ?></option><?php
		}
		?>
		</select>
	</td>
	<td class="w30"><a href="#" class="btn btn-danger btn-outline btn-sm btnRemovePeriod" data-index="{INDEX}"><i class="fa fa-trash"></i> <?php __('btnDelete');?></a></td>
</tr>
<tr>
	<td colspan="2" class="text-right"><?php __('period_default_price'); ?></td>
	<td colspan="3">
		<div class="input-group">
			<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></span>	
			<input type="text" class="form-control price-form-field required number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" name="default_price[{INDEX}]" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('prices_invalid_price', false, true);?>">
		</div>
	</td>
</tr>
<tr>
	<td colspan="2" class="text-right">
		<a href="javascript:void(0);" class="btn btn-primary btn-outline btnAdultsChildren" data-idx="{INDEX}"><i class="fa fa-plus"></i> <?php __('period_adults_children');?></a>
	</td>
	<td colspan="3"></td>
</tr>