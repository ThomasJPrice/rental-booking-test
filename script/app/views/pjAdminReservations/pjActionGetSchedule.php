<?php
$date = $tpl['date'];
$first_day_of_month = $tpl['first_day_of_month'];
$last_day_of_month  = $tpl['last_day_of_month'];
$prev_month = date('Y-m-d', strtotime($date . " -1 month"));
$next_month = date('Y-m-d', strtotime($date . " +1 month"));

$month_dates = array();
$run_date = $first_day_of_month;
while(strtotime($run_date) <= strtotime($last_day_of_month))
{
    $month_dates[] = $run_date;
    $run_date = date('Y-m-d', strtotime($run_date . " +1 day"));
}
$haystack = array(
	'calendarStatus1' => 'abCalendarDate',
	'calendarStatus2' => 'abCalendarReserved',
	'calendarStatus3' => 'abCalendarPending',
	'calendarStatus_1_2' => 'abCalendarReservedNightsStart',
	'calendarStatus_1_3' => 'abCalendarPendingNightsStart',
	'calendarStatus_2_1' => 'abCalendarReservedNightsEnd',
	'calendarStatus_2_3' => 'abCalendarNightsReservedPending',
	'calendarStatus_3_1' => 'abCalendarPendingNightsEnd',
	'calendarStatus_3_2' => 'abCalendarNightsPendingReserved'
);
$months = __('months', true);
ksort($months);
?>
<div class="m-b-md">
    <div class="row">
        <div class="col-md-2 col-sm-3 col-xs-6">
            <a href="#" class="btn btn-primary btn-outline pjRpbcMonthNav" data-date="<?php echo $prev_month;?>" title="Prev Month"><i class="fa fa-angle-left"></i></a>
        </div>

        <div class="col-md-2 col-sm-3 col-xs-6 text-right pull-right">
            <a href="#" class="btn btn-primary btn-outline pjRpbcMonthNav" data-date="<?php echo $next_month;?>" title="Next Month"><i class="fa fa-angle-right"></i></a>
        </div>

        <div class="col-md-8 col-sm-6 col-xs-12 text-center">
            <h2 class="m-n"><?php echo @$months[date('n', strtotime($date))] .' '. date('Y', strtotime($date)); ?></h2>
        </div> 
    </div>
</div>
<div class="table-responsive table-reservations m-t-xs">
    <table class="table table-bordered">
        <thead>
        	<tr>
        		<th>&nbsp;</th>
        		<?php
        		foreach($month_dates as $date)
        		{
        		    ?>
        		    <th class="text-center"><?php echo date('d', strtotime($date));?></th>
        		    <?php
        		}
        		?>
        	</tr>
        </thead>
        <tbody>
        	<?php
        	foreach($tpl['listing_arr'] as $listing)
        	{
        		$date_arr = $listing['date_arr'];
				if ((int) $listing['o_bookings_per_day'] === 1)
				{
					$date_arr = pjUtil::fixSingleDay($date_arr);
				}
            	?>
            	<tr>
            		<td>
            			<?php if (pjAuth::factory('pjAdminCalendars', 'pjActionUpdate')->hasAccess()) { ?>
            				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionRedirect&amp;nextController=pjAdminCalendars&amp;nextAction=pjActionUpdate&amp;calendar_id=<?php echo $listing['id']; ?>&amp;nextParams=<?php echo urlencode('id='. $listing['id']); ?>"><?php echo pjSanitize::html($listing['name']);?></a>
            			<?php } else { ?>
            				<a href="javascript:void(0);"><?php echo pjSanitize::html($listing['name']);?></a>
            			<?php } ?>
            		</td>
            		<?php
            		foreach($month_dates as $date)
            		{
            		    $ts = strtotime($date);            		    
            		    $tomorrow = strtotime('+1 day', $ts);
            		    $yesterday = strtotime('-1 day', $ts);	    	    		
            		    $class = pjUtil::getClass($date_arr, $ts, $tomorrow, $yesterday, $listing['o_bookings_per_day'], $haystack);
            		    if (empty($class)) { 
            		    	$class = ' donut-color-1';
            		    }
            		    ?>
            		    <td>
            		    	<?php if (isset($date_arr[$ts]['status']) && $date_arr[$ts]['status'] != 1) { ?>
            		    		<div class="car-reservation-inner <?php echo $class;?>">
            		    			<?php if (pjAuth::factory('pjAdminReservations', 'pjActionIndex')->hasAccess()) { ?>
            		    				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionIndex&amp;calendar_id=<?php echo $listing['id']; ?>&amp;date=<?php echo $date; ?>">&nbsp;</a>
            		    			<?php } ?>
            		    		</div>
            		    	<?php } else { 
            		    		$params = sprintf("date_from=%s&calendar_id=%u", $date, $listing['id']);
            		    		?>
            		    		<div class="car-reservation-inner <?php echo $class;?>">
            		    			<?php if (pjAuth::factory('pjAdminReservations', 'pjActionCreate')->hasAccess() && $date >= date('Y-m-d')) { ?>
            		    				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionRedirect&amp;nextController=pjAdminReservations&amp;nextAction=pjActionCreate&amp;calendar_id=<?php echo $listing['id']; ?>&amp;nextParams=<?php echo urlencode($params); ?>">&nbsp;</a>
            		    			<?php } ?>
            		    		</div>
            		    	<?php } ?>
            		    </td>
            		    <?php
            		}
            		?>
            	</tr>
            	<?php
        	}
            ?>
        </tbody>
    </table>
</div><!-- /.table-responsive table-reservations m-t-xs -->
<div class="hr-line-dashed"></div>
<?php
$rowCount_arr = array(10, 20, 50, 100, 200, 500);
?>
<input type="hidden" name="date" value="<?php echo $date;?>">

<div class="row table-responsive-actions">
	<div class="col-lg-5 col-md-12 m-b-sm">
		<div class="donut-chart-legend no-padding m-t-sm">
			<strong class="donut-color-1"></strong>
		    <span><?php __('legend_available');?></span>
		
		    <strong class="donut-color-2"></strong>
		    <span><?php __('legend_pending');?></span>
		    
		    <strong class="bg-danger"></strong>
		    <span><?php __('legend_confirmed');?></span>
		</div>
	</div>

	<div class="col-lg-7 col-md-12">
		<div class="row">
			<div class="col-md-4 col-sm-6 pull-right">
				<div class="input-group">
                    <span class="input-group-btn">
                    	<button type="button" class="btn btn-white pj-paginator-list-prev pj-paginator"<?php echo $tpl['paginator']['page'] == 1 ? ' disabled="disabled"' : NULL;?> data-page="<?php echo (int)$tpl['paginator']['page'] > 1 ? $tpl['paginator']['page'] - 1 : 1; ?>"><span class="hidden-sm"><?php __('plugin_base_grid_prev');?></span><i class="fa fa-step-backward visible-sm-inline-block"></i></button>
                    </span>
                    <input type="text" name="page" value="<?php echo $tpl['paginator']['page'];?>" class="form-control pj-selector-goto" data-min="1" data-max="<?php echo $tpl['paginator']['pages'];?>">
                    <span class="input-group-btn">
                    	<button type="button" class="btn btn-white pj-paginator-list-next pj-paginator"<?php echo $tpl['paginator']['page'] == $tpl['paginator']['pages'] ? ' disabled="disabled"' : NULL;?>data-page="<?php echo (int) $tpl['paginator']['page'] <= (int) $tpl['paginator']['pages'] ? $tpl['paginator']['page'] + 1 : $tpl['paginator']['pages']; ?>"><span class="hidden-sm"><?php __('plugin_base_grid_next');?></span><i class="fa fa-step-forward visible-sm-inline-block"></i></button>
                    </span>
                </div>
			</div>

			<div class="pull-right m-r-sm">
				<div class="form-inline show-total mobile-text-right">
		            <div class="form-group">
		                <label><?php __('plugin_base_grid_show');?></label>
		            </div>
		            <div class="form-group m-l-xs">
		                <select name="rowCount" class="form-control pj-selector-row-count">
		                	<?php
		                	foreach($rowCount_arr as $row)
		                	{
		                	    ?>
		                	    <option value="<?php echo $row;?>"<?php echo $tpl['paginator']['rowCount'] == $row ? ' selected' : NULL;?>><?php echo $row;?></option>
		                	    <?php
		                	}
		                	?>
		                </select>
		            </div>
		            <div class="form-group m-l-xs">
		                <label><?php __('plugin_base_grid_total_prefix');?> <strong><?php echo $tpl['paginator']['total'];?></strong> <?php __('plugin_base_grid_total_suffix');?></label>
		            </div>
		        </div>
			</div>
		</div>
	</div>
</div>