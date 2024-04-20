<?php
$payment_amount = 0;
if (isset($tpl['price_arr']) && is_array($tpl['price_arr']))
{
	$total = $tpl['price_arr']['amount'] + $tpl['price_arr']['tax'] + $tpl['price_arr']['tourist_tax'];
	if($tpl['option_arr']['o_to_be_paid'] == 'on_arrival')
	{
		$deposit = $tpl['price_arr']['deposit'];
	}else{
		$deposit = $tpl['price_arr']['deposit'] - $tpl['price_arr']['security'];
	}
	
	if(count($tpl['price_arr']['extras']) > 0)
	{
		?>
		<div class="abParagraph">
			<div class="abParagraphInner">
				<label class="abTitle"><?php __('bf_extra_price'); ?><br /><span class="abSubTitle">(<?php __('front_price_for_selected_extras');?>)</span></label>
				<span class="abValue"><?php echo pjCurrency::formatPrice($tpl['price_arr']['extra_price']); ?></span>
			</div>
		</div>
		<?php
	}
	$nights = (int) $tpl['price_arr']['nights'];
	$sub_title = '';
	if ($tpl['option_arr']['o_price_based_on'] == 'days')
	{
		if($nights != 1)
		{
			$sub_title = str_replace("{DAYS}", $nights, __('front_for_days', true));
		}else{
			$sub_title = __('front_for_1_day', true);
		}
	}else{
		if($nights != 1)
		{
			$sub_title = str_replace("{NIGHTS}", $nights, __('front_for_nights', true));
		}else{
			$sub_title = __('front_for_1_nights', true);
		}
	}
	$option_arr = $tpl['option_arr'];	 
	?>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle"><?php __('bf_price'); ?><br /><span class="abSubTitle">(<?php echo $sub_title;?>)</span></label>
			<span class="abValue">
				<?php echo pjCurrency::formatPrice($tpl['price_arr']['amount']); ?>
				
			</span>
		</div>
	</div>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle"><?php __('bf_subtotal'); ?>
				<?php
				if(count($tpl['price_arr']['extras']) > 0)
				{ 
					?>
					<br /><span class="abSubTitle">(<?php __('bf_price'); ?> + <?php __('bf_extra_price'); ?>)</span>
					<?php
				} 
				?>
			</label>
			<span class="abValue"><?php echo pjCurrency::formatPrice($tpl['price_arr']['sub_total']); ?>
				<?php
				if($controller->_get->toString('action') == 'pjActionGetBookingForm' || $controller->_get->toString('action') == 'pjActionGetPrice')
				{ 
					?>
					&nbsp;&nbsp;
					<a href="#" class="abEnterPromo"><?php __('front_enter_promo_code')?></a>
					<?php
				} 
				?>
			</span>
		</div>
	</div>
	<?php
	if($controller->_get->toString('action') == 'pjActionGetBookingForm' || $controller->_get->toString('action') == 'pjActionGetPrice')
	{
		?>
		<div id="abPromoWrapper" class="abParagraph" style="display: <?php echo !empty($tpl['price_arr']['promo_code']) ? 'block' : 'none';?>">
			<div class="abParagraphInner">
				<label class="abTitle">&nbsp;</label>
				<span class="abValue">
					<input type="text" name="promo_code" value="<?php echo $tpl['price_arr']['promo_code'];?>" class="abText abPromoText"/>&nbsp;&nbsp;<a href="#" class="abPromoApply"><?php __('front_apply_promo_code')?></a>
					<?php
					if( $tpl['price_arr']['promo_valid'] == 0 && !empty($tpl['price_arr']['promo_code']))
					{ 
						?>
						&nbsp;&nbsp;<span class="text-danger"><?php __('front_invalid_promo_code');?></span>
						<?php
					} 
					?>
				</span>
			</div>
		</div>
		<?php
	}
	if((int) $tpl['price_arr']['discount_valid'] == 1)
	{
		?>
		<div class="abParagraph">
			<div class="abParagraphInner">
				<label class="abTitle"><?php echo !empty($tpl['price_arr']['discount_name'] ) ? $tpl['price_arr']['discount_name'] : __('bf_discount_amount', true); ?> <?php echo $tpl['price_arr']['discount_type'] == 'percent' ? number_format($tpl['price_arr']['discount_percentage'], 2) . '%' : ''; ?></label>
				<span class="abValue">
					<?php echo pjCurrency::formatPrice($tpl['price_arr']['discount_amount']); ?>
				</span>
			</div>
		</div>
		<?php
	}
	
	if((int) $tpl['price_arr']['promo_valid'] == 1)
	{
		?>
		<div class="abParagraph">
			<div class="abParagraphInner">
				<?php
				if($tpl['price_arr']['promo_type'] != 'amount')
				{ 
					?>
					<label class="abTitle"><?php __('front_promo_code_used'); ?><br /><span class="abSubTitle">(<?php echo $tpl['price_arr']['promo_percentage'];?>% <?php __('front_discount')?>)</span></label>
					<span class="abValue">
						<?php echo pjCurrency::formatPrice($tpl['price_arr']['promo_amount']); ?>
					</span>
					<?php
				}else{
					?>
					<label class="abTitle"><?php __('front_promo_code_used'); ?><br /><span class="abSubTitle">(<?php echo pjCurrency::formatPrice($tpl['price_arr']['promo_amount']); ?> <?php __('front_discount')?>)</span></label>
					<span class="abValue">
						<?php echo pjCurrency::formatPrice($tpl['price_arr']['promo_amount']); ?>
					</span>
					<?php
				} 
				?>
			</div>
		</div>
		<?php
	}
	if((int) $tpl['price_arr']['discount_valid'] == 1 || (int) $tpl['price_arr']['promo_valid'] == 1)
	{
		$new_sub_total = $tpl['price_arr']['amount_after_discount'];
		?>
		<div class="abParagraph">
			<div class="abParagraphInner">
				<label class="abTitle"><?php __('bf_subtotal'); ?></label>
				<span class="abValue"><?php echo pjCurrency::formatPrice($new_sub_total); ?>
					
				</span>
			</div>
		</div>
		<?php
	} 
	?>
	
	<?php if ((float) $tpl['option_arr']['o_tax'] > 0) : ?>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle"><?php __('bf_tax'); ?> (<?php echo $tpl['option_arr']['o_tax']?>%)</label>
			<span class="abValue"><?php echo pjCurrency::formatPrice($tpl['price_arr']['tax']); ?></span>
		</div>
	</div>
	<?php endif; ?>
	<?php if ((float) $tpl['option_arr']['o_tourist_tax'] > 0) : ?>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle"><?php __('bf_tourist_tax'); ?><br /><span class="abSubTitle">(<?php echo pjCurrency::formatPrice($tpl['option_arr']['o_tourist_tax']); ?> <?php $tpl['option_arr']['o_price_based_on'] == 'days' ? __('front_per_person_per_day') : __('front_per_person_per_night');?>)</span></label>
			<span class="abValue"><?php echo pjCurrency::formatPrice($tpl['price_arr']['tourist_tax']); ?></span>
		</div>
	</div>
	<?php endif; ?>
	<?php
	$sub_title_arr = array();
	if((float) $tpl['option_arr']['o_tax'] > 0)
	{
		$sub_title_arr[] = __('bf_tax', true);
	}
	if((float) $tpl['option_arr']['o_tourist_tax'] > 0)
	{
		$sub_title_arr[] = __('bf_tourist_tax', true);
	}
	?>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle abBold">
				<?php __('bf_total'); ?>
				<?php
				if(!empty($sub_title_arr))
				{
					?><br /><span class="abSubTitle">(<?php __('bf_subtotal'); ?> + <?php echo join(" + ", $sub_title_arr); ?>)</span><?php
				} 
				?>
			</label>
			<span class="abValue abPrice"><?php echo pjCurrency::formatPrice($tpl['price_arr']['total']); ?></span>
		</div>
	</div>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle"><?php __('bf_deposit'); ?>
			<?php
			$date = new DateTime();
			$date->modify('+'.$tpl['option_arr']['o_require_all_within'].' day');
			if (isset($tpl['option_arr']['o_require_all_within']) && (int) $tpl['option_arr']['o_require_all_within'] > 0 && $date->getTimestamp() >= @$_SESSION[$controller->defaultCalendar]['start_dt'])
			{
			    ?>
				<br /><span class="abSubTitle">(<?php echo '100% ' . ' ' . __('front_from_total_price', true);?>)</span>
				<?php
			} elseif ($tpl['option_arr']['o_deposit_type'] == 'percent') {
				?>
				<br /><span class="abSubTitle">(<?php echo $tpl['option_arr']['o_deposit'] . '% ' . ' ' . __('front_from_total_price', true);?>)</span>
				<?php
			}
			?>
			</label>
			<span class="abValue"><?php echo pjCurrency::formatPrice($deposit); ?></span>
		</div>
	</div>
	<?php if ((float) $tpl['option_arr']['o_security'] > 0) : ?>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle">
				<?php __('bf_security'); ?>
				<br/><span class="abSubTitle">(<?php $tpl['option_arr']['o_to_be_paid'] == 'on_arrival' ? __('front_paid_on_arrival') : __('front_paid_when_booking'); ?>)</span>
			</label>
			<span class="abValue"><?php echo pjCurrency::formatPrice($tpl['price_arr']['security']); ?></span>
		</div>
	</div>
	<?php endif; ?>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle abBold"><?php __('bf_payment_required'); ?></label>
			<span class="abValue abPrice"><?php echo pjCurrency::formatPrice($tpl['price_arr']['deposit']); ?></span>
		</div>
	</div>
	<div class="abParagraph"></div>
	<?php
	$payment_amount = (float)$tpl['price_arr']['deposit'];
}
?>
<input type="hidden" name="payment_amount" value="<?php echo $payment_amount;?>" />