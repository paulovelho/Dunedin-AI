<?php
namespace App\Controls;

use Magrathea2\MagratheaModelControl;

class UserControl extends MagratheaModelControl {
    protected static $modelName      = "User";
    protected static $modelNamespace = "App\\Models";
    protected static $dbTable        = "users";
}
