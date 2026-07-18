<?php
use Dunedin\SharedLink\SharedLinkPublicApiControl;
use Magrathea2\Exceptions\MagratheaApiException;

class SharedLinkPublicApiControlTest extends \PHPUnit\Framework\TestCase {

    public function testReadThrowsNotFoundForUnknownUuid(): void {
        $api = new SharedLinkPublicApiControl();

        $this->expectException(MagratheaApiException::class);
        $this->expectExceptionCode(404);
        $api->Read(["uuid" => "00000000-0000-0000-0000-000000000000"]);
    }

    public function testVisitThrowsNotFoundForUnknownUuid(): void {
        $api = new SharedLinkPublicApiControl();

        $this->expectException(MagratheaApiException::class);
        $this->expectExceptionCode(404);
        $api->Visit(["uuid" => "00000000-0000-0000-0000-000000000000"]);
    }
}
