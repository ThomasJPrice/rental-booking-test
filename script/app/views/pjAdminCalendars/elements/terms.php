<div id="terms" class="tab-pane<?php echo $active_tab == 'terms' ? ' active' : NULL;?>">
    <div class="panel-body form-horizontal">
		<div class="panel-body-inner">
			<?php 
			$info = str_replace("[STAG]", "<a href='#' data-toggle='modal' data-target='#modalCopyTerms' class='btn btn-primary btn-outline'>", __('modalCopyTermsInfo', true));
			$info = str_replace("[ETAG]", "</a>", $info); 
			?>
			<div class="alert alert-success"><?php echo $info;?></div>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionUpdate" method="post" id="frmUpdateTerms" class="form pj-form">
				<input type="hidden" name="property_update" value="1" />
        		<input type="hidden" name="tab" value="terms" />
        		<input type="hidden" name="tab_id" value="6" />
        		
        		<div class="ibox-content ibox-heading">
					<h3><?php __('lblOptionsTermsURL'); ?></h3>
					<small><?php __('lblOptionsTermsURLDesc'); ?></small>
				</div>

				<div class="ibox-content">
					<div class="form-group">
						<?php
						foreach ($tpl['lp_arr'] as $v)
						{
							?>
							<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group ' : NULL;?>pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : 'table'; ?>">
								<span class="input-group-addon"><i class="fa fa-globe"></i></span>
								
								<input type="text" name="i18n[<?php echo $v['id']; ?>][terms_url]" type="text" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['terms_url']); ?>" class="form-control url" />
								<?php if ($tpl['is_flag_ready']) : ?>
								<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
								<?php endif; ?>
							</div>
							<?php
						}
						?>
					</div>
				</div>
	
				<div class="ibox-content ibox-heading">
					<h3><?php __('lblOptionsTermsContent'); ?></h3>
					<small><?php __('lblOptionsTermsContentDesc');?></small>
				</div>
				
				<div class="ibox-content">
					<div class="form-group">
						<?php
						foreach ($tpl['lp_arr'] as $v)
						{
							?>
							<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group ' : NULL;?>pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
								<textarea name="i18n[<?php echo $v['id']; ?>][terms_body]" rows="10" class="form-control mceEditor<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"><?php echo isset($tpl['arr']['i18n'][$v['id']]['terms_body']) && !empty($tpl['arr']['i18n'][$v['id']]['terms_body']) ? stripslashes($tpl['arr']['i18n'][$v['id']]['terms_body']) : ''; ?></textarea>	
								<?php if ($tpl['is_flag_ready']) : ?>
								<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
								<?php endif; ?>
							</div>
							<?php 
						}
						?>
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
	<div class="modal fade" id="modalCopyTerms" tabindex="-1" role="dialog" aria-labelledby="mymodalCopyTermsLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="mymodalCopyTermsLabel"><?php __('modalCopyTermsTitle');?></h4>
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
	            <input type="hidden" name="copy_tab_id" value="6" />
	            <input type="hidden" name="copy_tab" value="terms" />
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