<div id="reservations" class="tab-pane<?php echo $active_tab == 'reservations' ? ' active' : NULL;?>">
    <div class="panel-body">
		<div class="panel-body-inner">
			<div class="ibox float-e-margins">
            	<div class="ibox-content no-margins no-padding no-top-border">
            		<form action="" method="get" class="form-horizontal frm-filter-reservations">
						<div class="row m-b-md">
							<div class="col-sm-3">
								<?php
								if(pjAuth::factory('pjAdminReservations', 'pjActionCreate')->hasAccess())
								{
									?>
									<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate" class="btn btn-primary"><i class="fa fa-plus"></i> <?php __('btnAddReservation'); ?></a>
									<?php
								}
								?>
							</div><!-- /.col-md-6 -->
						
							<div class="col-md-5 col-sm-8">
								<div class="input-group">
									<input type="text" name="q" placeholder="<?php __('plugin_base_btn_search', false, true); ?>" class="form-control">
						
									<div class="input-group-btn">
										<button class="btn btn-primary" type="submit">
											<i class="fa fa-search"></i>
										</button>
									</div>
								</div>
							</div><!-- /.col-md-3 -->
						
							<div class="col-lg-2 col-lg-offset-2 col-md-12 text-right">
								<select id="filter_status" name="status" class="form-control">
									<option value="">-- <?php __('lblAll');?> --</option>
									<option value="Confirmed"><?php echo $rs['Confirmed'];?></option>
									<option value="Pending"><?php echo $rs['Pending'];?></option>
									<option value="Cancelled"><?php echo $rs['Cancelled'];?></option>
								</select>
							</div><!-- /.col-md-6 -->
						</div><!-- /.row -->
					</form>
					<div id="grid_reservations"></div>
				</div>
			</div>
		</div>
	</div>
</div>