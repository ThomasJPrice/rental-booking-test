<?php
if (isset($tpl['status']) && $tpl['status'] == 'IP_BLOCKED') {
	?>
	<div class="abForm abPaymentForm">
		<div class="abBox abGray">
			<span class="abError"><?php __('front_ip_address_blocked');?></span>
		</div>
	</div>
	<?php
} else {
	include dirname(__FILE__) . '/elements/menu.php';
	$STORAGE = @$_SESSION[$controller->defaultCalendar];
	$jquery_validation = __('jquery_validation', true);
	?>
	<form action="" method="post" class="abForm abSelectorBookingForm">
		<input type="hidden" name="start_dt" value="<?php echo @$STORAGE['start_dt']; ?>" />
		<input type="hidden" name="end_dt" value="<?php echo @$STORAGE['end_dt']; ?>" />
		<div class="abBox abWhite abHeading"><?php __('bf_booking'); ?></div>
		<div class="abBox abGray">
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_start_date'); ?></label>
					<span class="abValue"><?php echo date($tpl['option_arr']['o_date_format'], @$STORAGE['start_dt']); ?></span>
				</div>
			</div>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_end_date'); ?></label>
					<span class="abValue"><?php echo date($tpl['option_arr']['o_date_format'], @$STORAGE['end_dt']); ?></span>
				</div>
			</div>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle">&nbsp;</label>
					<span class="abValue">
						<?php
						$nights = $STORAGE['nights'];
				    	if ($tpl['option_arr']['o_price_based_on'] == 'days')
				    	{
				    		printf("%u %s", $nights, $nights > 1 ? __('bf_days', true) : __('bf_day', true));
				    	} else {
				    		printf("%u %s", $nights, $nights > 1 ? __('bf_nights', true) : __('bf_night', true));
				    	}
				    	?>&nbsp;(<a href="#" class="abSelectorChangeDates"><?php __('lblChangeDates');?></a>)
			    	</span>
			    </div>
			</div>
			<?php
			if ((int) $tpl['option_arr']['o_bf_adults'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_adults'); ?></label>
						<span class="abControl">
							<select name="c_adults" class="abSelect abW80<?php echo (int) $tpl['option_arr']['o_bf_adults'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>">
								<option value="">---</option>
								<?php
								foreach (range(1, $tpl['option_arr']['o_bf_adults_max']) as $i)
								{
									?><option value="<?php echo $i; ?>"<?php echo @$STORAGE['c_adults'] != $i ? NULL : ' selected="selected"'; ?>><?php echo $i; ?></option><?php
								}
								?>
							</select>
						</span>
					</div>
				</div>
				<?php
			}
			if ((int) $tpl['option_arr']['o_bf_children'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_children'); ?></label>
						
							<select name="c_children" class="abSelect abW80<?php echo (int) $tpl['option_arr']['o_bf_children'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>">
								<option value="0">---</option>
								<?php
								foreach (range(1, $tpl['option_arr']['o_bf_children_max']) as $i)
								{
									?><option value="<?php echo $i; ?>"<?php echo @$STORAGE['c_children'] != $i ? NULL : ' selected="selected"'; ?>><?php echo $i; ?></option><?php
								}
								?>
							</select>
						</span>
					</div>
				</div>
				<?php
			}
			$max_message = str_replace("{MAX}", $tpl['option_arr']['o_max_people'], __('front_max_people_msg', true));
			$min_message = str_replace("{MIN}", $tpl['option_arr']['o_min_people'], __('front_min_people_msg', true));
			?>
			<div id="pjRpcMaxPeople" data-max="<?php echo $tpl['option_arr']['o_max_people'];?>" class="abParagraph" style="display: none;">
				<div class="abParagraphInner">
					<label class="abTitle">&nbsp;</label>
					<span class="abControl abError">
						<?php echo $max_message;?>
					</span>
				</div>
			</div>
			<div id="pjRpcMinPeople" data-min="<?php echo $tpl['option_arr']['o_min_people'];?>" class="abParagraph" style="display: none;">
				<div class="abParagraphInner">
					<label class="abTitle">&nbsp;</label>
					<span class="abControl abError">
						<?php echo $min_message;?>
					</span>
				</div>
			</div>
			<?php
			if(!empty($tpl['extra_arr']))
			{ 
				?>
				<div class="abParagraph">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('front_extras');?></label>
						<span class="abControl">
							<div class="abExtraWrapper">
								<div class="abExtraHeading">
									<div class="abExtraName"><?php __('front_extra_name');?></div>
									<div class="abExtraQty"><?php __('front_qty');?></div>
								</div>
								<?php
								$price_types = $tpl['option_arr']['o_price_based_on'] == 'days' ? __('day_price_types', true) : __('price_types', true);
								$selected_extra_arr = isset($_SESSION[$controller->defaultCalendar]['extra']) ? $_SESSION[$controller->defaultCalendar]['extra'] : array();
								$selected_extra_qty_arr = isset($_SESSION[$controller->defaultCalendar]['qty']) ? $_SESSION[$controller->defaultCalendar]['qty'] : array();
								foreach($tpl['extra_arr'] as $k => $v)
								{
									?>
									<div class="abExtraRow">
										<div class="abExtraName">
											<div>
												<input type="checkbox" id="extra_<?php echo $v['id'];?>" name="extra[<?php echo $v['id'];?>]"<?php echo array_key_exists($v['id'], $selected_extra_arr) ? ' checked="checked"' : NULL;?> data-id="<?php echo $v['id'];?>"<?php echo $v['required'] == 'T' ? ' checked="checked"' : NULL;?><?php echo $v['required'] == 'T' ? ' disabled="disabled"' : NULL;?> class="abExtraCheckbox<?php echo $v['required'] == 'T' ? ' required' : NULL;?>" data-msg-required="<?php __('front_extra_required_msg');?>"/>
												<label for="extra_<?php echo $v['id'];?>">
													<?php echo pjSanitize::html($v['name']);?> (<?php echo pjCurrency::formatPrice($v['price']) . ' ' . $price_types[$v['price_type']];?>)
												</label>
											</div>
										</div>
										<div class="abExtraQty">
											<?php
											if($v['multi'] == 'F')
											{
												if($v['price_type'] == 'count' || $v['price_type'] == 'count_night')
												{
													?>
													<select id="qty_<?php echo $v['id'];?>" name="qty[<?php echo $v['id'];?>]" class="abSelect abW80 pjRpbExtraCount"<?php echo $v['required'] == 'F' ? (array_key_exists($v['id'], $selected_extra_arr) ? NULL : ' disabled="disabled"') : NULL;?>>
														<?php
														for($i = 1; $i <= (int) $v['max_count']; $i++)
														{
															?><option value="<?php echo $i;?>"<?php echo isset($selected_extra_qty_arr[$v['id']]) ? ($selected_extra_qty_arr[$v['id']] == $i ? ' selected="selected"' : '') : '';?>><?php echo $i;?></option><?php 
														} 
														?>
													</select>
													<?php
												}else{
													?>1<input type="hidden" name="qty[<?php echo $v['id'];?>]" value="1"/><?php
												} 
											} else {
												?>
												<select id="qty_<?php echo $v['id'];?>" name="qty[<?php echo $v['id'];?>]" class="abSelect abW80 pjRpbExtraCount"<?php echo $v['required'] == 'F' ? (array_key_exists($v['id'], $selected_extra_arr) ? NULL : ' disabled="disabled"') : NULL;?>>
													<?php
													for($i = 1; $i <= (int) $v['max_count']; $i++)
													{
														?><option value="<?php echo $i;?>"<?php echo isset($selected_extra_qty_arr[$v['id']]) ? ($selected_extra_qty_arr[$v['id']] == $i ? ' selected="selected"' : '') : '';?>><?php echo $i;?></option><?php 
													} 
													?>
												</select>
												<?php
											}
											?>
										</div>
									</div>
									<?php
								} 
								?>
							</div>
						</span>
					</div>
					
				</div>
				<?php
			}?>
			<div class="abSelectorPrice"><?php include dirname(__FILE__) . '/pjActionGetPrice.php'; ?></div>
		</div>
		<div class="abBox abWhite">
			<?php
			if ((int) $tpl['option_arr']['o_bf_name'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_name'); ?></label>
						<span class="abControl">
							<input type="text" name="c_name" class="abText abStretch<?php echo (int) $tpl['option_arr']['o_bf_name'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$STORAGE['c_name']); ?>" data-msg-required="<?php echo $jquery_validation['required'];?>" />
						</span>
					</div>
				</div>
				<?php
			}
			if ((int) $tpl['option_arr']['o_bf_email'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_email'); ?></label>
						<span class="abControl">
							<input type="text" name="c_email" class="abText abStretch email<?php echo (int) $tpl['option_arr']['o_bf_email'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$STORAGE['c_email']); ?>" data-msg-required="<?php echo $jquery_validation['required'];?>" data-msg-email="<?php echo $jquery_validation['email'];?>"/>
						</span>
					</div>
				</div>
				<?php
			}
			if ((int) $tpl['option_arr']['o_bf_phone'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_phone'); ?></label>
						<span class="abControl">
							<input type="text" name="c_phone" class="abText abStretch<?php echo (int) $tpl['option_arr']['o_bf_phone'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$STORAGE['c_phone']); ?>" data-msg-required="<?php echo $jquery_validation['required'];?>"/>
						</span>
					</div>
				</div>
				<?php
			}
			if ((int) $tpl['option_arr']['o_bf_address'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_address'); ?></label>
						<span class="abControl">
							<input type="text" name="c_address" class="abText abStretch<?php echo (int) $tpl['option_arr']['o_bf_address'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$STORAGE['c_address']); ?>" data-msg-required="<?php echo $jquery_validation['required'];?>"/>
						</span>
					</div>
				</div>
				<?php
			}
			if ((int) $tpl['option_arr']['o_bf_zip'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_zip'); ?></label>
						<span class="abControl">
							<input type="text" name="c_zip" class="abText abStretch<?php echo (int) $tpl['option_arr']['o_bf_zip'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$STORAGE['c_zip']); ?>" data-msg-required="<?php echo $jquery_validation['required'];?>"/>
						</span>
					</div>
				</div>
				<?php
			}
			if ((int) $tpl['option_arr']['o_bf_city'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_city'); ?></label>
						<span class="abControl">
							<input type="text" name="c_city" class="abText abStretch<?php echo (int) $tpl['option_arr']['o_bf_city'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$STORAGE['c_city']); ?>" data-msg-required="<?php echo $jquery_validation['required'];?>"/>
						</span>
					</div>
				</div>
				<?php
			}
			if ((int) $tpl['option_arr']['o_bf_state'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_state'); ?></label>
						<span class="abControl">
							<input type="text" name="c_state" class="abText abStretch<?php echo (int) $tpl['option_arr']['o_bf_state'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$STORAGE['c_state']); ?>" data-msg-required="<?php echo $jquery_validation['required'];?>"/>
						</span>
					</div>
				</div>
				<?php
			}
			if ((int) $tpl['option_arr']['o_bf_country'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_country'); ?></label>
						<span class="abControl">
							<select name="c_country" class="abSelect abStretch<?php echo (int) $tpl['option_arr']['o_bf_country'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>">
								<option value="">---</option>
								<?php
								foreach ($tpl['country_arr'] as $country)
								{
								    ?><option value="<?php echo $country['id']; ?>"<?php echo @$STORAGE['c_country'] != $country['id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html($country['name']); ?></option><?php
								}
								?>
							</select>
						</span>
					</div>
				</div>
				<?php
			}
			if ((int) $tpl['option_arr']['o_bf_notes'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_notes'); ?></label>
						<span class="abControl">
							<textarea name="c_notes" class="abTextarea abStretch abH70<?php echo (int) $tpl['option_arr']['o_bf_notes'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>"><?php echo @$STORAGE['c_notes']; ?></textarea>
						</span>
					</div>
				</div>
				<?php
			}
			if ((int) $tpl['option_arr']['o_disable_payments'] !== 1)
			{
				$plugins_payment_methods = pjObject::getPlugin('pjPayments') !== NULL? pjPayments::getPaymentMethods(): array();
			    $haveOnline = $haveOffline = false;
			    foreach ($tpl['payment_titles'] as $k => $v)
			    {
			        if( $k != 'cash' && $k != 'bank' )
			        {
			            if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)
			            {
			                $haveOnline = true;
			                break;
			            }
			        }
			    }
			    foreach ($tpl['payment_titles'] as $k => $v)
			    {
			        if( $k == 'cash' || $k == 'bank' )
			        {
			            if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)
			            {
			                $haveOffline = true;
			                break;
			            }
			        }
			    }
				?>
				<div class="abParagraph abPaymentMethodWrap" style="display: <?php echo  (float)$tpl['price_arr']['deposit'] > 0 ? '' : 'none';?>">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_payment'); ?></label>
						<span class="abControl">
							<select name="payment_method" class="abSelect required" data-msg-required="<?php echo $jquery_validation['required'];?>">
								<option value="">---</option>
								<?php
								if ($haveOnline && $haveOffline)
								{
								    ?><optgroup label="<?php __('script_online_payment_gateway', false, true); ?>"><?php
			                    }
			                    foreach ($tpl['payment_titles'] as $k => $v)
			                    {
			                        if($k == 'cash' || $k == 'bank' ){
			                            continue;
			                        }
			                        if (array_key_exists($k, $plugins_payment_methods))
			                        {
			                            if(!isset($tpl['payment_option_arr'][$k]['is_active']) || (isset($tpl['payment_option_arr']) && $tpl['payment_option_arr'][$k]['is_active'] == 0) )
			                            {
			                                continue;
			                            }
			                        }
			                        ?><option value="<?php echo $k; ?>"<?php echo isset($STORAGE['payment_method']) && $STORAGE['payment_method']==$k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
			                    }
			                    if ($haveOnline && $haveOffline)
			                    {
			                        ?>
			                    	</optgroup>
			                    	<optgroup label="<?php __('script_offline_payment', false, true); ?>">
			                    	<?php 
			                    }
			                    foreach ($tpl['payment_titles'] as $k => $v)
			                    {
			                        if( $k == 'cash' || $k == 'bank' )
			                        {
			                            if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)
			                            {
			                                ?><option value="<?php echo $k; ?>"<?php echo isset($STORAGE['payment_method']) && $STORAGE['payment_method']==$k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
			                            }
			                        }
			                    }
			                    if ($haveOnline && $haveOffline)
			                    {
			                        ?></optgroup><?php
			                    }
								?>
							</select>
						</span>
					</div>
				</div>
				
				<div class="abParagraph abCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_cc_type'); ?></label>
						<span class="abControl">
							<select name="cc_type" class="abSelect required" data-msg-required="<?php echo $jquery_validation['required'];?>">
								<option value="">---</option>
								<?php
								foreach (__('cc_types', true) as $k => $v)
								{
									?><option value="<?php echo $k; ?>"<?php echo @$STORAGE['cc_type'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
								}
								?>
							</select>
						</span>
					</div>
				</div>
				<div class="abParagraph abCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_cc_num'); ?></label>
						<span class="abControl">
							<input type="text" name="cc_num" class="abText abStretch required" value="<?php echo pjSanitize::html(@$STORAGE['cc_num']); ?>" data-msg-required="<?php echo $jquery_validation['required'];?>"/>
						</span>
					</div>
				</div>
				<div class="abParagraph abCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_cc_sec'); ?></label>
						<span class="abControl">
							<input type="text" name="cc_code" class="abText abStretch required" value="<?php echo pjSanitize::html(@$STORAGE['cc_code']); ?>" data-msg-required="<?php echo $jquery_validation['required'];?>"/>
						</span>
					</div>
				</div>
				<div class="abParagraph abCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_cc_exp'); ?></label>
						<span class="abControl">
						<?php
						$time = pjTime::factory()
							->attr('name', 'cc_exp_month')
							->attr('id', 'cc_exp_month')
							->attr('class', 'abText abW100 required')
							->attr('data-msg-required', $jquery_validation['required'])
							->prop('format', 'F');
						if (isset($STORAGE['cc_exp_month']) && !is_null($STORAGE['cc_exp_month']))
						{
							$time->prop('selected', $STORAGE['cc_exp_month']);
						}
						echo $time->month();
						?>
						<?php
						$time = pjTime::factory()
							->attr('name', 'cc_exp_year')
							->attr('id', 'cc_exp_year')
							->attr('class', 'abText abW80 required')
							->attr('data-msg-required', $jquery_validation['required'])
							->prop('left', 0)
							->prop('right', 10);
						if (isset($STORAGE['cc_exp_year']) && !is_null($STORAGE['cc_exp_year']))
						{
							$time->prop('selected', $STORAGE['cc_exp_year']);
						}
						echo $time->year();
						?>
						</span>
					</div>
				</div>
				<div class="abParagraph abBankWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'bank' ? 'none' : NULL; ?>">
					<div class="abParagraphInner">
						<label class="abTitle"><?php __('bf_bank_account'); ?></label>
						<span class="abValue"><?php echo nl2br(pjSanitize::html($tpl['bank_account'])); ?></span>
					</div>
				</div>
				<?php
			}
			if ((int) $tpl['option_arr']['o_bf_captcha'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner" style="position: relative">
						<label class="abTitle"><?php __('bf_captcha'); ?></label>
						<?php if($tpl['option_arr']['o_captcha_type_front'] == 'system') { ?>
							<span class="abControl">
								<img id="pjAbcCaptchaImage"  src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;cid=<?php echo $controller->_get->toInt('cid'); ?>&amp;rand=<?php echo rand(1, 99999); ?><?php echo $controller->_get->check('session_id') ? '&session_id=' . $controller->_get->toString('session_id') : NULL;?>" alt="<?php __('bf_captcha'); ?>" data-cid="<?php echo $controller->_get->toInt('cid'); ?>" class="abCaptcha" />
								<input type="text" id="pjAbcCaptchaInput" name="captcha" class="abText abW100<?php echo (int) $tpl['option_arr']['o_bf_captcha'] === 3 ? ' required' : NULL; ?>" maxlength="6" autocomplete="off" data-msg-required="<?php echo $jquery_validation['required'];?>" data-msg-remote="<?php __('front_v_captcha_match');?>"/>
							</span>
						<?php } else { ?>
							<div class="abControl">
								<div id="g-recaptcha_<?php echo $controller->_get->toInt('cid'); ?>" class="g-recaptcha" data-sitekey="<?php echo $tpl['option_arr']['o_captcha_site_key_front'] ?>"></div>
								<input type="text" style="border: 0 !important; background: none !important; font-size: 0 !important;" id="recaptcha" name="recaptcha" class="recaptcha<?php echo ($tpl['option_arr']['o_bf_captcha'] == 3) ? ' required' : NULL; ?>" autocomplete="off" data-msg-required="<?php __('front_v_captcha');?>" data-msg-remote="<?php __('front_v_captcha_match');?>"/>
							</div>
						<?php } ?>
					</div>
				</div>
				<div id="pjAbcCaptchaMessage" class="abParagraph" style="display: none;">
					<div class="abParagraphInner">
						<label class="abTitle">&nbsp;</label>
						<span class="abValue abError"><?php __('front_err_ARRAY_captcha'); ?></span>
					</div>
				</div>
				<?php
			}
			if ((int) $tpl['option_arr']['o_bf_terms'] !== 1)
			{
				?>
				<div class="abParagraph">
					<div class="abParagraphInner" style="position: relative">
						<label class="abTitle">&nbsp;</label>
						<span class="abControl">
							<input type="checkbox" name="terms" id="ab_terms_<?php echo $controller->_get->toInt('cid'); ?>" value="1" class="<?php echo (int) $tpl['option_arr']['o_bf_terms'] === 3 ? 'required': NULL; ?>" style="margin-left: 0;float: left; margin-right: 3px;" data-msg-required="<?php echo $jquery_validation['required'];?>"/>
							<label for="ab_terms_<?php echo $controller->_get->toInt('cid'); ?>" class="abTerms"><?php
							if (!empty($tpl['cal_arr']['terms_url']) && preg_match('|^http(s)?://|', $tpl['cal_arr']['terms_url']))
							{
								printf(__('bf_terms', true), '<a href="'.$tpl['cal_arr']['terms_url'].'" target="_blank">', '</a>');
							} else if (!empty($tpl['cal_arr']['terms_body'])) {
								printf(__('bf_terms', true), '<a class="abSelectorTerms" href="#">', '</a>');
							} else {
								echo str_replace('%s', '', __('bf_terms', true));
							}
							?></label>
						</span>
					</div>
				</div>
				<div class="abSelectorTermsBody" style="display: none"><?php echo $tpl['cal_arr']['terms_body']; ?></div>
				<?php
			}
			?>
			
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle">&nbsp;</label>
					<span class="abControl">
						<button type="submit" class="abButton abButtonDefault abSelectorContinue abFloatleft abMR5"><?php __('bf_continue'); ?></button>
						<button type="button" class="abButton abButtonCancel abSelectorCancel abFloatleft"><?php __('bf_cancel'); ?></button>
					</span>
				</div>
			</div>
			<div class="abParagraph"></div>
		</div>
	</form>
<?php } ?>