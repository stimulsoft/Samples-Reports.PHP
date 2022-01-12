<?php
require_once '../stimulsoft/helper.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
	<title>Exporting a Report from Code</title>
	<style>html, body { font-family: sans-serif; }</style>

	<!-- Stimulsoft Reports.PHP scripts -->
	<script src="../scripts/stimulsoft.reports.js" type="text/javascript"></script>
	
	<?php
		// Creating the default events handler
		// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_php_handler.htm
		StiHelper::init('Default Handler.php', 30);
	?>
	
	<script type="text/javascript">
		// Creating and loading the report template
		var report = Stimulsoft.Report.StiReport.createNewReport();
		report.loadFile("../reports/SimpleList.mrt");
		
		// Getting the report file name
		var fileName = Stimulsoft.System.StiString.isNullOrEmpty(report.reportAlias) ? report.reportName : report.reportAlias;
		
		// Exporting report to PDF format and saving to a file
		// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_export_from_code.htm
		function exportToPdf() {
			report.renderAsync(function() {
				report.exportDocumentAsync(function (data) {
					// Saving data to a file
					Stimulsoft.System.StiObject.saveAs(data, fileName + ".pdf", "application/pdf");
				}, Stimulsoft.Report.StiExportFormat.Pdf);
			});
		}
		
		// Exporting report to Excel format and saving to a file
		// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_export_from_code.htm
		function exportToExcel() {
			report.renderAsync(function() {
				report.exportDocumentAsync(function (data) {
					// Saving data to a file
					Stimulsoft.System.StiObject.saveAs(data, fileName + ".xlsx", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				}, Stimulsoft.Report.StiExportFormat.Excel2007);
			});
		}
		
		// Exporting report to HTML format and saving to a file
		// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_export_from_code.htm
		function exportToHtml() {
			report.renderAsync(function() {
				report.exportDocumentAsync(function (data) {
					// Saving data to a file
					Stimulsoft.System.StiObject.saveAs(data, fileName + ".html", "text/html");
				}, Stimulsoft.Report.StiExportFormat.Html);
			});
		}
	</script>
</head>
<body>
	<button onclick="exportToPdf();">Export Report to PDF</button>
	<br /><br />
	<button onclick="exportToExcel();">Export Report to Excel</button>
	<br /><br />
	<button onclick="exportToHtml();">Export Report to HTML</button>
</body>
</html>