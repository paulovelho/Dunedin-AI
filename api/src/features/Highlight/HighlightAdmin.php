<?php
namespace Dunedin\Highlight;

use Magrathea2\Admin\Features\CrudObject\AdminCrudObject;

class HighlightAdmin extends AdminCrudObject {

    public function Initialize() {
        $this->featureName = "Highlights";
        $this->featureId   = "Highlights";
        $this->SetObject(new Highlight());
    }

    public function CanCreate(): bool { return false; }
    public function CanEdit(): bool   { return false; }
    public function CanDelete(): bool { return true;  }
}
