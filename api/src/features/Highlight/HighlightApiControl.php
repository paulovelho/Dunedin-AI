<?php
namespace Dunedin\Highlight;

use Magrathea2\MagratheaApiControl;
use Magrathea2\Exceptions\MagratheaApiException;
use Magrathea2\DB\Query;
use Magrathea2\DB\Database;

use Dunedin\AuthControl;
use Dunedin\Note\NoteControl;

class HighlightApiControl extends MagratheaApiControl {

    public function List(): array {
        $userId = AuthControl::SessionUserId();
        $q      = $_GET["q"]      ?? null;
        $author = $_GET["author"] ?? null;
        $origin = $_GET["origin"] ?? null;
        $page   = max(1, (int)($_GET["page"]  ?? 1));
        $limit  = min(100, max(1, (int)($_GET["limit"] ?? 50)));
        $offset = ($page - 1) * $limit;

        $where = "user_id = " . $userId;
        if ($q)      $where .= " AND text   LIKE '%" . Query::Clean($q)      . "%'";
        if ($author) $where .= " AND author LIKE '%" . Query::Clean($author) . "%'";
        if ($origin) $where .= " AND origin = '"     . Query::Clean($origin) . "'";

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

    public function Search(): array {
        $userId = AuthControl::SessionUserId();
        $q      = trim($_GET["q"]      ?? "");
        $author = trim($_GET["author"] ?? "");
        $page   = max(1, (int)($_GET["page"]  ?? 1));
        $limit  = min(100, max(1, (int)($_GET["count"] ?? 20)));

        if ($q === "" && $author === "") {
            throw new MagratheaApiException("q or author is required", 400);
        }

        $where = "user_id = " . $userId;

        if ($q !== "") {
            $words = array_filter(preg_split('/\s+/', $q));
            foreach ($words as $word) {
                $where .= " AND text LIKE '%" . Query::Clean($word) . "%'";
            }
        }

        if ($author !== "") {
            $where .= " AND author LIKE '%" . Query::Clean($author) . "%'";
        }

        $query = Query::Select()
            ->Obj(new Highlight())
            ->Where($where)
            ->Order("date DESC");

        $total = 0;
        $rows  = HighlightControl::RunPagination($query, $total, $page - 1, $limit);

        return [
            "page"  => $page,
            "limit" => $limit,
            "total" => $total,
            "items" => array_map(fn($h) => $h->ToArray(), $rows),
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

        $notes  = NoteControl::GetWhere(["highlight_id" => $id, "user_id" => $userId]);
        $result = $highlight->ToArray();
        $result["notes"] = array_map(fn($n) => $n->ToArray(), $notes);
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
