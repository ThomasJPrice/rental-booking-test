<!DOCTYPE html>
<html>
	<head>
		<title>Rental Property Booking Calendar by PHPJabbers.com</title>
		<script async data-id="five-server" src="http://localhost:5500/fiveserver.js"></script>
		<meta charset="utf-8">
		<meta name="fragment" content="!">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	     <link href="core/framework/libs/pj/css/pj.bootstrap.min.css" type="text/css" rel="stylesheet" />
	    <?php
		if (isset($_GET['cid']))
		{
			if ((int) $_GET['cid'] > 0)
			{
				?><link href="index.php?controller=pjFront&action=pjActionLoadCss&cid=<?php echo (int)$_GET['cid']; ?>" type="text/css" rel="stylesheet" /><?php
			} elseif ($_GET['cid'] == 'all') {
				?><link type="text/css" rel="stylesheet" href="index.php?controller=pjFront&action=pjActionLoadAvailabilityCss" /><?php
			}
		}
		?>
	</head>
	<body>
		<?php
		if (isset($_GET['cid']))
		{
			if ((int) $_GET['cid'] > 0)
			{
				$style = "max-width: 600px; height: 450px;margin: 0 auto;";
				if (isset($_GET['view']))
				{
					switch ((int)$_GET['view'])
					{
						case 3:
							$style = "max-width: 800px; height: 650px;margin: 0 auto;";
							break;
						case 6:
							$style = "max-width: 800px; height: 650px;margin: 0 auto;";
							break;
						case 12:
							$style = "max-width: 800px; height: 650px;margin: 0 auto;";
							break;
					}
				}
				?>
				<div style="<?php echo $style; ?>">
					<script type="text/javascript" src="index.php?controller=pjFront&action=pjActionLoad&cid=<?php echo (int)$_GET['cid']; ?><?php echo isset($_GET['view']) && (int) $_GET['view'] > 0 ? '&view=' . (int) $_GET['view'] : NULL; ?><?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? '&locale=' . (int) $_GET['locale'] : NULL; ?>"></script>
				</div>
				<?php
			} elseif ($_GET['cid'] == 'all') {
				?>
				<div style="max-width: 1100px; height: 650px;margin: 0 auto;">
					<script type="text/javascript" src="index.php?controller=pjFront&action=pjActionLoadAvailability<?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? '&locale=' . (int) $_GET['locale'] : NULL; ?>"></script>
				</div>
				<?php
			}
		}
		?>
	</body>
</html>