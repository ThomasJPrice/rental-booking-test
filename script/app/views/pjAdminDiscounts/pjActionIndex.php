<?php 
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
?>
<div id="datePickerOptions" style="display:none;" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-format="<?php echo pjUtil::toBootstrapDate($tpl['option_arr']['o_date_format']); ?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-10">
				<h2><?php __('infoDiscountListTitle');?></h2>
			</div>
		</div><!-- /.row -->

		<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoDiscountListDesc');?></p>
	</div><!-- /.col-md-12 -->
</div>
<div class="row wrapper wrapper-content animated fadeInRight">
	<div class="col-lg-12">
		<div class="tabs-container">
			<ul class="nav nav-tabs">
				<li class="active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDiscounts&amp;action=pjActionIndex"><?php __('tabDiscounts'); ?></a></li>
				<?php if (pjAuth::factory('pjAdminVouchers', 'pjActionIndex')->hasAccess()) { ?>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionIndex"><?php __('tabCoupons'); ?></a></li>
				<?php } ?>
			</ul>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="tab11">
					<div class="panel-body">
						<div class="ibox float-e-margins">
							<div class="ibox-content">
								<div class="row">
									<?php if ($tpl['has_create']) { ?>
										<div class="col-lg-4 col-md-3 col-sm-3">
											<div class="form-group">
												<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDiscounts&amp;action=pjActionCreate" class="btn btn-primary m-r-xs btnAddDiscount"><i class="fa fa-plus m-r-xs"></i><?php __('btnAddDiscount');?></a>
											</div>
										</div>
									<?php } ?>
									<div class="col-lg-5 col-md-6 col-sm-6">
										<form action="" method="get" class="form-horizontal frm-filter">
											<div class="input-group">
												<input type="text" name="q" placeholder="<?php __('btnSearch', false, true); ?>" class="form-control">
												<div class="input-group-btn">
													<button class="btn btn-primary" type="submit">
														<i class="fa fa-search"></i>
													</button>
												</div>
											</div>
										</form>
									</div><!-- /.col-lg-6 -->
								</div><!-- /.row -->
								<div id="grid"></div>
							</div>			
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModalDiscount" role="dialog" aria-labelledby="myModalDiscountLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
    </div>
  </div>
</div>

<script type="text/javascript">	
var pjGrid = pjGrid || {};
pjGrid.queryString = "";
<?php
if ($controller->_get->check('calendar_id') && $controller->_get->toInt('calendar_id') > 0)
{
	?>pjGrid.queryString += "&calendar_id=<?php echo $controller->_get->toInt('calendar_id'); ?>";<?php
}
?>
var myLabel = myLabel || {};
myLabel.name = <?php x__encode('lblDiscountName', false, true); ?>;
myLabel.discount = <?php x__encode('lblDiscount', false, true); ?>;
myLabel.type = <?php x__encode('lblType', false, true); ?>;
myLabel.validity_from = <?php x__encode('lblValidityFrom', false, true); ?>;
myLabel.validity_to = <?php x__encode('lblValidityTo', false, true); ?>;
myLabel.options = <?php x__encode('lblDiscountOptions', false, true); ?>;
myLabel.exported = <?php x__encode('lblExport', false, true); ?>;
myLabel.delete_selected = <?php x__encode('plugin_base_delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('plugin_base_delete_confirmation'); ?>;
myLabel.has_update = <?php echo (int) $tpl['has_update']; ?>;
myLabel.has_export = <?php echo (int) $tpl['has_export']; ?>;
myLabel.has_delete = <?php echo (int) $tpl['has_delete']; ?>;
myLabel.has_delete_bulk = <?php echo (int) $tpl['has_delete_bulk']; ?>;
myLabel.localeId = "<?php echo $controller->getLocaleId();?>";
myLabel.loading = <?php x__encode('lblLoading'); ?>;
myLabel.isFlagReady = "<?php echo $tpl['is_flag_ready'] ? 1 : 0;?>";
<?php if ($tpl['is_flag_ready']) : ?>
var pjLocale = pjLocale || {};
pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
<?php endif; ?>
</script>