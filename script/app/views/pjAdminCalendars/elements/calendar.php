<style type="text/css">
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> table.abCalendarTable{
	height: 285px !important;
	max-width: 380px !important;
}
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarMonth{
	height: 40px !important;
}
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarMonthPrev a,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarMonthNext a{
	height: 40px !important;
	max-width: 40px !important;
}
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarWeekDay,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarWeekNum,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarToday,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarReserved,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarPending,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarPast,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarEmpty,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarDate,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarPendingNightsStart,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarPendingNightsEnd,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarReservedNightsStart,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarReservedNightsEnd,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarNightsReservedReserved,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarNightsReservedPending,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarNightsPendingReserved,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarNightsPendingPending{
	height: 40px !important;
	max-width: 40px !important;
}
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarReserved,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarPending,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarPast,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarNightsReservedReserved,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarNightsReservedPending,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarNightsPendingReserved,
#pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?> td.abCalendarNightsPendingPending{
	cursor: pointer !important;
}
</style>
<div id="calendar" class="tab-pane <?php echo $active_tab == 'calendar' ? ' active' : NULL;?>">
	<div class="panel-body">
		<div class="panel-body-inner">
			<div class="row">
				<div class="col-md-4">
					<div id="pjWrapperRPBC_<?php echo $controller->getCalendarId(); ?>">
						<div id="abCalendar_<?php echo $controller->getCalendarId(); ?>" class="abBackendView">
						<?php include PJ_VIEWS_PATH . 'pjAdminCalendars/pjActionGetCal.php'; ?>
						</div>
					</div>
				</div>

				<div class="col-md-8">
					<div class="ibox float-e-margins">
		            	<div class="ibox-content no-margins no-padding no-top-border">
		            		<div class="m-b-sm">
		            			<?php
								if(pjAuth::factory('pjAdminReservations', 'pjActionCreate')->hasAccess())
								{
									?>
									<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate" class="btn btn-primary btn-outline"><i class="fa fa-plus"></i> <?php __('btnAddReservation'); ?></a>
									<?php
								}
								?>
							</div>
		            		<div id="calendar_grid_reservations"></div>
						</div>
					</div>
				</div>
			</div>			
		</div>
	</div>
</div>