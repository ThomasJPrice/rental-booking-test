<?php
require $content_tpl;
$content = ob_get_contents();
ob_end_clean();

$content = preg_replace('/\r\n|\n|\t/', '', $content);
$content = str_replace("'", "\"", $content);

$pattern = '|<script.*>(.*)</script>|';
if (preg_match($pattern, $content, $matches))
{
	$content = preg_replace($pattern, '', $content);
}
mt_srand();
$index = mt_rand(1, 9999);
?>
(function () {
var pjInstallElement_<?php echo $index;?> = null;
var pjScriptTags_<?php echo $index;?> = document.getElementsByTagName("script");
for (var i = 0; i < pjScriptTags_<?php echo $index;?>.length; i++) 
{
	var src = pjScriptTags_<?php echo $index;?>[i].src;
	<?php 
	if ($controller->_get->toString('action') == 'pjActionLoadAvailability')
	{
		?>
		if(src.indexOf("<?php echo PJ_INSTALL_FOLDER;?>index.php?controller=pjFront&action=pjActionLoadAvailability") != -1)
		{
			pjInstallElement_<?php echo $index;?> = pjScriptTags_<?php echo $index;?>[i];
		}
		<?php
	} elseif ($controller->_get->toString('action') == 'pjActionLoad') {
		?>
		if(src.indexOf("<?php echo PJ_INSTALL_FOLDER;?>index.php?controller=pjFront&action=pjActionLoad") != -1 && src.indexOf("<?php echo PJ_INSTALL_FOLDER;?>index.php?controller=pjFront&action=pjActionLoadAvailability") === -1)
		{
			pjInstallElement_<?php echo $index;?> = pjScriptTags_<?php echo $index;?>[i];
		}
		<?php
	}
	?>
}
var pjNewDiv_<?php echo $index;?> = document.createElement('div');
pjNewDiv_<?php echo $index;?>.innerHTML = '<?php echo $content;?>';
if(pjInstallElement_<?php echo $index;?> != null)
{
	pjInstallElement_<?php echo $index;?>.parentNode.insertBefore(pjNewDiv_<?php echo $index;?>, pjInstallElement_<?php echo $index;?>);
}else{
	document.body.appendChild(pjNewDiv_<?php echo $index;?>);
}
<?php
if ($matches)
{
	?>
	var pjNewScript_<?php echo $index;?> = document.createElement('script');
	pjNewScript_<?php echo $index;?>.text = '<?php echo $matches[1];?>';
	if(pjInstallElement_<?php echo $index;?> != null)
	{
		pjInstallElement_<?php echo $index;?>.parentNode.insertBefore(pjNewScript_<?php echo $index;?>, pjInstallElement_<?php echo $index;?>);
	}else{
		document.body.appendChild(pjNewScript_<?php echo $index;?>);
	}
	<?php
}
?>
})();