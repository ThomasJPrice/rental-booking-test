<div id="feeds" class="tab-pane<?php echo $active_tab == 'feeds' ? ' active' : NULL;?>">
    <div class="panel-body">
        <div class="panel-body-inner">
            <div class="row">
                <div class="col-lg-7">
                	<div class="ibox float-e-margins">
                    	<div class="ibox-content no-margins no-padding no-top-border">
                    		<div id="feed_grid"></div>
                    	</div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="ibox float-e-margins settings-box">
                        <div class="ibox-content ibox-heading">
                            <h3 class="m-n"><?php __('infoAddICalFeed')?></h3>
                        </div>
                        <div class="ibox-content" id="pjFeedWrapper" >
                        	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionUpdate" method="post" id="frmFeeds" class="form pj-form">
	                        	<input type="hidden" name="calendar_id" value="<?php echo $tpl['arr']['id'];?>">
	                            <input class="form-control" type="hidden" id="provider_id" name="provider_id" value="1">
	                            <input class="form-control" type="hidden" name="feed_id" value="">
	                            
	                            <div class="form-group">
	                                <label class="control-label" for="url"><?php __('lblFeedURL');?></label>
	                                <div class="input-group">
	                                    <input class="form-control form-control-lg url" type="text" name="url" id="feed_url" value="" data-msg-required="<?php __('pj_field_required');?>" data-msg-url="<?php __('pj_field_url');?>" data-msg-remote="<?php __('pj_feed_error_msg')?>">
	                                    <span class="input-group-addon">
	                                    <i class="fa fa-globe"></i>
	                                    </span>
	                                </div>
	                            </div>
	                            <div class="hr-line-dashed"></div>
	                            <div class="clearfix">
	                                <button id="pjRpbcImportFeed" type="button" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
	                                    <span class="ladda-label"><?php __('btnImport'); ?></span>
	                                    <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
	                                </button>
	                                <a type="button" class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCalendars&action=pjActionIndex"><?php __('btnCancel'); ?></a>
	                            </div>
	                   		</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>