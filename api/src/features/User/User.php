<?php
namespace Dunedin\User;

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

    public int    $id           = 0;
    public string $firebase_uid = "";
    public string $email        = "";
    public string $display_name = "";
    public string $photo_url    = "";
    public string $last_login   = "";
    public bool   $active       = false;
    public string $status       = "";
    public string $created_at   = "";
    public string $updated_at   = "";
}
