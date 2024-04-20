<div id="booking_form" class="tab-pane<?php echo $active_tab == 'booking_form' ? ' active' : NULL;?>">
    <div class="panel-body">
        <div class="panel-body-inner">
        	
        	<?php 
			$info = str_replace("[STAG]", "<a href='#' data-toggle='modal' data-target='#modalCopyBookingForm' class='btn btn-primary btn-outline'>", __('modalCopyBookingFormInfo', true));
			$info = str_replace("[ETAG]", "</a>", $info); 
			?>
			<div class="alert alert-success"><?php echo $info;?></div>
        	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionUpdate" method="post" id="frmUpdateBookingForm" class="form pj-form">
				<input type="hidden" name="property_update" value="1" />
        		<input type="hidden" name="tab" value=booking_form />
        		<input type="hidden" name="tab_id" value="4" />
                <div class="row">
					<?php
					foreach ($tpl['group_1_arr'] as $option)
					{
						?>
						<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
							<div class="form-group">
								<label class="control-label"><?php __('opt_' . $option['key']); ?></label>
								<?php
								include dirname(__FILE__) . '/enum.php';
								?>
							</div><!-- /.form-group -->
						</div>
						<?php
					}
					?>
				</div>
				<div class="hr-line-dashed"></div>    
				<div class="row">
					<?php
					foreach ($tpl['group_2_arr'] as $option)
					{
						?>
						<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
							<div class="form-group">
								<label class="control-label"><?php __('opt_' . $option['key']); ?></label>
								<?php
								include dirname(__FILE__) . '/enum.php';
								?>
							</div><!-- /.form-group -->
						</div>
						<?php
					}
					?>
				</div>
				<div class="hr-line-dashed"></div>     
				<div class="row">
					<?php
					foreach ($tpl['group_3_arr'] as $option)
					{
						?>
						<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
							<div class="form-group">
								<label class="control-label"><?php __('opt_' . $option['key']); ?></label>
								<?php
								include dirname(__FILE__) . '/enum.php';
								?>
							</div><!-- /.form-group -->
						</div>
						<?php
					}
					?>
				</div>       
                <div class="hr-line-dashed"> </div> 
                
                <div class="clearfix">
                    <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                        <span class="ladda-label"><?php __('btnSave'); ?></span>
                        <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                    </button>
                    <a type="button" class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCalendars&action=pjActionIndex"><?php __('btnCancel'); ?></a>
                </div>
                <!-- /.clearfix -->
			</form>
        </div><!-- /.panel-body-inner -->
    </div><!-- /.panel-body -->
    
    <!-- Modal -->
	<div class="modal fade" id="modalCopyBookingForm" tabindex="-1" role="dialog" aria-labelledby="myCopyBookingFormLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myCopyBookingFormLabel"><?php __('modalCopyBookingFormTitle');?></h4>
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
	            <input type="hidden" name="copy_tab_id" value="4" />
	            <input type="hidden" name="copy_tab" value="booking_form" />
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