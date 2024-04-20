<div id="prices" class="tab-pane<?php echo $active_tab == 'prices' ? ' active' : NULL;?>">
    <div class="panel-body">
		<div class="panel-body-inner">
			<?php 
			$info = str_replace("[STAG]", "<a href='#' data-toggle='modal' data-target='#modalCopyPrices' class='btn btn-primary btn-outline'>", __('copyPricesInfo', true));
			$info = str_replace("[ETAG]", "</a>", $info); 
			?>
			<div class="alert alert-success"><?php echo $info;?></div>
			<?php if (isset($tpl['price_overlap_arr']) && !empty($tpl['price_overlap_arr'])) { ?>
				<div class="alert alert-warning">
    				<i class="fa fa-warning m-r-xs"></i>
    				<strong><?php echo @$titles['PPR09']; ?></strong>
    				<?php echo @$bodies['PPR09']; ?>
    			</div>
			<?php } ?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionUpdate" method="post" id="frmCreatePrice" class="form pj-form">
				<input type="hidden" name="property_update" value="1" />
        		<input type="hidden" name="tab" value="prices" />
        		<input type="hidden" name="tab_id" value="11" />
	
				<div class="tabs-container tabs-reservations tabs-prices m-b-lg">
					<ul class="nav nav-tabs" role="tablist">
						<?php
						$count = count($tpl['price_arr']);
						if ($count > 0)
						{
							$idx = array();
							foreach ($tpl['price_arr'] as $season => $season_arr)
							{
								$idx[] = $season_arr[0]['tab_id'];
							}
							$idx = array_unique($idx);
							sort($idx, SORT_NUMERIC);
						
							$br = 0;
							foreach ($tpl['price_arr'] as $season => $season_arr)
							{
								$index = $br > 0 ? $idx[$br] : 1;
								?>
								<li role="presentation" class="<?php echo $index == 1 ? 'active' : null;?>">
									<a href="#tab-content-<?php echo $index; ?>" id="tab-nav-<?php echo $index;?>" aria-controls="tabs-<?php echo $index; ?>" role="tab" data-toggle="tab"><?php echo pjSanitize::html($season); ?>
									<?php
									if ($br > 0)
									{
										?><span aria-hidden="true" class="lnkRemoveTabPrice" data-idx="<?php echo $index;?>" style="display:none;">&times;</span><?php
									}
									?>
									</a>
								</li>
								<?php
								$br++;
							}
						} else {
							?><li role="presentation" class="active"><a href="#tab-content-1" id="tab-nav-1" aria-controls="tabs-1" role="tab" data-toggle="tab"><?php __('prices_price_default'); ?></a></li><?php
						}
						?>
					</ul>
					<?php
					$price_days = array(
						'monday' => $days[1],
						'tuesday' => $days[2],
						'wednesday' => $days[3],
						'thursday' => $days[4],
						'friday' => $days[5],
						'saturday' => $days[6],
						'sunday' => $days[0]
					);
					?>
					<div class="tab-content">
						<?php 
						if ($count > 0)
						{
							$br = 0;
							foreach ($tpl['price_arr'] as $season => $season_arr)
							{
								$index = $br > 0 ? $idx[$br] : 1;
								?>
								<div role="tabpanel" class="tab-pane tab-price-panel <?php echo $index == 1 ? 'active' : null;?>" id="tab-content-<?php echo $index; ?>" data-idx="<?php echo $index; ?>">
									<?php
									$prices_include = $index == 1 ? 'default' : 'season';
									include dirname(__FILE__) . '/prices_tpl.php';
									?>
								</div> <!-- tabs-x -->
								<?php
								$br++;
							}
						} else {
							$rand = 'x_' . rand(100000, 999999);
							?>
							<div role="tabpanel" class="tab-pane tab-price-panel active" id="tab-content-1" data-idx="1">
								<div class="panel-body">
									<div class="ibox-content ibox-heading">
										<h3><?php __('prices_add_default_price_title');?></h3>
										<small><?php __('prices_add_default_price_desc');?></small>
									</div>                                    
                                    <div id="default_weekday_price_1">
    									<div class="tab-price-item">
    										<input type="hidden" name="1_adults[<?php echo $rand; ?>]" value="0" />
    										<input type="hidden" name="1_children[<?php echo $rand; ?>]" value="0" />
    										<input type="hidden" name="1_date_from[<?php echo $rand; ?>]" value="<?php echo pjDateTime::formatDate('0000-00-00', 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
    										<input type="hidden" name="1_date_to[<?php echo $rand; ?>]" value="<?php echo pjDateTime::formatDate('0000-00-00', 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
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
    				                                                    <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></span>	
    				                                                    <input type="text" class="form-control price-form-field required number" min="0" data-msg-min="<?php __('pj_field_negative_number_err');?>" name="1_day_<?php echo $i; ?>[<?php echo $rand; ?>]" data-msg-required="<?php __('pj_field_required');?>" data-msg-number="<?php __('prices_invalid_price', false, true);?>">
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
									<input type="hidden" name="tabs[1]" value="<?php __('prices_price_default', false, true); ?>" />
									
									<div class="hr-line-dashed"></div>
		
		                            <div class="ibox-content ibox-heading">
										<h3><?php __('prices_add_price_per_guests_title');?></h3>
										<small><?php __('prices_add_price_per_guests_desc');?></small>
									</div>
		                            
		                            <div id="guests_weekday_price_1_<?php echo $rand;?>" style="display:;">
    		                            <div class="table-responsive table-responsive-secondary">
    		                                <table class="table table-striped table-hover pj-table pj-tbl-adults-children-price-<?php echo $rand;?>" data-idx="<?php echo $rand;?>">
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
    		                                <a href="javascript:void(0);" class="btn btn-primary btn-outline lnkAddPrice" rel="1" data-idx="<?php echo $rand;?>"><i class="fa fa-plus"></i> <?php __('prices_add_price_adults_children');?></a>
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
							</div>
							<?php
						}
						?>			
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<div class="bxPriceErrors" style="height: 0 !important; display: none; overflow: hidden"></div>
	<div class="modal inmodal fade" id="modalAddSeasonPrice" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	             <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php __('btnClose'); ?></span></button>

                    <h2 class="no-margins"><?php __('prices_add_seasonal_price_title');?></h2>
                </div>

                <div class="panel-body bg-light">
                    <input type="text" name="tab_title" value="Season Title" class="form-control" placeholder="Season Title">
                </div><!-- /.panel-body -->
                
                <div class="modal-footer">
                    <a href="javascript:void(0);" class="btn btn-primary pjBtnAddSeasonalPrice"><?php __('btnAdd'); ?></a>
                    <a href="javascript:void(0);" class="btn btn-default pjBtnCloseModalSeasonalPrice" data-dismiss="modal"><?php __('btnCancel'); ?></a>
                </div>
	        </div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalDeleteSeasonPrices" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php __('btnClose'); ?></span></button>

                    <h2 class="no-margins"><?php __('prices_delete_seasonal_price_title');?></h2>
                </div>

                <div class="panel-body">
                    <p><?php __('prices_delete_seasonal_price_desc');?></p>
                </div><!-- /.panel-body -->
                
                <div class="modal-footer">
                    <a href="javascript:void(0);" class="btn btn-primary pjBtnDeleteSeasonalPrice"><?php __('btnDelete'); ?></a>
                    <a href="javascript:void(0);" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel'); ?></a>
                </div>
	        </div>
	    </div>
	</div>
	
	<div class="modal fade" id="modalInvalidPrices" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	        	<div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php __('btnClose'); ?></span></button>
                </div>
                <div class="panel-body">
                    <p class="text-danger"><?php __('prices_invalid_input');?></p>
                </div><!-- /.panel-body -->
	        </div>
	    </div>
	</div>

	<div id="tmplSeason" style="display: none;">
	<?php
	include dirname(__FILE__) . '/prices_season.php';
	?>
	</div>
	<div id="tmplDefault" style="display: none;">
	<?php
	include dirname(__FILE__) . '/prices_default.php';
	?>
	</div>
	
	<!-- Modal -->
	<div class="modal fade" id="modalCopyPrices" tabindex="-1" role="dialog" aria-labelledby="myCopyPricesLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myCopyPricesLabel"><?php __('modalCopyPricesTitle');?></h4>
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
	            <input type="hidden" name="copy_tab_id" value="11" />
	            <input type="hidden" name="copy_tab" value="prices" />
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