<?php
$plugins_payment_methods = pjObject::getPlugin('pjPayments') !== NULL? pjPayments::getPaymentMethods(): array();
$haveOnline = $haveOffline = false;
foreach ($tpl['payment_titles'] as $k => $v)
{
    if( $k != 'cash' && $k != 'bank' )
    {
        if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)
        {
            $haveOnline = true;
            break;
        }
    }
}
foreach ($tpl['payment_titles'] as $k => $v)
{
    if( $k == 'cash' || $k == 'bank' )
    {
        if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)
        {
            $haveOffline = true;
            break;
        }
    }
}
?>
<label><?php __('lblReservationPayment'); ?></label>
<div>
	<select name="payment_method" id="payment_method" class="form-control <?php //echo (int) $tpl['_option_arr']['o_disable_payments'] == 0 ? ' required' : NULL;?>" data-msg-required="<?php __('pj_field_required', false, true);?>">
		<option value="">-- <?php __('lblChoose'); ?>--</option>
		<?php
		if ($haveOnline && $haveOffline)
		{
		    ?><optgroup label="<?php __('script_online_payment_gateway', false, true); ?>"><?php
	    }
	    foreach ($tpl['payment_titles'] as $k => $v)
	    {
	        if($k == 'cash' || $k == 'bank' ){
	            continue;
	        }
	        if (array_key_exists($k, $plugins_payment_methods))
	        {
	            if(!isset($tpl['payment_option_arr'][$k]['is_active']) || (isset($tpl['payment_option_arr']) && $tpl['payment_option_arr'][$k]['is_active'] == 0) )
	            {
	                continue;
	            }
	        }
	        ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
	    }
	    if ($haveOnline && $haveOffline)
	    {
	        ?>
	    	</optgroup>
	    	<optgroup label="<?php __('script_offline_payment', false, true); ?>">
	    	<?php 
	    }
	    foreach ($tpl['payment_titles'] as $k => $v)
	    {
	        if( $k == 'cash' || $k == 'bank' )
	        {
	            if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)
	            {
	                ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
	            }
	        }
	    }
	    if ($haveOnline && $haveOffline)
	    {
	        ?></optgroup><?php
	    }
		?>
	</select>
</div>