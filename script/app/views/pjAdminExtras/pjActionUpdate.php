<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionUpdate" method="post" id="frmUpdateExtra" class="form pj-form" autocomplete="off">
	<input type="hidden" name="extra_update" value="1" />
	<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
	<input type="hidden" id="apply_max_count" name="apply_max_count" value="" />
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalExtraLabel"><?php __('infoUpdateExtraTitle');?></h4>
	</div>
	<div class="modal-body">
		<?php if ($tpl['is_flag_ready']) : ?>
		<div class="row">
			<div class="col-sm-12 btn-group-languages text-right">
				<div class="multilang"></div>
			</div>
		</div><!-- /.row -->
		
		<?php endif; ?>
		<div class="row">
			<div class="col-sm-8">
				<div class="form-group">
					<label class="control-label"><?php __('lblExtraName');?></label>
		
					<?php
					foreach ($tpl['lp_arr'] as $v)
					{
						?>
						<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group ' : NULL;?>pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
							<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][name]" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['name'])); ?>" data-msg-required="<?php __('pj_field_required');?>">	
							<?php if ($tpl['is_flag_ready']) : ?>
							<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
							<?php endif; ?>
						</div>
						<?php 
					}
					?>
				</div>
			</div>
		
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label"><?php __('lblStatus'); ?></label>
					<div class="clearfix">
						<div class="switch onoffswitch-data pull-left">
							<div class="onoffswitch">
								<input class="onoffswitch-checkbox" id="status" name="status" type="checkbox" <?php echo $tpl['arr']['status'] == 'T' ? 'checked="checked"' : '';?>>
								<label class="onoffswitch-label" for="status">
								<span class="onoffswitch-inner" data-on="<?php __('filter_ARRAY_active')?>" data-off="<?php __('filter_ARRAY_inactive')?>"></span>
								<span class="onoffswitch-switch"></span>
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div class="form-group">
			<label class="control-label"><?php __('lblAvailableFor'); ?></label>
		
			<select name="calendar_id[]" id="calendar_id" class="select-item form-control required" multiple data-msg-required="<?php __('pj_field_required');?>" data-placeholder="-- <?php __('lblChoose');?> --">
				<?php
				foreach ($tpl['calendar_arr'] as $v)
				{
					?><option value="<?php echo $v['id']; ?>" <?php echo in_array($v['id'], $tpl['calendar_id_arr']) ? ' selected="selected"' : NULL;?>><?php echo stripslashes($v['name']); ?></option><?php
				}
				?>
			</select>
		</div>
	
		<label class="control-label"><?php __('lblPrice'); ?></label>
	
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<div class="input-group">
						<input class="form-control text-right required number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" type="text" id="price" name="price" value="<?php echo $tpl['arr']['price'];?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('prices_invalid_price', false, true);?>">
						<span class="input-group-addon"><strong><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></strong></span>
					</div>
				</div>
			</div>
			<?php
			$price_types = $tpl['option_arr']['o_price_based_on'] == 'days' ? __('day_price_types', true) : __('price_types', true);
			$price_types = pjUtil::sortArrayByArray($price_types, array('person','night','person_night', 'count','count_night', 'one_time'));
			?>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<select class="form-control" name="price_type" id="price_type">
						<?php
						foreach ($price_types as $k => $v)
						{
							?><option value="<?php echo $k; ?>" <?php echo $k == $tpl['arr']['price_type'] ? ' selected="selected"' : null;?>><?php echo $v; ?></option><?php
						}
						?>                  
					</select>
				</div>
			</div>
		</div>
	
		<div class="row">
			<div class="col-sm-3">
				<div class="form-group">
					<label class="control-label"><?php __('lblRequired'); ?></label>
					<div class="m-t-xs">
						<div class="onoffswitch onoffswitch-yn">
							<input type="checkbox" class="onoffswitch-checkbox" id="required" name="required" <?php echo 'T' == $tpl['arr']['required'] ? ' checked="checked"' : null;?>>
							<label class="onoffswitch-label" for="required">
							<span class="onoffswitch-inner" data-on="<?php __('plugin_base_yesno_ARRAY_T', false, true); ?>" data-off="<?php __('plugin_base_yesno_ARRAY_F', false, true); ?>"></span>
							<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
		
			<div class="col-sm-3 pjExtraSetMultiQty" style="display: <?php echo in_array($tpl['arr']['price_type'], array('count','count_night','one_time')) ? 'none' : '';?>">
				<div class="form-group">
					<label class="control-label"><?php __('lblMultiQuantity'); ?></label>
					<div class="m-t-xs">
						<div class="onoffswitch onoffswitch-yn">
							<input type="checkbox" class="onoffswitch-checkbox" id="multi" name="multi" <?php echo $tpl['arr']['multi'] == 'T' ? ' checked="checked"' : null;?>>
							<label class="onoffswitch-label" for="multi">
							<span class="onoffswitch-inner" data-on="<?php __('plugin_base_yesno_ARRAY_T', false, true); ?>" data-off="<?php __('plugin_base_yesno_ARRAY_F', false, true); ?>"></span>
							<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
		
			<div class="col-sm-4" id="pjRpbMaxCount" style="display: <?php echo $tpl['arr']['multi'] == 'T' ? '' : 'none';?>;">
				<div class="form-group">
					<label class="control-label"><?php __('lblMaximumCount');?></label>
					<input class="touchspin3 form-control" type="text" id="max_count" name="max_count" value="<?php echo $tpl['arr']['max_count'];?>">
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnClose')?></button>
		<button type="button" class="ladda-button btn btn-primary btn-phpjabbers-loader btnSaveExtra" data-style="zoom-in" style="margin-right: 15px;">
			<span class="ladda-label"><?php __('btnSave'); ?></span>
			<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
		</button>
	</div>
</form>