<?php
namespace Dunedin\User;

use Magrathea2\MagratheaModelControl;

class UserControl extends MagratheaModelControl {
    protected static $modelName      = "User";
    protected static $modelNamespace = "Dunedin\\User";
    protected static $dbTable        = "users";
}
