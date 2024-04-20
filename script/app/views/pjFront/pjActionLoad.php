<div id="pjWrapperRPBC_<?php echo $controller->_get->toInt('cid'); ?>" class="abWrapper<?php echo $controller->_get->toInt('view') > 1 ? ' abWrapper13' : NULL; ?>">
	<div id="abLoader_<?php echo $controller->_get->toInt('cid'); ?>" class="abLoader">
		<div class="abLoaderInner">
		  	<div class="spinner-container container1">
		    	<div class="circle1"></div>
		    	<div class="circle2"></div>
		    	<div class="circle3"></div>
		    	<div class="circle4"></div>
		  	</div>
		  	<div class="spinner-container container2">
		    	<div class="circle1"></div>
		    	<div class="circle2"></div>
		    	<div class="circle3"></div>
		    	<div class="circle4"></div>
		  	</div>
		  	<div class="spinner-container container3">
		    	<div class="circle1"></div>
		    	<div class="circle2"></div>
		    	<div class="circle3"></div>
		    	<div class="circle4"></div>
		  	</div>
	  	</div>
	  	<span class="abLoaderMessage"></span>
	</div>
	<div id="abCalendar_<?php echo $controller->_get->toInt('cid'); ?>" class="abCalendar"></div>
</div>
<?php
$front_err = str_replace(array('"', "'"), array('\"', "\'"), __('front_err', true, true));
$days = str_replace(array('"', "'"), array('\"', "\'"), __('days', true, true)); 
?>
<script type="text/javascript">
var pjQ = pjQ || {},
	ABCalendar_<?php echo $controller->_get->toInt('cid'); ?>;
(function () {
	"use strict";
	var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),

	loadCssHack = function(url, callback){
		var link = document.createElement('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = url;

		document.getElementsByTagName('head')[0].appendChild(link);

		var img = document.createElement('img');
		img.onerror = function(){
			if (callback && typeof callback === "function") {
				callback();
			}
		};
		img.src = url;
	},
	loadRemote = function(url, type, callback) {
		if (type === "css" && isSafari) {
			loadCssHack(url, callback);
			return;
		}
		var _element, _type, _attr, scr, s, element;
		
		switch (type) {
		case 'css':
			_element = "link";
			_type = "text/css";
			_attr = "href";
			break;
		case 'js':
			_element = "script";
			_type = "text/javascript";
			_attr = "src";
			break;
		}
		
		scr = document.getElementsByTagName(_element);
		s = scr[scr.length - 1];
		element = document.createElement(_element);
		element.type = _type;
		if (type == "css") {
			element.rel = "stylesheet";
		}
		if (element.readyState) {
			element.onreadystatechange = function () {
				if (element.readyState == "loaded" || element.readyState == "complete") {
					element.onreadystatechange = null;
					if (callback && typeof callback === "function") {
						callback();
					}
				}
			};
		} else {
			element.onload = function () {
				if (callback && typeof callback === "function") {
					callback();
				}
			};
		}
		element[_attr] = url;
		s.parentNode.insertBefore(element, s.nextSibling);
	},
	loadScript = function (url, callback) {
		loadRemote(url, "js", callback);
	},
	loadCss = function (url, callback) {
		loadRemote(url, "css", callback);
	},
	isMSIE = function() {
		var ua = window.navigator.userAgent,
        	msie = ua.indexOf("MSIE ");

        if (msie !== -1) {
            return true;
        }

		return false;
	},
	getSessionId = function () {
		return sessionStorage.getItem("session_id") == null ? "" : sessionStorage.getItem("session_id");
	},
	createSessionId = function () {
		if(getSessionId()=="") {
			sessionStorage.setItem("session_id", "<?php echo session_id(); ?>");
		}
	},
	options = {
		server: "<?php echo PJ_INSTALL_URL; ?>",
		folder: "<?php echo PJ_INSTALL_URL; ?>",
		cid: <?php echo $controller->_get->toInt('cid'); ?>,
		view: <?php echo $controller->_get->check('view') && $controller->_get->toInt('view') > 0 ? $controller->_get->toInt('view') : 1; ?>,
		locale: <?php echo $controller->_get->check('locale') && $controller->_get->toInt('locale') > 0 ? $controller->_get->toInt('locale') : 'null'; ?>,
		index: <?php echo $controller->_get->check('index') && $controller->_get->toInt('index') > 0 ? $controller->_get->toInt('index') : 0; ?>,
		year: <?php echo $controller->_get->check('year') && preg_match('/^(19|20)\d{2}$/', $controller->_get->toInt('year')) ? $controller->_get->toInt('year') : date("Y"); ?>,
		month: <?php echo $controller->_get->check('month') && preg_match('/^(0?[1-9]|1[012])$/', $controller->_get->toInt('month')) ? $controller->_get->toInt('month') : date("n"); ?>,
		multi: 0,
				
		booking_behavior: <?php echo (int) @$tpl['option_arr']['o_booking_behavior']; ?>,
		price_based_on: "<?php echo @$tpl['option_arr']['o_price_based_on']; ?>",
		price_plugin: "<?php echo @$tpl['option_arr']['o_price_plugin']; ?>",
		accept_bookings: <?php echo (int) @$tpl['option_arr']['o_accept_bookings']; ?>,
		show_prices: <?php echo (int) @$tpl['option_arr']['o_show_prices']; ?>,
		week_start: <?php echo (int) @$tpl['option_arr']['o_week_start']; ?>,
		date_format: "<?php echo @$tpl['option_arr']['o_date_format']; ?>",
		thankyou_page: "<?php echo @$tpl['option_arr']['o_thankyou_page']; ?>",
		limits: <?php echo pjAppController::jsonEncode($tpl['limit_arr']); ?>,
		days: <?php echo pjAppController::jsonEncode($days); ?>,
		error_msg: <?php echo pjAppController::jsonEncode($front_err); ?>
	};
	<?php
	$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
	$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	?>
	loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('storage_polyfill'); ?>storagePolyfill.min.js", function () {
		if (isSafari) {
			createSessionId();
			options.session_id = getSessionId();
		}else{
			options.session_id = "";
		}
		loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_jquery'); ?>pjQuery.min.js", function () {
			window.pjQ.$.browser = {
				msie: isMSIE()
			};
			loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_validate'); ?>pjQuery.validate.min.js", function () {
				loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap'); ?>pjQuery.bootstrap.min.js", function () {
					<?php if($tpl['option_arr']['o_captcha_type_front'] == 'google'): ?>
				    loadScript('https://www.google.com/recaptcha/api.js', function () {
	                <?php endif; ?>
						loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH; ?>pjABCalendar.js", function () {
							ABCalendar_<?php echo $controller->_get->toInt('cid'); ?> = new ABCalendar(options);
						});
					<?php if($tpl['option_arr']['o_captcha_type_front'] == 'google'): ?>
	                });
				    <?php endif; ?>
				});
			});
		});
	});
})();
</script>