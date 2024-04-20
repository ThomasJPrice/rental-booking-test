<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('lblInstallInfoTitle') ?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('lblInstallInfoDesc') ?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
    	<div class="ibox float-e-margins">
			<div class="ibox-content">
				<form action="<?php echo PJ_INSTALL_URL; ?>preview.php" method="get" class="form-horizontal" target="_blank">					
					<div class="m-b-lg">
						<h2 class="no-margins"><?php __('lblInstallConfig');?></h2>
					</div>
	
					<div class="row">
						<div class="col-lg-8">
							<div class="form-group">
								<label class="col-lg-3 col-md-4 control-label"><?php __('lblInstallConfigCalendar');?></label>
	
								<div class="col-lg-5 col-md-8">
									<select id="install_calendar" name="cid" class="form-control">
										<?php if (!$controller->isOwner()) { ?>
											<option value="all"><?php __('lblInstallConfigAllCalendars'); ?></option>
										<?php } ?>
										<?php
										foreach ($tpl['calendars'] as $calendar)
										{
											?><option value="<?php echo $calendar['id']; ?>"<?php echo !isset($_GET['calendar_id']) || $_GET['calendar_id'] != $calendar['id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html($calendar['name']); ?></option><?php
										}
										?>
									</select>
								</div>
							</div>
							
							<div class="form-group" style="display: <?php echo count($tpl['locale_arr']) > 1 ? 'block' : 'none';?>">
								<label class="col-lg-3 col-md-4 control-label"><?php __('lblInstallConfigLocale');?></label>
	
								<div class="col-lg-5 col-md-8">
									<select id="install_locale" name="locale" class="form-control">
										<option value=""><?php __('lblInstallConfigLang'); ?></option>
										<?php
										foreach ($tpl['locale_arr'] as $locale)
										{
											?><option value="<?php echo $locale['id']; ?>"><?php echo pjSanitize::html($locale['title']); ?></option><?php
										}
										?>
									</select>
								</div>
							</div>	
							
							<div class="form-group" style="display: none">
								<label class="col-lg-3 col-md-4 control-label"><?php __('lblInstallConfigMonths');?></label>
	
								<div class="col-lg-5 col-md-8">
									<select id="install_months" name="view" disabled="disabled" class="form-control">
										<option value="1">1</option>
										<option value="3">3</option>
										<option value="6">6</option>
										<option value="12">12</option>
									</select>
								</div>
							</div>	
								
							<div class="form-group">
								<label class="col-lg-3 col-md-4 control-label">&nbsp;</label>
	
								<div class="col-lg-5 col-md-8">
									<input type="submit" value="<?php __('btnPreview', false, true); ?>" class="btn btn-primary btn-lg" />
								</div>
							</div>
						</div>
					</div>
					
					<div class="hr-line-dashed"></div>
		
					<div class="m-b-lg">
						<h2 class="no-margins"><?php __('infoInstallCodeTitle');?></h2>
					</div>
		
					<p class="alert alert-info alert-with-icon m-t-xs"><i class="fa fa-info-circle"></i> <?php __('infoInstallCodeBody') ?></p>
		
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<div class="col-lg-12 col-md-12">
									<textarea class="form-control textarea_install" rows="4">&lt;link href="<?php echo PJ_INSTALL_URL.PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadAvailabilityCss" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadAvailability"&gt;&lt;/script&gt;</textarea>
								</div>
							</div>
						</div>
					</div>
				</form>
				<div style="display: none" id="boxStandard">&lt;link href="<?php echo PJ_INSTALL_URL.PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss&cid={CID}" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoad&cid={CID}&view={VIEW}{LOCALE}"&gt;&lt;/script&gt;
</div>

<div style="display: none" id="boxAvailability">&lt;link href="<?php echo PJ_INSTALL_URL.PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link type="text/css" rel="stylesheet" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadAvailabilityCss" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadAvailability{LOCALE}"&gt;&lt;/script&gt;</div>
			</div>
		</div>
    </div><!-- /.col-lg-12 -->
</div>