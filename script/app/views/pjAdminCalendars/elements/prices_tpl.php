<div class="panel-body">
	<?php
	if ($prices_include == 'default') 
	{
	    ?>
	    <div class="row tab-price-item">
	    	<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label"><?php __('prices_season_title');?></label>
					<input type="text" class="form-control required" name="tabs[<?php echo $season_arr[0]['tab_id']; ?>]" value="<?php echo htmlspecialchars(stripslashes($season)); ?>"  data-msg-required="<?php __('pj_field_required');?>"/>
				</div>
			</div><!-- /.col-sm-4 -->   
	    </div>
	    <div class="hr-line-dashed"></div>
		<div class="ibox-content ibox-heading">
			<h3><?php __('prices_add_default_price_title');?></h3>
			<small><?php __('prices_add_default_price_desc');?></small>
		</div> 
		<div id="default_weekday_price_1">
    		<div class="tab-price-item">
    			<input type="hidden" name="1_adults[<?php echo $season_arr[0]['id']; ?>]" value="0" />
    			<input type="hidden" name="1_children[<?php echo $season_arr[0]['id']; ?>]" value="0" />
    			<input type="hidden" name="1_date_from[<?php echo $season_arr[0]['id']; ?>]" value="<?php echo pjDateTime::formatDate('0000-00-00', 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
    			<input type="hidden" name="1_date_to[<?php echo $season_arr[0]['id']; ?>]" value="<?php echo pjDateTime::formatDate('0000-00-00', 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
    		</div>
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
    								<div class="form-group">
    									<div class="input-group">
    										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false); ?></span>	
    										<input type="text" class="form-control price-form-field required number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" value="<?php echo $season_arr[0][substr($k, 0, 3)];?>" name="1_day_<?php echo $i; ?>[<?php echo $season_arr[0]['id']; ?>]" data-msg-required="<?php __('pj_field_required');?>"  data-msg-number="<?php __('prices_invalid_price', false, true);?>">
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
	<?php } else { ?>
		<div class="row tab-price-item">
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label"><?php __('prices_season_title');?></label>
					<input type="text" class="form-control required" id="tab_<?php echo $season_arr[0]['tab_id'];?>" name="tabs[<?php echo $season_arr[0]['tab_id'];?>]" value="<?php echo stripslashes($season_arr[0]['season']);?>" data-msg-required="<?php __('pj_field_required');?>" />
				</div>
			</div><!-- /.col-sm-4 -->    

			<div class="col-sm-3">
				<div class="form-group">
					<label class="control-label"><?php __('prices_date_range_from');?></label>
	
					<div class="input-group"> 
						<input type="text" name="<?php echo $season_arr[0]['tab_id'];?>_date_from[<?php echo $season_arr[0]['id']; ?>]" id="date_from_<?php echo $season_arr[0]['tab_id'];?>_<?php echo $season_arr[0]['id']; ?>" value="<?php echo !empty($season_arr[0]['date_from']) && $season_arr[0]['date_from'] != '0000-00-00' ? pjDateTime::formatDate($season_arr[0]['date_from'], 'Y-m-d', $tpl['option_arr']['o_date_format']) : date($tpl['option_arr']['o_date_format']); ?>" class="form-control datepick required" readonly="readonly" data-msg-required="<?php __('pj_field_required');?>" /> 
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
			</div><!-- /.col-sm-4 -->    

			<div class="col-sm-3">
				<div class="form-group">
					<label class="control-label"><?php __('prices_date_range_to');?></label>
	
					<div class="input-group"> 
						<input type="text" name="<?php echo $season_arr[0]['tab_id'];?>_date_to[<?php echo $season_arr[0]['id']; ?>]" id="date_to_<?php echo $season_arr[0]['tab_id'];?>_<?php echo $season_arr[0]['id']; ?>" value="<?php echo !empty($season_arr[0]['date_to']) && $season_arr[0]['date_to'] != '0000-00-00' ? pjDateTime::formatDate($season_arr[0]['date_to'], 'Y-m-d', $tpl['option_arr']['o_date_format']) : date($tpl['option_arr']['o_date_format']); ?>" class="form-control datepick required" readonly="readonly" data-msg-required="<?php __('pj_field_required');?>" /> 
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
			</div><!-- /.col-sm-4 -->    
			<div class="col-sm-2">
				<div class="form-group">
					<label class="control-label" style="display:block;">&nbsp;</label>
	
					<a href="#" class="btn btn-danger btn-outline btn-sm btn-delete-season" data-index="<?php echo $season_arr[0]['tab_id'];?>"><i class="fa fa-trash"></i> <?php __('btnDelete');?></a>
				</div>
			</div><!-- /.col-sm-2 -->    
		</div><!-- /.row -->

		<div class="hr-line-dashed"></div>

		<div class="ibox-content ibox-heading">
			<h3><?php __('prices_add_default_price_title');?></h3>
			<small><?php __('prices_add_default_price_desc');?></small>
		</div> 
		
		<div id="season_weekday_price_<?php echo $season_arr[0]['tab_id'];?>">
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
    									<input type="hidden" name="<?php echo $season_arr[0]['tab_id'];?>_adults[<?php echo $season_arr[0]['id']; ?>]" value="0" />
    									<input type="hidden" name="<?php echo $season_arr[0]['tab_id'];?>_children[<?php echo $season_arr[0]['id']; ?>]" value="0" />
    								<?php } ?>
    								<div class="form-group">
    									<div class="input-group">
    										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></span>	
    										<input type="text" class="form-control price-form-field required number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" value="<?php echo $season_arr[0][substr($k, 0, 3)];?>" name="<?php echo $season_arr[0]['tab_id'];?>_day_<?php echo $i; ?>[<?php echo $season_arr[0]['id']; ?>]" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('prices_invalid_price', false, true);?>">
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
	<?php } ?>
	
	<div id="guests_weekday_price_<?php echo $season_arr[0]['tab_id'];?>_<?php echo $season_arr[0]['id'];?>">
		<div class="ibox-content ibox-heading">
			<h3><?php __('prices_add_price_per_guests_title');?></h3>
			<small><?php __('prices_add_price_per_guests_desc');?></small>
		</div>
    	<div class="table-responsive table-responsive-secondary">
    		<table class="table table-striped table-hover pj-table pj-tbl-adults-children-price-<?php echo $season_arr[0]['id']; ?>" data-idx="<?php echo $season_arr[0]['id']; ?>">
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
    					<th>&nbsp;</th>
    					<th>&nbsp;</th>
    				</tr>
    			</thead>	                            
    			<tbody>
    				<?php foreach ($season_arr as $key => $price) { ?>
    					<?php if ($key > 0) { ?>
    						<tr class="tab-price-item">
    							<td>
    								<select class="form-control" name="<?php echo $price['tab_id'];?>_adults[<?php echo $price['id'];?>~:~<?php echo $season_arr[0]['id'];?>]">
    									<?php
    									foreach (range(1, $tpl['option_arr']['o_bf_adults_max']) as $i)
    									{
    										?><option value="<?php echo $i; ?>" <?php echo $price['adults'] == $i ? 'selected="selected"' : null;?>><?php echo $i; ?></option><?php
    									}
    									?>
    								</select>
    							</td>
    						
    							<td>
    								<select class="form-control" name="<?php echo $price['tab_id'];?>_children[<?php echo $price['id'];?>~:~<?php echo $season_arr[0]['id'];?>]">
    									<?php
    									foreach (range(0, $tpl['option_arr']['o_bf_children_max']) as $i)
    									{
    										?><option value="<?php echo $i; ?>" <?php echo $price['children'] == $i ? 'selected="selected"' : null;?>><?php echo $i; ?></option><?php
    									}
    									?>
    								</select>
    							</td>
    						
    							<?php
    							$i = 1;
    							foreach ($price_days as $k => $v)
    							{
    								if ($i > 6)
    								{
    									$i = 0;
    								}
    								?><td>
    									<div class="form-group">
    										<div class="input-group">
    											<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></span>	
    											<input type="text" class="form-control price-form-field required number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" value="<?php echo $price[substr($k, 0, 3)]; ?>" name="<?php echo $price['tab_id'];?>_day_<?php echo $i; ?>[<?php echo $price['id']; ?>~:~<?php echo $season_arr[0]['id'];?>]" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('prices_invalid_price', false, true);?>">
    										</div>
    									</div>
    								</td><?php
    								$i++;
    							}
    							?>
    							<td>
    								<div class="text-right">
    									<a href="javascript:void(0);" class="btn btn-danger btn-outline btn-sm btn-delete lnkRemovePriceRow"><i class="fa fa-trash"></i></a>
    								</div>
    							</td>
    						</tr>
    					<?php } ?>
    				<?php } ?>
    			</tbody>
    		</table>
    	</div>
    	<div class="m-b-md">
    		<a href="javascript:void(0);" class="btn btn-primary btn-outline lnkAddPrice" rel="<?php echo $season_arr[0]['tab_id'];?>" data-idx="<?php echo $season_arr[0]['id'];?>"><i class="fa fa-plus"></i> <?php __('prices_add_price_adults_children');?></a>
    	</div>
	</div>
	
	<div class="hr-line-dashed"></div>
	
	<div class="clearfix">
		<button type="submit" class="ladda-button btn btn-primary btn-lg m-r-sm btn-phpjabbers-loader" data-style="zoom-in">
			<span class="ladda-label"><?php __('btnSave', false, true); ?></span>
			<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
		</button>	
		<a class="btn btn-primary btn-outline btn-lg btnAddSeasonPrice" data-toggle="modal" data-target="#modalAddSeasonPrice" href="javascript:void(0);" type="submit"><i class="fa fa-plus"></i> <?php __('prices_add_seasonal_price');?></a>
		<a type="button" class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCalendars&action=pjActionIndex"><?php __('btnCancel'); ?></a> 
	</div><!-- /.clearfix -->
	<br/>
	<div class="alert alert-success bxPriceStatusStart" style="display: none"><?php __('prices_price_status_start'); ?></div>
	<div class="alert alert-success bxPriceStatusEnd" style="display: none"><?php __('prices_price_status_end'); ?></div>
	<div class="alert alert-danger bxPriceStatusFail" style="display: none"><?php __('prices_price_status_fail'); ?></div>
	<div class="alert alert-danger bxPriceStatusDuplicate" style="display: none"><?php __('prices_price_status_duplicate'); ?></div>
	
</div>