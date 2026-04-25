<?php
namespace App\Models;

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

    public ?int    $id         = null;
    public ?int    $user_id    = null;
    public ?string $text       = null;
    public ?string $origin     = null;
    public ?string $author     = null;
    public ?string $date       = null;
    public ?string $hash       = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
}
