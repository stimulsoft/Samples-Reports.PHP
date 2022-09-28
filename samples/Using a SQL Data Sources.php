<?php
require_once '../stimulsoft/helper.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
	<title>Using a SQL Data Sources</title>
	<style>html, body { font-family: sans-serif; }</style>

	<!-- Stimulsoft Reports.PHP scripts -->
	<script src="../scripts/stimulsoft.reports.js" type="text/javascript"></script>
	<script src="../scripts/stimulsoft.viewer.js" type="text/javascript"></script>
	
	<?php
		// Creating the events handler for this example
		// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_php_handler.htm
		StiHelper::init('Default Handler.php', 30);
	?>
	
	<script type="text/javascript">
		var options = new Stimulsoft.Viewer.StiViewerOptions();
		options.appearance.fullScreenMode = true;
		options.appearance.scrollbarsMode = true;
		options.toolbar.displayMode = Stimulsoft.Viewer.StiToolbarDisplayMode.Separated;
		options.height = "600px"; // Height for non-fullscreen mode
		
		var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);
		
		// Processing SQL data sources on the server-side
		// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_connecting_sql_data.htm
		viewer.onBeginProcessData = function (args, callback) {
			// Current data source name
			var dataSource = args.dataSource;
			// Connection string of the current data source
			var connectionString = args.connectionString;
			// SQL query string of the current data source
			var queryString = args.queryString;
			
			// Calling the server-side handler
			Stimulsoft.Helper.process(args);
		}
		
		var report = Stimulsoft.Report.StiReport.createNewReport();
		report.loadFile("../reports/SimpleListSQL.mrt");
		viewer.report = report;
		
		function onLoad() {
			viewer.renderHtml("viewerContent");
		}
	</script>
</head>
<body onload="onLoad();">
	<div id="viewerContent"></div>
</body>
</html>