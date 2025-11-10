// Service worker disengaja dinonaktifkan untuk memastikan stabilitas aplikasi
// Script ini akan menghapus service worker yang sebelumnya terdaftar

// 1. Definisikan nama cache dan daftar file statis yang akan di-cache
const CACHE_NAME = 'laravel-mpa-cache-v1';
const STATIC_ASSETS = [
    '/', // halaman utama
    '/css/app.css',
    '/js/app.js',
    '/favicon.ico',
    // Tambahkan asset lain jika perlu, misal logo, font, dll
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(STATIC_ASSETS))
            .catch(err => {
                // Log error jika gagal cache
                console.error('[Service Worker] Install cache error:', err);
            })
    );
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

// Tambahkan service worker unregistration di sini
// Client side script akan menghandle unregistration

// 4. Event fetch: serve dari cache, fallback ke network, fallback sederhana jika gagal
self.addEventListener('fetch', event => {
    // Hanya handle GET request ke domain sendiri
    if (event.request.method !== 'GET' || !event.request.url.startsWith(self.location.origin)) {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Jika ada di cache, kembalikan dari cache
                if (response) return response;

                // Jika tidak ada di cache, ambil dari network
                return fetch(event.request)
                    .then(networkResponse => {
                        // Simpan response ke cache jika response valid
                        if (
                            networkResponse &&
                            networkResponse.status === 200 &&
                            networkResponse.type === 'basic'
                        ) {
                            const responseClone = networkResponse.clone();
                            caches.open(CACHE_NAME).then(cache => {
                                cache.put(event.request, responseClone);
                            });
                        }
                        return networkResponse;
                    })
                    .catch(() => {
                        // Fallback sederhana jika fetch gagal (misal offline)
                        // Untuk halaman HTML, bisa kembalikan halaman offline sederhana
                        if (event.request.destination === 'document') {
                            return new Response(
                                '<h1>Offline</h1><p>Halaman tidak dapat diakses. Silakan cek koneksi Anda.</p>',
                                { headers: { 'Content-Type': 'text/html' } }
                            );
                        }
                        // Untuk asset lain, bisa return Response kosong
                        return new Response('', { status: 404 });
                    });
            })
    );
}); 