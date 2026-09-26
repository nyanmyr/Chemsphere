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
