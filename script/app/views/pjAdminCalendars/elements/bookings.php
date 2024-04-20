<div id="bookings" class="tab-pane<?php echo $active_tab == 'bookings' ? ' active' : NULL;?>">
	<div class="form-horizontal">
		<div class="panel-body">
			<div class="panel-body-inner">
				<?php 
				$info = str_replace("[STAG]", "<a href='#' data-toggle='modal' data-target='#modalCopyBookingOptions' class='btn btn-primary btn-outline'>", __('copyBookingOptionsInfo', true));
				$info = str_replace("[ETAG]", "</a>", $info); 
				?>
				<div class="alert alert-success"><?php echo $info;?></div>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionUpdate" method="post" id="frmUpdateBookingOptions" class="form pj-form">
					<input type="hidden" name="property_update" value="1" />
	        		<input type="hidden" name="tab" value="bookings" />
	        		<input type="hidden" name="tab_id" value="3" />
					<div class="form-group">
						<label class="col-sm-3 control-label" for="o_accept_bookings"><?php __('opt_o_accept_bookings');?>:</label>
						<div class="col-lg-5 col-sm-7">
							<div class="clearfix">
								<div class="switch onoffswitch-data pull-left">
									<div class="onoffswitch">
										<input class="onoffswitch-checkbox" id="o_accept_bookings" type="checkbox" name="o_accept_bookings" <?php echo 1 == $tpl['option_arr']['o_accept_bookings'] ? ' checked="checked"' : NULL;?>>
										<label class="onoffswitch-label" for="o_accept_bookings">
										<span class="onoffswitch-inner" data-on="<?php __('accept_booking_types_ARRAY_reservations');?>" data-off="<?php __('accept_booking_types_ARRAY_availability');?>"></span>
										<span class="onoffswitch-switch"></span>
										</label>
									</div>
								</div>
							</div>
							<input type="hidden" name="value-enum-o_accept_bookings" value="<?php echo '1|0::' . $tpl['option_arr']['o_accept_bookings'];?>">
							<small class="help-block m-b-none"><?php __('opt_o_accept_bookings_desc');?></small>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_booking_behavior')?>:</label>
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-8">
									<select class="form-control" name="value-enum-o_booking_behavior">
										<?php foreach (__('o_booking_behaviors', true) as $k => $v) { ?>
											<option value="1|2::<?php echo $k;?>" <?php echo $k == $tpl['option_arr']['o_booking_behavior'] ? 'selected="selected"' : '';?>><?php echo $v;?></option>
										<?php } ?>
									</select>
								</div>
							</div>
		
							<small class="help-block m-b-none"><?php __('opt_o_booking_behavior_desc')?></small>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_bookings_per_day');?>:</label>
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-8">
									<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_bookings_per_day'];?>" name="value-int-o_bookings_per_day">
								</div>
							</div>
		
							<small class="help-block m-b-none"><?php __('opt_o_bookings_per_day_desc');?></small>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_status_if_paid');?>:</label>
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-8">
									<select class="form-control" name="value-enum-o_status_if_paid">
										<?php foreach (__('property_b_statuses', true) as $k => $v) { ?>
											<option value="confirmed|pending|cancelled::<?php echo $k;?>" <?php echo $k == $tpl['option_arr']['o_status_if_paid'] ? 'selected="selected"' : '';?>><?php echo $v;?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<small class="help-block m-b-none"><?php __('opt_o_status_if_paid_desc');?></small>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_status_if_not_paid');?>:</label>
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-8">
									<select class="form-control" name="value-enum-o_status_if_not_paid">
										<?php foreach (__('property_b_statuses', true) as $k => $v) { ?>
											<option value="confirmed|pending|cancelled::<?php echo $k;?>" <?php echo $k == $tpl['option_arr']['o_status_if_not_paid'] ? 'selected="selected"' : '';?>><?php echo $v;?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<small class="help-block m-b-none"><?php __('opt_o_status_if_not_paid_desc');?></small>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_price_based_on');?>:</label>
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-8">
									<select class="form-control" name="value-enum-o_price_based_on">
										<?php foreach (__('property_price_based_on', true) as $k => $v) { ?>
											<option value="days|nights::<?php echo $k;?>" <?php echo $k == $tpl['option_arr']['o_price_based_on'] ? 'selected="selected"' : '';?>><?php echo $v;?></option>
										<?php } ?>
									</select>
								</div>
							</div>
		
							 <small class="help-block m-b-none"><?php __('opt_o_price_based_on_desc');?></small>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_price_plugin');?>: </label>
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-8">
									<select class="form-control" name="value-enum-o_price_plugin">
										<?php foreach (__('property_price_plugin', true) as $k => $v) { ?>
											<option value="price|period::<?php echo $k;?>" <?php echo $k == $tpl['option_arr']['o_price_plugin'] ? 'selected="selected"' : '';?>><?php echo $v;?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_bf_adults_max');?>:</label>
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-6">
									<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_bf_adults_max'];?>" name="value-int-o_bf_adults_max">
		
									<small class="help-block m-b-none"><?php __('lblAdults');?></small>
								</div>
		
								<div class="col-sm-6">
									<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_bf_children_max'];?>" name="value-int-o_bf_children_max">
									<small class="help-block m-b-none"><?php __('lblChildren');?></small>
								</div>
							</div>
						</div>
					</div>
		
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_max_people')?>:</label>
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-8">
									<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_max_people'];?>" name="value-int-o_max_people">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_min_people')?>:</label>
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-8">
									<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_min_people'];?>" name="value-int-o_min_people">
								</div>
							</div>
						</div>
					</div>
					<div class="hr-line-dashed"></div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="o_accept_bookings"><?php __('opt_o_disable_payments');?>:</label>
						<div class="col-lg-5 col-sm-7">
							<div class="clearfix">
								<div class="switch onoffswitch-data pull-left">
									<div class="onoffswitch">
										<input class="onoffswitch-checkbox" id="o_disable_payments" type="checkbox" name="o_disable_payments" <?php echo 1 == $tpl['option_arr']['o_disable_payments'] ? ' checked="checked"' : NULL;?>>
										<label class="onoffswitch-label" for="o_disable_payments">
										<span class="onoffswitch-inner" data-on="<?php __('plugin_base_yesno_ARRAY_T', false, true); ?>" data-off="<?php __('plugin_base_yesno_ARRAY_F', false, true); ?>"></span>
										<span class="onoffswitch-switch"></span>
										</label>
									</div>
								</div>
							</div>
							<input type="hidden" name="value-enum-o_disable_payments" value="<?php echo '1|0::' . $tpl['option_arr']['o_disable_payments'];?>">
							<small class="help-block m-b-none"><?php __('opt_o_disable_payments_desc');?></small>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_tourist_tax')?>:</label>
						
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-6">
									<div class="input-group">
										<input type="text" name="value-int-o_tourist_tax" class="form-control number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" value="<?php echo (float)$tpl['option_arr']['o_tourist_tax'];?>" data-msg-number="<?php __('pj_field_number');?>">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
									</div>
								</div>
					
								<div class="col-sm-6">
									<div class="input-group">
										<input type="text" name="value-int-o_max_tax" class="form-control number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" value="<?php echo (float)$tpl['option_arr']['o_max_tax'];?>" data-msg-number="<?php __('pj_field_number');?>">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
									</div>
									<small class="help-block m-b-none"><?php __('opt_o_max_tax');?></small>
								</div>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_deposit')?>:</label>
						
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-6">
									<input type="text" name="value-int-o_deposit" class="form-control number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" value="<?php echo (float)$tpl['option_arr']['o_deposit'];?>" data-msg-number="<?php __('pj_field_number');?>">
								</div>
								<div class="col-sm-6">
									<select name="value-enum-o_deposit_type" class="form-control">
										<?php foreach (__('deposit_types', true) as $k => $v) { ?>
											<option value="amount|percent::<?php echo $k;?>" <?php echo $k == $tpl['option_arr']['o_deposit_type'] ? 'selected="selected"' : '';?>><?php echo $v;?></option>
										<?php } ?>
									</select>
								</div>
							</div>
					
							<small class="help-block m-b-none"><?php __('opt_o_deposit_desc')?></small>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_security')?>:</label>
						
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-6">
									<div class="input-group">
										<input type="text" name="value-int-o_security" class="form-control number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" value="<?php echo (float)$tpl['option_arr']['o_security'];?>" data-msg-number="<?php __('pj_field_number');?>">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
									</div>
								</div>
								<div class="col-sm-6">
									<select name="value-enum-o_to_be_paid" class="form-control">
										<?php foreach (__('security_paid_types', true) as $k => $v) { ?>
											<option value="when_booking|on_arrival::<?php echo $k;?>" <?php echo $k == $tpl['option_arr']['o_to_be_paid'] ? 'selected="selected"' : '';?>><?php echo $v;?></option>
										<?php } ?>
									</select>
								</div>
							</div>
					
							<small class="help-block m-b-none"><?php __('opt_o_security_desc');?></small>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_tax')?>:</label>
						
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-6">
									<div class="input-group">
										<input type="text" name="value-int-o_tax" class="form-control number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" value="<?php echo (float)$tpl['option_arr']['o_tax'];?>" data-msg-number="<?php __('pj_field_number');?>">
										<span class="input-group-addon">%</span>
									</div>
								</div>
							</div>
					
							<small class="help-block m-b-none"><?php __('opt_o_tax_desc')?></small>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_require_all_within');?>:</label>
						
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-6">
									<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_require_all_within'];?>" name="value-int-o_require_all_within">
								</div>
					
								<div class="col-sm-6">
									<div class="m-t-xs"><?php __('opt_o_require_all_within_desc');?></div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_thankyou_page');?></label>
						
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-12">
									<input type="text" class="form-control" name="value-string-o_thankyou_page" value="<?php echo @$tpl['option_arr']["o_thankyou_page"]; ?>">
								</div>
							</div>
					
							<small class="help-block m-b-none"><?php __('opt_o_thankyou_page_desc');?></small>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php __('opt_o_cancel_url');?></label>
						
						<div class="col-lg-5 col-sm-7">
							<div class="row">
								<div class="col-sm-12">
									<input type="text" class="form-control" name="value-string-o_cancel_url" value="<?php echo @$tpl['option_arr']["o_cancel_url"]; ?>">
								</div>
							</div>
					
							<small class="help-block m-b-none"><?php __('opt_o_cancel_url_desc');?></small>
						</div>
					</div>

					<div class="hr-line-dashed"></div>
	
					<div class="clearfix">
						<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
							<span class="ladda-label"><?php __('btnSave'); ?></span>
							<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
						</button>
						<a type="button" class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCalendars&action=pjActionIndex"><?php __('btnCancel'); ?></a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="modalCopyBookingOptions" tabindex="-1" role="dialog" aria-labelledby="myCopyBookingOptionsLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myCopyBookingOptionsLabel"><?php __('modalCopyBookingOptionsTitle');?></h4>
	      </div>
	      <div class="modal-body">
	        <div class="form-group">
	            <label class="control-label"><?php __('lblCopyFrom');?>:</label>
	
	            <select name="copy_calendar_id" class="form-control form-control-lg">
	                <?php
					foreach ($tpl['calendars'] as $calendar)
					{
						if ($calendar['id'] == $controller->getCalendarId())
						{
							continue;
						}
						?><option value="<?php echo $calendar['id']; ?>"><?php echo stripslashes($calendar['name']); ?></option><?php
					}
					?>
	            </select>
	            <input type="hidden" name="copy_tab_id" value="3" />
	            <input type="hidden" name="copy_tab" value="bookings" />
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnClose');?></button>
	        <button type="button" class="ladda-button btn btn-primary btn-phpjabbers-loader btnCopyOptions" data-style="zoom-in" style="margin-right: 15px;">
				<span class="ladda-label"><?php __('btnCopy'); ?></span>
				<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
			</button>
	      </div>
	    </div>
	  </div>
	</div>
</div>