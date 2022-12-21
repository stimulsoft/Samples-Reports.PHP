<?php

namespace Stimulsoft;

class StiReportEventArgs extends StiEventArgs
{
    /** @var object The current report object with the set of all properties. */
    public $report;

    /** @var string The current report object as a JSON string. */
    public $reportJson;

    /** @var string The name of the report file to save. */
    public $fileName;

    /** @var bool A flag indicating that the wizard was used when creating the report. */
    public $isWizardUsed;
}