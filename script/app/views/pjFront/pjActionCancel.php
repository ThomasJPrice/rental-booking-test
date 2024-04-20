<div id="container">
	<div id="header"></div>
	<div id="middle">
		<div id="right">
		<?php if (isset($tpl['status']) && $tpl['status'] == 'IP_BLOCKED') { 
			pjUtil::printNotice('', __('front_ip_address_blocked', true), false, false);
		} else {
			$titles = __('error_titles', true);
			$bodies = __('error_bodies', true);
			if (isset($tpl['status']))
			{
				pjUtil::printNotice(@$titles[$tpl['status']], @$bodies[$tpl['status']], true, false);
			} else {
				$reservation_statuses = __('reservation_statuses', true);
				$payment_methods = __('payment_methods', true);
				$cc_types = __('cc_types', true);
				if ($tpl['arr']['status'] != 'Cancelled')
				{
					pjUtil::printNotice(@$titles['AR14'], @$bodies['AR14'], true, false);
				} else {
					pjUtil::printNotice(@$titles['AR17'], @$bodies['AR17'], true, false);
				}
				if ($controller->_get->check('err'))
				{
					pjUtil::printNotice(@$titles[$controller->_get->toString('err')], @$bodies[$controller->_get->toString('err')]);
				}
				?>
				<form action="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCancel&amp;cid=<?php echo $controller->_get->toInt('cid'); ?>&amp;id=<?php echo $controller->_get->toString('id'); ?>&hash=<?php echo $controller->_get->toString('hash'); ?>" method="post" class="pj-form form">
					<input type="hidden" name="cancel_booking" value="1" />
					<input type="hidden" name="id" value="<?php echo $controller->_get->toString('id'); ?>" />
					<input type="hidden" name="hash" value="<?php echo $controller->_get->toString('hash'); ?>" />
					
					<fieldset class="fieldset white float_left w350">
						<legend><?php __('lblReservationInfo'); ?></legend>
						<p>
							<label class="title"><?php __('lblReservationStatus'); ?></label>
							<span class="left"><?php echo @$reservation_statuses[$tpl['arr']['status']];	?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationUuid'); ?></label>
							<span class="left"><?php echo htmlspecialchars(stripslashes($tpl['arr']['uuid'])); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationFrom'); ?></label>
							<span class="left"><?php echo pjDateTime::formatDate($tpl['arr']['date_from'], "Y-m-d", $tpl['option_arr']['o_date_format']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationTo'); ?></label>
							<span class="left"><?php echo pjDateTime::formatDate($tpl['arr']['date_to'], "Y-m-d", $tpl['option_arr']['o_date_format']); ?></span>
						</p>
						<?php if (in_array($tpl['option_arr']['o_bf_adults'], array(2,3))) : ?>
						<p>
							<label class="title"><?php __('lblReservationAdults'); ?></label>
							<span class="left"><?php echo $tpl['arr']['c_adults']; ?></span>
						</p>
						<?php endif; ?>
						<?php if (in_array($tpl['option_arr']['o_bf_children'], array(2,3))) : ?>
						<p>
							<label class="title"><?php __('lblReservationChildren'); ?></label>
							<span class="left"><?php echo $tpl['arr']['c_children']; ?></span>
						</p>
						<?php endif; ?>
					</fieldset>
					
					<fieldset class="fieldset white float_right w350">
						<legend><?php __('lblReservationAmount'); ?></legend>
						<p>
							<label class="title"><?php __('lblReservationPayment'); ?></label>
							<span class="left"><?php echo @$payment_methods[$tpl['arr']['payment_method']]; ?></span>
						</p>
						<p class="vrCC" style="display: <?php echo $tpl['arr']['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
							<label class="title"><?php echo __('lblReservationCCType'); ?></label>
							<span class="left"><?php echo @$cc_types[$tpl['arr']['cc_type']]; ?></span>
						</p>
						<p class="vrCC" style="display: <?php echo $tpl['arr']['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
							<label class="title"><?php __('lblReservationCCNum'); ?></label>
							<span class="left"><?php echo $tpl['arr']['cc_num']; ?></span>
						</p>
						<p class="vrCC" style="display: <?php echo $tpl['arr']['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
							<label class="title"><?php __('lblReservationCCCode'); ?></label>
							<span class="left"><?php echo $tpl['arr']['cc_code']; ?></span>
						</p>
						<p class="vrCC" style="display: <?php echo $tpl['arr']['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
							<label class="title"><?php __('lblReservationCCExp'); ?></label>
							<span class="left"><?php printf("%s/%s", $tpl['arr']['cc_exp_month'], $tpl['arr']['cc_exp_year']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationAmount'); ?></label>
							<span class="left"><?php echo pjCurrency::formatPrice($tpl['arr']['amount']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationDeposit'); ?></label>
							<span class="left"><?php echo pjCurrency::formatPrice($tpl['arr']['deposit']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationSecurity'); ?></label>
							<span class="left"><?php echo pjCurrency::formatPrice($tpl['arr']['security']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationTax'); ?></label>
							<span class="left"><?php echo pjCurrency::formatPrice($tpl['arr']['tax']); ?></span>
						</p>
					</fieldset>
					
					<br class="clear_both" />
					
					<fieldset class="fieldset white">
						<legend><?php __('lblReservationClientInfo'); ?></legend>
						<p>
							<label class="title"><?php __('lblReservationName'); ?></label>
							<span class="left"><?php echo pjSanitize::clean($tpl['arr']['c_name']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationEmail'); ?></label>
							<span class="left"><?php echo pjSanitize::clean($tpl['arr']['c_email']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationPhone'); ?></label>
							<span class="left"><?php echo pjSanitize::clean($tpl['arr']['c_phone']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationAddress'); ?></label>
							<span class="left"><?php echo pjSanitize::clean($tpl['arr']['c_address']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationCity'); ?></label>
							<span class="left"><?php echo pjSanitize::clean($tpl['arr']['c_city']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationState'); ?></label>
							<span class="left"><?php echo pjSanitize::clean($tpl['arr']['c_state']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationZip'); ?></label>
							<span class="left"><?php echo pjSanitize::clean($tpl['arr']['c_zip']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationCountry'); ?></label>
							<span class="left"><?php echo pjSanitize::clean($tpl['arr']['country']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationNotes'); ?></label>
							<span class="left"><?php echo pjSanitize::clean($tpl['arr']['c_notes']); ?></span>
						</p>
						<p>
							<label class="title"><?php __('lblReservationCreated'); ?></label>
							<span class="left"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['created'])); ?>, <?php echo date("H:i", strtotime($tpl['arr']['created'])); ?></span>
						</p>
						<?php if ($tpl['arr']['status'] != 'Cancelled') : ?>
						<p>
							<label class="title">&nbsp;</label>
							<span class="left"><input type="submit" class="pj-button" value="<?php __('btnCancel'); ?>" /></span>
						</p>
						<?php endif; ?>
					</fieldset>
				</form>
				<?php
			}
		}
		?>
		</div>
	</div>
</div>