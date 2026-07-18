<?php
namespace Dunedin\SharedLink;

use Magrathea2\MagratheaApiControl;
use Magrathea2\Exceptions\MagratheaApiException;
use Magrathea2\DB\Database;
use function Magrathea2\now;

class SharedLinkPublicApiControl extends MagratheaApiControl {

    public function Read($params = false): array {
        $link = $this->FindValidLink($params);

        $rows = Database::Instance()->QueryAll(
            "SELECT h.text, h.origin, h.author, h.title, h.date
             FROM highlights h
             INNER JOIN shared_link_highlights slh ON slh.highlight_id = h.id
             WHERE slh.shared_link_id = " . (int)$link->id
        );

        return [
            "uuid"        => $link->uuid,
            "description" => $link->description,
            "visits"      => (int) $link->visits,
            "created_at"  => $link->created_at,
            "highlights"  => $rows,
        ];
    }

    public function Visit($params = false): array {
        $link = $this->FindValidLink($params);
        $link->visits = (int)$link->visits + 1;
        $link->Save();
        return ["visits" => (int) $link->visits];
    }

    private function FindValidLink($params) {
        if (!is_array($params)) $params = [];
        $uuid = $params["uuid"] ?? "";

        $link = SharedLinkControl::GetRowWhere(["uuid" => $uuid]);
        if (!$link || !$link->active) {
            throw new MagratheaApiException("not found", 404);
        }
        if ($link->expire && $link->expire <= now()) {
            throw new MagratheaApiException("not found", 404);
        }
        return $link;
    }
}
