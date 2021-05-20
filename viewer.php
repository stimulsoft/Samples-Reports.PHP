<?php
require_once 'stimulsoft/helper.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<title>Stimulsoft Reports.PHP - Viewer</title>
	<style>html, body { font-family: sans-serif; }</style>

	<!-- Office2013 White-Teal style -->
	<link href="css/stimulsoft.viewer.office2013.whiteblue.css" rel="stylesheet">

	<!-- Stimulsoft Reports.JS -->
	<script src="scripts/stimulsoft.reports.js" type="text/javascript"></script>
	
	<!-- Stimulsoft Dashboards.JS -->
	<script src="scripts/stimulsoft.dashboards.js" type="text/javascript"></script>
	
	<!-- Stimulsoft JS Viewer -->
	<script src="scripts/stimulsoft.viewer.js" type="text/javascript"></script>
	
	<?php
		// Add JavaScript helpers and init options to work with the PHP server
		// You can change the handler file and timeout if required
		StiHelper::init('handler.php', 30);
	?>
	
	<script type="text/javascript">
		// Create and set options.
		// More options can be found in the documentation at the link:
		// https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_viewer_settings.htm
		var options = new Stimulsoft.Viewer.StiViewerOptions();
		options.toolbar.showSendEmailButton = true;
		options.toolbar.displayMode = Stimulsoft.Viewer.StiToolbarDisplayMode.Separated;
		options.appearance.fullScreenMode = true;
		options.appearance.scrollbarsMode = true;
		options.height = "600px"; // Height for non-fullscreen mode
		
		// Create Viewer component.
		// A description of the parameters can be found in the documentation at the link:
		// https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_viewer_showing_reports.htm
		var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);
		
		// Optional Viewer events for fine tuning. You can uncomment and change any event or all of them, if necessary.
		// In this case, the built-in handler will be overridden by the selected event.
		// You can read and, if necessary, change the parameters in the args before server-side handler.
		
		// All events and their details can be found in the documentation at the link:
		// https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_viewer_viewer_events.htm
		
		
		/*
		
		// Process report variables before rendering.
		viewer.onPrepareVariables = function (args, callback) {
			
			// Call the server-side handler
			Stimulsoft.Helper.process(args, callback);
		}
		
		*/
		
		/*
		
		// Process SQL data sources. It can be used if it is necessary to correct the parameters of the data request.
		viewer.onBeginProcessData = function (args, callback) {
			
			// Call the server-side handler
			Stimulsoft.Helper.process(args, callback);
		}
		
		*/
		
		/*
		
		// Manage export settings and, if necessary, transfer them to the server and manage there
		viewer.onBeginExportReport = function (args, callback) {
			
			// Call the server-side handler
			Stimulsoft.Helper.process(args, callback);
			
			// Manage export settings
			// args.fileName = "MyReportName";
		}
		
		*/
		
		/*
		
		// Process exported report file on the server side
		viewer.onEndExportReport = function (args) {
			
			// Prevent built-in handler (save the exported report as a file)
			args.preventDefault = true;
			
			// Call the server-side handler
			Stimulsoft.Helper.process(args);
		}
		
		*/
		
		/*
		
		// Send exported report to Email
		viewer.onEmailReport = function (args) {
			
			// Call the server-side handler
			Stimulsoft.Helper.process(args);
		}
		
		*/
		
		// Create a report and load a template from an MRT file:
		var report = new Stimulsoft.Report.StiReport();
		report.loadFile("reports/SimpleList.mrt");
		
		// Assigning a report to the Viewer:
		viewer.report = report;
		
		// After loading the HTML page, display the visual part of the Viewer in the specified container.
		function onLoad() {
			viewer.renderHtml("viewerContent");
		}
	</script>
</head>
<body onload="onLoad();">
	<div id="viewerContent"></div>
</body>
</html>
