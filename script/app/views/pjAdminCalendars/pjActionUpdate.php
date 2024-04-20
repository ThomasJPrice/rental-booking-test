<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-6">
                <h2><?php echo sprintf(__('infoUpdatePropertyTitle', true), @$tpl['arr']['i18n'][$controller->getLocaleId()]['name']);?></h2>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 btn-group-languages">
                <?php if ($tpl['is_flag_ready']) : ?>
				<div class="multilang"></div>
				<?php endif; ?>
        	</div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoUpdatePropertyDesc');?></p>
    </div><!-- /.col-md-12 -->
</div>
<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);

$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
$days = __('days', true);
$days = pjUtil::sortArrayByArray($days, array('1','2','3','4','5','6','0'));
$rs = __('reservation_statuses', true);
?>
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<?php
        	$error_code = $controller->_get->toString('err');
        	if (!empty($error_code))
        	{
        		switch (true)
        		{
        			case in_array($error_code, array('ACR01', 'ACR03', 'AO01', 'AO02', 'AO03', 'AO04', 'AO05', 'AO06', 'AO07', 'AO10', 'AO11'), 'AO12'):
        				?>
        				<div class="alert alert-success">
        					<i class="fa fa-check m-r-xs"></i>
        					<strong><?php echo @$titles[$error_code]; ?></strong>
        					<?php echo @$bodies[$error_code];?>
        				</div>
        				<?php 
        				break;
        			case in_array($error_code, array('ACR04', 'ACR08')):	
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
        	
        	$active_tab = $controller->_get->check('tab') ? $controller->_get->toString('tab') : 'calendar';
        	?>
        	<input type="hidden" name="calendar_id" id="calendar_id" value="<?php echo $controller->getCalendarId();?>" />
			<div id="datePickerOptions" style="display:none;" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-format="<?php echo pjUtil::toBootstrapDate($tpl['option_arr']['o_date_format']); ?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>
			<div class="tabs-container">
				<div class="tabs-left">
					<ul class="nav nav-tabs">
						<li class="<?php echo $active_tab == 'calendar' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="calendar" href="#calendar" aria-expanded="true"><?php __('menuCalendar');?></a></li>
						<li class="<?php echo $active_tab == 'reservations' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="reservations" href="#reservations" aria-expanded="false"><?php __('menuReservations');?></a></li>
						<?php if($tpl['option_arr']['o_price_plugin'] == 'price') { ?>
							<li class="<?php echo $active_tab == 'prices' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="prices" href="#prices" aria-expanded="false"><?php __('menuPrices');?></a></li>
						<?php } else { ?>
							<li class="<?php echo $active_tab == 'periods' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="periods" href="#periods" aria-expanded="false"><?php __('menuPrices');?></a></li>
						<?php } ?>
						<li class="<?php echo $active_tab == 'limits' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="limits" href="#limits" aria-expanded="false"><?php __('menuLimits');?></a></li>
						<li class="<?php echo $active_tab == 'appearance' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="appearance" href="#appearance" aria-expanded="false"><?php __('menuAppearance');?></a></li>
						<li class="<?php echo $active_tab == 'general_settings' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="general_settings" href="#general_settings" aria-expanded="false"><?php __('menuGeneralSettings');?></a></li>
						<li class="<?php echo $active_tab == 'bookings' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="bookings" href="#bookings" aria-expanded="false"><?php __('menuBookingOptions');?></a></li>
						<li class="<?php echo $active_tab == 'payments' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="payments" href="#payments" aria-expanded="false"><?php __('menuPayments');?></a></li>
						<li class="<?php echo $active_tab == 'terms' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="terms" href="#terms" aria-expanded="false"><?php __('menuTerms');?></a></li>
						<li class="<?php echo $active_tab == 'notifications' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="notifications" href="#notifications" aria-expanded="false"><?php __('menuEmailNotifications');?></a></li>
						<li class="<?php echo $active_tab == 'booking_form' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="booking_form" href="#booking_form" aria-expanded="false"><?php __('menuBookingForm');?></a></li>
						<li class="<?php echo $active_tab == 'feeds' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="feeds" href="#feeds" aria-expanded="false"><?php __('menuIcalFeeds');?></a></li>
						<?php if (pjAuth::factory('pjAdminReservations', 'pjActionExportReservation')->hasAccess()) { ?>
							<li class="<?php echo $active_tab == 'export' ? 'active' : NULL;?>"><a data-toggle="tab" data-tab="export" href="#export" aria-expanded="false"><?php __('menuExport');?></a></li>
						<?php } ?>
					</ul>
					<div class="tab-content">
						<?php
						include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/calendar.php';                        	
						include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/reservations.php';  
						if($tpl['option_arr']['o_price_plugin'] == 'price') {                      	
							include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/prices.php';
						} else {
							include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/periods.php';
						}                        	
						include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/limits.php';                        	
						include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/appearance.php';
						include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/general_settings.php'; 
						include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/bookings.php';
						include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/payments.php';                        	
						include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/terms.php';                        	
						include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/notifications.php';                        	
						include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/booking_form.php';                        	
						include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/feeds.php';        
						if (pjAuth::factory('pjAdminReservations', 'pjActionExportReservation')->hasAccess()) {                	
							include PJ_VIEWS_PATH . 'pjAdminCalendars/elements/export.php';
						}
						?>
					</div><!-- /.tab-content -->
				</div><!-- /.tabs-left -->
			</div><!-- /.tabs-container -->	
		</div><!-- /.col-lg-12 -->
	</div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->

<script type="text/javascript">	
var pjGrid = pjGrid || {};
pjGrid.queryString = "";
pjGrid.queryString += "&calendar_id=<?php echo $tpl['arr']['id']; ?>";

var myLabel = myLabel || {};
myLabel.choose = <?php x__encode('lblChoose'); ?>;
myLabel.id = <?php x__encode('lblID'); ?>;
myLabel.res_id = <?php x__encode('lblReservationID'); ?>;
myLabel.from_to = <?php x__encode('lblReservationFromTo'); ?>;
myLabel.date_from_to = <?php x__encode('lblReservationDateFromTo'); ?>;
myLabel.nights = <?php x__encode('lblReservationNights'); ?>;
myLabel.name_email = <?php x__encode('lblReservationNameEmail'); ?>;
myLabel.amount = <?php x__encode('lblReservationAmount'); ?>;
myLabel.status = "<?php __('lblStatus'); ?>";
myLabel.pending = "<?php echo $rs['Pending']; ?>";
myLabel.confirmed = "<?php echo $rs['Confirmed']; ?>";
myLabel.cancelled = "<?php echo $rs['Cancelled']; ?>";
myLabel.delete_selected = <?php x__encode('plugin_base_delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('plugin_base_delete_confirmation'); ?>;
myLabel.prices_invalid_input = "<?php __('prices_invalid_input');?>";
myLabel.duplicated_special_prices = "<?php __('prices_duplicated_special_prices');?>";

myLabel.property = <?php x__encode('lblCalendarName'); ?>;
myLabel.provider = <?php x__encode('rpbc_feeds_provider'); ?>;
myLabel.cnt = <?php x__encode('rpbc_feed_upcoming_reservations'); ?>;
myLabel.alert_btn_import = <?php x__encode('plugin_base_btn_import'); ?>;
myLabel.alert_btn_close = <?php x__encode('plugin_base_btn_close'); ?>;
myLabel.import_title = <?php x__encode('rpbc_feeds_import_title'); ?>;
myLabel.import_desc = <?php x__encode('rpbc_feeds_import_desc'); ?>;
myLabel.import_success_desc = <?php x__encode('rpbc_feeds_import_success_desc'); ?>;
myLabel.import_error_desc = <?php x__encode('rpbc_feeds_import_error_desc'); ?>;
myLabel.import_standard_title = <?php x__encode('rpbc_feeds_import_standard_title'); ?>;
myLabel.import_standard_desc = <?php x__encode('rpbc_feeds_import_standard_desc'); ?>;

myLabel.btn_export = <?php x__encode('btnExport'); ?>;
myLabel.btn_get_url = <?php x__encode('btnGetFeedURL'); ?>;
myLabel.format = <?php x__encode('lblFormat'); ?>;
myLabel.reservations = <?php x__encode('lblReservations'); ?>;
myLabel.period = <?php x__encode('lblPeriod'); ?>;

myLabel.alert_btn_yes = <?php x__encode('btnYes'); ?>;
myLabel.alert_btn_no = <?php x__encode('btnNo'); ?>;

pjGrid.hasUpdateBooking = <?php echo pjAuth::factory('pjAdminReservations', 'pjActionUpdate')->hasAccess() ? 'true' : 'false'; ?>;
pjGrid.hasDeleteSingleBooking = <?php echo pjAuth::factory('pjAdminReservations', 'pjActionDeleteReservation')->hasAccess() ? 'true' : 'false'; ?>;
pjGrid.hasDeleteMultiBookings = <?php echo pjAuth::factory('pjAdminReservations', 'pjActionDeleteReservationBulk')->hasAccess() ? 'true' : 'false'; ?>;

myLabel.isFlagReady = "<?php echo $tpl['is_flag_ready'] ? 1 : 0;?>";
<?php if ($tpl['is_flag_ready']) : ?>
var pjLocale = pjLocale || {};
pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
<?php endif; ?>
</script>