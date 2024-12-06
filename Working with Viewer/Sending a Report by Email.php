<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Events\StiEmailEventArgs;
use Stimulsoft\Report\StiReport;
use Stimulsoft\StiResult;
use Stimulsoft\Viewer\StiViewer;


// Creating a viewer object and set the necessary javascript options
$viewer = new StiViewer();
$viewer->javascript->useRelativeUrls = false;
$viewer->javascript->appendHead('<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">');

// Defining viewer options: displaying the Send Email button
$viewer->options->toolbar->showSendEmailButton = true;

// If required, define default values for the email sending form.
$viewer->options->email->defaultEmailAddress = 'mail.recipient@stimulsoft.com';
$viewer->options->email->defaultEmailSubject = 'Default subject';
$viewer->options->email->defaultEmailMessage = 'Default message for email body.';

// Defining viewer events
// It is allowed to assign a PHP function, or the name of a JavaScript function, or a JavaScript function as a string
// Also it is possible to add several functions of different types using the append() method
$viewer->onEmailReport = function (StiEmailEventArgs $args) {

    // Defining the required options for sending (host, login, password), they will not be passed to the client side
    $args->settings->from = 'mail.sender@stimulsoft.com';
    $args->settings->host = 'smtp.stimulsoft.com';
    $args->settings->login = '********';
    $args->settings->password = '********';

    // These parameters are optional
    //$args->settings->name = 'John Smith';
    //$args->settings->port = 465;
    //$args->settings->secure = 'ssl';
    //$args->settings->cc[] = 'copy1@stimulsoft.com';
    //$args->settings->bcc[] = 'copy2@stimulsoft.com';
    //$args->settings->bcc[] = 'copy3@stimulsoft.com John Smith';

    // You can return a message about the successful sending of an email
    // If the message is not required, do not return the result
    // If an error occurred while sending an email, a message from the email sending module will be displayed
    return StiResult::getSuccess('The Email has been sent successfully.');
};

// Processing the request and, if successful, immediately printing the result
$viewer->process();

// Creating a report object
$report = new StiReport();

// Loading a report by URL
// This method does not load the report object on the server side, it only generates the necessary JavaScript code
// The report will be loaded into a JavaScript object on the client side
$report->loadFile('../reports/SimpleList.mrt');

// Assigning a report object to the viewer
$viewer->report = $report;

// Displaying the visual part of the viewer as a prepared HTML page
$viewer->printHtml();
