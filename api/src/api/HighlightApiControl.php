<?php
namespace App\Api;

use Magrathea2\MagratheaApiControl;
use Magrathea2\Exceptions\MagratheaApiException;
use Magrathea2\DB\Query;
use Magrathea2\DB\Database;

use App\Controls\HighlightControl;
use App\Controls\NoteControl;

class HighlightApiControl extends MagratheaApiControl {

    public function List(): array {
        $userId = AuthControl::SessionUserId();
        $q      = $_GET["q"]      ?? null;
        $author = $_GET["author"] ?? null;
        $origin = $_GET["origin"] ?? null;
        $page   = max(1, (int)($_GET["page"]  ?? 1));
        $limit  = min(100, max(1, (int)($_GET["limit"] ?? 50)));
        $offset = ($page - 1) * $limit;

        $where = "user_id = ".$userId;
        if ($q)      $where .= " AND text   LIKE '%".Query::Clean($q)."%'";
        if ($author) $where .= " AND author LIKE '%".Query::Clean($author)."%'";
        if ($origin) $where .= " AND origin = '".Query::Clean($origin)."'";

        $sql = Query::Select()
            ->Table("highlights")
            ->Where($where)
            ->Order("date DESC")
            ->Limit($limit)
            ->Offset($offset)
            ->SQL();

        $rows = Database::Instance()->QueryAll($sql);
        return [
            "page"  => $page,
            "limit" => $limit,
            "items" => $rows,
        ];
    }

    public function Read($params = false): array {
        if (!is_array($params)) $params = [];
        $userId = AuthControl::SessionUserId();
        $id     = (int)($params["id"] ?? 0);

        $highlight = HighlightControl::GetRowWhere(["id" => $id, "user_id" => $userId]);
        if (!$highlight) {
            throw new MagratheaApiException("not found", 404);
        }

        $notes = NoteControl::GetWhere(["highlight_id" => $id, "user_id" => $userId]);

        $result = (array)$highlight->ToJson();
        $result["notes"] = array_map(fn($n) => $n->ToJson(), $notes);
        return $result;
    }

    public function Delete($params = false): array {
        if (!is_array($params)) $params = [];
        $userId = AuthControl::SessionUserId();
        $id     = (int)($params["id"] ?? 0);

        $highlight = HighlightControl::GetRowWhere(["id" => $id, "user_id" => $userId]);
        if (!$highlight) {
            throw new MagratheaApiException("not found", 404);
        }
        $highlight->Delete();
        return ["deleted" => true, "id" => $id];
    }
}
