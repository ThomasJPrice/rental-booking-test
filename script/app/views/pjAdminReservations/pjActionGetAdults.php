<?php if (in_array(@$tpl['option_arr']['o_bf_adults'], array(2,3))) {  ?>
	<?php
	ob_start();
	?>
	<div class="form-group">
		<label><?php __('lblReservationAdults'); ?></label>
		<div>
			<input class="pjRpbcAdultsChildSelector form-control <?php echo (int) $tpl['option_arr']['o_bf_adults'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>" type="text" value="1" name="c_adults" id="c_adults" data-max="<?php echo $tpl['option_arr']['o_bf_adults_max'];?>">
		</div>
	</div>
	<?php
	$ob_content = ob_get_contents();
	ob_clean();
	$max_message = str_replace("{MAX}", $tpl['option_arr']['o_max_people'], __('lblMaxPeopleMsg', true));
	$min_message = str_replace("{MIN}", $tpl['option_arr']['o_min_people'], __('lblMinPeopleMsg', true));
	pjAppController::jsonResponse(array('ob_content' => $ob_content, 'o_disable_payments' => $tpl['option_arr']['o_disable_payments'], 
	    'o_min_people' => $tpl['option_arr']['o_min_people'], 'o_max_people' => $tpl['option_arr']['o_max_people'],
	    'min_message' => $min_message, 'max_message'=> $max_message
	));
	?>
<?php } ?>