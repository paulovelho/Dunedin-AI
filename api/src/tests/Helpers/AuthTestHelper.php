<?php
namespace Dunedin\Tests\Helpers;

use Dunedin\AuthControl;
use ReflectionClass;

/**
 * AuthControl::$sessionInfo is private static, populated only by ValidateToken()
 * (which needs a real Firebase ID token). Tests inject it directly via reflection.
 */
class AuthTestHelper {
    public static function SetSessionUserId(int $userId): void {
        $prop = (new ReflectionClass(AuthControl::class))->getProperty("sessionInfo");
        $prop->setAccessible(true);
        $prop->setValue(null, (object)["user_id" => $userId]);
    }

    public static function ClearSession(): void {
        $prop = (new ReflectionClass(AuthControl::class))->getProperty("sessionInfo");
        $prop->setAccessible(true);
        $prop->setValue(null, null);
    }
}
