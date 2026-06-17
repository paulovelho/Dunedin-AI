<?php
namespace Dunedin\Note;

use Magrathea2\MagratheaModelControl;

class NoteControl extends MagratheaModelControl {
    protected static $modelName      = "Note";
    protected static $modelNamespace = "Dunedin\\Note";
    protected static $dbTable        = "notes";
}
