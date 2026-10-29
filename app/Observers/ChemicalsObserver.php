<?php

namespace App\Observers;

use App\Models\ChemicalBatch;
use App\Services\ChemicalAlertService;

class ChemicalsObserver
{
    public function __construct(protected ChemicalAlertService $alertService) {}

    public function created(ChemicalBatch $batch): void
    {
        $this->alertService->checkChemical($batch);
    }

    public function updated(ChemicalBatch $batch): void
    {
        if ($batch->wasChanged(['current_quantity', 'expiration_date', 'quantity_received'])) {
            $this->alertService->checkChemical($batch);
        }
    }
}
