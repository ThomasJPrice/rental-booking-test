<?php 
$reservation_statuses = __('reservation_statuses', true);
$map_statuses = array(
	'Confirmed' => 'badge-success',
	'Pending' => 'bg-pending',
	'Cancelled' => 'bg-cancelled'
);
?>
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-4 col-sm-6">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<span class="label label-primary pull-right"><?php __('dash_today');?></span>
					<h5><?php __('dash_new_reservations');?></h5>
				</div>
				<div class="ibox-content ibox-content-stats">
					<div class="row">
						<div class="col-lg-3 col-xs-5">
							<p class="h1 no-margins">
								<a href="#"><?php echo (int) @$tpl['cnt_bookings_today'];?></a>
							</p>
						</div>
						<div class="col-lg-9 col-xs-7 text-right">
							<p class="h1 no-margins">
								<?php echo pjCurrency::formatPrice($tpl['total_amount_today']);?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-sm-6">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<span class="label label-primary pull-right"><?php __('dash_this_month');?></span>
					<h5><?php __('dash_total_reservations');?></h5>
				</div>
				<div class="ibox-content ibox-content-stats">
					<div class="row">
						<div class="col-lg-3 col-xs-5">
							<p class="h1 no-margins">
								<a href="#"><?php echo $tpl['cnt_bookings_this_month'];?></a>
							</p>
						</div>
						<div class="col-lg-9 col-xs-7 text-right">
							<p class="h1 no-margins">
								<?php echo pjCurrency::formatPrice($tpl['total_amount_this_month']);?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-2 col-xs-6">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5><?php __('lblDashUsers');?></h5>
				</div>
				<div class="ibox-content ibox-content-stats">
					<p class="h1 no-margins">
						<a href="#"><?php echo (int)$tpl['cnt_users'];?></a>
					</p>
				</div>
			</div>
		</div>

		<div class="col-lg-2 col-xs-6">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5><?php __('lblDashProperties');?></h5>
				</div>
				<div class="ibox-content ibox-content-stats">
					<p class="h1 no-margins">
						<a href="#"><?php echo (int)$tpl['cnt_calendars'];?></a>
					</p>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-4">
			<div class="ibox float-e-margins">
				<div class="ibox-content ibox-heading clearfix">
					<div class="pull-left dashboard-stats4">
						<h3><?php __('lblDashLatestReservations'); ?></h3>
						<small><?php echo count($tpl['latest_reservation_arr']) > 1 ? sprintf(__('dash_you_received_bookings', true), '<strong>'.count($tpl['latest_reservation_arr']).'</strong>') : sprintf(__('dash_you_received_booking', true), '<strong>'.count($tpl['latest_reservation_arr']).'</strong>');?></small>
					</div>
					<!-- /.pull-left -->
					<div class="pull-right m-t-md dashboard-stats4">
						<?php
                    	if(pjAuth::factory('pjAdminReservations', 'pjActionIndex')->hasAccess())
                    	{
                        	?>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionIndex" class="btn btn-primary btn-sm btn-outline m-n"><?php __('dash_view_all')?></a>
                            <?php
                    	}
                        ?>
					</div>
					<!-- /.pull-right -->
				</div>
				<div class="ibox-content inspinia-timeline">
					<?php if ($tpl['latest_reservation_arr']) {
						$has_update_booking = pjAuth::factory('pjAdminReservations', 'pjActionUpdate')->hasAccess();
						$url_update_booking = 'javascript:void(0);';
						foreach ($tpl['latest_reservation_arr'] as $val) { 
							$nights = $val['nights'];
							if ($has_update_booking) {
								$url_update_booking = $_SERVER['PHP_SELF'].'?controller=pjAdminReservations&amp;action=pjActionUpdate&amp;id='.$val['id'];
							}
							?>
							<div class="timeline-item">
								<div class="content clearfix">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-6">
												<p><i class="fa fa-calendar"></i> <?php echo date($tpl['option_arr']['o_date_format'] . ', ' . $tpl['option_arr']['o_time_format'], strtotime($val['created']));?> </p>
											</div>
		
											<div class="col-xs-6 text-right">
												<div class="badge <?php echo $map_statuses[$val['status']];?> b-r-sm"><?php echo $reservation_statuses[$val['status']];?></div>
											</div>
										</div>
									</div>
		
									<a href="<?php echo $url_update_booking;?>" class="item-image-thumb col-xs-12">
										<p class="m-n">
											<?php __('lblReservationCalendar')?>: <em><?php echo pjSanitize::html($val['calendar_name']);?></em>
										</p>
										<p class="m-n">
											<?php __('lblReservationUuid')?>: <em><?php echo pjSanitize::html($val['uuid']);?></em>
										</p>
										<p class="m-n">
											<?php __('lblReservationClient')?>: <em><?php echo pjSanitize::html($val['c_name']);?></em>
										</p>
										<p class="m-n">
											<?php __('lblReservationDates')?>: <em><?php echo date($tpl['option_arr']['o_date_format'], strtotime($val['date_from'])); ?> - <?php echo date($tpl['option_arr']['o_date_format'], strtotime($val['date_to'])); ?> </em>
										</p>
										<p class="m-b-sm">
											<?php __('lblDashNights')?>: <em><?php echo $nights;?></em>
										</p>
									</a>
								</div>
							</div>
						<?php } ?>
					<?php } else { ?>
						<p><?php __('dashboard_reservations_empty');?></p>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="ibox float-e-margins">
				<div class="ibox-content ibox-heading clearfix">
					<div class="pull-left dashboard-stats4">
						<h3><?php __('dash_reservations_arriving_today');?></h3>
						<small><?php echo count($tpl['arriving_today_arr']) > 1 ? sprintf(__('dash_you_have_bookings_arriving_today', true), '<strong>'.count($tpl['arriving_today_arr']).'</strong>') : sprintf(__('dash_you_have_booking_arriving_today', true), '<strong>'.count($tpl['arriving_today_arr']).'</strong>');?></small>
					</div>
					<!-- /.pull-left -->
					<div class="pull-right m-t-md dashboard-stats4">
						<?php
                    	if(pjAuth::factory('pjAdminReservations', 'pjActionIndex')->hasAccess())
                    	{
                        	?>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionIndex" class="btn btn-primary btn-sm btn-outline m-n"><?php __('dash_view_all')?></a>
                            <?php
                    	}
                        ?>
					</div>
					<!-- /.pull-right -->
				</div>
				<div class="ibox-content inspinia-timeline">
					<?php 
					if ($tpl['arriving_today_arr']) {
						foreach ($tpl['arriving_today_arr'] as $val) { 
							$nights = $val['nights'];
							if ($has_update_booking) {
								$url_update_booking = $_SERVER['PHP_SELF'].'?controller=pjAdminReservations&amp;action=pjActionUpdate&amp;id='.$val['id'];
							}
							$total = $val['amount'] + $val['extra_price'] - $val['discount_amount'] - $val['promo_amount'] + $val['tax'] + $val['tourist_tax'];
							?>
							<div class="timeline-item">
								<div class="content clearfix">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-6">
												<p><i class="fa fa-calendar"></i> <?php echo date($tpl['option_arr']['o_date_format'] . ', ' . $tpl['option_arr']['o_time_format'], strtotime($val['created']));?> </p>
											</div>
		
											<div class="col-xs-6 text-right">
												<div class="badge <?php echo $map_statuses[$val['status']];?> b-r-sm"><?php echo $reservation_statuses[$val['status']];?></div>
											</div>
										</div>
									</div>
		
									<a href="<?php echo $url_update_booking;?>" class="item-image-thumb col-xs-12">
										<p class="m-n">
											<?php __('lblReservationCalendar')?>: <em><?php echo pjSanitize::html($val['calendar_name']);?></em>
										</p>
										<p class="m-n">
											<?php __('lblReservationUuid')?>: <em><?php echo pjSanitize::html($val['uuid']);?></em>
										</p>
										<p class="m-n">
											<?php __('lblReservationClient')?>: <em><?php echo pjSanitize::html($val['c_name']);?></em>
										</p>
										<p class="m-n">
											<?php __('lblReservationDates')?>: <em><?php echo date($tpl['option_arr']['o_date_format'], strtotime($val['date_from'])); ?> - <?php echo date($tpl['option_arr']['o_date_format'], strtotime($val['date_to'])); ?> </em>
										</p>
										<p class="m-n">
											<?php __('lblDashNights')?>: <em><?php echo $nights;?></em>
										</p>
		
										<p class="m-b-sm">
											<?php __('lblReservationTotalPrice')?>: <em><?php echo pjCurrency::formatPrice($total);?></em>
										</p>
									</a>
								</div>
							</div>
						<?php } ?>
					<?php } else { ?>
						<p><?php __('dashboard_reservations_empty');?></p>
					<?php } ?>					
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="ibox float-e-margins">
				<div class="ibox-content ibox-heading clearfix">
					<div class="pull-left dashboard-stats4">
						<h3><?php __('dash_reservations_leaving_today');?></h3>
						<small><?php echo count($tpl['arriving_today_arr']) > 1 ? sprintf(__('dash_you_have_bookings_leaving_today', true), '<strong>'.count($tpl['leaving_today_arr']).'</strong>') : sprintf(__('dash_you_have_booking_leaving_today', true), '<strong>'.count($tpl['leaving_today_arr']).'</strong>');?></small>
					</div>
					<!-- /.pull-left -->
					<div class="pull-right m-t-md dashboard-stats4">
						<?php
                    	if(pjAuth::factory('pjAdminReservations', 'pjActionIndex')->hasAccess())
                    	{
                        	?>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionIndex" class="btn btn-primary btn-sm btn-outline m-n"><?php __('dash_view_all')?></a>
                            <?php
                    	}
                        ?>
					</div>
					<!-- /.pull-right -->
				</div>
				<div class="ibox-content inspinia-timeline">
					<?php 
					if ($tpl['leaving_today_arr']) {
						foreach ($tpl['leaving_today_arr'] as $val) { 
							$nights = $val['nights'];
							if ($has_update_booking) {
								$url_update_booking = $_SERVER['PHP_SELF'].'?controller=pjAdminReservations&amp;action=pjActionUpdate&amp;id='.$val['id'];
							}
							$total = $val['amount'] + $val['extra_price'] - $val['discount_amount'] - $val['promo_amount'] + $val['tax'] + $val['tourist_tax'];
							?>
							<div class="timeline-item">
								<div class="content clearfix">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-6">
												<p><i class="fa fa-calendar"></i> <?php echo date($tpl['option_arr']['o_date_format'] . ', ' . $tpl['option_arr']['o_time_format'], strtotime($val['created']));?> </p>
											</div>
		
											<div class="col-xs-6 text-right">
												<div class="badge <?php echo $map_statuses[$val['status']];?> b-r-sm"><?php echo $reservation_statuses[$val['status']];?></div>
											</div>
										</div>
									</div>
		
									<a href="<?php echo $url_update_booking;?>" class="item-image-thumb col-xs-12">
										<p class="m-n">
											<?php __('lblReservationCalendar')?>: <em><?php echo pjSanitize::html($val['calendar_name']);?></em>
										</p>
										<p class="m-n">
											<?php __('lblReservationUuid')?>: <em><?php echo pjSanitize::html($val['uuid']);?></em>
										</p>
										<p class="m-n">
											<?php __('lblReservationClient')?>: <em><?php echo pjSanitize::html($val['c_name']);?></em>
										</p>
										<p class="m-n">
											<?php __('lblReservationDates')?>: <em><?php echo date($tpl['option_arr']['o_date_format'], strtotime($val['date_from'])); ?> - <?php echo date($tpl['option_arr']['o_date_format'], strtotime($val['date_to'])); ?> </em>
										</p>
										<p class="m-n">
											<?php __('lblDashNights')?>: <em><?php echo $nights;?></em>
										</p>
		
										<p class="m-b-sm">
											<?php __('lblReservationTotalPrice')?>: <em><?php echo pjCurrency::formatPrice($total);?></em>
										</p>
									</a>
								</div>
							</div>
						<?php } ?>	
					<?php } else { ?>
						<p><?php __('dashboard_reservations_empty');?></p>
					<?php } ?>				
				</div>
			</div>
		</div>
	</div>
</div><!-- /.wrapper wrapper-content -->