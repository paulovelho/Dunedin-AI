<?php

namespace Dunedin\Import;

use Magrathea2\MagratheaModelControl;
use Magrathea2\Exceptions\MagratheaApiException;

use Dunedin\File\File;
use Dunedin\Import\ImportServices\Kindle3Importer;

class ImportControl extends MagratheaModelControl {
	protected static $modelName      = "Import";
	protected static $modelNamespace = "Dunedin\\Import";
	protected static $dbTable        = "imports";

	public static function ProcessFile(File $file): Import {
		switch ($file->type) {
			case 'kindle3':
				return Kindle3Importer::Process($file);
			default:
				throw new MagratheaApiException("unsupported file type: {$file->type}", 422);
		}
	}
}
