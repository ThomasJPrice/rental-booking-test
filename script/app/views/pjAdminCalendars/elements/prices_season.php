<div role="tabpanel" class="tab-pane tab-price-panel" id="tab-content-{INDEX}" data-idx="{INDEX}">
	<div class="panel-body">
		<div class="row tab-price-item">
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label"><?php __('prices_season_title');?></label>
					<input type="text" class="form-control required" id="tab_{INDEX}" name="tabs[{INDEX}]" value="{TAB_TITLE}" />
				</div>
			</div><!-- /.col-sm-4 -->    

			<div class="col-sm-3">
				<div class="form-group">
					<label class="control-label"><?php __('prices_date_range_from');?></label>
	
					<div class="input-group"> 
						<input type="text" name="{INDEX}_date_from[{RAND}]" id="date_from_{INDEX}_{RAND}" value="<?php echo date($tpl['option_arr']['o_date_format'], time());?>" class="form-control datepick required" readonly="readonly" /> 
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
			</div><!-- /.col-sm-4 -->    

			<div class="col-sm-3">
				<div class="form-group">
					<label class="control-label"><?php __('prices_date_range_to');?></label>
	
					<div class="input-group"> 
						<input type="text" name="{INDEX}_date_to[{RAND}]" id="date_to_{INDEX}_{RAND}" value="<?php echo date($tpl['option_arr']['o_date_format'], time());?>" class="form-control datepick required" readonly="readonly" /> 
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
			</div><!-- /.col-sm-4 -->
			<div class="col-sm-2">
				<div class="form-group">
					<label class="control-label" style="display:block;">&nbsp;</label>
	
					<a href="#" class="btn btn-danger btn-outline btn-sm btn-delete-season" data-index="{INDEX}"><i class="fa fa-trash"></i> <?php __('btnDelete');?></a>
				</div>
			</div><!-- /.col-sm-2 -->       
		</div><!-- /.row -->

		<div class="hr-line-dashed"></div>

		<div class="ibox-content ibox-heading">
			<h3><?php __('prices_add_default_price_title');?></h3>
			<small><?php __('prices_add_default_price_desc');?></small>
		</div>
		
		<div id="season_weekday_price_{INDEX}">
    		<div class="table-responsive table-responsive-secondary">
    			<table class="table table-striped table-hover">
    				<thead>
    					<tr>
    						<?php
    						foreach ($price_days as $k => $v)
    						{
    							?><th><?php echo $v; ?></th><?php
    						}
    						?>
    					</tr>
    				</thead>
    		
    				<tbody>
    					<tr class="tab-price-item">
    						<?php
    						$i = 1;
    						foreach ($price_days as $k => $v)
    						{
    							if ($i > 6)
    							{
    								$i = 0;
    							}
    							?><td>
    								<?php if ($i == 1) { ?>
    								<input type="hidden" name="{INDEX}_adults[{RAND}]" value="0" />
    								<input type="hidden" name="{INDEX}_children[{RAND}]" value="0" />
    								<?php } ?>
    								<div class="form-group">
    									<div class="input-group">
    										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></span>	
    										<input type="text" class="form-control price-form-field required number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" name="{INDEX}_day_<?php echo $i; ?>[{RAND}]" data-msg-number="<?php __('prices_invalid_price', false, true);?>">
    									</div>
    								</div>
    							</td><?php
    							$i++;
    						}
    						?>
    					</tr>
    				</tbody>
    			</table>
    		</div>
    	</div>

		<div class="hr-line-dashed"></div>
				
		<div id="guests_weekday_price_{INDEX}_{RAND}">		
    		<div class="ibox-content ibox-heading">
				<h3><?php __('prices_add_price_per_guests_title');?></h3>
				<small><?php __('prices_add_price_per_guests_desc');?></small>
			</div>
    		<div class="table-responsive table-responsive-secondary">
    			<table class="table table-striped table-hover pj-table pj-tbl-adults-children-price-{RAND}" data-idx="{RAND}">
    				<thead>
    					<tr>
    						 <th width="90px"><?php __('prices_adults');?></th>
                             <th width="90px"><?php __('prices_children');?></th>
    						<?php
    						foreach ($price_days as $k => $v)
    						{
    							?><th><?php echo $v; ?></th><?php
    						}
    						?>
    						<th></th>
    					</tr>
    				</thead>
    		
    				<tbody>
    					
    				</tbody>
    			</table>
    		</div>
    		
    		<div class="m-b-md">
    			<a href="javascript:void(0);" class="btn btn-primary btn-outline lnkAddPrice" rel="{INDEX}" data-idx="{RAND}"><i class="fa fa-plus"></i> <?php __('prices_add_price_adults_children');?></a>
    		</div>
		</div>
		<div class="hr-line-dashed"></div>

		<div class="clearfix">
			<button type="submit" class="ladda-button btn btn-primary btn-lg m-r-sm btn-phpjabbers-loader" data-style="zoom-in">
				<span class="ladda-label"><?php __('btnSave', false, true); ?></span>
				<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
			</button>	
			<a class="btn btn-primary btn-outline btn-lg btnAddSeasonPrice" data-toggle="modal" data-target="#modalAddSeasonPrice" href="javascript:void(0);"><i class="fa fa-plus"></i> <?php __('prices_add_seasonal_price');?></a>
			<a type="button" class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCalendars&action=pjActionIndex"><?php __('btnCancel'); ?></a> 
		</div><!-- /.clearfix -->
		<br/>
		<div class="alert alert-success bxPriceStatusStart" style="display: none"><?php __('prices_price_status_start'); ?></div>
		<div class="alert alert-success bxPriceStatusEnd" style="display: none"><?php __('prices_price_status_end'); ?></div>
		<div class="alert alert-danger bxPriceStatusFail" style="display: none"><?php __('prices_price_status_fail'); ?></div>
		<div class="alert alert-danger bxPriceStatusDuplicate" style="display: none"><?php __('prices_price_status_duplicate'); ?></div>
	</div>
</div>