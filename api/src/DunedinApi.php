<?php
namespace Dunedin;

use Magrathea2\MagratheaApi;
use Magrathea2\Config;

use Dunedin\Highlight\HighlightApiControl;
use Dunedin\Note\NoteApiControl;
use Dunedin\File\FileApiControl;

class DunedinApi extends MagratheaApi {

    public function __construct() {
        $this->Initialize();
    }

    public function Initialize() {
        $this->AllowAll();
        $this->SetAddress(Config::Instance()->Get("app_url") . "/api/v1");
        $this->SetAuth();
        $this->Highlights();
        $this->Notes();
        $this->Files();
    }

    private function SetAuth() {
        $auth = new AuthControl();
        $this->BaseAuthorization($auth, "ValidateToken");
        $this->Add("GET",  "health",     $auth, "Health");
        $this->Add("GET",  "me",         $auth, "Me",    true);
        $this->Add("POST", "auth/login", $auth, "Login", true);
    }

    private function Highlights() {
        $api = new HighlightApiControl();
        $this->Add("GET",    "highlights",      $api, "List",   true);
        $this->Add("GET",    "search",          $api, "Search", true);
        $this->Add("GET",    "highlights/:id",  $api, "Read",   true);
        $this->Add("DELETE", "highlights/:id",  $api, "Delete", true);
    }

    private function Notes() {
        $api = new NoteApiControl();
        $this->Add("POST", "highlights/:id/notes", $api, "Create", true);
        $this->Add("PUT",  "notes/:id",            $api, "Update", true);
        $this->Add("DELETE", "notes/:id",          $api, "Delete", true);
    }

    private function Files() {
        $api = new FileApiControl();
        $this->Add("POST", "files/upload",      $api, "Upload",    true);
        $this->Add("GET",  "files",             $api, "List",      true);
        $this->Add("GET",  "files/:id/import",  $api, "GetImport", true);
        $this->Add("POST", "files/:id/import",  $api, "Import",    true);
    }
}
