<?php if (in_array(@$tpl['option_arr']['o_bf_children'], array(2,3))) {  ?>
	<div class="form-group">
		<label><?php __('lblReservationChildren'); ?></label>
		<div>
			<input class="pjRpbcAdultsChildSelector form-control <?php echo (int) $tpl['option_arr']['o_bf_children'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>" type="text" value="0" name="c_children" id="c_children" data-max="<?php echo $tpl['option_arr']['o_bf_children_max'];?>">
		</div>
	</div>
<?php } ?>