<?php
namespace Dunedin\File;

use Magrathea2\MagratheaModel;

class File extends MagratheaModel {
    protected $dbTable = "files";
    protected $dbPk    = "id";
    protected $dbValues = [
        "id"            => "int",
        "user_id"       => "int",
        "filename"      => "string",
        "type"          => "string",
        "status"        => "string",
        "size"          => "int",
        "created_at"    => "datetime",
        "updated_at"    => "datetime",
    ];

    public int     $id            = 0;
    public int     $user_id       = 0;
    public string  $filename      = "";
    public string  $type          = "";
    public string  $status        = "";
    public int     $size          = 0;
    public string  $created_at    = "";
    public string  $updated_at    = "";
}
