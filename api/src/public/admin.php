<?php
use Magrathea2\Admin\AdminManager;

require __DIR__ . '/inc.php';

try {
    AdminManager::Instance()->Start(new \Dunedin\DunedinAdmin());
} catch (Exception $ex) {
    \Magrathea2\p_r($ex);
}
