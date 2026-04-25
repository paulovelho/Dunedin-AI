<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Magrathea2\MagratheaPHP;
use Magrathea2\MagratheaApi;

use App\Api\AuthControl;
use App\Api\HighlightApiControl;
use App\Api\NoteApiControl;
use App\Api\FileApiControl;

MagratheaPHP::LoadVendor();
MagratheaPHP::Instance()
    ->AppPath(__DIR__)
    ->AddCodeFolder(
        __DIR__ . "/../models",
        __DIR__ . "/../controls",
        __DIR__ . "/../api"
    )
    // ->Dev()
    ->Load()
    ->Connect();

$api = new MagratheaApi();
$api->AllowAll();
$api->SetAddress("/api/v1");
$api->BaseAuthorization(new AuthControl(), "ValidateToken");

$api->Add("GET",    "health",                new AuthControl(),         "Health");
$api->Add("GET",    "me",                    new AuthControl(),         "Me",     true);
$api->Add("POST",   "auth/login",            new AuthControl(),         "Login",  true);

$api->Add("GET",    "highlights",            new HighlightApiControl(), "List",   true);
$api->Add("GET",    "highlights/:id",        new HighlightApiControl(), "Read",   true);
$api->Add("DELETE", "highlights/:id",        new HighlightApiControl(), "Delete", true);

$api->Add("POST",   "highlights/:id/notes",  new NoteApiControl(),      "Create", true);
$api->Add("PUT",    "notes/:id",             new NoteApiControl(),      "Update", true);
$api->Add("DELETE", "notes/:id",             new NoteApiControl(),      "Delete", true);

$api->Add("POST",   "files/upload",          new FileApiControl(),      "Upload", true);
$api->Add("GET",    "files",                 new FileApiControl(),      "List",   true);
$api->Add("POST",   "files/:id/import",      new FileApiControl(),      "Import", true);

$api->Run();
