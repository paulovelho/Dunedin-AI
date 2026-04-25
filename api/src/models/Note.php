<?php
namespace App\Models;

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

    public ?int    $id           = null;
    public ?int    $highlight_id = null;
    public ?int    $user_id      = null;
    public ?string $note         = null;
    public ?string $date         = null;
    public ?string $created_at   = null;
    public ?string $updated_at   = null;
}
