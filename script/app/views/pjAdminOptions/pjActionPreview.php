<?php $cid = $controller->_get->check('view_cid') && $controller->_get->toInt('view_cid') > 0 ? $controller->_get->toInt('view_cid') : $controller->getCalendarId();?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoPreviewTitle') ?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoPreviewDesc') ?></p>
    </div><!-- /.col-md-12 -->
</div>
<div class="row border-bottom bar-top">
    <div class="col-1of3">
        <a target="_blank" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjBaseLocale&amp;action=pjActionLabels" class="btn btn-secondary"><i class="fa fa-refresh m-r-xs"></i> <?php __('script_change_labels') ?></a>

    </div>

    <div class="col-1of3 text-center">
        <a class="device-view active" href="#" data-device="desktop"><i class="fa fa-desktop"></i></a>

        <div class="device-view-holder">
            <a class="device-view" href="#" data-device="tablet" data-orientation="portrait"><i class="fa fa-tablet"></i></a>
            <a class="device-view device-view-rotate" href="#" data-device="tablet" data-orientation="landscape"><i class="fa fa-tablet"></i></a>
        </div>

        <div class="device-view-holder">
            <a class="device-view" href="#" data-device="phone" data-orientation="portrait"><i class="fa fa-mobile phone"></i></a>
            <a class="device-view device-view-rotate" href="#" data-device="phone" data-orientation="landscape"><i class="fa fa-mobile phone"></i></a>
        </div>
    </div>

    <div class="col-1of3 text-right">
        <a href="preview.php?cid=<?php echo $cid; ?>&view=1" class="btn btn-secondary open-new-window" target="_blank"><i class="fa fa-eye m-r-xs"></i> <?php __('script_preview_your_website') ?></a>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionInstall" class="btn btn-secondary"><i class="fa fa-wrench m-r-xs"></i> <?php __('script_install_your_website') ?></a>
    </div>
</div>
<br/>
<div class="row">
	<div class="col-md-4 col-md-offset-4 text-center">
		<form class="form-horizontal">
			<div class="row form-group">
				<label class="control-label col-sm-4 col-xs-12"><?php __('lblCalendar');?></label>
				<div class="col-sm-8 col-xs-12">
					<select id="preview_calendar_id" name="cid" class="form-control">
						<?php
						foreach ($tpl['calendars'] as $calendar)
						{
							?><option value="<?php echo $calendar['id']; ?>"<?php echo $cid == $calendar['id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($calendar['name']); ?></option><?php
						}
						?>
					</select>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="row">
	<div class="col-md-4 col-md-offset-4 text-center">
		<form class="form-horizontal">
			<div class="row form-group">
				<label class="control-label col-sm-4 col-xs-12"><?php __('lblInstallConfigMonths');?></label>
				<div class="col-sm-8 col-xs-12">
					<select id="preview_months" name="view" class="form-control">
						<option value="1">1</option>
						<option value="3">3</option>
						<option value="6">6</option>
						<option value="12">12</option>
					</select>
				</div>
			</div>
		</form>
	</div>
</div>	
<br/>
<iframe id="iframeEditor" class="iframe-editor" src="preview.php?cid=<?php echo $cid; ?>&view=1"></iframe>

<div id="iframeDevice" class="hidden">
	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-10">
					<h2 id="device_title"></h2>
				</div>
			</div>
			<p class="m-b-none"><i class="fa fa-info-circle"></i> <span id="device_info"></span></p>
		</div>
	</div>

	<div class="row wrapper wrapper-content">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<br>
					<div id="iframeHolder"></div>
					<br>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="hidden" id="phone_portrait"><?php __('plugin_base_editor_phone_portrait'); ?></div>
<div class="hidden" id="phone_landscape"><?php __('plugin_base_editor_phone_landscape'); ?></div>
<div class="hidden" id="tablet_portrait"><?php __('plugin_base_editor_tablet_portrait'); ?></div>
<div class="hidden" id="tablet_landscape"><?php __('plugin_base_editor_tablet_landscape'); ?></div>
<div class="hidden" id="phone_portrait_info"><?php __('plugin_base_editor_phone_portrait_info'); ?></div>
<div class="hidden" id="phone_landscape_info"><?php __('plugin_base_editor_phone_landscape_info'); ?></div>
<div class="hidden" id="tablet_portrait_info"><?php __('plugin_base_editor_tablet_portrait_info'); ?></div>
<div class="hidden" id="tablet_landscape_info"><?php __('plugin_base_editor_tablet_landscape_info'); ?></div>