<div id="export" class="tab-pane<?php echo $active_tab == 'export' ? ' active' : NULL;?>">
    <div class="panel-body">
        <div class="panel-body-inner">
        	<?php 
        	$export_formats = __('export_formats', true, false);
			$export_types = __('export_types', true, false);
			$export_periods = __('export_periods', true, false);
        	?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionUpdate&amp;id=<?php echo $controller->getCalendarId();?>&amp;tab=export" method="post" id="frmExportReservations">
				<input type="hidden" name="property_update" value="1" />
				<input type="hidden" name="tab" value="export" />
				<input type="hidden" name="tab_id" value="12" />
				<div class="alert alert-success"><?php echo @$bodies['AR21'];?></div>
				<div class="row">
					<div class="col-lg-3 col-md-4 col-sm-6">
						<div class="form-group">
							<label class="control-label"><?php __('lblFormat'); ?></label>
		
							<select name="format" id="format" class="form-control">
								<?php
								foreach ($export_formats as $k => $v)
								{
									?><option value="<?php echo $k; ?>"<?php echo $controller->_post->toString('format') == $k ? ' selected="selected"' : null; ?>><?php echo pjSanitize::html($v); ?></option><?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="col-lg-3 col-md-4 col-sm-6">
						<div class="form-group">
							<label class="control-label"><?php __('lblType'); ?></label>
		
							<div class="m-t-xs">
								<div class="clearfix">
									<div class="pull-left m-r-lg">
										<label>
											<input type="radio" name="type" id="file" value="file"<?php echo $controller->_post->check('type') ? ($controller->_post->toString('type') == 'file' ? ' checked="checked"' : null) : ' checked="checked"'; ?> class="iChecks"/>
											<?php echo $export_types['file'];?>
										</label>
									</div>
		
									<div class="pull-left">
										<label>
											<input type="radio" name="type" id="feed" value="feed"<?php echo $controller->_post->check('type')  ? ($controller->_post->toString('type') == 'feed' ? ' checked="checked"' : null) : null; ?> class="iChecks"/>
											<?php echo $export_types['feed'];?>
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>					
				</div>
				
				<div class="row abPassowrdContainer" style="display:<?php echo $controller->_post->check('type') ? ($controller->_post->toString('type') == 'file' ? ' none' : ' block' ) : ' none'; ?>">
					<div class="col-lg-3 col-md-4 col-sm-6">
						<div class="form-group">
							<label class="control-label"><?php __('lblEnterPassword');?></label>
		
							<input type="text" id="feed_password" name="password" class="form-control" value="<?php echo $controller->_post->check('password') ? $controller->_post->toString('password') : null; ?>"/>
						</div>
					</div>
				</div>
				<label class="control-label"><?php __('lblReservations'); ?></label>
				<div class="row">
					<div class="col-lg-3 col-md-4 col-sm-6">
						<div class="form-group">
							<select name="period" id="export_period" class="form-control">
								<option value="all"<?php echo $controller->_post->check('period') ? ($controller->_post->toString('period') == 'all' ? ' selected="selected"' : null) : ' selected="selected"'; ?>>-- <?php echo pjSanitize::html($export_periods['all']); ?> --</option>
								<option value="range"<?php echo $controller->_post->check('period') ? ($controller->_post->toString('period') == 'range' ? ' selected="selected"' : null) : null; ?>><?php echo pjSanitize::html($export_periods['range']); ?></option>
								<option value="next"<?php echo $controller->_post->check('period') ? ($controller->_post->toString('period') == 'next' ? ' selected="selected"' : null) : null; ?>><?php echo pjSanitize::html($export_periods['next']); ?></option>
								<option value="last"<?php echo $controller->_post->check('period') ? ($controller->_post->toString('period') == 'last' ? ' selected="selected"' : null) : null; ?>><?php echo pjSanitize::html($export_periods['last']); ?></option>
							</select>
						</div>
					</div>
					<div class="col-lg-3 col-md-4 col-sm-6" id="next_label" style="display:<?php echo $controller->_post->check('period') ? ($controller->_post->toString('period') == 'next' ? ' block' : ' none') : ' none'; ?>;">
						<div class="form-group">
							<select name="coming_period" id="coming_period" class="form-control">
								<?php
								foreach(__('coming_arr', true) as $k => $v)
								{
									?><option value="<?php echo $k;?>"<?php echo $controller->_post->check('coming_period') ? ($controller->_post->toString('coming_period') == $k ? ' selected="selected"' : null) : null; ?>><?php echo $v;?></option><?php 
								} 
								?>
							</select>
						</div>
					</div>
					<div class="col-lg-3 col-md-4 col-sm-6" id="last_label" style="display:<?php echo $controller->_post->check('period') ? ($controller->_post->toString('period') == 'last' ? ' block' : ' none') : ' none'; ?>;">
						<div class="form-group">
							<select name="made_period" id="made_period" class="form-control">
								<?php
								foreach(__('made_arr', true) as $k => $v)
								{
									?><option value="<?php echo $k;?>"<?php echo $controller->_post->check('made_period') ? ($controller->_post->toString('made_period') == $k ? ' selected="selected"' : null) : null; ?>><?php echo $v;?></option><?php 
								} 
								?>
							</select>
						</div>
					</div>
					
					<div class="col-lg-9 col-md-8 col-sm-12" id="range_label" style="display:<?php echo $controller->_post->check('period') ? ($controller->_post->toString('period') == 'range' ? ' block' : ' none') : ' none'; ?>;">
						<div class="row">
			                <div class="col-md-4 col-sm-6 col-xs-12">
			                    <div class="input-group"> 
									<input type="text" name="date_from" id="date_from" class="form-control datepick" value="<?php echo date($tpl['option_arr']['o_date_format'], time());?>" readonly="readonly" data-msg-required="<?php __('pj_field_required');?>" /> 
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								</div>
			                </div>
			
			                <div class="col-md-4 col-sm-6 col-xs-12">
			                    <div class="input-group"> 
									<input type="text" name="date_to" id="date_to" class="form-control datepick" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime('+7 days'));?>" readonly="readonly" data-msg-required="<?php __('lblFieldRequired');?>" />
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								</div>
			                </div>
			            </div>
					</div>					
				</div>		
				
				<div class="hr-line-dashed"></div>
				
				<div class="clearfix">
					<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
						<span class="ladda-label" id="abSubmitButton"><?php $controller->_post->check('type') ? ($controller->_post->toString('type') == 'file' ? __('btnExport') : __('btnGetFeedURL') ) :  __('btnExport'); ?></span>
						<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
					</button>
					<a type="button" class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCalendars&action=pjActionIndex"><?php __('btnCancel'); ?></a>
				</div><!-- /.clearfix -->	
				
				<?php
				if($controller->_post->check('type') && $controller->_post->toString('type') == 'feed') 
				{
					?>
					<div class="abFeedContainer">
						<div class="hr-line-dashed"></div>
						<p class="alert alert-success alert-with-icon m-t-xs"><i class="fa fa-info-circle"></i> <?php  __('infoReservationFeedDesc');?></p>
						<div class="form-group">
							<textarea id="reservations_feed" name="reservations_feed" class="form-control" rows="3"><?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionExportFeed&amp;format=<?php echo $controller->_post->toString('format'); ?>&amp;calendar_id=<?php echo $controller->getCalendarId();?>&amp;type=<?php echo $controller->_post->toString('period'); ?>&amp;period=<?php echo $controller->_post->toString('period') == 'next' ? $controller->_post->toString('coming_period') : $controller->_post->toString('made_period'); ?>&amp;p=<?php echo isset($tpl['password']) ? $tpl['password'] : null;?></textarea>
						</div>
					</div>
					<?php
				} 
				?>
					
			</form>
			<br/>
			<div class="ibox-content no-margins no-padding no-top-border abFeedContainer">
				<div id="export_grid"></div>
			</div>
        </div>
    </div>
</div>