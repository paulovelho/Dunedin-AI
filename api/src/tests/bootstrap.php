<?php
require_once __DIR__ . '/../inc.php';

use Magrathea2\DB\Database;
use Magrathea2\Debugger;

// Tests never hit a real MariaDB — swap in the framework's DatabaseSimulate.
Database::Instance()->Mock();

// Default debug mode tries to log every thrown MagratheaException to disk
// (logs_path from magrathea.conf), which doesn't exist in the test runner.
Debugger::Instance()->SetType(Debugger::NONE);
