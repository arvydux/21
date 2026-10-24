// Service worker for the online order board: shows the "order ready" push.
self.addEventListener('install', () => self.skipWaiting());

self.addEventListener('activate', (event) => event.waitUntil(self.clients.claim()));

self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : {};
    const tag = `order-${data.number}`;

    // A notification that replaces one still on screen stays silent on some phones,
    // so the old one is closed first and the new one rings like the first time.
    event.waitUntil(self.registration.getNotifications({ tag }).then((shown) => {
        shown.forEach((notification) => notification.close());

        return self.registration.showNotification(data.title || 'Prusakovų užeiga', {
            body: data.body || 'Jūsų užsakymas paruoštas!',
            tag,
            renotify: true,
            silent: false,
            icon: '/online-icon-192.png',
            badge: '/online-icon-192.png',
            vibrate: [300, 150, 300],
            requireInteraction: true,
            data: { url: data.url || '/online' },
        });
    }));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    event.waitUntil(self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windows) => {
        const board = windows.find((client) => new URL(client.url).pathname === event.notification.data.url);

        return board ? board.focus() : self.clients.openWindow(event.notification.data.url);
    }));
});
