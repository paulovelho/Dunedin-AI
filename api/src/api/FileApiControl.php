<?php
namespace App\Api;

use Magrathea2\MagratheaApiControl;
use Magrathea2\Exceptions\MagratheaApiException;

use App\Controls\FileControl;
use App\Models\File;

class FileApiControl extends MagratheaApiControl {

    private const UPLOAD_DIR = __DIR__ . '/../../../storage/uploads';

    public function Upload(): array {
        $userId = AuthControl::SessionUserId();

        if (empty($_FILES["file"]) || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
            throw new MagratheaApiException("file is required", 400);
        }

        $original = basename($_FILES["file"]["name"]);
        $safe     = preg_replace('/[^A-Za-z0-9._-]/', '_', $original);
        $stored   = time()."_".bin2hex(random_bytes(6))."_".$safe;

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0775, true);
        }

        $target = self::UPLOAD_DIR.'/'.$stored;
        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target)) {
            throw new MagratheaApiException("failed to save file", 500);
        }

        $file           = new File();
        $file->user_id  = $userId;
        $file->filename = $stored;
        $file->type     = "kindle3";
        $file->status   = "pending";
        $file->Save();

        return (array)$file->ToJson();
    }

    public function List(): array {
        $userId = AuthControl::SessionUserId();
        $files  = FileControl::GetWhere(["user_id" => $userId]);
        return array_map(fn($f) => $f->ToJson(), $files);
    }

    public function Import(array $params = []): array {
        $userId = AuthControl::SessionUserId();
        $id     = (int)($params["id"] ?? 0);

        $file = FileControl::GetRowWhere(["id" => $id, "user_id" => $userId]);
        if (!$file) {
            throw new MagratheaApiException("file not found", 404);
        }

        throw new MagratheaApiException("import not yet implemented", 501);
    }
}
