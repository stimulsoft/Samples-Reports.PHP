<?php

namespace Stimulsoft;

class StiRequest extends StiDataRequest
{
    public $sender;
    public $event;
    public $data;
    public $fileName;
    public $action;
    public $printAction;
    public $format;
    public $formatName;
    public $settings;
    public $variables;
    public $escapeQueryParameters;
    public $isWizardUsed;
    public $report;
    public $reportJson;

    protected function checkRequestParams($obj)
    {
        if (!isset($obj->event) && isset($obj->command) && ($obj->command == StiDataCommand::TestConnection || StiDataCommand::ExecuteQuery))
            $this->event = StiEventType::BeginProcessData;

        if (isset($obj->report)) {
            $this->report = $obj->report;
            $this->reportJson = json_encode($this->report, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return StiResult::success(null, $this);
    }
}