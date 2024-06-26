
START TRANSACTION;


UPDATE `fields` SET `source`='plugin' WHERE `key` IN ('payment_methods_ARRAY_world_pay');

UPDATE `fields` SET `source`='plugin' WHERE `key` IN ('plugin_world_pay_payment_title');

UPDATE `fields` SET `source`='plugin' WHERE `key` IN ('payment_plugin_messages_ARRAY_world_pay');
SET @id := (SELECT `id` FROM `fields` WHERE `key`='payment_plugin_messages_ARRAY_world_pay');
UPDATE `multi_lang` SET `source`='plugin' WHERE `foreign_id`=@id AND `model`='pjField' AND `field`='title';


COMMIT;