// Service worker for the online order board: shows the "order ready" push.
self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : {};

    event.waitUntil(self.registration.showNotification(data.title || 'Prusakovų užeiga', {
        body: data.body || 'Jūsų užsakymas paruoštas!',
        tag: `order-${data.number}`,
        renotify: true,
        silent: false,
        icon: '/online-icon-192.png',
        badge: '/online-icon-192.png',
        vibrate: [300, 150, 300],
        requireInteraction: true,
        data: { url: data.url || '/online' },
    }));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    event.waitUntil(self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windows) => {
        const board = windows.find((client) => new URL(client.url).pathname === event.notification.data.url);

        return board ? board.focus() : self.clients.openWindow(event.notification.data.url);
    }));
});
