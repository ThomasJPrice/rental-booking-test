<div id="general_settings" class="tab-pane<?php echo $active_tab == 'general_settings' ? ' active' : NULL;?>">
    <div class="panel-body form-horizontal">
		<div class="panel-body-inner">
			<?php 
			$info = str_replace("[STAG]", "<a href='#' data-toggle='modal' data-target='#modalCopyGeneralSettings' class='btn btn-primary btn-outline'>", __('copyGeneralSettingsInfo', true));
			$info = str_replace("[ETAG]", "</a>", $info); 
			?>
			<div class="alert alert-success"><?php echo $info;?></div>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionUpdate" method="post" id="frmUpdateCalendar" class="form pj-form">
				<input type="hidden" name="property_update" value="1" />
        		<input type="hidden" name="tab" value="general_settings" />
        		<input type="hidden" name="tab_id" value="1" />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php __('lblName');?>:</label>
					<div class="col-lg-5 col-sm-7">
						<div class="row">
							<div class="col-sm-10">
								<?php
								foreach ($tpl['lp_arr'] as $v)
								{
									?>
									<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group ' : NULL;?>pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
										<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][name]" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['name']); ?>" data-msg-required="<?php __('pj_field_required', false, true);?>">	
										<?php if ($tpl['is_flag_ready']) : ?>
										<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
										<?php endif; ?>
									</div>
									<?php 
								}
								?>
							</div>
						</div>
					</div>
				</div>
	
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php __('lblOwner'); ?>:</label>
					<div class="col-lg-5 col-sm-7">
						<div class="row">
							<div class="col-sm-10">
								<select name="user_id" id="user_id" class="form-control select-item required" data-msg-required="<?php __('pj_field_required');?>" data-placeholder="-- <?php __('lblChoose'); ?> --">
									<option value="">-- <?php __('lblChoose'); ?> --</option>
									<?php
									foreach ($tpl['user_arr'] as $v)
									{
										?><option value="<?php echo $v['id']; ?>" <?php echo $tpl['arr']['user_id'] == $v['id'] ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($v['name']); ?></option><?php
									}
									?>
								</select>
							</div>
						</div>
					</div>
				</div>
	
				<div class="form-group">
					<label class="col-sm-3 control-label" for="o_accept_bookings"><?php __('opt_o_show_prices');?>:</label>
					<div class="col-lg-5 col-sm-7">
						<div class="clearfix">
							<div class="switch onoffswitch-data pull-left">
								<div class="onoffswitch">
									<input class="onoffswitch-checkbox" id="o_show_prices" type="checkbox" name="o_show_prices" <?php echo 1 == $tpl['option_arr']['o_show_prices'] ? ' checked="checked"' : NULL;?>>
									<label class="onoffswitch-label" for="o_show_prices">
									<span class="onoffswitch-inner" data-on="<?php __('plugin_base_yesno_ARRAY_T', false, true); ?>" data-off="<?php __('plugin_base_yesno_ARRAY_F', false, true); ?>"></span>
									<span class="onoffswitch-switch"></span>
									</label>
								</div>
							</div>
						</div>
						<small class="help-block m-b-none"><?php __('opt_o_show_prices_desc');?></small>
						<input type="hidden" name="value-enum-o_show_prices" value="<?php echo '1|0::' . $tpl['option_arr']['o_show_prices'];?>">
					</div>
				</div>
	
				<div class="form-group">
					<label class="col-sm-3 control-label" for="o_accept_bookings"><?php __('opt_o_show_week_numbers');?>:</label>
					<div class="col-lg-5 col-sm-7">
						<div class="clearfix">
							<div class="switch onoffswitch-data pull-left">
								<div class="onoffswitch">
									<input class="onoffswitch-checkbox" id="o_show_week_numbers" type="checkbox" name="o_show_week_numbers" <?php echo 1 == $tpl['option_arr']['o_show_week_numbers'] ? ' checked="checked"' : NULL;?>>
									<label class="onoffswitch-label" for="o_show_week_numbers">
									<span class="onoffswitch-inner" data-on="<?php __('plugin_base_yesno_ARRAY_T', false, true); ?>" data-off="<?php __('plugin_base_yesno_ARRAY_F', false, true); ?>"></span>
									<span class="onoffswitch-switch"></span>
									</label>
								</div>
							</div>
						</div>
						<small class="help-block m-b-none"><?php __('opt_o_show_week_numbers_desc');?></small>
						<input type="hidden" name="value-enum-o_show_week_numbers" value="<?php echo '1|0::' . $tpl['option_arr']['o_show_week_numbers'];?>">
					</div>
				</div>
	
				<div class="form-group">
					<label class="col-sm-3 control-label" for="o_accept_bookings"><?php __('opt_o_show_legend');?>:</label>
					<div class="col-lg-5 col-sm-7">
						<div class="clearfix">
							<div class="switch onoffswitch-data pull-left">
								<div class="onoffswitch">
									<input class="onoffswitch-checkbox" id="o_show_legend" type="checkbox" name="o_show_legend" <?php echo 1 == $tpl['option_arr']['o_show_legend'] ? ' checked="checked"' : NULL;?>>
									<label class="onoffswitch-label" for="o_show_legend">
									<span class="onoffswitch-inner" data-on="<?php __('plugin_base_yesno_ARRAY_T', false, true); ?>" data-off="<?php __('plugin_base_yesno_ARRAY_F', false, true); ?>"></span>
									<span class="onoffswitch-switch"></span>
									</label>
								</div>
							</div>
						</div>
						<small class="help-block m-b-none"><?php __('opt_o_show_legend_desc');?></small>
						<input type="hidden" name="value-enum-o_show_legend" value="<?php echo '1|0::' . $tpl['option_arr']['o_show_legend'];?>">
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
	<!-- Modal -->
	<div class="modal fade" id="modalCopyGeneralSettings" tabindex="-1" role="dialog" aria-labelledby="myCopyGeneralSettingsLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myCopyGeneralSettingsLabel"><?php __('modalCopyGeneralSettingsTitle');?></h4>
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
	            <input type="hidden" name="copy_tab_id" value="1" />
	            <input type="hidden" name="copy_tab" value="general_settings" />
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