<?php
namespace App\Api;

use Magrathea2\MagratheaApiControl;
use Magrathea2\Exceptions\MagratheaApiException;
use function Magrathea2\now;

use App\Controls\HighlightControl;
use App\Controls\NoteControl;
use App\Models\Note;

class NoteApiControl extends MagratheaApiControl {

    public function Create($params = false): array {
        if (!is_array($params)) $params = [];
        $userId      = AuthControl::SessionUserId();
        $highlightId = (int)($params["id"] ?? 0);

        $highlight = HighlightControl::GetRowWhere(["id" => $highlightId, "user_id" => $userId]);
        if (!$highlight) {
            throw new MagratheaApiException("highlight not found", 404);
        }

        $post = $this->GetPost();
        $text = trim($post["note"] ?? "");
        if ($text === "") {
            throw new MagratheaApiException("note is required", 400);
        }

        $note = new Note();
        $note->highlight_id = $highlightId;
        $note->user_id      = $userId;
        $note->note         = $text;
        $note->date         = now();
        $note->Save();

        return (array)$note->ToJson();
    }

    public function Update($params): array {
        if (!is_array($params)) $params = [];
        $userId = AuthControl::SessionUserId();
        $id     = (int)($params["id"] ?? 0);

        $note = NoteControl::GetRowWhere(["id" => $id, "user_id" => $userId]);
        if (!$note) {
            throw new MagratheaApiException("not found", 404);
        }

        $body = $this->GetPut();
        $text = trim($body["note"] ?? "");
        if ($text === "") {
            throw new MagratheaApiException("note is required", 400);
        }
        $note->note = $text;
        $note->Save();
        return (array)$note->ToJson();
    }

    public function Delete($params = false): array {
        if (!is_array($params)) $params = [];
        $userId = AuthControl::SessionUserId();
        $id     = (int)($params["id"] ?? 0);

        $note = NoteControl::GetRowWhere(["id" => $id, "user_id" => $userId]);
        if (!$note) {
            throw new MagratheaApiException("not found", 404);
        }
        $note->Delete();
        return ["deleted" => true, "id" => $id];
    }
}
