<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-10">
				<h2><?php echo @$titles['AR18'];?></h2>
			</div>
		</div><!-- /.row -->

		<p class="m-b-none"><i class="fa fa-info-circle"></i><?php echo @$bodies['AR18'];?></p>
	</div><!-- /.col-md-12 -->
</div>
<div class="wrapper wrapper-content animated fadeInRight">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate" method="post" id="frmCreateReservation" novalidate="novalidate">
		<input type="hidden" name="reservation_create" value="1" />
		<div id="datePickerOptions" style="display:none;" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-format="<?php echo pjUtil::toBootstrapDate($tpl['option_arr']['o_date_format']); ?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>
		<div class="tabs-container">
			<ul class="nav nav-tabs">
				<li class="active"><a class="tab-reservation-details" href="#reservation-details" rev="1" aria-controls="reservation-details" role="tab" data-toggle="tab" aria-expanded="true"><?php __('tabDetails');?></a></li>
				<li class=""><a class="tab-client-details" href="#client-details" rev="2" aria-controls="client-details" role="tab" data-toggle="tab" aria-expanded="false"><?php __('tabClient');?></a></li>
			</ul>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="reservation-details">
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-3 col-sm-4 col-sm-6">
								<div class="form-group">
									<label><?php __('lblReservationCalendar'); ?></label>
									<div>
										<select name="calendar_id" id="calendar_id" class="form-control select-item required" data-msg-required="<?php __('pj_field_required', false, true);?>" aria-required="true">
											<option value="">-- <?php __('lblChoose'); ?> --</option>
											<?php
											foreach ($tpl['calendars'] as $calendar)
											{
												?><option value="<?php echo $calendar['id']; ?>"<?php echo !$controller->_get->check('calendar_id') || $controller->_get->toInt('calendar_id') != $calendar['id'] ? NULL : ' selected="selected"'?>><?php echo pjSanitize::html($calendar['name']); ?></option><?php
											}
											?>
										</select>
									</div>
								</div>
							</div>

							<div class="col-lg-3 col-sm-4 col-sm-6">
								<div class="form-group">
									<label><?php __('lblReservationUuid'); ?></label>
									<div>
										<input class="form-control required" name="uuid" id="uuid" value="<?php echo pjUtil::uuid(); ?>" maxlength="255" data-msg-required="<?php __('pj_field_required', false, true);?>" type="text" aria-required="true">
									</div>
								</div>
							</div>

							<div class="col-lg-3 col-sm-4 col-sm-6">
								<div class="form-group">
									<label><?php __('lblReservationStatus'); ?></label>
									<div>
										<select name="status" id="status" class="form-control required" data-msg-required="<?php __('pj_field_required', false, true);?>" aria-required="true">
											<option value="">-- <?php __('lblChoose'); ?> --</option>
											<?php
											foreach (__('reservation_statuses', true) as $k => $v)
											{
												?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
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
										<input class="form-control required" id="date_from" name="date_from" value="<?php echo $controller->_get->check('date_from') ? pjDateTime::formatDate($controller->_get->toString('date_from'), 'Y-m-d', $tpl['option_arr']['o_date_format']) : date($tpl['option_arr']['o_date_format']); ?>" type="text" readonly="readonly" data-msg-required="<?php __('pj_field_required');?>">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
							</div>

							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label ><?php __('lblReservationTo'); ?></label>
									<div class="input-group date datepicker-item">
										<input class="form-control required" id="date_to" type="text" name="date_to" value="<?php echo $controller->_get->check('date_from') ? pjDateTime::formatDate(date('Y-m-d', strtotime($controller->_get->toString('date_from')) + 86400), 'Y-m-d', $tpl['option_arr']['o_date_format']) : date($tpl['option_arr']['o_date_format'], strtotime('+1 day')); ?>" readonly="readonly" data-msg-required="<?php __('pj_field_required');?>">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>
									<input type="hidden" name="dates" id="dates" value="0" />
								</div>
							</div>
							<div class="col-lg-6 col-sm-12">
								<div class="row">
									<div class="col-sm-4" id="boxAdults">
										<?php if (in_array($tpl['option_arr']['o_bf_adults'], array(2,3))) {  ?>
											<div class="form-group">
												<label ><?php __('lblReservationAdults'); ?></label>
												<div>
													<input class="pjRpbcAdultsChildSelector form-control <?php echo (int) $tpl['option_arr']['o_bf_adults'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>" type="text" value="1" name="c_adults" id="c_adults" data-max="<?php echo $tpl['option_arr']['o_bf_adults_max'];?>">
												</div>
											</div>
										<?php } ?>
									</div>
									
									<div class="col-sm-4" id="boxChildren">
										<?php if (in_array($tpl['option_arr']['o_bf_children'], array(2,3))) { ?>
											<div class="form-group">
												<label ><?php __('lblReservationChildren'); ?></label>
												<div>
													<input class="pjRpbcAdultsChildSelector form-control <?php echo (int) $tpl['option_arr']['o_bf_children'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>" type="text" value="0" name="c_children" id="c_children" data-max="<?php echo $tpl['option_arr']['o_bf_children_max'];?>">
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

						<div id="boxExtras" style="display: none;"></div>

						<div class="row">
							<div class="col-lg-3 col-md-4 col-sm-6">
								<div id="payment-method-wrapper" class="form-group">
									<label><?php __('lblReservationPayment'); ?></label>
									<div>
										<select name="payment_method" id="payment_method" class="form-control" data-msg-required="<?php __('pj_field_required');?>">
											 <option value="">-- <?php __('lblChoose'); ?> --</option>
										</select>
									</div>
								</div>
							</div>

							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label><?php __('lblReservationPromoCode'); ?></label>

									<input type="text" name="promo_code" id="promo_code" class="form-control">
									<input type="hidden" name="promo_type" id="promo_type" class="form-control"/>
								</div>
							</div>
							
							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label><?php __('lblReservationPromoAmount'); ?></label>
									<div class="input-group">
										<input type="text" class="form-control number" name="promo_amount" id="promo_amount" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
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
										<input type="text" class="form-control number" name="amount" id="amount" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
									</div>
								</div>
							</div>

							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label><?php __('lblReservationExtraPrice'); ?></label>
									<div class="input-group">
										<input type="text" class="form-control number" name="extra_price" id="extra_price" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
									</div>
								</div>
							</div>

							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label><?php __('lblReservationDiscountAmount'); ?></label>
									<div class="input-group">
										<input type="text" class="form-control number" name="discount_amount" id="discount_amount" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
									</div>
								</div>
							</div>

							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label><?php __('lblReservationTax'); ?></label>
									<div class="input-group">
										<input type="text" class="form-control number" name="tax" id="tax" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
									</div>
								</div>
							</div>

							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label><?php __('lblReservationTouristTax'); ?></label>
									<div class="input-group">
										<input type="text" class="form-control number" name="tourist_tax" id="tourist_tax" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
									</div>
								</div>
							</div>

							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label><?php __('lblReservationTotal'); ?></label>
									<div class="input-group">
										<input type="text" class="form-control number" name="total" id="total" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
									</div>
								</div>
							</div>

							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label><?php __('lblReservationDeposit'); ?></label>
									<div class="input-group">
										<input type="text" class="form-control number" name="deposit" id="deposit" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
									</div>
								</div>
							</div>
							
							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label><?php __('lblReservationSecurity'); ?></label>
									<div class="input-group">
										<input type="text" class="form-control number" name="security" id="security" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('pj_field_number');?>">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
									</div>
								</div>
							</div>
							
						</div>

						<div class="hr-line-dashed"> </div>

						<div class="clearfix">
							<button class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in">
								<span class="ladda-label"><?php __('btnSave'); ?></span>
								<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>   
							</button>
						
							<button class="btn btn-white btn-lg pull-right" type="button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminReservations&action=pjActionIndex';"><?php __('btnCancel'); ?></button>
						</div>
					</div>
				</div>

				<div role="tabpanel" class="tab-pane" id="client-details">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4 col-sm-4 pjBookingFormField">
								<div class="form-group">
									<label><?php __('lblReservationName'); ?></label>
									<div>
										<input type="text" name="c_name" id="c_name" class="form-control <?php echo (int) $tpl['option_arr']['o_bf_name'] === 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-sm-4 pjBookingFormField">
								<div class="form-group">
									<label><?php __('lblReservationEmail'); ?></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-at"></i></span>
										<input type="text" name="c_email" id="c_email" class="form-control email<?php echo $tpl['option_arr']['o_bf_email'] == 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>" data-msg-email="<?php __('plugin_base_email_invalid', false, true);?>"/>
									</div>
								</div>
							</div>
							<div class="col-md-4 col-sm-4 pjBookingFormField">
								<div class="form-group">
									<label><?php __('lblReservationPhone'); ?></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-phone"></i></span>
										<input type="text" name="c_phone" id="c_phone" class="form-control<?php echo $tpl['option_arr']['o_bf_phone'] == 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>" />
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 col-sm-4 pjBookingFormField">
								<div class="form-group">
									<label><?php __('lblReservationAddress'); ?></label>
									<div>
										<input type="text" name="c_address" id="c_address" class="form-control <?php echo (int) $tpl['option_arr']['o_bf_address'] === 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-sm-4 pjBookingFormField">
								<div class="form-group">
									<label><?php __('lblReservationCity'); ?></label>
									<div>
										<input type="text" name="c_city" id="c_city" class="form-control <?php echo (int) $tpl['option_arr']['o_bf_city'] === 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-sm-4 pjBookingFormField">
								<div class="form-group">
									<label><?php __('lblReservationState'); ?></label>
									<div>
										<input type="text" name="c_state" id="c_state" class="form-control <?php echo (int) $tpl['option_arr']['o_bf_state'] === 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 col-sm-4">
								<div class="form-group pjBookingFormField">
									<label><?php __('lblReservationZip'); ?></label>
									<div>
										<input type="text" name="c_zip" id="c_zip" class="form-control <?php echo (int) $tpl['option_arr']['o_bf_zip'] === 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">
									</div>
								</div>
								<div class="form-group pjBookingFormField">
									<label><?php __('lblReservationCountry'); ?></label>
									<div>
										<select name="c_country" id="c_country" class="form-control select-item <?php echo (int) $tpl['option_arr']['o_bf_country'] === 3 ? ' pjRpbcRequired required' : NULL; ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">
											<option value="">-- <?php __('lblChoose'); ?> --</option>
											<?php
											foreach ($tpl['country_arr'] as $country)
											{
												?><option value="<?php echo $country['id']; ?>"><?php echo stripslashes($country['name']); ?></option><?php
											}
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-8 col-sm-8 pjBookingFormField">
								<div class="form-group">
									<label><?php __('lblReservationNotes'); ?></label>
									<div>
										<textarea name="c_notes" id="c_notes" class="form-control<?php echo $tpl['option_arr']['o_bf_notes'] == 3 ? ' pjRpbcRequired required' : NULL; ?>" rows="4" cols="30" data-msg-required="<?php __('pj_field_required', false, true);?>"></textarea>
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
	</form>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.dateRangeValidation = "<?php __('lblReservationDateRangeValidation'); ?>";
	myLabel.duplicatedUniqueID = "<?php __('lblDuplicatedUniqueID'); ?>";
	myLabel.choose = <?php x__encode('lblChoose');?>;
	</script>
</div>