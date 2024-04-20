<?php
if (isset($tpl['form']))
{
    if (strpos($tpl['form']['action'], '?') !== false)
    {
        list($action, $query) = explode('?', $tpl['form']['action']);
        parse_str($query, $vars);
    } else {
        $action = $tpl['form']['action'];
        $vars = array();
    }
    ?>
    <form action="<?php echo $action; ?>"
          method="<?php echo strtolower($tpl['form']['method']); ?>"
          name="<?php echo $tpl['arr']['name']; ?>"
          id="<?php echo $tpl['arr']['id']; ?>"
          target="<?php echo $tpl['arr']['target']; ?>"
          style="display: inline">
    <?php
    foreach ($vars as $name => $value)
    {
        ?>
        <input type="hidden" name="<?php echo htmlspecialchars($name); ?>" value="<?php echo htmlspecialchars($value); ?>">
        <?php
    }
    if (isset($tpl['arr']['submit']))
	{
		?><button type="submit" class="<?php echo @$tpl['arr']['submit_class']; ?>"><?php echo htmlspecialchars($tpl['arr']['submit']); ?></button><?php
	}
    ?>
    </form>
    <?php
}