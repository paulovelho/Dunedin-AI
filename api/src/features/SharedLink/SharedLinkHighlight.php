<?php
namespace Dunedin\SharedLink;

use Magrathea2\MagratheaModel;

class SharedLinkHighlight extends MagratheaModel {
    protected $dbTable = "shared_link_highlights";
    protected $dbPk    = "id";
    protected $dbValues = [
        "id"             => "int",
        "shared_link_id" => "int",
        "highlight_id"   => "int",
        "created_at"     => "datetime",
        "updated_at"     => "datetime",
    ];

    public int    $id             = 0;
    public int    $shared_link_id = 0;
    public int    $highlight_id   = 0;
    public string $created_at     = "";
    public string $updated_at     = "";
}
