<div id="appearance" class="tab-pane<?php echo $active_tab == 'appearance' ? ' active' : NULL;?>">
    <div class="panel-body">
		<div class="panel-body-inner">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionUpdate" method="post" id="frmUpdateAppearance" class="form pj-form" enctype="multipart/form-data">
				<input type="hidden" name="property_update" value="1" />
        		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
        		<input type="hidden" name="tab" value="appearance" />
        		<input type="hidden" name="tab_id" value="2" />
				<?php 
				$info = str_replace("[STAG]", "<a href='#' data-toggle='modal' data-target='#modalCopyAppearance' class='btn btn-primary btn-outline'>", __('copyAppearanceInfo', true));
				$info = str_replace("[ETAG]", "</a>", $info); 
				?>
				<div class="alert alert-success"><?php echo $info;?></div>
	
				<div class="row">
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
							<label><?php __('lblDatesFontFamily');?></label>
		
							<select class="form-control" name="value-enum-o_font_family">
								<?php
							    $default = explode("::", $tpl['calendar_option_arr']['o_font_family']['value']);
							    $enum = explode("|", $default[0]);
							    
							    $enumLabels = array();
							    if (!empty($tpl['calendar_option_arr']['o_font_family']['label']) && strpos($tpl['calendar_option_arr']['o_font_family']['label'], "|") !== false)
							    {
							        $enumLabels = explode("|", $tpl['calendar_option_arr']['o_font_family']['label']);
							    }
							    foreach ($enum as $k => $el)
							    {
							    	if ($default[1] == $el)
							    	{
							    		?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
							       	} else {
							       	    ?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
							       	}
								}
							    ?>
							</select>
						</div>
					</div>
		
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
							<label><?php __('lblLegendFontFamily');?></label>
							
							<select class="form-control" name="value-enum-o_font_family_legend">
								<?php
							    $default = explode("::", $tpl['calendar_option_arr']['o_font_family_legend']['value']);
							    $enum = explode("|", $default[0]);
							    
							    $enumLabels = array();
							    if (!empty($tpl['calendar_option_arr']['o_font_family_legend']['label']) && strpos($tpl['calendar_option_arr']['o_font_family_legend']['label'], "|") !== false)
							    {
							        $enumLabels = explode("|", $tpl['calendar_option_arr']['o_font_family_legend']['label']);
							    }
							    foreach ($enum as $k => $el)
							    {
							    	if ($default[1] == $el)
							    	{
							    		?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
							       	} else {
							       	    ?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
							       	}
								}
							    ?>
							</select>
						</div>
					</div>
				</div>
	
				<div class="hr-line-dashed"></div>
	
				<div class="row">
					<div class="col-lg-3">
						<h2><?php __('lblDateColors');?></h2>
		
						<div class="form-group">
							<label><?php __('opt_o_background_past');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" name="value-color-o_background_past" value="<?php echo $tpl['option_arr']['o_background_past'];?>" class="form-control" />
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_color_past');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" name="value-color-o_color_past" class="form-control" value="<?php echo $tpl['option_arr']['o_color_past'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_background_booked');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" name="value-color-o_background_booked" class="form-control" value="<?php echo $tpl['option_arr']['o_background_booked'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_color_booked');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" name="value-color-o_color_booked" class="form-control" value="<?php echo $tpl['option_arr']['o_color_booked'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_background_available');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_background_available" value="<?php echo $tpl['option_arr']['o_background_available'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_color_available');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_color_available" value="<?php echo $tpl['option_arr']['o_color_available'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_background_select');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_background_select" value="<?php echo $tpl['option_arr']['o_background_select'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_background_pending');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_background_pending" value="<?php echo $tpl['option_arr']['o_background_pending'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_color_pending');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_color_pending" value="<?php echo $tpl['option_arr']['o_color_pending'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_background_empty');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_background_empty" value="<?php echo $tpl['option_arr']['o_background_empty'];?>">
							</div>
						</div>
					</div>
		
					<div class="col-lg-3 col-lg-offset-1">
						<h2><?php __('lblWeekMonthColors');?></h2>
		
						<div class="form-group">
							<label><?php __('opt_o_color_weekday');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_color_weekday" value="<?php echo $tpl['option_arr']['o_color_weekday'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_background_weekday');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_background_weekday" value="<?php echo $tpl['option_arr']['o_background_weekday'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_color_month');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_color_month" value="<?php echo $tpl['option_arr']['o_color_month'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_background_nav');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_background_nav" value="<?php echo $tpl['option_arr']['o_background_nav'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_background_month');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_background_month" value="<?php echo $tpl['option_arr']['o_background_month'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_background_nav_hover');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_background_nav_hover" value="<?php echo $tpl['option_arr']['o_background_nav_hover'];?>">
							</div>
						</div>
		
						<br>
		
						<h2><?php __('lblToolColors');?></h2>
		
						<div class="form-group">
							<label><?php __('opt_o_color_legend');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_color_legend" value="<?php echo $tpl['option_arr']['o_color_legend'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_border_inner');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_border_inner" value="<?php echo $tpl['option_arr']['o_border_inner'];?>">
							</div>
						</div>
		
						<div class="form-group">
							<label><?php __('opt_o_border_outer');?></label>
		
							<div class="input-group colorpicker-component">
								<span class="input-group-addon">
									<i class="form-control-colorpicker"></i>
								</span>
								<input type="text" class="form-control" name="value-color-o_border_outer" value="<?php echo $tpl['option_arr']['o_border_outer'];?>">
							</div>
						</div>
					
					</div>
		
					<div class="col-lg-4 col-lg-offset-1">
						<h2><?php __('lblFontSizes');?></h2>
		
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label><?php __('opt_o_font_size_past');?></label>
		
									<div class="row">
										<div class="col-xs-8">
											<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_font_size_past'];?>" name="value-int-o_font_size_past">
										</div>
										<div class="col-xs-4">
											<div class="m-t-xs">px</div>
										</div>
									</div>
								</div>
							</div>
		
							<div class="col-sm-6">
								<div class="form-group">
									<label><?php __('opt_o_font_size_month');?></label>
		
									<div class="row">
										<div class="col-xs-8">
											<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_font_size_month'];?>" name="value-int-o_font_size_month">
										</div>
										<div class="col-xs-4">
											<div class="m-t-xs">px</div>
										</div>
									</div>
								</div>
							</div>
		
							<div class="col-sm-6">
								<div class="form-group">
									<label><?php __('opt_o_font_size_booked');?></label>
		
									<div class="row">
										<div class="col-xs-8">
											<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_font_size_booked'];?>" name="value-int-o_font_size_booked">
										</div>
										<div class="col-xs-4">
											<div class="m-t-xs">px</div>
										</div>
									</div>
								</div>
							</div>
		
							<div class="col-sm-6">
								<div class="form-group">
									<label><?php __('opt_o_font_size_legend');?></label>
		
									<div class="row">
										<div class="col-xs-8">
											<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_font_size_legend'];?>" name="value-int-o_font_size_legend">
										</div>
										<div class="col-xs-4">
											<div class="m-t-xs">px</div>
										</div>
									</div>
								</div>
							</div>
		
							<div class="col-sm-6">
								<div class="form-group">
									<label><?php __('opt_o_font_size_pending');?></label>
		
									<div class="row">
										<div class="col-xs-8">
											<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_font_size_pending'];?>" name="value-int-o_font_size_pending">
										</div>
										<div class="col-xs-4">
											<div class="m-t-xs">px</div>
										</div>
									</div>
								</div>
							</div>
		
							<div class="col-sm-6">
								<div class="form-group">
									<label><?php __('opt_o_font_size_weekday');?></label>
		
									<div class="row">
										<div class="col-xs-8">
											<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_font_size_weekday'];?>" name="value-int-o_font_size_weekday">
										</div>
										<div class="col-xs-4">
											<div class="m-t-xs">px</div>
										</div>
									</div>
								</div>
							</div>
		
							<div class="col-sm-6">
								<div class="form-group">
									<label><?php __('opt_o_font_size_available');?></label>
		
									<div class="row">
										<div class="col-xs-8">
											<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_font_size_available'];?>" name="value-int-o_font_size_available">
										</div>
										<div class="col-xs-4">
											<div class="m-t-xs">px</div>
										</div>
									</div>
								</div>
							</div>
						</div>
		
						<br>
		
						<h2><?php __('lblFontStyles');?></h2>
		
						<div class="row">
							<div class="col-lg-11">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label><?php __('opt_o_font_style_past')?></label>
		
											<select class="form-control" name="value-enum-o_font_style_past">
												<?php
											    $default = explode("::", $tpl['calendar_option_arr']['o_font_style_past']['value']);
											    $enum = explode("|", $default[0]);
											    
											    $enumLabels = array();
											    if (!empty($tpl['calendar_option_arr']['o_font_style_past']['label']) && strpos($tpl['calendar_option_arr']['o_font_style_past']['label'], "|") !== false)
											    {
											        $enumLabels = explode("|", $tpl['calendar_option_arr']['o_font_style_past']['label']);
											    }
											    foreach ($enum as $k => $el)
											    {
											    	if ($default[1] == $el)
											    	{
											    		?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	} else {
											       	    ?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	}
												}
											    ?>
											</select>
										</div>
									</div>
		
									<div class="col-sm-6">
										<div class="form-group">
											<label><?php __('opt_o_font_style_month');?></label>
		
											<select class="form-control" name="value-enum-o_font_style_month">
												<?php
											    $default = explode("::", $tpl['calendar_option_arr']['o_font_style_month']['value']);
											    $enum = explode("|", $default[0]);
											    
											    $enumLabels = array();
											    if (!empty($tpl['calendar_option_arr']['o_font_style_month']['label']) && strpos($tpl['calendar_option_arr']['o_font_style_month']['label'], "|") !== false)
											    {
											        $enumLabels = explode("|", $tpl['calendar_option_arr']['o_font_style_month']['label']);
											    }
											    foreach ($enum as $k => $el)
											    {
											    	if ($default[1] == $el)
											    	{
											    		?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	} else {
											       	    ?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	}
												}
											    ?>
											</select>
										</div>
									</div>
		
									<div class="col-sm-6">
										<div class="form-group">
											<label><?php __('opt_o_font_style_booked');?></label>
		
											<select class="form-control" name="value-enum-o_font_style_booked">
												<?php
											    $default = explode("::", $tpl['calendar_option_arr']['o_font_style_booked']['value']);
											    $enum = explode("|", $default[0]);
											    
											    $enumLabels = array();
											    if (!empty($tpl['calendar_option_arr']['o_font_style_booked']['label']) && strpos($tpl['calendar_option_arr']['o_font_style_booked']['label'], "|") !== false)
											    {
											        $enumLabels = explode("|", $tpl['calendar_option_arr']['o_font_style_booked']['label']);
											    }
											    foreach ($enum as $k => $el)
											    {
											    	if ($default[1] == $el)
											    	{
											    		?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	} else {
											       	    ?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	}
												}
											    ?>
											</select>
										</div>
									</div>
		
									<div class="col-sm-6">
										<div class="form-group">
											<label><?php __('opt_o_font_style_legend');?></label>
		
											<select class="form-control" name="value-enum-o_font_style_legend">
												<?php
											    $default = explode("::", $tpl['calendar_option_arr']['o_font_style_legend']['value']);
											    $enum = explode("|", $default[0]);
											    
											    $enumLabels = array();
											    if (!empty($tpl['calendar_option_arr']['o_font_style_legend']['label']) && strpos($tpl['calendar_option_arr']['o_font_style_legend']['label'], "|") !== false)
											    {
											        $enumLabels = explode("|", $tpl['calendar_option_arr']['o_font_style_legend']['label']);
											    }
											    foreach ($enum as $k => $el)
											    {
											    	if ($default[1] == $el)
											    	{
											    		?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	} else {
											       	    ?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	}
												}
											    ?>
											</select>
										</div>
									</div>
		
									<div class="col-sm-6">
										<div class="form-group">
											<label><?php __('opt_o_font_style_pending');?></label>
		
											<select class="form-control" name="value-enum-o_font_style_pending">
												<?php
											    $default = explode("::", $tpl['calendar_option_arr']['o_font_style_pending']['value']);
											    $enum = explode("|", $default[0]);
											    
											    $enumLabels = array();
											    if (!empty($tpl['calendar_option_arr']['o_font_style_pending']['label']) && strpos($tpl['calendar_option_arr']['o_font_style_pending']['label'], "|") !== false)
											    {
											        $enumLabels = explode("|", $tpl['calendar_option_arr']['o_font_style_pending']['label']);
											    }
											    foreach ($enum as $k => $el)
											    {
											    	if ($default[1] == $el)
											    	{
											    		?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	} else {
											       	    ?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	}
												}
											    ?>
											</select>
										</div>
									</div>
		
									<div class="col-sm-6">
										<div class="form-group">
											<label><?php __('opt_o_font_style_weekday');?></label>
		
											<select class="form-control" name="value-enum-o_font_style_weekday">
												<?php
											    $default = explode("::", $tpl['calendar_option_arr']['o_font_style_weekday']['value']);
											    $enum = explode("|", $default[0]);
											    
											    $enumLabels = array();
											    if (!empty($tpl['calendar_option_arr']['o_font_style_weekday']['label']) && strpos($tpl['calendar_option_arr']['o_font_style_weekday']['label'], "|") !== false)
											    {
											        $enumLabels = explode("|", $tpl['calendar_option_arr']['o_font_style_weekday']['label']);
											    }
											    foreach ($enum as $k => $el)
											    {
											    	if ($default[1] == $el)
											    	{
											    		?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	} else {
											       	    ?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	}
												}
											    ?>
											</select>
										</div>
									</div>
		
									<div class="col-sm-6">
										<div class="form-group">
											<label><?php __('opt_o_font_style_available');?></label>
		
											<select class="form-control" name="value-enum-o_font_style_available">
												<?php
											    $default = explode("::", $tpl['calendar_option_arr']['o_font_style_available']['value']);
											    $enum = explode("|", $default[0]);
											    
											    $enumLabels = array();
											    if (!empty($tpl['calendar_option_arr']['o_font_style_available']['label']) && strpos($tpl['calendar_option_arr']['o_font_style_available']['label'], "|") !== false)
											    {
											        $enumLabels = explode("|", $tpl['calendar_option_arr']['o_font_style_available']['label']);
											    }
											    foreach ($enum as $k => $el)
											    {
											    	if ($default[1] == $el)
											    	{
											    		?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	} else {
											       	    ?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
											       	}
												}
											    ?>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
		
						<br>
		
						<h2><?php __('lblBorderWidth');?></h2>
		
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label><?php __('opt_o_border_inner_size');?></label>
		
									<div class="row">
										<div class="col-xs-8">
											<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_border_inner_size'];?>" name="value-int-o_border_inner_size">
										</div>
										<div class="col-xs-4">
											<div class="m-t-xs">px</div>
										</div>
									</div>
								</div>
							</div>
		
							<div class="col-sm-6">
								<div class="form-group">
									<label><?php __('opt_o_border_outer_size');?> </label>
		
									<div class="row">
										<div class="col-xs-8">
											<input class="touchspin3 form-control" type="text" value="<?php echo $tpl['option_arr']['o_border_outer_size'];?>" name="value-int-o_border_outer_size">
										</div>
										<div class="col-xs-4">
											<div class="m-t-xs">px</div>
										</div>
									</div>
								</div>
							</div>
						</div>
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
	<div class="modal fade" id="modalCopyAppearance" tabindex="-1" role="dialog" aria-labelledby="myCopyAppearanceLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myCopyAppearanceLabel"><?php __('modalCopyAppearanceTitle');?></h4>
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
	            <input type="hidden" name="copy_tab_id" value="2" />
	            <input type="hidden" name="copy_tab" value="appearance" />
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