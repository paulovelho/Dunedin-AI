<?php
namespace Dunedin\Highlight;

use Magrathea2\MagratheaModel;

class Highlight extends MagratheaModel {
    protected $dbTable = "highlights";
    protected $dbPk    = "id";
    protected $dbValues = [
        "id"         => "int",
        "user_id"    => "int",
        "text"       => "text",
        "origin"     => "string",
        "author"     => "string",
        "date"       => "datetime",
        "hash"       => "string",
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    public int    $id         = 0;
    public int    $user_id    = 0;
    public string $text       = "";
    public string $origin     = "";
    public string $author     = "";
    public string $date       = "";
    public string $hash       = "";
    public string $created_at = "";
    public string $updated_at = "";
}
