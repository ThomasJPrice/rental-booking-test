
START TRANSACTION;


INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_paypal_client_id', 'backend', 'Plugin / Paypal / Client ID', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client ID', 'plugin');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'plugin_paypal_secret', 'backend', 'Plugin / Paypal / Secret', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Secret', 'plugin');


COMMIT;