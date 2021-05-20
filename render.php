<?php
require_once 'stimulsoft/helper.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<title>Stimulsoft Reports.PHP - Render &amp; Export</title>
	<style>html, body { font-family: sans-serif; }</style>

	<!-- Stimulsoft Reports.JS -->
	<script src="scripts/stimulsoft.reports.js" type="text/javascript"></script>
	
	<?php
		// Add JavaScript helpers and init options to work with the PHP server
		// You can change the handler file and timeout if required
		StiHelper::init('handler.php', 30);
	?>
	
	<script type="text/javascript">
		function onLoad() {
			// Create a report and load a template from an MRT file:
			var report = new Stimulsoft.Report.StiReport();
			report.loadFile("reports/SimpleList.mrt");
			
			// Optional Engine event for fine tuning. You can uncomment and change it, if necessary.
			// In this case, the built-in handler will be overridden by this event.
			
			/*
		
			// Process SQL data sources. It can be used if it is necessary to correct the parameters of the data request.
			report.onBeginProcessData = function (args, callback) {
				
				// Call the server-side handler
				Stimulsoft.Helper.process(args, callback);
			}
			
			*/
			
			report.renderAsync(function() {
				
				// Export rendered document to the specified format
				report.exportDocumentAsync(function (pdfData) {
					
					// Get report file name
					var fileName = Stimulsoft.System.StiString.isNullOrEmpty(report.reportAlias) ? report.reportName : report.reportAlias;
					// Save data to file
					Stimulsoft.System.StiObject.saveAs(pdfData, fileName + ".pdf", "application/pdf");
					
				}, Stimulsoft.Report.StiExportFormat.Pdf);
			});
		}
	</script>
	</head>
<body onload="onLoad();">
	Render & Export
</body>
</html>
