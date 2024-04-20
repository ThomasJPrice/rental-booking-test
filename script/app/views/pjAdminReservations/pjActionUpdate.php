<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
$reservation_statuses = __('reservation_statuses', true);
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-10">
				<h2><?php __('infoUpdateReservationTitle');?></h2>
			</div>
		</div><!-- /.row -->

		<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoUpdateReservationDesc');?></p>
	</div><!-- /.col-md-12 -->
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<?php
		$error_code = $controller->_get->toString('err');
		if (!empty($error_code))
		{
			switch (true)
			{
				case in_array($error_code, array('AR01', 'AR03')):
					?>
					<div class="alert alert-success">
						<i class="fa fa-check m-r-xs"></i>
						<strong><?php echo @$titles[$error_code]; ?></strong>
						<?php echo @$bodies[$error_code];?>
					</div>
					<?php 
					break;
				case in_array($error_code, array('AR04', 'AR08', 'AR11')):	
					?>
					<div class="alert alert-danger">
						<i class="fa fa-exclamation-triangle m-r-xs"></i>
						<strong><?php echo @$titles[$error_code]; ?></strong>
						<?php echo @$bodies[$error_code];?>
					</div>
					<?php
					break;
			}
		} 
		?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>" id="frmUpdateReservation" method="post" novalidate="novalidate">
		<input type="hidden" name="reservation_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<input type="hidden" name="calendar_id" value="<?php echo $tpl['arr']['calendar_id']; ?>" />
		<input type="hidden" name="locale_id" value="<?php echo $tpl['arr']['locale_id']; ?>" />
		<div id="datePickerOptions" style="display:none;" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-format="<?php echo pjUtil::toBootstrapDate($tpl['option_arr']['o_date_format']); ?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>
		<div class="row">
			<div class="col-lg-9">
				<div class="tabs-container">
					<ul class="nav nav-tabs">
						<li class="active"><a class="tab-reservation-details" href="#reservation-details" rev="1" aria-controls="reservation-details" role="tab" data-toggle="tab" aria-expanded="true"><?php __('tabDetails');?></a></li>
						<li class=""><a class="tab-client-details" href="#client-details" rev="2" aria-controls="client-details" role="tab" data-toggle="tab" aria-expanded="false"><?php __('tabClient');?></a></li>
					</ul>
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="reservation-details">
							<div class="panel-body">
								<div class="row">
									<div class="col-lg-4 col-sm-4 col-sm-6">
										<div class="form-group">
											<label><?php __('lblReservationUuid'); ?></label>
											<div>
												<input class="form-control required" name="uuid" id="uuid" maxlength="255" value="<?php echo pjSanitize::html($tpl['arr']['uuid']); ?>" data-msg-required="<?php __('pj_field_required', false, true);?>" type="text" aria-required="true">
											</div>
										</div>
									</div>
									<div class="col-lg-4 col-sm-4 col-sm-6">
										<div class="form-group">
											<label><?php __('lblReservationStatus'); ?></label>
											<div>
												<select name="status" id="status" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true);?>" aria-required="true">
													<option value="">-- <?php __('lblChoose'); ?> --</option>
													<?php
													foreach ($reservation_statuses as $k => $v)
													{
														?><option value="<?php echo $k; ?>" <?php echo $tpl['arr']['status'] == $k ? 'selected="selected"' : '';?>><?php echo stripslashes($v); ?></option><?php
													}
													?>
												</select>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<label ><?php __('lblReservationFrom'); ?></label>
											<div class="input-group date datepicker-item">
												<input class="form-control required" id="date_from" name="date_from" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['date_from']));?>" type="text" readonly="readonly" data-msg-required="<?php __('pj_field_required');?>">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											</div>
										</div>
									</div>
		
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<label ><?php __('lblReservationTo'); ?></label>
											<div class="input-group date datepicker-item">
												<input class="form-control required" id="date_to" type="text" name="date_to" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['date_to']));?>" readonly="readonly" data-msg-required="<?php __('pj_field_required');?>">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											</div>
											<input type="hidden" name="dates" id="dates" value="1" />
										</div>
									</div>
									<div class="col-lg-6 col-sm-12">
										<div class="row">
											<div class="col-sm-4" id="boxAdults">
												<?php if (in_array($tpl['option_arr']['o_bf_adults'], array(2,3))) {  ?>
													<div class="form-group">
														<label ><?php __('lblReservationAdults'); ?></label>
														<div>
															<input class="pjRpbcAdultsChildSelector form-control <?php echo (int) $tpl['__option_arr']['o_bf_adults'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>" type="text" value="<?php echo $tpl['arr']['c_adults'];?>" name="c_adults" id="c_adults" data-max="<?php echo $tpl['option_arr']['o_bf_adults_max'];?>">
														</div>
													</div>
												<?php } ?>
											</div>
											
											<div class="col-sm-4" id="boxChildren">
												<?php if (in_array($tpl['option_arr']['o_bf_children'], array(2,3))) { ?>
													<div class="form-group">
														<label ><?php __('lblReservationChildren'); ?></label>
														<div>
															<input class="pjRpbcAdultsChildSelector form-control <?php echo (int) $tpl['__option_arr']['o_bf_children'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>" type="text" value="<?php echo $tpl['arr']['c_children'];?>" name="c_children" id="c_children" data-max="<?php echo $tpl['option_arr']['o_bf_children_max'];?>">
														</div>
													</div>
												<?php } ?>
											</div>
											
											<div class="col-sm-4">
												<label >&nbsp;</label>
												<div class="form-group">											
													<button class="btn btn-primary btn-outline btnCalculate" type="button"><i class="fa fa-calculator"></i> <?php __('btnCalculate'); ?></button>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="alert alert-info m-t-sm">
									<i class="fa fa-info-circle m-r-xs"></i>
									<?php __('lblReservationPriceDetailsDesc');?>
								</div>

								<div id="boxExtras" style="display: <?php echo !empty($tpl['extra_arr']) ? 'block' : 'none';?>;">
									<h3><?php __('lblExtras'); ?></h3>
									<div class="table-responsive-actions">	
										<table class="table table-bordered abExtraTable">
											<thead>
												<tr>
													<th><?php __('lblExtraName');?></th>
													<th><?php __('lblPrice');?></th>
													<th><?php __('lblQty');?></th>
												</tr>
											</thead>
											<tbody>
												<?php
												$price_types = $tpl['__option_arr']['o_price_based_on'] == 'days' ? __('day_price_types', true) : __('price_types', true);
												foreach($tpl['extra_arr'] as $k => $v)
												{
													?>
													<tr>
														<td>
															<div>
																<label class="clearfix">
																	<input type="checkbox" id="extra_<?php echo $v['id'];?>" name="extra[<?php echo $v['id'];?>]" <?php echo array_key_exists($v['id'], $tpl['extra_id_arr']) ? ' checked="checked"' : NULL;?> data-id="<?php echo $v['id'];?>" class="i-checks abExtraCheckbox<?php echo $v['required'] == 'T' ? ' required' : NULL;?>" data-msg-required="<?php __('lblExtraRequiredMessage');?>"/>
																	<?php echo pjSanitize::html($v['name']);?>
																</label>
															</div>
														</td>
														<td><?php echo pjCurrency::formatPrice($v['price']) . ' ' . $price_types[$v['price_type']];?></td>
														<td>
															<?php
															if($v['multi'] == 'T')
															{
																?>
																<select id="qty_<?php echo $v['id'];?>" name="qty[<?php echo $v['id'];?>]" class="form-control abTextQty">
																	<?php
																	for($i = 1; $i <= (int) $v['max_count']; $i++)
																	{
																		?><option value="<?php echo $i;?>" <?php echo array_key_exists($v['id'], $tpl['extra_id_arr']) ? ($tpl['extra_id_arr'][$v['id']] == $i ? ' selected="selected"': NULL) : NULL;?>><?php echo $i;?></option><?php 
																	} 
																	?>
																</select>
																<?php
															}else{
																if($v['price_type'] == 'count' || $v['price_type'] == 'count_night')
																{
																	?>
																	<select id="qty_<?php echo $v['id'];?>" name="qty[<?php echo $v['id'];?>]" class="form-control abTextQty">
																		<?php
																		for($i = 1; $i <= (int) $v['max_count']; $i++)
																		{
																			?><option value="<?php echo $i;?>" <?php echo array_key_exists($v['id'], $tpl['extra_id_arr']) ? ($tpl['extra_id_arr'][$v['id']] == $i ? ' selected="selected"': NULL) : NULL;?>><?php echo $i;?></option><?php 
																		} 
																		?>
																	</select>
																	<?php
																}else{
																	?>1<input type="hidden" name="qty[<?php echo $v['id'];?>]" value="1"/><?php
																}
															}
															?>
														</td>
													</tr>
													<?php
												} 
												?>
											</tbody>
										</table>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div id="payment-method-wrapper" class="form-group">
											<?php
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
											<label><?php __('lblReservationPayment'); ?></label>
											<div>
												<select name="payment_method" id="payment_method" class="form-control <?php //echo (int) $tpl['__option_arr']['o_disable_payments'] == 0 ? ' required' : NULL;?>" data-msg-required="<?php __('pj_field_required');?>">
													 <option value="">-- <?php __('lblChoose'); ?>--</option>
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
	                                                    ?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['payment_method'] == $k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
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
	                                                            ?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['payment_method'] == $k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
	                                                        }
	                                                    }
	                                                }
	                                                if ($haveOnline && $haveOffline)
	                                                {
	                                                    ?></optgroup><?php
	                                                }
	                                            	?>
												</select>
											</div>
										</div>
									</div>
		
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<label><?php __('lblReservationPromoCode'); ?></label>
		
											<input type="text" name="promo_code" id="promo_code" value="<?php echo pjSanitize::html($tpl['arr']['promo_code']);?>" class="form-control">
											<input type="hidden" name="promo_type" id="promo_type" value="<?php echo pjSanitize::html($tpl['arr']['promo_type']); ?>" class="form-control"/>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<label><?php __('lblReservationPromoAmount'); ?></label>
											<div class="input-group">
												<input type="text" class="form-control number" name="promo_amount" id="promo_amount" value="<?php echo $tpl['arr']['promo_amount'];?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
												<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
											</div>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-4 col-sm-6">
										<label >&nbsp;</label>
										<div class="form-group">											
											<button class="btn btn-primary btn-outline btnCalculate" type="button"><i class="fa fa-calculator"></i> <?php __('btnCalculate'); ?></button>
										</div>
									</div>
									
								</div>

								<div class="row">
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<label><?php __('lblReservationAmount'); ?></label>
											<div class="input-group">
												<input type="text" class="form-control number" name="amount" id="amount" value="<?php echo $tpl['arr']['amount'];?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
												<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
											</div>
										</div>
									</div>
		
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<label><?php __('lblReservationExtraPrice'); ?></label>
											<div class="input-group">
												<input type="text" class="form-control number" name="extra_price" id="extra_price" value="<?php echo $tpl['arr']['extra_price'];?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
												<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
											</div>
										</div>
									</div>
		
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<label><?php __('lblReservationDiscountAmount'); ?></label>
											<div class="input-group">
												<input type="text" class="form-control number" name="discount_amount" id="discount_amount" value="<?php echo $tpl['arr']['discount_amount'];?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
												<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
											</div>
										</div>
									</div>
		
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<label><?php __('lblReservationTax'); ?></label>
											<div class="input-group">
												<input type="text" class="form-control number" name="tax" id="tax" value="<?php echo $tpl['arr']['tax'];?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
												<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
											</div>
										</div>
									</div>
		
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<label><?php __('lblReservationTouristTax'); ?></label>
											<div class="input-group">
												<input type="text" class="form-control number" name="tourist_tax" id="tourist_tax" value="<?php echo $tpl['arr']['tourist_tax'];?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
												<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
											</div>
										</div>
									</div>
		
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<?php
											$total =  $tpl['arr']['amount'] + $tpl['arr']['extra_price'] - $tpl['arr']['discount_amount'] - $tpl['arr']['promo_amount'] + $tpl['arr']['tax'] + $tpl['arr']['tourist_tax'];
											$total = $total < 0 ? 0 : $total;
											$payment_made = $tpl['arr']['status'] == 'Confirmed' ? $tpl['arr']['deposit'] : 0;
											$payment_due = $total - $payment_made;
											$payment_due = $payment_due < 0 ? 0 : $payment_due;
											?>
											<label><?php __('lblReservationTotal'); ?></label>
											<div class="input-group">
												<input type="text" class="form-control number" name="total" id="total" value="<?php echo $total;?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
												<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
											</div>
										</div>
									</div>
		
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<label><?php __('lblReservationDeposit'); ?></label>
											<div class="input-group">
												<input type="text" class="form-control number" name="deposit" id="deposit" value="<?php echo $tpl['arr']['deposit'];?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
												<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
											</div>
										</div>
									</div>
									
									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<label><?php __('lblReservationSecurity'); ?></label>
											<div class="input-group">
												<input type="text" class="form-control number" name="security" id="security" value="<?php echo $tpl['arr']['security'];?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
												<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
											</div>
										</div>
									</div>
									
								</div>

								<div class="hr-line-dashed"> </div>

								<div class="clearfix">
									<button type="submit" class="btn btn-primary btn-lg pull-left"><?php __('btnSave'); ?></button>
									<a class="btn btn-white btn-lg pull-right" href="#"><?php __('btnCancel')?></a>
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane" id="client-details">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-4 col-sm-4 pjBookingFormField" style="display: <?php echo (int) $tpl['__option_arr']['o_bf_name'] === 1 ? 'none' : '';?>">
										<div class="form-group">
											<label><?php __('lblReservationName'); ?></label>
											<div>
												<input type="text" name="c_name" id="c_name" value="<?php echo pjSanitize::html($tpl['arr']['c_name']);?>" class="form-control <?php echo (int) $tpl['__option_arr']['o_bf_name'] === 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>">
											</div>
										</div>
									</div>
									<div class="col-md-4 col-sm-4 pjBookingFormField" style="display: <?php echo (int) $tpl['__option_arr']['o_bf_email'] === 1 ? 'none' : '';?>">
										<div class="form-group">
											<label><?php __('lblReservationEmail'); ?></label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-at"></i></span>
												<input type="text" name="c_email" id="c_email" value="<?php echo pjSanitize::html($tpl['arr']['c_email']);?>" class="form-control email<?php echo $tpl['__option_arr']['o_bf_email'] == 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-email="<?php __('plugin_base_email_invalid', false, true);?>"/>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-sm-4 pjBookingFormField" style="display: <?php echo (int) $tpl['__option_arr']['o_bf_phone'] === 1 ? 'none' : '';?>">
										<div class="form-group">
											<label><?php __('lblReservationPhone'); ?></label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-phone"></i></span>
												<input type="text" name="c_phone" id="c_phone" value="<?php echo pjSanitize::html($tpl['arr']['c_phone']);?>" class="form-control<?php echo $tpl['__option_arr']['o_bf_phone'] == 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>" />
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4 col-sm-4 pjBookingFormField" style="display: <?php echo (int) $tpl['__option_arr']['o_bf_address'] === 1 ? 'none' : '';?>">
										<div class="form-group">
											<label><?php __('lblReservationAddress'); ?></label>
											<div>
												<input type="text" name="c_address" id="c_address" value="<?php echo pjSanitize::html($tpl['arr']['c_address']);?>" class="form-control <?php echo (int) $tpl['__option_arr']['o_bf_address'] === 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">
											</div>
										</div>
									</div>
									<div class="col-md-4 col-sm-4 pjBookingFormField" style="display: <?php echo (int) $tpl['__option_arr']['o_bf_city'] === 1 ? 'none' : '';?>">
										<div class="form-group">
											<label><?php __('lblReservationCity'); ?></label>
											<div>
												<input type="text" name="c_city" id="c_city" value="<?php echo pjSanitize::html($tpl['arr']['c_city']);?>" class="form-control <?php echo (int) $tpl['__option_arr']['o_bf_city'] === 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">
											</div>
										</div>
									</div>
									<div class="col-md-4 col-sm-4 pjBookingFormField" style="display: <?php echo (int) $tpl['__option_arr']['o_bf_state'] === 1 ? 'none' : '';?>">
										<div class="form-group">
											<label><?php __('lblReservationState'); ?></label>
											<div>
												<input type="text" name="c_state" id="c_state" value="<?php echo pjSanitize::html($tpl['arr']['c_state']);?>" class="form-control <?php echo (int) $tpl['__option_arr']['o_bf_state'] === 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4 col-sm-4">
										<div class="form-group pjBookingFormField" style="display: <?php echo (int) $tpl['__option_arr']['o_bf_zip'] === 1 ? 'none' : '';?>">
											<label><?php __('lblReservationZip'); ?></label>
											<div>
												<input type="text" name="c_zip" id="c_zip" value="<?php echo pjSanitize::html($tpl['arr']['c_zip']);?>" class="form-control <?php echo (int) $tpl['__option_arr']['o_bf_zip'] === 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">
											</div>
										</div>
										<div class="form-group pjBookingFormField" style="display: <?php echo (int) $tpl['__option_arr']['o_bf_country'] === 1 ? 'none' : '';?>">
											<label><?php __('lblReservationCountry'); ?></label>
											<div>
												<select name="c_country" id="c_country" class="form-control select-item <?php echo (int) $tpl['__option_arr']['o_bf_country'] === 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">
													<option value="">-- <?php __('lblChoose'); ?> --</option>
													<?php
													foreach ($tpl['country_arr'] as $country)
													{
														?><option value="<?php echo $country['id']; ?>" <?php echo $tpl['arr']['c_country'] == $country['id'] ? 'selected="selected"' : '';?>><?php echo stripslashes($country['name']); ?></option><?php
													}
													?>
												</select>
											</div>
										</div>
									</div>
									<div class="col-md-8 col-sm-8 pjBookingFormField" style="display: <?php echo (int) $tpl['__option_arr']['o_bf_notes'] === 1 ? 'none' : '';?>">
										<div class="form-group">
											<label><?php __('lblReservationNotes'); ?></label>
											<div>
												<textarea name="c_notes" id="c_notes" class="form-control<?php echo $tpl['__option_arr']['o_bf_notes'] == 3 ? ' pjRpbcRequired required' : NULL; ?>" rows="4" cols="30" data-msg-required="<?php __('pj_field_required', false, true);?>"><?php echo stripslashes($tpl['arr']['c_notes']); ?></textarea>
											</div>
										</div>
									</div>
									
								</div>
								<div class="hr-line-dashed"></div>
								<div class="clearfix">
									<button class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in">
										<span class="ladda-label"><?php __('btnSave'); ?></span>
										<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>   
									</button>
								
									<button class="btn btn-white btn-lg pull-right" type="button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminReservations&action=pjActionIndex';"><?php __('btnCancel'); ?></button>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="m-b-lg">
					<div class="edit-reservation-actions">
						<a href="#" class="btn btn-primary btn-outline btn-block confirmation-email" data-id="<?php echo $tpl['arr']['id'];?>" title="<?php __('lblReservationResend'); ?>"><i class="fa fa-envelope"></i> <?php __('lblReservationResend'); ?></a>
						<a href="#" class="btn btn-primary btn-outline btn-block cancellation-email" data-id="<?php echo $tpl['arr']['id'];?>" title="<?php __('lblReservationCancel'); ?>"><i class="fa fa-times"></i> <?php __('lblReservationCancel'); ?></a>
					</div>
					<div id="pjRpbcSummaryWrapper" class="panel no-borders">
						<div id="panel-status" class="panel-heading bg-<?php echo strtolower($tpl['arr']['status']);?>">
							<p class="lead m-n">
								<i class="fa fa-exclamation-triangle"></i> <?php __('lblReservationStatus'); ?>: <span class="pull-right status-text"><?php echo @$reservation_statuses[$tpl['arr']['status']];?></span>
							</p>
						</div>
						<div class="panel-body">
							<p class="lead m-b-xs">
								<i class="fa color-pending fa-key"></i> <?php __('lblReservationUuid')?>:<span class="pull-right"><?php echo pjSanitize::html($tpl['arr']['uuid']);?></span>
							</p>
							<p class="lead m-b-md">
								<i class="fa color-pending fa-calendar"></i> <?php __('lblReservationCreated'); ?>: <span class="pull-right"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['created'])); ?>, <?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['created'])); ?></span>
							</p>
							<p class="lead m-b-xs">
								<i class="fa color-pending fa-globe"></i> <?php __('lblIp'); ?>:<span class="pull-right"><?php echo pjSanitize::html($tpl['arr']['ip']);?></span>
							</p>
							<p class="lead m-b-xs">
								<i class="fa color-pending fa-gift"></i> <?php __('lblReservationCalendar'); ?>:
								<?php if (pjAuth::factory('pjAdminCalendars', 'pjActionUpdate')->hasAccess()) { ?>
		            				<span class="pull-right"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionRedirect&amp;nextController=pjAdminCalendars&amp;nextAction=pjActionUpdate&amp;calendar_id=<?php echo $tpl['arr']['calendar_id']; ?>&amp;nextParams=<?php echo urlencode('id='. $tpl['arr']['calendar_id']); ?>"><?php echo pjSanitize::html($tpl['arr']['calendar_name']);?></a></span>
		            			<?php } else { ?>
		            				<span class="pull-right"><a href="javascript:void(0);"><?php echo pjSanitize::html($tpl['arr']['calendar_name']);?></a></span>
		            			<?php } ?>
							</p>
						</div>
					</div>
					<div class="edit-reservation-widgets" style="margin: 0;">
						<div class="m-b-md">
							<a href="javascript:void(0);" class="widget widget-bg widget-client-info">
							<?php if (!empty($tpl['arr']['c_name'])) { ?>
								<p class="lead m-b-xs">
									<i class="fa fa-user"></i> <?php echo pjSanitize::html($tpl['arr']['c_name']);?>
								</p>
							<?php } ?>
							<?php if (!empty($tpl['arr']['c_email'])) { ?>
								<p class="lead m-b-xs">
									<i class="fa fa-envelope-o"></i> <?php echo pjSanitize::html($tpl['arr']['c_email']);?>
								</p>
							<?php } ?>
							<?php if (!empty($tpl['arr']['c_phone'])) { ?>
								<p class="lead m-n">
									<i class="fa fa-phone"></i> <?php echo pjSanitize::html($tpl['arr']['c_phone']);?>
								</p>
							<?php } ?>
							</a>
						</div>
						<div class="m-b-md">
							<a href="javascript:void(0);" class="widget widget-bg">
								<p class="lead m-b-xs">
									 <?php __('lblReservationTotalPrice');?>: <strong class="pull-right cr-total-quote"><?php echo pjCurrency::formatPrice($total);?></strong>
								</p>
								<p class="lead m-b-xs">
									 <?php __('lblReservationPaymentMade');?>: <strong class="pull-right pj_collected"><?php echo pjCurrency::formatPrice($payment_made);?></strong>
								</p>
								<p class="lead m-n">
									 <?php __('lblReservationPaymentDue');?>: <strong id="pj_due_payment" class="pull-right"><?php echo pjCurrency::formatPrice($payment_due);?></strong>
								</p>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" id="confirmEmailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
		      <div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        	<h4 class="modal-title"><?php __('rpbc_email_confirmation'); ?></h4>
		      </div>
		      <div id="confirmEmailContentWrapper" class="modal-body"></div>
		      <div class="modal-footer">
		        	<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel');?></button>
		        	<button id="btnSendEmailConfirm" type="button" class="btn btn-primary"><?php __('btnSend');?></button>
		      </div>
	    </div><!-- /.modal-content -->
  	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="cancellationEmailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
		      <div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        	<h4 class="modal-title"><?php __('rpbc_email_cancellation'); ?></h4>
		      </div>
		      <div id="cancellationEmailContentWrapper" class="modal-body"></div>
		      <div class="modal-footer">
		        	<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel');?></button>
		        	<button id="btnSendEmailCancellation" type="button" class="btn btn-primary"><?php __('btnSend');?></button>
		      </div>
	    </div><!-- /.modal-content -->
  	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.dateRangeValidation = "<?php __('lblReservationDateRangeValidation'); ?>";
myLabel.duplicatedUniqueID = "<?php __('lblDuplicatedUniqueID'); ?>";
myLabel.choose = <?php x__encode('lblChoose');?>;
</script>