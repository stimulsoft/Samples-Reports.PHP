<?php
require_once 'stimulsoft/helper.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<title>Stimulsoft Reports.PHP - Designer</title>
	<style>html, body { font-family: sans-serif; }</style>

	<!-- Office2013 White-Teal style -->
	<link href="css/stimulsoft.viewer.office2013.whiteblue.css" rel="stylesheet">
	<link href="css/stimulsoft.designer.office2013.whiteblue.css" rel="stylesheet">

	<!-- Stimulsoft Reports.JS -->
	<script src="scripts/stimulsoft.reports.js" type="text/javascript"></script>
	
	<!-- Stimulsoft Dashboards.JS -->
	<script src="scripts/stimulsoft.dashboards.js" type="text/javascript"></script>
	
	<!-- Stimulsoft JS Viewer (for preview tab) and Stimulsoft JS Designer-->
	<script src="scripts/stimulsoft.viewer.js" type="text/javascript"></script>
	<script src="scripts/stimulsoft.designer.js" type="text/javascript"></script>
	
	<?php
		// Add JavaScript helpers and init options to work with the PHP server
		// You can change the handler file and timeout if required
		StiHelper::init('handler.php', 30);
	?>
	
	<script type="text/javascript">
		// Create and set options.
		// More options can be found in the documentation at the link:
		// https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_designer_settings.htm
		var options = new Stimulsoft.Designer.StiDesignerOptions();
		options.appearance.fullScreenMode = true;
		
		// Create Designer component.
		// A description of the parameters can be found in the documentation at the link:
		// https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_designer_add_designer.htm
		var designer = new Stimulsoft.Designer.StiDesigner(options, "StiDesigner", false);
		
		// Optional Designer events for fine tuning. You can uncomment and change any event or all of them, if necessary.
		// In this case, the built-in handler will be overridden by the selected event.
		// You can read and, if necessary, change the parameters in the args before server-side handler.
		
		// All events and their details can be found in the documentation at the link:
		// https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_designer_designer_events.htm
		
		
		/*
		
		// Process SQL data sources. It can be used if it is necessary to correct the parameters of the data request.
		designer.onBeginProcessData = function (args, callback) {
			
			// Call the server-side handler
			Stimulsoft.Helper.process(args, callback);
		}
		
		*/
		
		/*
		
		// Save report template on the server side.
		designer.onSaveReport = function (args, callback) {
			
			// Call the server-side handler
			Stimulsoft.Helper.process(args, callback);
		}
		
		*/
		
		// Create a report and load a template from an MRT file:
		var report = new Stimulsoft.Report.StiReport();
		report.loadFile("reports/SimpleList.mrt");
		
		// Assigning a report to the Designer:
		designer.report = report;
		
		// After loading the HTML page, display the visual part of the Designer in the specified container.
		function onLoad() {
			designer.renderHtml("designerContent");
		}
	</script>
</head>
<body onload="onLoad();">
	<div id="designerContent"></div>
</body>
</html>
