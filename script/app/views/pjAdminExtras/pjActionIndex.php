<?php 
$filter = __('filter', true);
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-10">
				<h2><?php __('infoExtrasTitle');?></h2>
			</div>
		</div><!-- /.row -->

		<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoExtrasDesc');?></p>
	</div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-content">
				<div class="row">
					<?php if ($tpl['has_create']) { ?>
						<div class="col-lg-4 col-md-3 col-sm-3">
							<div class="form-group">
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionCreate" class="btn btn-primary m-r-xs btnAddExtra"><i class="fa fa-plus m-r-xs"></i><?php __('btnAddExtra');?></a>
							</div>
						</div>
					<?php } ?>
					<div class="col-lg-4 col-md-5 col-sm-4">
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
				
					<div class="col-lg-4 col-md-4 col-sm-5 text-right">
						<div class="m-b-md m-t-xs">
							<button class="btn btn-primary btn-all active" type="button"><span class="p-w-xs"><?php __('lblAll');?></span></button>
							<button class="btn btn-default btn-filter" type="button" data-column="status" data-value="T"><i class="fa fa-check"></i> <?php echo $filter['active']; ?></button>
							<button class="btn btn-default btn-filter" type="button" data-column="status" data-value="F"><i class="fa fa-times"></i> <?php echo $filter['inactive']; ?></button>
						</div>
					</div><!-- /.col-lg-6 -->
				</div><!-- /.row -->
				<div id="grid"></div>
			</div>			
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModalExtra" tabindex="-1" role="dialog" aria-labelledby="myModalExtraLabel">
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
myLabel.name = <?php x__encode('lblExtraName'); ?>;
myLabel.price = <?php x__encode('lblPrice'); ?>;
myLabel.max_count = <?php x__encode('lblMaximumCount'); ?>;
myLabel.status = <?php x__encode('lblStatus'); ?>;
myLabel.active = <?php x__encode('filter_ARRAY_active'); ?>;
myLabel.inactive = <?php x__encode('filter_ARRAY_inactive'); ?>;
myLabel.delete_selected = <?php x__encode('plugin_base_delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('plugin_base_delete_confirmation'); ?>;
myLabel.has_update = <?php echo (int) $tpl['has_update']; ?>;
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