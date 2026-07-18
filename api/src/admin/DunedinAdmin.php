<?php
namespace Dunedin;

use Magrathea2\Admin\AdminMenu;
use Magrathea2\Admin\Features\ApiExplorer\ApiExplorer;
use Magrathea2\Admin\Features\AppConfig\AdminFeatureAppConfig;

use Dunedin\User\UserAdmin;
use Dunedin\Highlight\HighlightAdmin;
use Dunedin\Note\NoteAdmin;
use Dunedin\File\FileAdmin;
use Dunedin\Import\ImportAdmin;

class DunedinAdmin extends \Magrathea2\Admin\Admin implements \Magrathea2\Admin\iAdmin {

    private $features = [];
    private $apiFeature;

    public function Initialize() {
        $this->SetTitle("Dunedin Admin");
        $this->SetPrimaryColor("#5a2672");
        $this->SetAdminLogo(__DIR__ . "/../../assets/logo.svg");
    }

    public function Auth($user): bool {
        return parent::Auth($user);
    }

    public function SetFeatures() {
        parent::SetFeatures();
        $this->LoadConfig();
        $this->LoadFeatures();
        $this->LoadApi();
    }

    public function LoadApi() {
        $this->apiFeature = new ApiExplorer();
        $this->apiFeature->SetApi(new DunedinApi());
        $this->AddFeature($this->apiFeature);
        $this->AddFeature(new SwaggerAdmin());
    }

    public function LoadConfig() {
        $this->features["app-config"] = new AdminFeatureAppConfig(true);
        $this->features["app-config"]->featureId   = "AppConfig";
        $this->features["app-config"]->featureName = "Settings";
        $this->AddFeature($this->features["app-config"]);
    }

    public function LoadFeatures() {
        $this->AddCrudFeature(new UserAdmin());
        $this->AddCrudFeature(new HighlightAdmin());
        $this->AddCrudFeature(new NoteAdmin());
        $this->AddCrudFeature(new FileAdmin());
        $this->features["imports"] = new ImportAdmin();
        $this->AddFeature($this->features["imports"]);
    }

    public function BuildMenu(): AdminMenu {
        $menu = new AdminMenu();

        $menu->Add($this->features["app-config"]->GetMenuItem());

        $menu->Add($menu->CreateTitle("Api"))
             ->Add($this->apiFeature->GetMenuItem())
             ->Add($this->GetMenuItem("SwaggerAdmin"));

        $menu->Add($menu->CreateTitle("Data"));
        $this->AddFeaturesMenu($menu);
        $menu->Add($this->features["imports"]->GetMenuItem());

        $menu->Add(["title" => "Magrathea", "type" => "main"]);
        $this->AddMagratheaMenu($menu);
        $menu->Add($menu->GetLogoutMenuItem());

        return $menu;
    }
}
