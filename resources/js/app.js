import './bootstrap';

const userId = document.querySelector('meta[name="user-id"]')?.content;

// Each entry: the table's element id, its private channel, and the event prefix.
const liveTables = [
    { table: 'inventory-table', channel: 'inventory', prefix: 'Chemical' },
    { table: 'equipment-table', channel: 'equipment', prefix: 'Equipment' },
    { table: 'locations-table', channel: 'locations', prefix: 'Location' },
    { table: 'alerts-table', channel: `alerts.${userId}`, prefix: 'Alert' },
    { table: 'users-table', channel: 'users', prefix: 'User' },
    { table: 'usage-logs-table', channel: 'usage_logs', prefix: 'UsageLog' },
    { table: 'audit-logs-table', channel: 'audit_logs', prefix: 'AuditLog' },
    { table: 'used-today', channel: 'usage_logs', prefix: 'UsageLog' },
];

document.addEventListener('DOMContentLoaded', () => {
    liveTables.forEach(({ table, channel, prefix }) => {
        if (!document.getElementById(table)) return;

        const refresh = async () => {
            const response = await fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const fresh = new DOMParser()
                .parseFromString(await response.text(), 'text/html')
                .getElementById(table);

            if (fresh) document.getElementById(table)?.replaceWith(fresh);
        };

        window.Echo.private(channel)
            .listen(`.${prefix}Created`, refresh)
            .listen(`.${prefix}Updated`, refresh)
            .listen(`.${prefix}Deleted`, refresh);

        const dots = document.querySelectorAll('[data-unread-dot]');
        if (!userId || dots.length === 0) return;

        let timer;
        const refreshDots = () => {
            clearTimeout(timer);
            timer = setTimeout(async () => {
                const { data } = await window.axios.get('/alerts/unread-count');
                dots.forEach((dot) => dot.classList.toggle('invisible', data.count < 1));
            }, 250);
        };

        window.Echo.private(`alerts.${userId}`)
            .listen('.AlertCreated', refreshDots)
            .listen('.AlertUpdated', refreshDots)
            .listen('.AlertDeleted', refreshDots);

        // Catch up if the socket dropped and reconnected
        window.Echo.connector.pusher.connection.bind('connected', refreshDots);
    });
});
