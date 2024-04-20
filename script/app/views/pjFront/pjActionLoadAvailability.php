<?php
$index = rand(1,99999);
?>
<div id="pjWrapperRPBCAvailability_<?php echo $index; ?>" class="abAvailability"></div>
<script type="text/javascript">
var pjQ = pjQ || {},
	ABCalendarAvailability_<?php echo $index; ?>;
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
			loadCssHack.call(null, url, callback);
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
		loadRemote.call(null, url, "js", callback);
	},
	loadCss = function (url, callback) {
		loadRemote.call(null, url, "css", callback);
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
	<?php
	$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
	$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	?>
	options = {
		server: "<?php echo PJ_INSTALL_URL; ?>",
		folder: "<?php echo PJ_INSTALL_URL; ?>",
		pj_jquery_url: "<?php echo PJ_INSTALL_URL . $dm->getPath('pj_validate'); ?>",
		index: <?php echo $index; ?>,
		locale: <?php echo $controller->_get->check('locale') && $controller->_get->toInt('locale') > 0 ? $controller->_get->toInt('locale') : 'null'; ?>,
		year: <?php echo $controller->_get->check('year') && preg_match('/^(19|20)\d{2}$/', $controller->_get->toInt('year')) ? $controller->_get->toInt('year') : date("Y"); ?>,
		month: <?php echo $controller->_get->check('month') && preg_match('/^(0?[1-9]|1[012])$/', $controller->_get->toInt('month')) ? $controller->_get->toInt('month') : date("n"); ?>
	};
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
			loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap'); ?>pjQuery.bootstrap.min.js", function () {
				<?php if($tpl['option_arr']['o_captcha_type_front'] == 'google'): ?>
			    loadScript('https://www.google.com/recaptcha/api.js', function () {
                <?php endif; ?>
					loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH; ?>pjABCalendar.Availability.js", function () {
						ABCalendarAvailability_<?php echo $index; ?> = new ABCalendarAvailability(options);
					});
				<?php if($tpl['option_arr']['o_captcha_type_front'] == 'google'): ?>
                });
			    <?php endif; ?>
			});
		});
	});
})();
</script>