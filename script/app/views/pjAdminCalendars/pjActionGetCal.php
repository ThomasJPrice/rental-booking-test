<?php
$month = $year = array();

if ($controller->_get->check('year') && $controller->_get->toInt('year') > 0 && $controller->_get->check('month') && $controller->_get->toInt('month') > 0)
{
	$y = $controller->_get->toInt('year');
	$m = $controller->_get->toInt('month');
} else {
	list($y, $m) = explode("-", date("Y-m"));
}

$month[1] = intval($m);
foreach (range(2, 12) as $i)
{
	$month[$i] = ($month[1] + $i - 1) > 12 ? $month[1] + $i - 1 - 12 : $month[1] + $i - 1;
}

$year[1] = intval($y);
foreach (range(2, 12) as $i)
{
	$year[$i] = ($month[1] + $i - 1) > 12 ? $year[1] + 1 : $year[1];
}

echo $tpl['ABCalendar']->getMonthView($month[1], $year[1]);

if ((int) $tpl['option_arr']['o_show_legend'] === 1)
{
	echo $tpl['ABCalendar']->getLegend($tpl['option_arr'], pjRegistry::getInstance()->get('fields'));
}
?>