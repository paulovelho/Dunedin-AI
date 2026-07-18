<?php
require_once __DIR__ . '/../Helpers/AuthTestHelper.php';

use Dunedin\Tests\Helpers\AuthTestHelper;
use Dunedin\SharedLink\SharedLinkApiControl;
use Magrathea2\Exceptions\MagratheaApiException;

class SharedLinkApiControlTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        AuthTestHelper::SetSessionUserId(1);
        $_POST = [];
        $_PUT  = [];
    }

    protected function tearDown(): void {
        AuthTestHelper::ClearSession();
    }

    public function testCreateThrowsWhenHighlightIdsMissing(): void {
        $api = new SharedLinkApiControl();
        $_POST = ["description" => "no highlights here"];

        $this->expectException(MagratheaApiException::class);
        $this->expectExceptionCode(400);
        $api->Create();
    }

    public function testCreateThrowsWhenHighlightIdsNotOwnedByUser(): void {
        // DatabaseSimulate always returns an empty/null result, so the
        // ownership-count check never matches the requested id count.
        $api = new SharedLinkApiControl();
        $_POST = ["highlight_ids" => [1, 2, 3]];

        $this->expectException(MagratheaApiException::class);
        $this->expectExceptionCode(400);
        $api->Create();
    }

    public function testUpdateThrowsNotFoundForUnknownLink(): void {
        $api = new SharedLinkApiControl();
        $_PUT = ["active" => false];

        $this->expectException(MagratheaApiException::class);
        $this->expectExceptionCode(404);
        $api->Update(["id" => 999]);
    }

    public function testDeleteThrowsNotFoundForUnknownLink(): void {
        $api = new SharedLinkApiControl();

        $this->expectException(MagratheaApiException::class);
        $this->expectExceptionCode(404);
        $api->Delete(["id" => 999]);
    }

    public function testCropDescriptionLeavesShortTextUntouched(): void {
        $api    = new SharedLinkApiControl();
        $method = new \ReflectionMethod($api, "CropDescription");
        $method->setAccessible(true);

        $short = "a short highlight";
        $this->assertEquals($short, $method->invoke($api, $short));
    }

    public function testCropDescriptionCropsLongTextAndAppendsEllipsis(): void {
        $api    = new SharedLinkApiControl();
        $method = new \ReflectionMethod($api, "CropDescription");
        $method->setAccessible(true);

        $long   = str_repeat("x", 150);
        $result = $method->invoke($api, $long);

        $this->assertEquals(103, mb_strlen($result)); // 100 chars + "..."
        $this->assertStringEndsWith("...", $result);
        $this->assertEquals(str_repeat("x", 100), mb_substr($result, 0, 100));
    }
}
