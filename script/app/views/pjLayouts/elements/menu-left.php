<?php
$controller_name = $controller->_get->toString('controller');
$action_name = $controller->_get->toString('action');

// Dashboard
$isScriptDashboard = in_array($controller_name, array('pjAdmin')) && in_array($action_name, array('pjActionIndex'));

// Calendars
$isScriptCalendars = in_array($controller_name, array('pjAdminCalendars'));

// Reservations
$isScriptReservations = in_array($controller_name, array('pjAdminReservations'));
$isScriptReservationsController = $isScriptReservations && in_array($action_name, array('pjActionIndex', 'pjActionCreate', 'pjActionUpdate'));
$isScriptScheduleController = $isScriptReservations && in_array($action_name, array('pjActionSchedule'));

// Extras
$isScriptExtras = in_array($controller_name, array('pjAdminExtras'));

// Vouchers
$isScriptVouchers = in_array($controller_name, array('pjAdminVouchers'));

// Discounts
$isScriptDiscounts = in_array($controller_name, array('pjAdminDiscounts'));

// Settings
$isScriptOptions = in_array($controller_name, array('pjAdminOptions')) && !in_array($action_name, array('pjActionPreview', 'pjActionInstall'));
$isScriptOptionsNotifications         = $isScriptOptions && in_array($action_name, array('pjActionNotifications'));


// Permissions - Dashboard
$hasAccessScriptDashboard = pjAuth::factory('pjAdmin', 'pjActionIndex')->hasAccess();

// Permissions - Calendars
$hasAccessScriptCalendars          = pjAuth::factory('pjAdminCalendars')->hasAccess();

// Permissions - Reservations
$hasAccessScriptReservations          = pjAuth::factory('pjAdminReservations', 'pjActionIndex')->hasAccess();
$hasAccessScriptSchedule          = pjAuth::factory('pjAdminReservations', 'pjActionSchedule')->hasAccess();

// Permissions - Settings
$hasAccessScriptOptions                 = pjAuth::factory('pjAdminOptions')->hasAccess();
$hasAccessScriptOptionsNotifications          = pjAuth::factory('pjAdminOptions', 'pjActionNotifications')->hasAccess();

// Permissions - Extras
$hasAccessScriptExtras          = pjAuth::factory('pjAdminExtras', 'pjActionIndex')->hasAccess();

// Permissions - Vouchers
$hasAccessScriptVouchers          = pjAuth::factory('pjAdminVouchers', 'pjActionIndex')->hasAccess();

// Permissions - Discounts
$hasAccessScriptDiscounts          = pjAuth::factory('pjAdminDiscounts', 'pjActionIndex')->hasAccess();

?>

<?php if ($hasAccessScriptDashboard): ?>
    <li<?php echo $isScriptDashboard ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex"><i class="fa fa-th-large"></i> <span class="nav-label"><?php __('plugin_base_menu_dashboard');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptCalendars): ?>
    <li<?php echo $isScriptCalendars ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionIndex" class="pjTsCheckAssignCalendar"><i class="fa fa-calendar"></i> <span class="nav-label"><?php __('menuCalendars');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptReservations || $hasAccessScriptSchedule): ?>
    <li<?php echo $isScriptReservations ? ' class="active"' : NULL; ?>>
        <a href="#"><i class="fa fa-list"></i> <span class="nav-label"><?php __('menuReservations');?></span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            <?php if ($hasAccessScriptSchedule): ?>
                <li<?php echo $isScriptScheduleController ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionSchedule"><?php __('menuSchedule');?></a></li>
            <?php endif; ?>

            <?php if ($hasAccessScriptReservations): ?>
                <li<?php echo $isScriptReservationsController ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionIndex"><?php __('menuReservationsList');?></a></li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptOptionsNotifications || $hasAccessScriptExtras || $hasAccessScriptVouchers || $hasAccessScriptDiscounts): ?>
    <li<?php echo $isScriptOptionsNotifications || $isScriptExtras || $isScriptVouchers || $isScriptDiscounts ? ' class="active"' : NULL; ?>>
        <a href="#"><i class="fa fa-cogs"></i> <span class="nav-label"><?php __('menuOptions');?></span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            <?php if ($hasAccessScriptOptionsNotifications): ?>
                <li<?php echo $isScriptOptionsNotifications ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionNotifications"><?php __('menuNotifications');?></a></li>
            <?php endif; ?>

            <?php if ($hasAccessScriptExtras): ?>
                <li<?php echo $isScriptExtras ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionIndex"><?php __('menuExtras');?></a></li>
            <?php endif; ?>
            
            <?php if ($hasAccessScriptVouchers || $hasAccessScriptDiscounts): ?>
            	<?php if ($hasAccessScriptDiscounts) { ?>
            		<li<?php echo $isScriptDiscounts ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDiscounts&amp;action=pjActionIndex"><?php __('menuCouponsDiscounts');?></a></li>
                <?php } else { ?>
                	<li<?php echo $isScriptVouchers ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionIndex"><?php __('menuCouponsDiscounts');?></a></li>
                <?php } ?>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>
