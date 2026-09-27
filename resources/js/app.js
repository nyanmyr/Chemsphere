import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('inventory-table');

    if (!table) {
        return;
    }

    const refreshInventoryTable = async () => {
        const response = await fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        const html = await response.text();
        const fresh = new DOMParser()
            .parseFromString(html, 'text/html')
            .getElementById('inventory-table');

        if (fresh) {
            document.getElementById('inventory-table').replaceWith(fresh);
        }
    };

    window.Echo.private('inventory')
        .listen('.ChemicalCreated', refreshInventoryTable)
        .listen('.ChemicalUpdated', refreshInventoryTable)
        .listen('.ChemicalDeleted', refreshInventoryTable);
});

document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('equipment-table');

    if (!table) {
        return;
    }

    const refreshEquipmentTable = async () => {
        const response = await fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        const html = await response.text();
        const fresh = new DOMParser()
            .parseFromString(html, 'text/html')
            .getElementById('equipment-table');

        if (fresh) {
            document.getElementById('equipment-table').replaceWith(fresh);
        }
    };

    window.Echo.private('equipment')
        .listen('.EquipmentCreated', refreshEquipmentTable)
        .listen('.EquipmentUpdated', refreshEquipmentTable)
        .listen('.EquipmentDeleted', refreshEquipmentTable);
});

document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('locations-table');

    if (!table) {
        return;
    }

    const refreshLocationsTable = async () => {
        const response = await fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        const html = await response.text();
        const fresh = new DOMParser()
            .parseFromString(html, 'text/html')
            .getElementById('locations-table');

        if (fresh) {
            document.getElementById('locations-table').replaceWith(fresh);
        }
    };

    window.Echo.private('locations')
        .listen('.LocationCreated', refreshLocationsTable)
        .listen('.LocationUpdated', refreshLocationsTable)
        .listen('.LocationDeleted', refreshLocationsTable);
});

document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('alerts-table');

    if (!table) {
        return;
    }

    const refreshAlertTable = async () => {
        const response = await fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        const html = await response.text();
        const fresh = new DOMParser()
            .parseFromString(html, 'text/html')
            .getElementById('alerts-table');

        if (fresh) {
            document.getElementById('alerts-table').replaceWith(fresh);
        }
    };

    window.Echo.private('alerts')
        .listen('.AlertCreated', refreshAlertTable)
        .listen('.AlertUpdated', refreshAlertTable)
        .listen('.AlertDeleted', refreshAlertTable);
});
