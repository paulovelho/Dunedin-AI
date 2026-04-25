<?php
namespace App\Models;

use Magrathea2\MagratheaModel;

class File extends MagratheaModel {
    protected $dbTable = "files";
    protected $dbPk    = "id";
    protected $dbValues = [
        "id"            => "int",
        "user_id"       => "int",
        "filename"      => "string",
        "type"          => "string",
        "imported_date" => "datetime",
        "status"        => "string",
        "created_at"    => "datetime",
        "updated_at"    => "datetime",
    ];

    public ?int    $id            = null;
    public ?int    $user_id       = null;
    public ?string $filename      = null;
    public ?string $type          = null;
    public ?string $imported_date = null;
    public ?string $status        = null;
    public ?string $created_at    = null;
    public ?string $updated_at    = null;
}
