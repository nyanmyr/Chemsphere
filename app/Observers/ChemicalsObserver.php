<?php

namespace App\Observers;

use App\Models\Chemical;
use App\Services\ChemicalAlertService;

class ChemicalsObserver
{
    public function __construct(protected ChemicalAlertService $alertService) {}

    public function created(Chemical $chemical): void
    {
        $this->alertService->checkChemical($chemical);
    }

    public function updated(Chemical $chemical): void
    {
        if ($chemical->wasChanged(['current_quantity', 'expiration_date'])) {
            $this->alertService->checkChemical($chemical);
        }
    }
}
