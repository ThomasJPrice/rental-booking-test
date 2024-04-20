
START TRANSACTION;


SET @label := 'Plugin / Payment method / Paypal';
INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES (NULL, 'payment_methods_ARRAY_paypal', 'arrays', @label, 'plugin', NULL)
ON DUPLICATE KEY UPDATE `fields`.`type` = 'arrays', `label` = @label, `source` = 'plugin', `modified` = NULL;
SET @content := 'Paypal';
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, `id`, 'pjField', '::LOCALE::', 'title', @content, 'plugin' FROM `fields` WHERE `key` = 'payment_methods_ARRAY_paypal' ON DUPLICATE KEY UPDATE `multi_lang`.`content` = @content, `source` = 'plugin';



COMMIT;