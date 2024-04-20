
START TRANSACTION;


INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_paypal_cancel_url', 'backend', 'Plugin / Paypal / Cancel page', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cancel page', 'plugin');



COMMIT;