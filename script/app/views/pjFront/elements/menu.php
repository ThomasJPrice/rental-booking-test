<?php
$hasReferer = $controller->_get->check('index') && $controller->_get->toInt('index') > 0;
if ((
		(in_array($controller->_get->toString('action'), array('pjActionGetCalendar')) && $controller->_get->check('view') && $controller->_get->toInt('view') === 1)
		|| in_array($controller->_get->toString('action'), array('pjActionGetAvailability', 'pjActionGetSummaryForm', 'pjActionGetBookingForm'))
	)
	&& $controller->_get->check('locale')
	&& $controller->_get->toInt('locale') > 0
	&& !$hasReferer
	)
{
	//skip
} else {
	if($controller->_get->toString('action') == 'pjActionGetCalendar')
	{
		?><div class="abCalendarNote"><?php __('lblCalendarMessage');?></div><?php
	}
	$front_err = str_replace(array('"', "'"), array('\"', "\'"), __('front_err', true, true));
	?>
	<div class="abErrorMessage" style="display: none" data-msg="<?php echo htmlentities(pjAppController::jsonEncode($front_err)); ?>"></div>
	
	<?php
	ob_start();
	?>
	<div class="col-xs-6">
		<?php 
		if ($hasReferer)
		{
			if($controller->_get->toString('action') == 'pjActionGetCalendar')
			{
				?><a href="#" class="abReturnToAvailability">&laquo;&nbsp;<?php __('lblBackToCalendars'); ?></a><?php
			}else if(in_array($controller->_get->toString('action'), array('pjActionGetSummaryForm', 'pjActionGetBookingForm'))){
				?><a href="#" class="abReturnToCalendar">&laquo;&nbsp;<?php __('lblBackToCalendar'); ?></a><?php
			}
		}else{
			if(in_array($controller->_get->toString('action'), array('pjActionGetSummaryForm', 'pjActionGetBookingForm'))){
				?><a href="#" class="abReturnToCalendar">&laquo;&nbsp;<?php __('lblBackToCalendar'); ?></a><?php
			}
		}
		if ($controller->_get->toString('action') == 'pjActionGetCalendar' && $controller->_get->check('view') && $controller->_get->toInt('view') > 1)
		{
			?>
			<ul class="abMenuNav abMenuList">
				<li class="abMenuNavPrev"><a href="#" class="abCalendarLinkMonth" data-cid="<?php echo $controller->_get->toInt('cid'); ?>" data-year="<?php echo $prev_year; ?>" data-month="<?php echo $prev_month; ?>"><i class="fa fa-chevron-left"></i></a></li>
				<li class="abMenuNavNext"><a href="#" class="abCalendarLinkMonth" data-cid="<?php echo $controller->_get->toInt('cid'); ?>" data-year="<?php echo $next_year; ?>" data-month="<?php echo $next_month; ?>"><i class="fa fa-chevron-right"></i></a></li>
			</ul>
			<?php
		}
		?>
	</div>
	<?php 
	if (!$controller->_get->check('locale') || $controller->_get->toInt('locale') <= 0)
	{
		if (isset($tpl['locale_arr']) && is_array($tpl['locale_arr']) && !empty($tpl['locale_arr']) && count($tpl['locale_arr']) > 1)
		{
			$locale_id = $controller->pjActionGetLocale();
			$selected_title = null;
			$selected_src = NULL;
			foreach ($tpl['locale_arr'] as $locale)
			{
				if($locale_id == $locale['id'])
				{
					$selected_title = $locale['language_iso'];
					$lang_iso = explode("-", $selected_title);
					if(isset($lang_iso[1]))
					{
						$selected_title = $lang_iso[1];
					}
					if (!empty($locale['flag']) && is_file(PJ_INSTALL_PATH . $locale['flag']))
					{
						$selected_src = PJ_INSTALL_URL . $locale['flag'];
					} elseif (!empty($locale['file']) && is_file(PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'])) {
						$selected_src = PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $locale['file'];
					}
					break;
				}
			}
			?>			
			<div class="col-xs-6 ">
				<div class="btn-group  pull-right pjRpbcLanguage" role="group" aria-label="">
					<button type="button" class="btn btn-default dropdown-toggle pjRpbcBtnNav" data-toggle="dropdown" aria-expanded="false">
						<img src="<?php echo $selected_src; ?>" alt=""> <?php echo $selected_title;?>
						<span class="caret"></span>
					</button>
					
					<ul class="dropdown-menu text-capitalize" role="menu">
						<?php
						foreach ($tpl['locale_arr'] as $locale)
						{
							?><li><a href="#" class="abSelectorLocale<?php echo $locale_id == $locale['id'] ? ' pjFdBtnActive' : NULL; ?>" data-id="<?php echo $locale['id']; ?>" title="<?php echo pjSanitize::html($locale['name']); ?>"><?php echo pjSanitize::html($locale['name']); ?></a></li><?php
						} 
						?>
					</ul>
				</div>
			</div>
			
			<?php
		}
	}
	$menu_content = ob_get_contents();
	ob_end_clean();
	if(!empty($menu_content))
	{
		?><div class="abMenu"><div class="row"><?php echo $menu_content;?></div></div><?php
	}
	?>
	<?php
}
?>