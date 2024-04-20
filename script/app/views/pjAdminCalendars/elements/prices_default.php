<table cellspacing="0" cellpadding="0" style="width: 100%" data-idx="{RAND}">
	<tbody>
		<tr class="tab-price-item">
			<td>
				<select class="form-control" name="{INDEX}_adults[{RAND}]">
					<?php
					foreach (range(1, $tpl['option_arr']['o_bf_adults_max']) as $i)
					{
						?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
					}
					?>
				</select>
			</td>
		
			<td>
				<select class="form-control" name="{INDEX}_children[{RAND}]">
					<?php
					foreach (range(0, $tpl['option_arr']['o_bf_children_max']) as $i)
					{
						?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
					}
					?>
				</select>
			</td>
		
			<?php
			$i = 1;
			foreach ($price_days as $k => $v)
			{
				if ($i > 6)
				{
					$i = 0;
				}
				?><td>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></span>	
							<input type="text" class="form-control price-form-field required number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" name="{INDEX}_day_<?php echo $i; ?>[{RAND}]" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('prices_invalid_price', false, true);?>">
						</div>
					</div>
				</td><?php
				$i++;
			}
			?>
			<td>
				<div class="text-right">
					<a href="javascript:void(0);" class="btn btn-danger btn-outline btn-sm m-l-xs lnkRemovePriceRow"><i class="fa fa-trash"></i></a>
				</div>
			</td>
		</tr>
	</tbody>
</table>