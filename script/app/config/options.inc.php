<?php
$stop = false;
if (isset($_GET['controller']) && $_GET['controller'] == 'pjInstaller')
{
	$stop = true;
	if (isset($_GET['install']))
	{
		switch ($_GET['install'])
		{
			case 1:
				$stop = true;
				break;
			default:
				$stop = false;
				break;
		}
	}
	if (in_array($_GET['action'], array('pjActionLicense', 'pjActionHash')) || strpos($_GET['action'], 'pjActionSecure') === 0)
	{
		$stop = false;
	}
}

if (!$stop)
{
	require dirname(__FILE__) . '/config.inc.php';

	if (!defined("PJ_HOST") || preg_match('/\[hostname\]/', PJ_HOST))
	{
		header("Location: index.php?controller=pjInstaller&action=pjActionStep1&install=1");
		exit;
	}
}

if (!defined("PJ_APP_PATH")) define("PJ_APP_PATH", ROOT_PATH . "app/");
if (!defined("PJ_CORE_PATH")) define("PJ_CORE_PATH", ROOT_PATH . "core/");
if (!defined("PJ_LIBS_PATH")) define("PJ_LIBS_PATH", "core/libs/");
if (!defined("PJ_THIRD_PARTY_PATH")) define("PJ_THIRD_PARTY_PATH", "third-party/");
if (!defined("PJ_FRAMEWORK_PATH")) define("PJ_FRAMEWORK_PATH", PJ_CORE_PATH . "framework/");
if (!defined("PJ_FRAMEWORK_LIBS_PATH")) define("PJ_FRAMEWORK_LIBS_PATH", "core/framework/libs/");
if (!defined("PJ_CONFIG_PATH")) define("PJ_CONFIG_PATH", PJ_APP_PATH . "config/");
if (!defined("PJ_CONTROLLERS_PATH")) define("PJ_CONTROLLERS_PATH", PJ_APP_PATH . "controllers/");
if (!defined("PJ_COMPONENTS_PATH")) define("PJ_COMPONENTS_PATH", PJ_APP_PATH . "controllers/components/");
if (!defined("PJ_MODELS_PATH")) define("PJ_MODELS_PATH", PJ_APP_PATH . "models/");
if (!defined("PJ_PLUGINS_PATH")) define("PJ_PLUGINS_PATH", "plugins/");
if (!defined("PJ_VIEWS_PATH")) define("PJ_VIEWS_PATH", PJ_APP_PATH . "views/");
if (!defined("PJ_WEB_PATH")) define("PJ_WEB_PATH", PJ_APP_PATH . "web/");
if (!defined("PJ_CSS_PATH")) define("PJ_CSS_PATH", "app/web/css/");
if (!defined("PJ_IMG_PATH")) define("PJ_IMG_PATH", "app/web/img/");
if (!defined("PJ_FONT_PATH")) define("PJ_FONT_PATH", "app/web/fonts/");
if (!defined("PJ_JS_PATH")) define("PJ_JS_PATH", "app/web/js/");
if (!defined("PJ_UPLOAD_PATH")) define("PJ_UPLOAD_PATH", "app/web/upload/");

if (!defined("PJ_SCRIPT_VERSION")) define("PJ_SCRIPT_VERSION", "3.0");
if (!defined("PJ_SCRIPT_ID")) define("PJ_SCRIPT_ID", "242");
if (!defined("PJ_SCRIPT_BUILD")) define("PJ_SCRIPT_BUILD", "3.0.0");
if (!defined("PJ_SCRIPT_PREFIX")) define("PJ_SCRIPT_PREFIX", "rpbcalendar_");
if (!defined("PJ_TEST_MODE")) define("PJ_TEST_MODE", true);
if (!defined("PJ_DISABLE_MYSQL_CHECK")) define("PJ_DISABLE_MYSQL_CHECK", false);

if (!defined("PJ_ENABELE_OFFLINE_PAYMENTS")) define("PJ_ENABELE_OFFLINE_PAYMENTS", true);

if (!defined("PJ_INVOICE_PLUGIN")) define("PJ_INVOICE_PLUGIN", 'index.php?controller=pjAdminReservations&action=pjActionUpdate&uuid={ORDER_ID}');

if (!defined("PJ_RSA_MODULO")) define("PJ_RSA_MODULO", '1481520313354086969195005236818182195268088406845365735502215319550493699869327120616729967038217547');
if (!defined("PJ_RSA_PRIVATE")) define("PJ_RSA_PRIVATE", '7');

$CONFIG = array();
$CONFIG['plugins'] = array('pjBase', 'pjAuth', 'pjInstaller', 'pjPayments', 'pjPaypal', 'pjAuthorize', 'pj2checkout', 'pjBraintree', 'pjMollie', 'pjPaypalExpress', 'pjSkrill', 'pjStripe');
$CONFIG['plugins_base_auth_updates'] = true; // Execute pjBase and pjAuth updates before script database.sql
$CONFIG['role_permissions_admin'] = array('*'); // Full access
$CONFIG['role_permissions_editor'] = array(
    // pjBase permissions
    'pjBaseUsers_pjActionProfile',
    // Script dashboard permission
    'pjAdmin_pjActionIndex',
    
    // Script permissions
    'pjAdminCalendars',
    'pjAdminCalendars_pjActionIndex',
    'pjAdminCalendars_pjActionUpdate',
	'pjAdminReservations*'
);
$CONFIG['role_permissions_owner'] = array(
    // pjBase permissions
    'pjBaseUsers_pjActionProfile',
    // Script dashboard permission
    'pjAdmin_pjActionIndex',
    
    // Script permissions
    'pjAdminCalendars',
    'pjAdminCalendars_pjActionIndex',
    'pjAdminCalendars_pjActionUpdate',
	'pjAdminReservations*',
    'pjAdminOptions',
    'pjAdminOptions_pjActionPreview',
	'pjAdminOptions_pjActionInstall'
);
$CONFIG['spam_protection'] = array(
    'banned_words' => false,
    'banned_ips' => true,
);
$CONFIG['api_keys'] = array(
    'google_api_key' => false,
);

$CONFIG['permission_items_order'] = array(
    'pjAdmin_pjActionIndex',
    'pjAdminCalendars',
    'pjAdminReservations',
	'pjAdminExtras',
	'pjAdminDiscounts',
	'pjAdminVouchers',
	'pjAdminOptions_pjActionNotifications',
    'pjBaseOptions',
    'pjBaseUsers',
    'pjBaseUsers_pjActionProfile',
    'pjAdminOptions_pjActionPreview',
    'pjAdminOptions_pjActionInstall',
);
?>