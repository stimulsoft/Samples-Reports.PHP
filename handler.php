<?php
namespace Stimulsoft;

require_once 'vendor/autoload.php';

// You can configure the security level as you required.
// By default is to allow any requests from any domains.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token');
header('Cache-Control: no-cache');

$handler = new StiHandler();

/** @var $args StiVariablesEventArgs */
$handler->onPrepareVariables = function ($args)
{
    // You can change the values of the variables used in the report.
    // The new values will be passed to the report generator.
    /*
    $args->variables['VariableString']->value = 'Value from Server-Side';
    $args->variables['VariableDateTime']->value = '2020-01-31 22:00:00';

    $args->variables['VariableStringRange']->value->from = 'Aaa';
    $args->variables['VariableStringRange']->value->to = 'Zzz';

    $args->variables['VariableStringList']->value[0] = 'Test';
    $args->variables['VariableStringList']->value = ['1', '2', '2'];

    $args->variables['NewVariable'] = ['value' => 'New Value'];
    */

    // Values for 'Variables.mrt' report template.
    if (count($args->variables) > 0) {
        $args->variables['Name']->value = 'Maria';
        $args->variables['Surname']->value = 'Anders';
        $args->variables['Email']->value = 'm.anders@stimulsoft.com';
        $args->variables['Address']->value = 'Obere Str. 57, Berlin';
        $args->variables['Sex']->value = false;
        $args->variables['BirthDay']->value = '1982-03-20 00:00:00';
    }

    return StiResult::success();
};

/** @var $args StiDataEventArgs */
$handler->onBeginProcessData = function ($args)
{
    // You can change the connection string.
    /*
    if ($args->connection == 'MyConnectionName')
        $args->connectionString = 'Server=localhost;Database=test;uid=root;password=******;';
    */

    // You can change the SQL query.
    /*
    if ($args->dataSource == 'MyDataSource')
        $args->queryString = 'SELECT * FROM MyTable';
    */


    // You can change the SQL query parameters with the required values.
    // For example: SELECT * FROM @Parameter1 WHERE Id = @Parameter2 AND Date > @Parameter3
    /*
    if ($args->dataSource == 'MyDataSourceWithParams') {
        $args->parameters['Parameter1']->value = 'TableName';
        $args->parameters['Parameter2']->value = 10;
        $args->parameters['Parameter3']->value = '2019-01-20';
    }
    */

    // Values for 'SimpleListSQLParameters.mrt' report template.
    if ($args->dataSource == 'customers') {
        $args->parameters['Country']->value = "Germany";
    }

    // You can send a successful result.
    return StiResult::success();
    // You can send an informational message.
    //return StiResult::success('Some warning or other useful information.');
    // You can send an error message.
    //return StiResult::error('Message about any connection error.');
};

/** @var $args StiDataEventArgs */
$handler->onEndProcessData = function ($args)
{
    return StiResult::success();
};

/** @var $args StiExportEventArgs */
$handler->onPrintReport = function ($args)
{
    return StiResult::success();
};

/** @var $args StiExportEventArgs */
$handler->onBeginExportReport = function ($args)
{
    return StiResult::success();
};

/** @var $args StiExportEventArgs */
$handler->onEndExportReport = function ($args)
{
    // Getting the file name with the extension.
    $reportName = $args->fileName . '.' . $args->fileExtension;

    // By default, the exported file is saved to the 'reports' folder.
    // You can change this behavior if required.
    file_put_contents('reports/' . $reportName, base64_decode($args->data));

    //return StiResult::success();
    return StiResult::success("The exported report is saved successfully as $reportName");
    //return StiResult::error('An error occurred while exporting the report.');
};

/** @var $args StiExportEventArgs */
$handler->onEmailReport = function ($args)
{
    // These parameters will be used when sending the report by email. You must set the correct values.
    $args->emailSettings->from = '*****@gmail.com';
    $args->emailSettings->host = 'smtp.google.com';
    $args->emailSettings->login = '*****';
    $args->emailSettings->password = '*****';

    // These parameters are optional.
    //$args->emailSettings->name = 'John Smith';
    //$args->emailSettings->port = 465;
    //$args->emailSettings->cc[] = 'copy1@gmail.com';
    //$args->emailSettings->bcc[] = 'copy2@gmail.com';
    //$args->emailSettings->bcc[] = 'copy3@gmail.com John Smith';

    return StiResult::success('Email sent successfully.');
};

/** @var $args StiReportEventArgs */
$handler->onCreateReport = function ($args)
{
    // You can load a new report and send it to the designer.
    //$args->report = file_get_contents('reports/SimpleList.mrt');

    return StiResult::success();
};

/** @var $args StiReportEventArgs */
$handler->onSaveReport = function ($args)
{
    // Getting the correct file name of the report template.
    $reportFileName = strlen($args->fileName) > 0 ? $args->fileName : 'Report.mrt';
    if (strlen($reportFileName) < 5 || substr($reportFileName, -4) !== '.mrt')
        $reportFileName .= '.mrt';

    // For example, you can save a report to the 'reports' folder on the server-side.
    file_put_contents('reports/' . $reportFileName, $args->reportJson);

    //return StiResult::success();
    return StiResult::success('Report file saved successfully as ' . $args->fileName);
    //return StiResult::error('An error occurred while saving the report file on the server side.');
};

/** @var $args StiReportEventArgs */
$handler->onSaveAsReport = function ($args)
{
    // This event works the same as the 'onSaveReport' event.
    return StiResult::success();
};

// Process request
$handler->process();
