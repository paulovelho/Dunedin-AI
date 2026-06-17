<?php
namespace Dunedin\Note;

use Magrathea2\Admin\Features\CrudObject\AdminCrudObject;

class NoteAdmin extends AdminCrudObject {

    public function Initialize() {
        $this->featureName = "Notes";
        $this->featureId   = "Notes";
        $this->SetObject(new Note());
    }

    public function CanCreate(): bool { return false; }
    public function CanEdit(): bool   { return true;  }
    public function CanDelete(): bool { return true;  }
}
