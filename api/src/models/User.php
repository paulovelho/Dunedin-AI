<?php
namespace App\Models;

use Magrathea2\MagratheaModel;

class User extends MagratheaModel {
    protected $dbTable = "users";
    protected $dbPk    = "id";
    protected $dbValues = [
        "id"           => "int",
        "firebase_uid" => "string",
        "email"        => "string",
        "display_name" => "string",
        "photo_url"    => "string",
        "last_login"   => "datetime",
        "active"       => "boolean",
        "status"       => "string",
        "created_at"   => "datetime",
        "updated_at"   => "datetime",
    ];

    public ?int    $id           = null;
    public ?string $firebase_uid = null;
    public ?string $email        = null;
    public ?string $display_name = null;
    public ?string $photo_url    = null;
    public ?string $last_login   = null;
    public ?bool   $active       = null;
    public ?string $status       = null;
    public ?string $created_at   = null;
    public ?string $updated_at   = null;
}
