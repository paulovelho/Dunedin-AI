<?php

// die;
require "../vendor/autoload.php";

// MAGRATHEA ADMIN:
Magrathea2\MagratheaPHP::Instance()->AppPath(realpath(dirname(__FILE__)))->Load();
Magrathea2\Admin\AdminManager::Instance()->StartDefault();
