<h3><?php __('lblExtras'); ?></h3>
<div class="table-responsive-actions">	
	<table class="table table-bordered abExtraTable">
		<thead>
			<tr>
				<th><?php __('lblExtraName');?></th>
				<th><?php __('lblPrice');?></th>
				<th><?php __('lblQty');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$price_types = $tpl['option_arr']['o_price_based_on'] == 'days' ? __('day_price_types', true) : __('price_types', true);
			foreach($tpl['extra_arr'] as $k => $v)
			{
				?>
				<tr>
					<td>
						<div>
							<label class="clearfix">
								<input type="checkbox" name="extra[<?php echo $v['id'];?>]" id="extra_<?php echo $v['id'];?>" data-id="<?php echo $v['id'];?>" class="i-checks abExtraCheckbox<?php echo $v['required'] == 'T' ? ' required' : NULL;?>" data-msg-required="<?php __('lblExtraRequiredMessage');?>"/>
								<?php echo pjSanitize::html($v['name']);?>
							</label>
						</div>
					</td>
					<td><?php echo pjCurrency::formatPrice($v['price']) . ' ' . $price_types[$v['price_type']];?></td>
					<td>
						<?php
						if($v['multi'] == 'T')
						{
							?>
							<select id="qty_<?php echo $v['id'];?>" name="qty[<?php echo $v['id'];?>]" class="form-control abTextQty">
								<?php
								for($i = 1; $i <= (int) $v['max_count']; $i++)
								{
									?><option value="<?php echo $i;?>"><?php echo $i;?></option><?php 
								} 
								?>
							</select>
							<?php
						}else{
							if($v['price_type'] == 'count' || $v['price_type'] == 'count_night')
							{
								?>
								<select id="qty_<?php echo $v['id'];?>" name="qty[<?php echo $v['id'];?>]" class="form-control abTextQty">
									<?php
									for($i = 1; $i <= (int) $v['max_count']; $i++)
									{
										?><option value="<?php echo $i;?>"><?php echo $i;?></option><?php 
									} 
									?>
								</select>
								<?php
							}else{
								?>1<input type="hidden" name="qty[<?php echo $v['id'];?>]" value="1"/><?php
							}
						}
						?>
					</td>
				</tr>
				<?php
			} 
			?>
		</tbody>
	</table>
</div>