<?php

namespace App\Observers;

use App\Models\ChemicalContainer;
use App\Services\ChemicalContainerAlertService;

class ChemicalContainersObserver
{
    public function __construct(protected ChemicalContainerAlertService $alertService) {}
    public function created(ChemicalContainer $container): void { $this->alertService->checkContainer($container); }
    public function updated(ChemicalContainer $container): void
    {
        if ($container->wasChanged(['current_quantity', 'expiration_date', 'quantity_received'])) $this->alertService->checkContainer($container);
    }
}
