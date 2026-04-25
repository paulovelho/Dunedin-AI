<?php
namespace App\Controls;

use Magrathea2\MagratheaModelControl;

class NoteControl extends MagratheaModelControl {
    protected static $modelName      = "Note";
    protected static $modelNamespace = "App\\Models";
    protected static $dbTable        = "notes";
}
