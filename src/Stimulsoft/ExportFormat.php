<?php

namespace Stimulsoft;

class ExportFormat
	{
	const Pdf = 1;

	const Text = 11;

	const Excel2007 = 14;

	const Word2007 = 15;

	const Csv = 17;

	const ImageSvg = 28;

	const Html = 32;

	const Ods = 33;

	const Odt = 34;

	const Ppt2007 = 35;

	const Html5 = 36;

	const Document = 1000;

	/**
	 * @return array of valid format numbers, indexed by format name
	 */
	public static function getFormatNumbers()
		{
		$reflection = new \ReflectionClass(__CLASS__);

		return $reflection->getConstants();
		}

	/**
	 * @return array of file extensions, indexed by format name
	 */
	public static function getFormatExtensions()
		{
		return [
			'Pdf' => 'pdf',
			'Text' => 'txt',
			'Excel2007' => 'xlsx',
			'Word2007' => 'docx',
			'Csv' => 'csv',
			'ImageSvg' => 'svg',
			'Html' => 'html',
			'Ods' => 'ods',
			'Odt' => 'odt',
			'Ppt2007' => 'pptx',
			'Html5' => 'html',
			'Document' => 'doc'
			];
		}

	}
