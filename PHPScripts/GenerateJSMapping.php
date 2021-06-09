<?php

require_once __DIR__ . '/../vendor/autoload.php';

class GenerateJSMapping
{
	private string $rootPath;

	private array $classesGenned = array();

	public function __construct(private string $namespace)
	{
		$this->namespace = \trim($this->namespace, '\\/');
		$this->rootPath = \getcwd();
	}

	/**
	 * @param string $baseClassName with no namespace
	 */
	public function makeClasses(array $structure, string $baseClassName) : void
	{
		$this->classesGenned = array();
		$dir = $this->getCleanPath($this->namespace);

		if (! \is_dir($dir)) {
			\mkdir($dir, recursive : true);
		}
		$this->makeClass($structure, $baseClassName);
	}

	public function getCleanPath(string $namespace) : string
	{
		return \str_replace('\\', '/', $this->rootPath . '/' . $namespace);
	}

	public function setRootPath(string $rootPath) : self
	{
		$this->rootPath = $rootPath;
	}

	/**
	 * Make a class and return class name with namespace
	 *
	 * @param string $classname with no namespace
	 */
	private function makeClass(array $structure, string $classname) : string
	{
		$fieldTypes = array();
		$fieldDefaults = array();

		foreach ($structure as $name => $element) {
			$type = \gettype($element);

			if (\str_ends_with($name, 'Color')) {
				$type = $this->namespace . '\\Color';
				$fieldDefaults[$name] = $element;
			}

			if ('array' == $type) {
				$fieldTypes[$name] = $this->makeClass($element, $this->getUniqueClassName($name));
			// no default types for objects
			} else {
				$fieldTypes[$name] = $type;
				$fieldDefaults[$name] = $element;
			}
		}

		$this->createClass($classname, $fieldTypes, $fieldDefaults);

		return $this->namespace . '\\' . $classname;
	}

	/**
	 * @param string $classname with no namespace
	 */
	private function createClass(string $classname, array $fieldTypes, array $fieldDefaults) : void
	{
		$file = <<<'PHP'
<?php

namespace ~namespace~;

class ~classname~ extends Base
{

	protected static $validFields = ~validFields~;

	protected static $defaults = ~defaults~;

}
PHP;
		$file = \str_replace(
			array('~namespace~', '~classname~', '~validFields~', '~defaults~'),
			array($this->namespace, $classname, $this->export($fieldTypes), $this->export($fieldDefaults)),
			$file
		);

		$fileName = $this->getCleanPath($this->namespace . '\\' . $classname . '.php');

		\file_put_contents($fileName, $file);

		echo "Generating {$fileName}\n";
	}

	private function export(array $fields) : string
	{
		\ksort($fields);

		$php = \var_export($fields, true);

		return \str_replace(array('array (', ')', '  '), array('[', "\t\t]", "\t\t"), $php);
	}

	/**
	 * Make a name unique class name based on what came before
	 *
	 * @param string $classname with no namespace
	 *
	 * @return string class name with no namespace
	 */
	private function getUniqueClassName(string $classname) : string
	{
		$prefix = '';
		$classname = \ucfirst($classname);

		if (isset($this->classesGenned[$classname])) {
			$prefix = 'Viewer';
		}

		$this->classesGenned[$prefix . $classname] = true;

		return $prefix . $classname;
	}
}

// Define the cli options.
$cli = new \Garden\Cli\Cli();

echo "Generate classes based on JavaScript object. -? for help.\n\n";
$title = <<<TEXT
This program converts a JavaScript object into PHP type safe classes.

Steps to run:

    1. Send object in json format to browser console with "console.log(JSON.stringify(options));".
    2. Copy expanded lines and save in file.
    3. Run this program passing the file as -f <filename>
    4. If you do not specify a namespace, it will default to App\JavaScript.

TEXT;

$cli->description($title)
	->opt('file:f', 'Input file exported from FireFox.', true, 'string')
	->opt('namespace:n', 'Namespace (path) to use.', false, 'string')
	->opt('classname:c', 'Base class name to use.', false, 'string');

// Parse and return cli args.
$args = $cli->parse($argv, true);

$file = $args['file'];
$className = $args->getOpt('classname', 'StiDesignerOptions');

if (! \file_exists($file)) {
	echo "File {$file} was not found.\n";

	exit;
}

$namespace = \trim($args->getOpt('namespace', 'App\JavaScript'), '/\\');

$json = \file_get_contents($file);

try {
	$structure = \json_decode($json, true, flags : JSON_THROW_ON_ERROR);
} catch (\Throwable $e) {
	echo "JSON in file {$file} is not valid. ERROR: {$e->getMessage()}\n";

	exit;
}

$classifer = new GenerateJSMapping($namespace);
$classifer->makeClasses($structure, $className);
