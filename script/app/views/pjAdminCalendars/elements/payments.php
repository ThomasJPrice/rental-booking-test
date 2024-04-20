<div id="payments" class="tab-pane<?php echo $active_tab == 'payments' ? ' active' : NULL;?>">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionUpdate" method="post" id="frmUpdatePayments" class="form pj-form">
		<input type="hidden" name="property_update" value="1" />
		<input type="hidden" name="tab" value="payments" />
		<input type="hidden" name="tab_id" value="7" />
		<input type="hidden" name="id" value="<?php echo $controller->getCalendarId();?>" />
		<div class="fakeLanguageWrapper" style="display: none;">
	        <?php
	        foreach ($tpl['lp_arr'] as $v)
	        {
	            ?>
	            <div class="<?php echo $tpl['is_flag_ready'] ? 'input-group ' : NULL;?>pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? 'block' : 'none'; ?>">
	                <input type="text" class="form-control" name="i18n[<?php echo $v['id']; ?>][fake_field]" data-msg-required="<?php __('pj_field_required');?>">
	                <?php if ($tpl['is_flag_ready']) : ?>
	                <span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
	                <?php endif; ?>
	            </div>
	            <?php
	        }
	        ?>
	    </div>
	
	    <div class="panel-body">
	        <div class="panel-body-inner">
	            <?php 
				$info = str_replace("[STAG]", "<a href='#' data-toggle='modal' data-target='#modalCopyPayments' class='btn btn-primary btn-outline'>", __('modalCopyPaymentsInfo', true));
				$info = str_replace("[ETAG]", "</a>", $info); 
				?>
				<div class="alert alert-success"><?php echo $info;?></div>
	            <?php
	            $payment_methods = __('payment_methods', true);
	            $sort_arr = array('paypal','authorize','paypal_express','2checkout','stripe','mollie','skrill','world_pay','braintree');
	            $active_arr = array();
	            $other_methods = array();
	            if(pjObject::getPlugin('pjPayments') !== NULL)
	            {
	                $active_arr = pjPayments::getActivePaymentMethods($tpl['arr']['id']);
	                $other_methods = pjPayments::getPaymentMethods();
	            }
	            if(!empty($active_arr))
	            {
	                ?>
	                <div class="ibox-content ibox-heading">
	                    <h3><?php __('plugin_payments_active_payment_gateways');?></h3>
	                </div>
	                <div class="ibox-content">
	                    <div class="row">
	                        <?php
	                        if(pjObject::getPlugin('pjPayments') !== NULL)
	                        {
	                            $active_arr = pjUtil::sortArrayByArray($active_arr, $sort_arr);
	                            foreach($active_arr as $payment_method => $name)
	                            {
	                                if(!in_array($payment_method, array('cash', 'bank')))
	                                {
	                                    $pjPlugin = pjPayments::getPluginName($payment_method);
	                                    if(pjObject::getPlugin($pjPlugin) !== NULL)
	                                    {
	                                        ?>
	                                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
	                                            <a href="#" class="payment paymentLink active" data-method="<?php echo $payment_method;?>" title="<?php echo $name;?>"><img src="<?php echo PJ_IMG_PATH?>backend/payments/<?php echo $payment_method?>.png" alt="<?php echo $name;?>"></a>
	                                        </div>
	                                        <?php
	                                    }
	                                }else{
	                                    ?>
	                                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
	                                        <a href="#" class="payment paymentLink active" data-method="<?php echo $payment_method;?>" title="<?php echo $name;?>"><img src="<?php echo PJ_IMG_PATH?>backend/payments/<?php echo $payment_method?>.png" alt="<?php echo $name;?>"></a>
	                                    </div>
	                                    <?php
	                                }
	                            }
	                        }
	                        ?>
	                    </div><!-- /.row -->
	                </div><!-- /.ibox-content -->
	                <?php
	            }
	            if(!empty($other_methods))
	            {
	                ?>
	                <div class="ibox-content ibox-heading">
	                    <h3><?php __('plugin_payments_add_payment_gateways');?></h3>
	                </div>
	                <div class="ibox-content">
	                    <div class="row">
	                        <?php
	                        if(pjObject::getPlugin('pjPayments') !== NULL)
	                        {
	                            $other_methods = pjUtil::sortArrayByArray($other_methods, $sort_arr);
	                            foreach($other_methods as $payment_method => $name)
	                            {
	                                if(!array_key_exists($payment_method, $active_arr))
	                                {
	                                    $pjPlugin = pjPayments::getPluginName($payment_method);
	                                    if(pjObject::getPlugin($pjPlugin) !== NULL)
	                                    {
	                                        ?>
	                                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
	                                            <a href="#" class="payment paymentLink" data-method="<?php echo $payment_method;?>" title="<?php echo $name;?>"><img src="<?php echo PJ_IMG_PATH?>backend/payments/<?php echo $payment_method?>.png" alt="<?php echo $name;?>"></a>
	                                        </div>
	                                        <?php
	                                    }
	                                }
	                            }
	                        }
	                        ?>
	                    </div>
	                </div><!-- /.ibox-content -->
	                <?php
	            }
	            if((array_key_exists('cash', $other_methods) && !array_key_exists('cash', $active_arr)) || (array_key_exists('bank', $other_methods) && !array_key_exists('bank', $active_arr)))
	            {
	                ?>
	                <div class="ibox-content ibox-heading">
	                    <h3><?php __('script_offline_payment_methods');?></h3>
	                </div>
	                <div class="ibox-content">
	                    <div class="row">
	                        <?php
	                    	if(array_key_exists('bank', $other_methods) && !array_key_exists('bank', $active_arr))
	                    	{
	                    	    ?>
	                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
	                                <a href="#" class="payment paymentLink" data-method="bank" title="<?php echo $payment_methods['bank'];?>"><img src="<?php echo PJ_IMG_PATH?>backend/payments/bank.png" alt="<?php echo $payment_methods['bank'];?>"></a>
	                            </div>
	                            <?php
	                    	}
	                    	if(array_key_exists('cash', $other_methods) && !array_key_exists('cash', $active_arr))
	                    	{
	                    	    ?>
	                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
	                                <a href="#" class="payment paymentLink" data-method="cash" title="<?php echo $payment_methods['cash'];?>"><img src="<?php echo PJ_IMG_PATH?>backend/payments/cash.png" alt="<?php echo $payment_methods['cash'];?>"></a>
	                            </div>
	                            <?php
	                    	}
	                    	?>
	                    </div>
	                </div><!-- /.ibox-content -->
	                <?php
	            }
	            ?>
							
	            <div class="hr-line-dashed"> </div> 
	            
	            <div class="clearfix">
	                <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
	                    <span class="ladda-label"><?php __('btnSave'); ?></span>
	                    <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
	                </button>
	                <a type="button" class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCalendars&action=pjActionIndex"><?php __('btnCancel'); ?></a>
	            </div>
	            <!-- /.clearfix -->
	        </div>
	    </div>
	</form>
	<div class="modal inmodal fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div id="modalContent" class="modal-content">
	
			</div>
		</div>
	</div>
	
	<!-- Modal -->
	<div class="modal fade" id="modalCopyPayments" tabindex="-1" role="dialog" aria-labelledby="myCopyPaymentsLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myCopyPaymentsLabel"><?php __('modalCopyPaymentsTitle');?></h4>
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
	            <input type="hidden" name="copy_tab_id" value="7" />
	            <input type="hidden" name="copy_tab" value="payments" />
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