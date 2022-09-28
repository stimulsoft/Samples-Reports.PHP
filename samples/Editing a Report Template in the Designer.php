<?php
require_once '../stimulsoft/helper.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
	<title>Editing a Report Template in the Designer</title>
	<style>html, body { font-family: sans-serif; }</style>

	<!-- Stimulsoft Reports.PHP scripts -->
	<script src="../scripts/stimulsoft.reports.js" type="text/javascript"></script>
	<script src="../scripts/stimulsoft.viewer.js" type="text/javascript"></script>
	<script src="../scripts/stimulsoft.designer.js" type="text/javascript"></script>
	
	<!-- Stimulsoft Blockly editor for JS Designer -->
	<script src="../scripts/stimulsoft.blockly.editor.js" type="text/javascript"></script>
	
	<?php
		// Creating the default events handler
		// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_php_handler.htm
		StiHelper::init('Default Handler.php', 30);
	?>
	
	<script type="text/javascript">
		// Creating the Designer options
		// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_designer_settings.htm
		var options = new Stimulsoft.Designer.StiDesignerOptions();
		options.appearance.fullScreenMode = true;
		
		// Creating the Designer control
		// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_designer_deployment.htm
		var designer = new Stimulsoft.Designer.StiDesigner(options, "StiDesigner", false);
		
		// Creating, loading, and then assigning the report template to the Designer
		// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_designer_creating_editing_report.htm
		var report = Stimulsoft.Report.StiReport.createNewReport();
		report.loadFile("../reports/SimpleList.mrt");
		designer.report = report;
		
		function onLoad() {
			designer.renderHtml("designerContent");
		}
	</script>
</head>
<body onload="onLoad();">
	<div id="designerContent"></div>
</body>
</html>