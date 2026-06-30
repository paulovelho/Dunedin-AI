<?php
namespace Dunedin\Import;

use Magrathea2\Admin\AdminElements;
use Magrathea2\Admin\AdminFeature;
use Magrathea2\Admin\iAdminFeature;

class ImportAdmin extends AdminFeature implements iAdminFeature {

    public string $featureName = "Import History";
    public string $featureId   = "Imports";

    public function Initialize() {
        $this->SetClassPath(__DIR__ . '/pages');
    }

    public function HasPermission($action = null): bool {
        return true;
    }

    public function List() {
        $imports = ImportControl::GetAll();
        $columns = [
            ["title" => "ID",         "key" => "id"],
            ["title" => "File ID",    "key" => "file_id"],
            ["title" => "Status",     "key" => "status"],
            ["title" => "Highlights", "key" => "highlight_count"],
            ["title" => "Skipped",    "key" => fn($i) => $this->getSkipped($i)],
            ["title" => "Time (ms)",  "key" => "execution_time"],
            ["title" => "Date",       "key" => "imported_date"],
        ];
        $list = array_map(fn($i) => $i->ToArray(), $imports);
        AdminElements::Instance()->Table($list, $columns);
    }

    private function getSkipped(array $row): string {
        $info = json_decode($row["info"] ?? '{}', true);
        return (string)($info["skipped"] ?? 0);
    }
}
