<?php
namespace Dunedin\Import;

use Magrathea2\MagratheaModel;

class Import extends MagratheaModel {
    protected $dbTable = "imports";
    protected $dbPk    = "id";
    protected $dbValues = [
        "id"              => "int",
        "file_id"         => "int",
        "status"          => "string",
        "highlight_count" => "int",
        "execution_time"  => "int",
        "info"            => "string",
        "imported_date"   => "datetime",
        "created_at"      => "datetime",
    ];

    public int     $id              = 0;
    public int     $file_id         = 0;
    public string  $status          = "pending";
    public int     $highlight_count = 0;
    public int     $execution_time  = 0;
    public ?string $info            = null;
    public ?string $imported_date   = null;
    public string  $created_at      = "";
}
