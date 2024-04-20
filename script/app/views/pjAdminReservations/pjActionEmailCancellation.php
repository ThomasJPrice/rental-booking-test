<?php
if (isset($tpl['arr']) && !empty($tpl['arr']))
{
    ?>
	<form action="" method="post" class="">
		<input type="hidden" name="send_email" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<?php if (!empty($tpl['arr']['to'])) : ?>
		<div class="form-group">
			<label class="control-label"><?php __('lblReservationEmail'); ?></label>
	
			<input type="text" name="to" class="form-control required email" value="<?php echo pjSanitize::html($tpl['arr']['to']); ?>" data-msg-required="<?php __('plugin_base_this_field_is_required');?>" data-msg-email="<?php __('plugin_base_email_invalid');?>" />
		</div>
		<?php endif; ?>
		<div class="form-group">
			<label class="control-label"><?php __('lblSubject');?></label>
			<input type="text" name="subject" id="confirm_subject" class="form-control required" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['subject'])); ?>" data-msg-required="<?php __('plugin_base_this_field_is_required');?>" />
		</div>
		<div class="form-group">
			<label class="control-label"><?php __('lblMessage');?></label>
			<div id="crMessageEditorWrapper">
				<textarea name="message" id="mceEditor" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required');?>"><?php echo stripslashes(str_replace(array('\r\n', '\n'), '&#10;', $tpl['arr']['message'])); ?></textarea>
			</div>			
		</div>
	</form>
	<?php
}else{
    ?>
    <div id="pjResendAlert" class="alert alert-warning">
   		<?php __('lblEmailNotificationNotSet')?>
    </div>
    <?php    
}
?>