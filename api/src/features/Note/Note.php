<?php
namespace Dunedin\Note;

use Magrathea2\MagratheaModel;

class Note extends MagratheaModel {
    protected $dbTable = "notes";
    protected $dbPk    = "id";
    protected $dbValues = [
        "id"           => "int",
        "highlight_id" => "int",
        "user_id"      => "int",
        "note"         => "text",
        "date"         => "datetime",
        "created_at"   => "datetime",
        "updated_at"   => "datetime",
    ];

    public int    $id           = 0;
    public int    $highlight_id = 0;
    public int    $user_id      = 0;
    public string $note         = "";
    public string $date         = "";
    public string $created_at   = "";
    public string $updated_at   = "";
}
