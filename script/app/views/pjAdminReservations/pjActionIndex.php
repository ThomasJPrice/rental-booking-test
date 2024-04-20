<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
$rs = __('reservation_statuses', true);
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoReservationsTitle')?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoReservationsDesc')?></p>
    </div><!-- /.col-md-12 -->
</div>
<div class="row wrapper wrapper-content animated fadeInRight">
	<div class="col-lg-12">
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
				case in_array($error_code, array('AR02', 'AR04', 'AR08', 'AR09', 'AR10')):	
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
		<div id="datePickerOptions" style="display:none;" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-format="<?php echo pjUtil::toBootstrapDate($tpl['option_arr']['o_date_format']); ?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>
		<div class="ibox float-e-margins">
			<div class="ibox-content">
				<form method="get" class="frm-filter">
					<div class="row m-b-md">
						<div class="col-sm-3">
							<?php
							if(pjAuth::factory('pjAdminReservations', 'pjActionCreate')->hasAccess())
							{
								?>
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate" class="btn btn-primary"><i class="fa fa-plus"></i> <?php __('btnAddReservation'); ?></a>
								<?php
							}
							?>
						</div><!-- /.col-md-6 -->
			
						<div class="col-md-3 col-sm-5">
							<div class="input-group">
								<input type="text" name="q" placeholder="<?php __('plugin_base_btn_search', false, true); ?>" class="form-control">
			
								<div class="input-group-btn">
									<button class="btn btn-primary" type="submit">
										<i class="fa fa-search"></i>
									</button>
								</div>
							</div>
						</div><!-- /.col-md-3 -->
			
						<div class="col-lg-2 col-md-3 col-sm-4">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="btn btn-primary btn-outline btn-advance-search"><?php __('btnAdvancedSearch'); ?></a>
						</div><!-- /.col-md-2 -->
			
						<div class="col-lg-2 col-lg-offset-2 col-md-12 text-right">
							<select id="filter_status" name="status" class="form-control">
								<option value="">-- <?php __('lblAll');?> --</option>
								<option value="Confirmed"><?php echo $rs['Confirmed'];?></option>
								<option value="Pending"><?php echo $rs['Pending'];?></option>
								<option value="Cancelled"><?php echo $rs['Cancelled'];?></option>
							</select>
						</div><!-- /.col-md-6 -->
					</div><!-- /.row -->
				</form>
				<div id="collapseOne" class="collapse" style="height: 0;" aria-expanded="false">
					<div class="m-b-lg">
						<ul class="agile-list no-padding">
							<li class="success-element b-r-sm">
							<div class="panel-body">
								<form method="get" class="frm-filter-advanced">
									<div class="row">
										<div class="col-lg-3 col-md-4 col-sm-6">
											<div class="form-group">
												<label class="control-label"><?php __('lblReservationName'); ?></label>
												<input class="form-control" type="text" name="c_name" value="<?php echo $controller->_get->check('c_name') ? pjSanitize::html($controller->_get->toString('c_name')) : NULL; ?>">
											</div>
										</div>
				
										<div class="col-lg-2 col-md-4 col-sm-6">
											<div class="form-group">
												<label class="control-label"><?php __('lblReservationEmail'); ?></label>
												<input class="form-control" type="text" name="c_email" value="<?php echo $controller->_get->check('c_email') ? pjSanitize::html($controller->_get->toString('c_email')) : NULL; ?>">
											</div>
										</div>
				
										<div class="col-lg-2 col-md-4 col-sm-6">
											<div class="form-group">
												<label class="control-label"><?php __('lblReservationUuid'); ?></label>
												<input class="form-control" type="text" name="uuid" value="<?php echo $controller->_get->check('uuid') ? pjSanitize::html($controller->_get->toString('uuid')) : NULL; ?>">
											</div>
										</div>
				
										<div class="col-lg-3 col-md-4 col-sm-6">
											<div class="form-group">
												<label class="control-label"><?php __('lblReservationCalendar'); ?></label>
												<select class="form-control" name="calendar_id">
													<option value="">-- <?php __('lblChoose'); ?> --</option>
													<?php
													foreach ($tpl['calendar_arr'] as $v)
													{
														?><option value="<?php echo $v['id']; ?>"<?php echo $controller->_get->check('calendar_id') && $controller->_get->toInt('calendar_id') == $v['id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($v['name']); ?></option><?php
													}
													?>
												</select>
											</div>
										</div>
				
										 <div class="col-lg-2 col-md-4 col-sm-6">
											<div class="form-group">
												<label class="control-label"><?php __('lblReservationStatus'); ?></label>
												<select name="status" class="form-control">
													<option value="">-- <?php __('lblChoose'); ?> --</option>
													<?php
													foreach ($rs as $k => $v)
													{
														?><option value="<?php echo $k; ?>"<?php echo $controller->_get->check('status') && $controller->_get->toString('status') == $k ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($v); ?></option><?php
													}
													?>
												</select>
											</div>
											<!-- /.form-group -->
										</div>
									</div>
									<!-- /.row -->
									<div class="hr-line-dashed">
									</div>
									<div class="row">
										<div class="col-lg-4 col-md-6">
											<h3 class="m-b-md"><?php __('lblFilterDate'); ?></h3>
											<div class="row">
												<div class="col-sm-6">
													<div class="form-group">
														<label class="control-label"><?php __('lblFilterFrom'); ?></label>
														<div class="input-group date datepicker-item">
															<input class="form-control" type="text" name="date_from" id="date_from" autocomplete="off" value="<?php echo $controller->_get->check('date_from') ? pjSanitize::html($controller->_get->toString('date_from')) : NULL; ?>">
															<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
														</div>
													</div>
													<!-- /.form-group -->
												</div>
												<!-- /.col-md-4 -->
												<div class="col-sm-6">
													<div class="form-group">
														<label class="control-label"><?php __('lblFilterTo'); ?></label>
														<div class="input-group date datepicker-item">
															<input class="form-control date" type="text" name="date_to" id="date_to" autocomplete="off" value="<?php echo $controller->_get->check('date_to') ? pjSanitize::html($controller->_get->toString('date_to')) : NULL; ?>">
															<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
														</div>
													</div>
													<!-- /.form-group -->
												</div>
												<!-- /.col-md-4 -->
											</div>
											<!-- /.row -->
										</div>
				
										<div class="col-lg-4 col-md-6">
											<h3 class="m-b-md"><?php __('lblFilterAmount'); ?></h3>
											<div class="row">
												<div class="col-sm-6">
													<div class="form-group">
														<label class="control-label"><?php __('lblFilterFrom'); ?></label>
														<div class="input-group">
															<input class="form-control text-right" type="text" name="amount_from">
															<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
														</div>
													</div>
													<!-- /.form-group -->
												</div>
												<!-- /.col-md-4 -->
												<div class="col-sm-6">
													<div class="form-group">
														<label class="control-label"><?php __('lblFilterTo'); ?></label>
														<div class="input-group">
															<input class="form-control text-right" type="text" name="amount_to">
															<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']);?></span>
														</div>
													</div>
													<!-- /.form-group -->
												</div>
												<!-- /.col-md-4 -->
											</div>
											<!-- /.row -->
										</div>
									</div>
									<div class="m-t-sm">
										<button class="btn btn-primary" type="submit"><?php __('btnSearch');?></button>
										<button class="btn btn-primary btn-outline" type="reset"><?php __('btnCancel');?></button>
									</div>
								</form>
							</div>
							<!-- /.panel-body -->
							</li>
							<!-- /.panel panel-primary -->
						</ul>
					</div>
					<!-- /.m-b-lg -->
				</div>
				
				<div id="grid" class="pj-grid"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var pjGrid = pjGrid || {};
pjGrid.jqDateFormat = "<?php echo pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']); ?>";
pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
pjGrid.isOwner = <?php echo $controller->isOwner() ? 'true' : 'false'; ?>;
pjGrid.hasUpdateProperty = <?php echo pjAuth::factory('pjAdminCalendars', 'pjActionUpdate')->hasAccess() ? 'true' : 'false'; ?>;
pjGrid.hasUpdate = <?php echo pjAuth::factory('pjAdminReservations', 'pjActionUpdate')->hasAccess() ? 'true' : 'false'; ?>;
pjGrid.hasDeleteSingle = <?php echo pjAuth::factory('pjAdminReservations', 'pjActionDeleteReservation')->hasAccess() ? 'true' : 'false'; ?>;
pjGrid.hasDeleteMulti = <?php echo pjAuth::factory('pjAdminReservations', 'pjActionDeleteReservationBulk')->hasAccess() ? 'true' : 'false'; ?>;
pjGrid.hasExport = <?php echo pjAuth::factory('pjAdminReservations', 'pjActionExport')->hasAccess() ? 'true' : 'false'; ?>;
pjGrid.queryString = "";
<?php
if ($controller->_get->check('listing_id') && $controller->_get->toInt('listing_id') > 0)
{
    ?>pjGrid.queryString += "&listing_id=<?php echo $controller->_get->toInt('listing_id'); ?>";<?php
}
if ($controller->_get->check('calendar_id') && $controller->_get->toInt('calendar_id') > 0)
{
    ?>pjGrid.queryString += "&calendar_id=<?php echo $controller->_get->toInt('calendar_id'); ?>";<?php
}
if ($controller->_get->check('date') && !$controller->_get->isEmpty('date'))
{
    ?>pjGrid.queryString += "&date=<?php echo $controller->_get->toString('date'); ?>";<?php
}
if ($controller->_get->check('status') && !$controller->_get->isEmpty('status'))
{
    ?>pjGrid.queryString += "&status=<?php echo $controller->_get->toString('status'); ?>";<?php
}
if ($controller->_get->check('last_7days') && !$controller->_get->isEmpty('last_7days'))
{
    ?>pjGrid.queryString += "&last_7days=<?php echo $controller->_get->toString('last_7days'); ?>";<?php
}
if ($controller->_get->check('current_week') && !$controller->_get->isEmpty('current_week'))
{
    ?>pjGrid.queryString += "&current_week=<?php echo $controller->_get->toString('current_week'); ?>";<?php
}
?>
var myLabel = myLabel || {};
myLabel.choose = <?php x__encode('lblChoose'); ?>;
myLabel.calendar = "<?php __('lblReservationCalendar'); ?>";
myLabel.uuid = <?php x__encode('lblReservationUuid'); ?>;
myLabel.date_from_to = <?php x__encode('lblReservationDateFromTo'); ?>;
myLabel.nights = <?php x__encode('lblReservationNights'); ?>;
myLabel.name_email = <?php x__encode('lblReservationNameEmail'); ?>;
myLabel.status = "<?php __('lblStatus'); ?>";
myLabel.pending = "<?php echo $rs['Pending']; ?>";
myLabel.confirmed = "<?php echo $rs['Confirmed']; ?>";
myLabel.cancelled = "<?php echo $rs['Cancelled']; ?>";
myLabel.exportSelected = "<?php __('lblExportSelected'); ?>";
myLabel.delete_selected = <?php x__encode('plugin_base_delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('plugin_base_delete_confirmation'); ?>;
</script>