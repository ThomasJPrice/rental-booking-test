<style>
div.abCalendarReservedNightsStart {
	background-image: url("<?php echo PJ_INSTALL_URL;?>index.php?controller=pjFront&action=pjActionImage&color1=5ac5b6&color2=ed5565&width=43&height=31");
	background-position: center center;
}
div.abCalendarReservedNightsEnd {
	background-image: url("<?php echo PJ_INSTALL_URL;?>index.php?controller=pjFront&action=pjActionImage&color1=ed5565&color2=5ac5b6&width=43&height=31");
	background-position: center center;
}
div.abCalendarNightsPendingPending {
	background-image: url("<?php echo PJ_INSTALL_URL;?>index.php?controller=pjFront&action=pjActionImage&color1=fbc994&color2=fbc994&width=43&height=31");
	background-position: center center;
}
div.abCalendarNightsReservedPending {
	background-image: url("<?php echo PJ_INSTALL_URL;?>index.php?controller=pjFront&action=pjActionImage&color1=ed5565&color2=fbc994&width=43&height=31");
	background-position: center center;
}
div.abCalendarNightsPendingReserved {
	background-image: url("<?php echo PJ_INSTALL_URL;?>index.php?controller=pjFront&action=pjActionImage&color1=fbc994&color2=ed5565&width=43&height=31");
	background-position: center center;
}
div.abCalendarNightsReservedReserved {
	background-image: url("<?php echo PJ_INSTALL_URL;?>index.php?controller=pjFront&action=pjActionImage&color1=ed5565&color2=ed5565&width=43&height=31");
	background-position: center center;
}
div.abCalendarPendingNightsStart {
	background-image: url("<?php echo PJ_INSTALL_URL;?>index.php?controller=pjFront&action=pjActionImage&color1=5ac5b6&color2=fbc994&width=43&height=31");
	background-position: center center;
}
div.abCalendarPendingNightsEnd {
	background-image: url("<?php echo PJ_INSTALL_URL;?>index.php?controller=pjFront&action=pjActionImage&color1=fbc994&color2=5ac5b6&width=43&height=31");
	background-position: center center;
}
div.abCalendarReserved{
	background-color: #ed5565;	
}
div.abCalendarPending{
	background-color: #fbc994;	
}
</style>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-6">
                <h2><?php __('infoScheduleTitle')?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoScheduleDesc')?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
    	<div class="ibox float-e-margins">
			<div class="ibox-content">
        		<form id="frmSchedule"  action="#"></form>
        	</div>
        </div>
    </div><!-- /.col-lg-12 -->
</div><!-- /.wrapper wrapper-content -->

<script type="text/javascript">
var myLabel = myLabel || {};
</script>