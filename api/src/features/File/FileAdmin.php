<?php
namespace Dunedin\File;

use Magrathea2\Admin\Features\CrudObject\AdminCrudObject;

class FileAdmin extends AdminCrudObject {

    public function Initialize() {
        $this->featureName = "Files";
        $this->featureId   = "Files";
        $this->SetObject(new File());
    }

    public function CanCreate(): bool { return false; }
    public function CanEdit(): bool   { return false; }
    public function CanDelete(): bool { return true;  }
}
