<div id="periods" class="tab-pane<?php echo $active_tab == 'periods' ? ' active' : NULL;?>">
	<?php 
	$days[7] = $days[0];
	$days[0] = NULL;
	unset($days[0]);
	?>
    <div class="panel-body">
		<div class="panel-body-inner">
			<?php 
			$info = str_replace("[STAG]", "<a href='#' data-toggle='modal' data-target='#modalCopyPrices' class='btn btn-primary btn-outline'>", __('copyPricePerPeriodsInfo', true));
			$info = str_replace("[ETAG]", "</a>", $info); 
			?>
			<div class="alert alert-success"><?php echo $info;?></div>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionUpdate" method="post" id="frmPeriods" class="form pj-form">
				<input type="hidden" name="property_update" value="1" />
        		<input type="hidden" name="tab" value="prices" />
        		<input type="hidden" name="tab_id" value="11" />
				<div class="table-responsive table-responsive-secondary">
					<table id="tblPeriods" class="table table-condensed">
						<thead>
							<tr>
								<th><?php __('period_start_date'); ?></th>
								<th><?php __('period_end_date'); ?></th>
								<th><?php __('period_from_day'); ?></th>
								<th><?php __('period_to_day'); ?></th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						<?php
						foreach ($tpl['period_arr'] as $period)
						{
							?>
							<tr class="mainPeriod" data-idx="<?php echo $period['id']; ?>">
								<td>
									<div class="input-group"> 
										<input type="text" name="start_date[<?php echo $period['id']; ?>]" id="start_date_<?php echo $period['id']; ?>" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($period['start_date']));?>" class="form-control datepick required" readonly="readonly" data-msg-required="<?php __('pj_field_required');?>" /> 
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>
								</td>
								<td>
									<div class="input-group"> 
										<input type="text" name="end_date[<?php echo $period['id']; ?>]" id="end_date_<?php echo $period['id']; ?>" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($period['end_date']));?>" class="form-control datepick required" readonly="readonly" data-msg-required="<?php __('pj_field_required');?>" /> 
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>
								</td>
								<td>
									<select name="from_day[<?php echo $period['id']; ?>]" class="form-control">
									<?php
									foreach ($days as $index => $day)
									{
										?><option value="<?php echo $index; ?>"<?php echo $period['from_day'] == $index ? ' selected="selected"' : NULL; ?>><?php echo $day; ?></option><?php
									}
									?>
									</select>
								</td>
								<td>
									<select name="to_day[<?php echo $period['id']; ?>]" class="form-control">
									<?php
									foreach ($days as $index => $day)
									{
										?><option value="<?php echo $index; ?>"<?php echo $period['to_day'] == $index ? ' selected="selected"' : NULL; ?>><?php echo $day; ?></option><?php
									}
									?>
									</select>
								</td>
								<td class="w30">
									<a href="#" class="btn btn-danger btn-outline btn-sm btnDeletePeriod" data-id="<?php echo $period['id']; ?>"><i class="fa fa-trash"></i> <?php __('btnDelete');?></a>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="text-right"><?php __('period_default_price'); ?></td>
								<td colspan="3">
									<div class="input-group">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></span>	
										<input type="text" class="form-control required number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" name="default_price[<?php echo $period['id']; ?>]" value="<?php echo $period['default_price'];?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('prices_invalid_price', false, true);?>">
									</div>
								</td>
							</tr>
							<?php
							if (isset($period['price_arr']))
							{
								foreach ($period['price_arr'] as $item)
								{
									$rand = 'x_' . rand(100000, 999999);
									?>
									<tr>
										<td colspan="2" class="text-right">
											<div class="row">
												<div class="col-xs-6 col-sm-3">
													<p class="form-control-static"><?php __('period_adults'); ?></p>
												</div>
												<div class="col-xs-6 col-sm-3">
													<select name="adults[<?php echo $period['id']; ?>][<?php echo $rand;?>]" class="form-control">
													<?php
													foreach (range(1, $tpl['option_arr']['o_bf_adults_max']) as $i)
													{
														?><option value="<?php echo $i; ?>"<?php echo $item['adults'] == $i ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option><?php
													}
													?>
													</select>
												</div>
												<div class="col-xs-6 col-sm-3">
													<p class="form-control-static"><?php __('period_children'); ?></p>
												</div>
												<div class="col-xs-6 col-sm-3">
													<select name="children[<?php echo $period['id']; ?>][<?php echo $rand;?>]" class="form-control"><?php
													foreach (range(0, $tpl['option_arr']['o_bf_children_max']) as $i)
													{
														?><option value="<?php echo $i; ?>"<?php echo $item['children'] == $i ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option><?php
													}
													?>
													</select>
												</div>
											</div>
										</td>
										<td colspan="2">
											<div class="input-group">
												<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></span>	
												<input type="text" class="form-control required number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" name="price[<?php echo $period['id']; ?>][<?php echo $rand;?>]" value="<?php echo $item['price'];?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('prices_invalid_price', false, true);?>">
											</div>
										</td>
										<td><a href="#" class="pj-table-icon-delete btnRemoveAdultsChildren"></a></td>
									</tr>
									<?php
								}
							}
							?>
							<tr>
								<td colspan="2" class="text-right">
									<a href="javascript:void(0);" class="btn btn-primary btn-outline btnAdultsChildren" data-idx="<?php echo $period['id']; ?>"><i class="fa fa-plus"></i> <?php __('period_adults_children');?></a>
								</td>
								<td colspan="3"></td>
							</tr>
							<?php
						}
						if (count($tpl['period_arr']) === 0)
						{
							ob_start();
							include dirname(__FILE__) . '/periods_tpl.php';
							$content = ob_get_contents();
							ob_end_clean();
							echo str_replace('{INDEX}', 'new_'.rand(1, 99999), $content);
						}
						?>
						</tbody>
					</table>
				</div>
				
				<div class="hr-line-dashed"></div>
				<div class="clearfix">
					<button type="submit" class="ladda-button btn btn-primary btn-lg m-r-sm btn-phpjabbers-loader" data-style="zoom-in">
						<span class="ladda-label"><?php __('btnSave', false, true); ?></span>
						<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
					</button>
					<a class="btn btn-primary btn-outline btn-lg btnAddPeriod" href="javascript:void(0);"><i class="fa fa-plus"></i> <?php __('period_add_period');?></a>
					<a type="button" class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCalendars&action=pjActionIndex"><?php __('btnCancel'); ?></a> 
				</div><!-- /.clearfix -->
				<br/>
				<div class="alert alert-success bxPeriodStatus bxPeriodStatusStart" style="display: none"><?php __('period_status_start'); ?></div>
				<div class="alert alert-success bxPeriodStatus bxPeriodStatusEnd" style="display: none"><?php __('period_status_end'); ?></div>
			</form>
		</div>
	</div>
	
	<table id="periodAdults" style="display: none">
		<tbody>
			<tr>
				<td colspan="2" class="text-right">
					<div class="row">
						<div class="col-xs-6 col-sm-3">
							<p class="form-control-static"><?php __('period_adults'); ?></p>
						</div>
						<div class="col-xs-6 col-sm-3">
							<select name="adults[{INDEX}][{RAND}]" class="form-control">
							<?php
							foreach (range(1, $tpl['option_arr']['o_bf_adults_max']) as $i)
							{
								?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
							}
							?>
							</select>
						</div>
						<div class="col-xs-6 col-sm-3">
							<p class="form-control-static"><?php __('period_children'); ?></p>
						</div>
						<div class="col-xs-6 col-sm-3">
							<select name="children[{INDEX}][{RAND}]" class="form-control"><?php
							foreach (range(0, $tpl['option_arr']['o_bf_children_max']) as $i)
							{
								?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
							}
							?>
							</select>
						</div>
					</div>
				</td>
				<td colspan="2">
					<div class="input-group">
						<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></span>	
						<input type="text" class="form-control required number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" name="price[{INDEX}][{RAND}]" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('prices_invalid_price', false, true);?>">
					</div>
				</td>
				<td><a href="#" class="btn btn-danger btn-outline btn-sm m-n btnRemoveAdultsChildren"><i class="fa fa-trash"></i></a></td>
			</tr>
		</tbody>
	</table>
	<table id="periodDefault" style="display: none">
		<tbody><?php include dirname(__FILE__) . '/periods_tpl.php'; ?></tbody>
	</table>
	
	<div class="modal inmodal fade" id="modalDeletePeriod" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php __('btnClose'); ?></span></button>

                    <h2 class="no-margins"><?php __('period_del_title');?></h2>
                </div>

                <div class="panel-body">
                    <p><?php __('period_del_desc');?></p>
                </div><!-- /.panel-body -->
                
                <div class="modal-footer">
                    <a href="javascript:void(0);" class="btn btn-primary btnConfirmDeletePeriod"><?php __('btnDelete'); ?></a>
                    <a href="javascript:void(0);" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel'); ?></a>
                </div>
	        </div>
	    </div>
	</div>
		
	<!-- Modal -->
	<div class="modal fade" id="modalCopyPrices" tabindex="-1" role="dialog" aria-labelledby="myCopyPricesLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myCopyPricesLabel"><?php __('modalCopyPricePerPeriodsTitle');?></h4>
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
	            <input type="hidden" name="copy_tab_id" value="11" />
	            <input type="hidden" name="copy_tab" value="prices" />
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