<?php

namespace Stimulsoft;

use Stimulsoft\Enums\Encoding;

class StiRequest extends StiBaseRequest
{

### Properties

    public $sender;
    public $data;
    public $fileName;
    public $action;
    public $printAction;
    public $format;
    public $formatName;
    public $settings;
    public $variables;
    public $isWizardUsed;
    public $report;
    public $autoSave;
    public $pageRange;
    public $reportType;


### Helpers

    protected function setProperty($name, $value)
    {
        if ($name == 'report' || $name == 'settings') {
            $this->$name = json_decode($value);

            if ($name == 'settings' && property_exists($this->settings, 'encoding')) {
                $encodingName = $this->settings->encoding->encodingName;
                $this->settings->encoding = Encoding::getByName($encodingName);
            }
        }
        else
            parent::setProperty($name, $value);
    }


    /*protected function checkRequestParams($obj)
    {
        if (!isset($obj->event) && isset($obj->command) &&
                ($obj->command == StiDataCommand::TestConnection || $obj->command == StiDataCommand::RetrieveSchema ||
                $obj->command == StiDataCommand::Execute || $obj->command == StiDataCommand::ExecuteQuery))
            $this->event = StiEventType::BeginProcessData;

        if (isset($obj->report)) {
            $this->report = $obj->report;
            $this->reportJson = json_encode($this->report, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return StiResult::success(null, $this);
    }*/
}