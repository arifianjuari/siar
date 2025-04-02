const CACHE_NAME = 'siar-app-v4';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/images/pwa/icon-192.png',
    '/images/pwa/icon-512.png',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css'
];

// Install service worker
self.addEventListener('install', event => {
    console.log('[Service Worker] Installing Service Worker...', event);
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('[Service Worker] Caching app shell');
                return cache.addAll(urlsToCache);
            })
            .catch(error => {
                console.error('[Service Worker] Cache addAll error: ', error);
            })
    );
});

// Activate service worker
self.addEventListener('activate', event => {
    console.log('[Service Worker] Activating Service Worker...', event);
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Cache and return requests
self.addEventListener('fetch', event => {
    console.log('[Service Worker] Fetching resource: ', event.request.url);

    // Skip cross-origin requests
    if (!event.request.url.startsWith(self.location.origin) &&
        !event.request.url.startsWith('https://cdn.jsdelivr.net') &&
        !event.request.url.startsWith('https://cdnjs.cloudflare.com') &&
        !event.request.url.startsWith('https://raw.githubusercontent.com')) {
        console.log('[Service Worker] Skipping non-same-origin fetch:', event.request.url);
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Cache hit - return response
                if (response) {
                    console.log('[Service Worker] Returning cached resource:', event.request.url);
                    return response;
                }

                // Clone the request
                const fetchRequest = event.request.clone();

                return fetch(fetchRequest)
                    .then(response => {
                        // Check if we received a valid response
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            console.log('[Service Worker] Not caching response for:', event.request.url);
                            return response;
                        }

                        // Clone the response
                        const responseToCache = response.clone();

                        caches.open(CACHE_NAME)
                            .then(cache => {
                                console.log('[Service Worker] Caching new resource:', event.request.url);
                                cache.put(event.request, responseToCache);
                            })
                            .catch(error => {
                                console.error('[Service Worker] Error caching resource:', error);
                            });

                        return response;
                    })
                    .catch(error => {
                        console.error('[Service Worker] Fetch error:', error);
                        // You can provide a fallback response here if needed
                    });
            })
    );
}); 