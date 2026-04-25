<?php
namespace App\Api;

use Magrathea2\MagratheaApiAuth;
use Magrathea2\Config;
use Magrathea2\Exceptions\MagratheaApiException;
use function Magrathea2\now;

use Kreait\Firebase\JWT\IdTokenVerifier;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;

use App\Models\User;
use App\Controls\UserControl;

class AuthControl extends MagratheaApiAuth {

    private static ?object $sessionInfo = null;

    public static function SessionUserId(): int {
        if (!self::$sessionInfo) {
            throw new MagratheaApiException("not authenticated", 401, null, true);
        }
        return (int) self::$sessionInfo->user_id;
    }

    private ?IdTokenVerifier $verifier = null;

    private function verifier(): IdTokenVerifier {
        if ($this->verifier === null) {
            $projectId = Config::Instance()->Get("firebase_id");
            $this->verifier = IdTokenVerifier::createWithProjectId($projectId);
        }
        return $this->verifier;
    }

    public function Health(): array {
        return ["status" => "ok"];
    }

    public function Me(): array {
        $userId = self::SessionUserId();
        $user   = UserControl::GetRowWhere(["id" => $userId]);
        if (!$user) {
            throw new MagratheaApiException("user not found", 404, null, true);
        }
        return (array)$user->ToJson();
    }

    public function ValidateToken(): bool {
        $token = $this->GetAuthorizationToken();
        if (!$token) {
            throw new MagratheaApiException("missing bearer token", 401, null, true);
        }

        try {
            $verified = $this->verifier()->verifyIdToken($token);
        } catch (IdTokenVerificationFailed $e) {
            throw new MagratheaApiException("invalid token", 401, null, true);
        } catch (\Throwable $e) {
            throw new MagratheaApiException("auth failed: " . $e->getMessage(), 401, null, true);
        }

        $payload     = $verified->payload();
        $firebaseUid = $payload["sub"]     ?? null;
        $email       = $payload["email"]   ?? null;
        $name        = $payload["name"]    ?? null;
        $picture     = $payload["picture"] ?? null;

        $user = UserControl::GetRowWhere(["firebase_uid" => $firebaseUid]);
        if (!$user) {
            $user               = new User();
            $user->firebase_uid = $firebaseUid;
            $user->email        = $email ?? "";
            $user->display_name = $name;
            $user->photo_url    = $picture;
            $user->last_login   = now();
            $user->active       = true;
            $user->status       = "active";
            $user->Save();
        } else {
            $changed = false;
            if ($email   && $user->email        !== $email)   { $user->email        = $email;   $changed = true; }
            if ($name    && $user->display_name !== $name)    { $user->display_name = $name;    $changed = true; }
            if ($picture && $user->photo_url    !== $picture) { $user->photo_url    = $picture; $changed = true; }
            if ($changed) $user->Save();
        }

        $this->userInfo = (object)[
            "user_id"      => (int)$user->id,
            "firebase_uid" => $firebaseUid,
            "email"        => $email,
        ];
        self::$sessionInfo = $this->userInfo;
        return true;
    }

    public function Login(): array {
        $userId = self::SessionUserId();
        $user   = UserControl::GetRowWhere(["id" => $userId]);
        if (!$user) {
            throw new MagratheaApiException("user not found", 404, null, true);
        }
        $user->last_login = now();
        $user->Save();
        return (array)$user->ToJson();
    }
}
