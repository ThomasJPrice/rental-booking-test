<div id="notifications" class="tab-pane<?php echo $active_tab == 'notifications' ? ' active' : NULL;?>">
    <div class="panel-body">
        <div class="panel-body-inner">
        	<?php 
			$info = str_replace("[STAG]", "<a href='#' data-toggle='modal' data-target='#modalCopyEmailNotifications' class='btn btn-primary btn-outline'>", __('modalCopyEmailNotificationsInfo', true));
			$info = str_replace("[ETAG]", "</a>", $info); 
			?>
			<div class="alert alert-success"><?php echo $info;?></div>
        	<div id="boxNotificationsWrapper">
            	<div class="ibox float-e-margins settings-box">
                    <div class="ibox-content ibox-heading">
            			<h3><?php __('notifications_main_title'); ?></h3>
            			<small><?php __('notifications_main_subtitle'); ?></small>
            		</div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-3 col-sm-5">
                                <div class="m-b-sm">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h3><?php __('notifications_recipient'); ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
            						<div class="radio radio-primary">
            							<input class="i-checks" type="radio" id="recipient_client" name="recipient" value="client"<?php echo !isset($tpl['query']['recipient']) || $tpl['query']['recipient'] == 'client' ? ' checked' : NULL; ?>>
            							<label for="recipient_client"><?php __('recipients_ARRAY_client'); ?></label>
            						</div>
            					</div>
                                <div class="form-group">
            						<div class="radio radio-primary">
            							<input class="i-checks" type="radio" id="recipient_owner" name="recipient" value="owner"<?php echo isset($tpl['query']['recipient']) && $tpl['query']['recipient'] == 'owner' ? ' checked' : NULL; ?>>
            							<label for="recipient_owner"><?php __('recipients_ARRAY_owner'); ?></label>
            						</div>
            					</div>
            
                            </div>
                            <div class="col-lg-9 col-sm-7 ibox-content-notification" id="boxNotificationsMetaData">
                                
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-9 ibox-content-notification" id="boxNotificationsContent">
                        
                    </div>
                    <div class="col-lg-3">
                        <div class="ibox float-e-margins settings-box">
                            <div class="ibox-content ibox-heading">
            					<h3><?php __('notifications_tokens'); ?></h3>
            	
            					<small><?php __('notifications_tokens_note'); ?></small>
            				</div>
                            <div class="ibox-content">
            					<div class="notifyTokens reservationTokens">
            						<?php __('lblAvailableTokens');?>
            					</div>
            				</div>
                        </div>
                    </div>
                </div><!-- /.row -->
            </div>
		</div>
    </div>
    <!-- Modal -->
	<div class="modal fade" id="modalCopyEmailNotifications" tabindex="-1" role="dialog" aria-labelledby="myCopyEmailNotificationsLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myCopyEmailNotificationsLabel"><?php __('modalCopyEmailNotificationsTitle');?></h4>
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
	            <input type="hidden" name="copy_tab_id" value="5" />
	            <input type="hidden" name="copy_tab" value="notifications" />
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