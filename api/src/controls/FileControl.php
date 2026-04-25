<?php
namespace App\Controls;

use Magrathea2\MagratheaModelControl;

class FileControl extends MagratheaModelControl {
    protected static $modelName      = "File";
    protected static $modelNamespace = "App\\Models";
    protected static $dbTable        = "files";
}
