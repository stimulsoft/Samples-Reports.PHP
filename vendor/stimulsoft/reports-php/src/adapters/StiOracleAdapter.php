<?php

namespace Stimulsoft\Adapters;

use Stimulsoft\StiDataResult;
use Stimulsoft\StiResult;

class StiOracleAdapter extends StiDataAdapter
{
    public $version = '2023.4.3';
    public $checkVersion = true;

    protected $driverName = 'oci';

    protected function getLastErrorResult($message = 'An unknown error has occurred.')
    {
        if ($this->driverType == 'PDO')
            return parent::getLastErrorResult($message);

        $code = 0;
        $error = oci_error();
        if ($error !== false) {
            $code = $error['code'];
            $error = $error['message'];
        }

        if ($error) $message = $error;

        return $code == 0 ? StiResult::error($message) : StiResult::error("[$code] $message");
    }

    protected function connect()
    {
        if ($this->driverType == 'PDO')
            return parent::connect();

        if (!function_exists('oci_connect'))
            return StiResult::error('Oracle driver not found. Please configure your PHP server to work with Oracle.');

        if ($this->connectionInfo->privilege == '')
            $this->connectionLink = oci_connect(
                $this->connectionInfo->userId, $this->connectionInfo->password, $this->connectionInfo->database,
                $this->connectionInfo->charset);
        else
            $this->connectionLink = oci_pconnect(
                $this->connectionInfo->userId, $this->connectionInfo->password, $this->connectionInfo->database,
                $this->connectionInfo->charset, $this->connectionInfo->privilege);

        if (!$this->connectionLink)
            return $this->getLastErrorResult();

        return StiDataResult::success();
    }

    protected function disconnect()
    {
        if ($this->driverType == 'PDO')
            parent::disconnect();
        else if ($this->connectionLink) {
            oci_close($this->connectionLink);
            $this->connectionLink = null;
        }
    }

    public function parse($connectionString)
    {
        if (parent::parse($connectionString))
            return true;

        $this->connectionInfo->port = 3306;
        $this->connectionInfo->charset = 'AL32UTF8';

        $parameterNames = array(
            'database' => ['database', 'data source', 'dbname'],
            'userId' => ['uid', 'user', 'user id'],
            'password' => ['pwd', 'password'],
            'charset' => ['charset']
        );

        return $this->parseParameters($parameterNames);
    }

    protected function parseUnknownParameter($parameter, $name, $value)
    {
        parent::parseUnknownParameter($parameter, $name, $value);

        if ($name == 'dba privilege' || $name == 'privilege') {
            $value = strtolower($value);
            $this->connectionInfo->privilege = OCI_DEFAULT;
            if ($value == 'sysoper' || $value == 'oci_sysoper') $this->connectionInfo->privilege = OCI_SYSOPER;
            if ($value == 'sysdba' || $value == 'oci_sysdba') $this->connectionInfo->privilege = OCI_SYSDBA;
        }
    }

    protected function parseType($meta, $extended = false)
    {
        switch ($meta) {
            case 'SMALLINT':
            case 'INTEGER':
            case 'BIGINT':
                return 'int';

            case 'NUMBER':
                return 'number';

            case 'DATE':
            case 'TIMESTAMP':
                return 'datetime';

            case 'CHAR':
            case 'VARCHAR2':
            case 'INTERVAL DAY TO SECOND':
            case 'INTERVAL YEAR TO MONTH':
            case 'ROWID':
                return 'string';

            case 'BFILE':
            case 'BLOB':
                return $extended ? 'blob' : 'array';

            case 'LONG':
            case 'CLOB':
                return $extended ? 'clob' : 'string';

            case 'RAW':
            case 100:
            case 101:
                return 'array';
        }

        return 'string';
    }

    protected function getValue($type, $value)
    {
        if (is_null($value))
            return null;

        if ($type == 'blob' || $type == 'clob') {
            try {
                $data = $value->load();
                return $type == 'blob' ? base64_encode($data) : $data . "\n";
            }
            catch (\Exception $e) {
                return null;
            }
        }

        if (strlen($value) == 0)
            return null;

        switch ($type) {
            case 'array':
                return base64_encode($value);

            case 'datetime':
                $dateTime = \DateTime::createFromFormat("d#M#y H#i#s*A", $value);
                if ($dateTime !== false) $format = $dateTime->format("Y-m-d\TH:i:s.v");
                else {
                    $timestamp = strtotime($value);
                    $format = date("Y-m-d\TH:i:s.v", $timestamp);
                }
                if (strpos($format, '.v') > 0) $format = date("Y-m-d\TH:i:s.000", $timestamp);
                return $format;
        }

        return $value;
    }

    public function makeQuery($procedure, $parameters)
    {
        $paramsString = parent::makeQuery($procedure, $parameters);
        return "SQLEXEC 'CALL $procedure ($paramsString)'";
    }

    protected function executeNative($queryString, $result)
    {
        $query = oci_parse($this->connectionLink, $queryString);
        if (!$query || !oci_execute($query))
            return $this->getLastErrorResult();

        $result->count = oci_num_fields($query);
        $types = array();

        for ($i = 1; $i <= $result->count; $i++) {
            $name = oci_field_name($query, $i);
            $result->columns[] = $name;

            $type = oci_field_type($query, $i);
            $result->types[] = $this->parseType($type);
            $types[] = $this->parseType($type, true);
        }

        while ($rowItem = oci_fetch_assoc($query)) {
            $row = array();
            foreach ($rowItem as $key => $value) {
                if (count($result->columns) < count($rowItem)) $result->columns[] = $key;
                $type = $types[count($row)];
                $row[] = $this->getValue($type, $value);
            }
            $result->rows[] = $row;
        }

        return $result;
    }

    protected function executePDO($queryString, $result)
    {
        // PDO Oracle driver doesn't support getColumnMeta()
        // The type is determined by the first value

        return $this->executePDOv2($queryString, $result);
    }
}