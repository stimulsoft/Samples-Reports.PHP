<?php

namespace Stimulsoft;

abstract class OptionsBase
{
	protected static $validFields = array();

	protected $varName = '';

	protected $className = '';

	protected static $defaults = array();

	private $data = array();

	private $setFields = array();

	private $myDefaults = array();

	private static $scalars = array(
		'boolean' => true,
		'double' => true,
		'integer' => true,
		'string' => true,
	);

	/**
	 * You can constuct from an array with full type safety
	 *
	 * @param array $data key => value pairs
	 * @param bool $defaultValues set default values to $data if true.
	 */
	public function __construct($data = array(), $defaultValues = false)
	{
		$this->myDefaults = static::$defaults;

		if ($defaultValues) {
			$this->myDefaults = \array_merge($this->myDefaults, $data);
		}

		foreach (static::$validFields as $field => $type) {
			if (! is_array($type) && ! isset(self::$scalars[$type])) {
				if (isset($this->myDefaults[$field]) && \is_array($this->myDefaults[$field])) {
					$this->data[$field] = new $type($this->myDefaults[$field], true);
				} else {
					$this->data[$field] = new $type();
				}
			}
		}

		foreach ($data as $name => $value) {
			$this->__set($name, $value);
		}

		// if values should be default, we won't keep track of just setting them now.
		if ($defaultValues) {
			$this->setFields = array();
		}
	}

	/**
	 * Unset fields will return default value
	 *
	 * @param string $field name of the field
	 *
	 */
	public function __get($field)
	{
		if (! isset(static::$validFields[$field])) {
			throw new \Exception("{$field} is not a valid field for " . get_class($this));
		}

		$this->setFields[$field] = true;

		if (! isset($this->data[$field])) {
			return $this->myDefaults[$field];
		}

		return $this->data[$field];
	}

	/**
	 * set a value
	 *
	 * @param string $field name to be set
	 * @param mixed $value to be set
	 *
	 * @return mixed to allow for chaining assignments
	 */
	public function __set($field, $value)
	{
		if (! isset(static::$validFields[$field])) {
			throw new \Exception("{$field} is not a valid field for " . get_class($this));
		}
		$expectedType = static::$validFields[$field];
		$type = \gettype($value);

		if ('object' == $type) {
			$type = \get_class($value);
		}

		if (\is_array($expectedType)) {
			if (false === ($index = \array_search($value, $expectedType)) || empty($value)) {
				throw new \Exception("{$field} is {$value} but must be one of " . \implode(', ', $expectedType) . ' for ' . get_class($this));
			} else {
				$value = $index;
			}
		} elseif ($expectedType != $type) {
			throw new \Exception("{$field} is of type {$type} but should be type {$expectedType} for " . get_class($this));
		}

		$this->setFields[$field] = true;

		return $this->data[$field] = $value;
	}

	/**
	 * return the JavaScript that represents this object;
	 *
	 * @return string JavaScript assignment statements
	 */
	public function __toString()
	{
		$js = '';

		if ($this->varName) {
			$js = 'var ' . $this->varName . ' = new ' . $this->className . "();\n";
		}

		$js .= $this->getJavaScript($this->varName, $this);

		return $js;
	}

	/**
	 * get a scalar type
	 *
	 * @param string $name to get, can include dots(.) for JavaScript
	 * @param mixed $value to dump to JavaScript
	 *
	 * @return string JavaScript assignment statement with trailing semicolin
	 */
	private function getField($name, $value)
	{
		$js = $name . ' = ';

		$type = \gettype($value);

		if ('string' == $type) {
			$js .= '"' . \str_replace('"', '\"', $value) . '"';
		} elseif ('boolean' == $type) {
			$js .= $value ? 'true' : 'false';
		} elseif ('NULL' == $type) {
			$js .= 'null';
		} else {
			$js .= $value;
		}
		$js .= ";\n";

		return $js;
	}

	/**
	 * return the JavaScript that represents the object
	 *
	 * @param string $name of the JavaScript variable to be output
	 * @param Stimulsoft\OptionsBase $object to dump to JavaScript
	 *
	 * @return string JavaScript assignment statements to $name
	 */
	private function getJavaScript($name, \Stimulsoft\OptionsBase $object)
	{
		$js = '';

		foreach ($object->data as $fieldName => $data) {
			if ('object' == \gettype($data)) {
				$js .= $this->getJavaScript($name . '.' . $fieldName, $data);
			} elseif (! isset($object->myDefaults[$fieldName]) || $object->myDefaults[$fieldName] !== $data) {
				$js .= $this->getField($name . '.' . $fieldName, $data);
			}
		}

		return $js;
	}
}
