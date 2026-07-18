<?php
namespace Dunedin\SharedLink;

use Magrathea2\MagratheaModel;

class SharedLink extends MagratheaModel {
    protected $dbTable = "shared_links";
    protected $dbPk    = "id";
    protected $dbValues = [
        "id"          => "int",
        "user_id"     => "int",
        "uuid"        => "uuid",
        "active"      => "boolean",
        "description" => "string",
        "visits"      => "int",
        "expire"      => "datetime",
        "created_at"  => "datetime",
        "updated_at"  => "datetime",
    ];

    public int     $id          = 0;
    public int     $user_id     = 0;
    public string  $uuid        = "";
    public bool    $active      = true;
    public string  $description = "";
    public int     $visits      = 0;
    public ?string $expire      = null;
    public string  $created_at  = "";
    public string  $updated_at  = "";
}
