<?php
namespace Dunedin\User;

use Magrathea2\Admin\Features\CrudObject\AdminCrudObject;

class UserAdmin extends AdminCrudObject {

    public function Initialize() {
        $this->featureName = "Users";
        $this->featureId   = "Users";
        $this->SetObject(new User());
    }

    public function CanCreate(): bool { return false; }
    public function CanEdit(): bool   { return false; }
    public function CanDelete(): bool { return true;  }
}
