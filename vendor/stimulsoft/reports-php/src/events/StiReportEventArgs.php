<?php

namespace Stimulsoft\Events;

use Stimulsoft\StiComponent;
use Stimulsoft\StiHandler;

class StiReportEventArgs extends StiEventArgs
{

### Properties

    /** @var object The current report JSON object with the set of all properties. */
    public $report;

    /** @var string The name of the report file to save. */
    public $fileName;

    /** @var bool A flag indicating that the wizard was used when creating the report. */
    public $isWizardUsed;

    /** @var bool A flag indicating that the report was saved automatically. */
    public $autoSave;

    /**
     * @var string The current report object as a JSON string.
     * @deprecated Please use the '$args->getReportJson()' method.
     */
    public $reportJson;


### Helpers

    protected function getHandler()
    {
        if ($this->sender instanceof StiHandler)
            return $this->sender;

        if ($this->sender instanceof StiComponent)
            return $this->sender->handler;

        return null;
    }

    protected function setProperty($name, $value)
    {
        parent::setProperty($name, $value);

        if ($name == 'report') {
            $this->report = clone $value;

            if (StiHandler::$legacyMode)
                $this->reportJson = $this->getReportJson();
        }
    }

    /** @return string|bool Report as a JSON string, or false if the conversion is unsuccessful. */
    public function getReportJson()
    {
        return json_encode($this->report, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /** @param string $value Report as a JSON string. */
    public function setReportJson(string $value): bool
    {
        $report = json_decode($value);
        if ($report !== null) {
            $this->report = $report;
            return true;
        }

        return false;
    }
}