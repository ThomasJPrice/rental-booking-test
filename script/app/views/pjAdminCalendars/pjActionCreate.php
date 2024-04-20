<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-6">
                <h2><?php echo @$titles['ACR11'];?></h2>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 btn-group-languages">
                <?php if ($tpl['is_flag_ready']) : ?>
				<div class="multilang"></div>
				<?php endif; ?>
        	</div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php echo @$bodies['ACR11'];?></p>
    </div><!-- /.col-md-12 -->
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionCreate" method="post" id="frmCreateCalendar">
                        <input type="hidden" name="calendar_create" value="1" />
                        
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label class="control-label" for="listing_refid"><?php __('lblRefId'); ?></label>
                                    <input type="text" name="uuid" id="uuid" class="form-control required" value="<?php echo pjUtil::uuid(); ?>" maxlength="12"  data-msg-required="<?php __('pj_field_required');?>" data-msg-remote="<?php __('rpbc_duplicate_ref_id');?>"/>
                                </div>
                            </div>
                            <!-- /.col-md-3 -->
                            <div class="col-lg-4 col-md-4 col-sm-4">
	                            <div class="form-group">
	                                <label class="control-label"><?php __('lblName');?></label>
									<?php
									foreach ($tpl['lp_arr'] as $v)
									{
										?>
										<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group ' : NULL;?>pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
											<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][name]" data-msg-required="<?php __('pj_field_required', false, true);?>">	
											<?php if ($tpl['is_flag_ready']) : ?>
											<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
											<?php endif; ?>
										</div>
										<?php 
									}
									?>
	                            </div>
				           	</div>
                            <!-- /.col-md-3 -->
                            <div class="col-lg-4 col-md-4 col-sm-4">
								<div class="form-group">
									<label class="control-label"><?php __('lblUser'); ?></label>
									<select name="user_id" id="user_id" class="form-control select-item required" data-msg-required="<?php __('pj_field_required');?>" data-placeholder="-- <?php __('lblChoose'); ?> --">
										<option value="">-- <?php __('lblChoose'); ?> --</option>
										<?php
										foreach ($tpl['user_arr'] as $v)
										{
											?><option value="<?php echo $v['id']; ?>"><?php echo pjSanitize::html($v['name']); ?></option><?php
										}
										?>
									</select>
								</div>
								<!-- /.form-group -->
							</div>							
                        </div>
                        <!-- /.row -->
                        <div class="hr-line-dashed"></div>
                        
                        <div class="clearfix">
                            <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                                <span class="ladda-label"><?php __('btnSave'); ?></span>
                                <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                            </button>
                            <a type="button" class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCalendars&action=pjActionIndex"><?php __('btnCancel'); ?></a>
                        </div>
                        <!-- /.clearfix -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">	
var myLabel = myLabel || {};
myLabel.isFlagReady = "<?php echo $tpl['is_flag_ready'] ? 1 : 0;?>";
<?php if ($tpl['is_flag_ready']) : ?>
var pjLocale = pjLocale || {};
pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
<?php endif; ?>
</script>