<?php
use Magrathea2\Admin\AdminElements;
use Magrathea2\Admin\AdminManager;

$feature = AdminManager::Instance()->GetActiveFeature();
$elements = AdminElements::Instance();
$elements->Header($feature->featureName);
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Import History</div>
                <div class="card-body">
                    <?php $feature->List(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
