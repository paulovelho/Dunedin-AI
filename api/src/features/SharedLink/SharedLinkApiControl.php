<?php
namespace Dunedin\SharedLink;

use Magrathea2\MagratheaApiControl;
use Magrathea2\Exceptions\MagratheaApiException;
use Magrathea2\DB\Query;
use Magrathea2\DB\Database;
use function Magrathea2\now;

use Dunedin\AuthControl;
use Dunedin\Highlight\HighlightControl;

class SharedLinkApiControl extends MagratheaApiControl {

    private const DESCRIPTION_MAX = 100;

    public function List(): array {
        $userId = AuthControl::SessionUserId();
        $links  = SharedLinkControl::GetWhere(["user_id" => $userId]);

        return array_map(function ($link) {
            $result = $link->ToArray();
            $result["highlights_count"] = (int) Database::Instance()->QueryOne(
                "SELECT COUNT(*) FROM shared_link_highlights WHERE shared_link_id = " . (int)$link->id
            );
            return $result;
        }, $links);
    }

    public function Create($data = false): array {
        $userId = AuthControl::SessionUserId();
        $post   = $this->GetPost();

        $highlightIds = array_values(array_unique(array_map("intval", $post["highlight_ids"] ?? [])));
        if (empty($highlightIds)) {
            throw new MagratheaApiException("highlight_ids is required", 400);
        }

        $idList = implode(",", $highlightIds);
        $owned  = (int) Database::Instance()->QueryOne(
            "SELECT COUNT(*) FROM highlights WHERE user_id = " . $userId . " AND id IN (" . $idList . ")"
        );
        if ($owned !== count($highlightIds)) {
            throw new MagratheaApiException("invalid highlight_ids", 400);
        }

        $description = trim($post["description"] ?? "");
        if ($description === "") {
            if (count($highlightIds) === 1) {
                $highlight   = HighlightControl::GetRowWhere(["id" => $highlightIds[0], "user_id" => $userId]);
                $description = $highlight ? $highlight->text : "";
            } else {
                $description = "list of " . count($highlightIds) . " items";
            }
        }
        $description = $this->CropDescription($description);

        $link              = new SharedLink();
        $link->user_id     = $userId;
        $link->active      = true;
        $link->description = $description;
        $link->visits      = 0;
        $link->Save();

        foreach ($highlightIds as $highlightId) {
            $join                 = new SharedLinkHighlight();
            $join->shared_link_id = (int) $link->id;
            $join->highlight_id   = $highlightId;
            $join->Save();
        }

        $result              = $link->ToArray();
        $result["highlights_count"] = count($highlightIds);
        return $result;
    }

    public function Update($params = false): array {
        if (!is_array($params)) $params = [];
        $userId = AuthControl::SessionUserId();
        $id     = (int)($params["id"] ?? 0);

        $link = SharedLinkControl::GetRowWhere(["id" => $id, "user_id" => $userId]);
        if (!$link) {
            throw new MagratheaApiException("not found", 404);
        }

        $put = $this->GetPut();
        if (!array_key_exists("active", $put)) {
            throw new MagratheaApiException("active is required", 400);
        }
        $link->active = (bool) $put["active"];
        $link->Save();

        return $link->ToArray();
    }

    public function Delete($params = false): array {
        if (!is_array($params)) $params = [];
        $userId = AuthControl::SessionUserId();
        $id     = (int)($params["id"] ?? 0);

        $link = SharedLinkControl::GetRowWhere(["id" => $id, "user_id" => $userId]);
        if (!$link) {
            throw new MagratheaApiException("not found", 404);
        }
        $link->Delete();
        return ["deleted" => true, "id" => $id];
    }

    private function CropDescription(string $text): string {
        $text = trim($text);
        if (mb_strlen($text) <= self::DESCRIPTION_MAX) {
            return $text;
        }
        return mb_substr($text, 0, self::DESCRIPTION_MAX) . "...";
    }
}
