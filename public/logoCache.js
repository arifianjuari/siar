// Logo Cache Service Worker
const CACHE_NAME = 'logo-cache-v1';

// Event listener untuk instalasi service worker
self.addEventListener('install', (event) => {
    console.log('Logo Service Worker: Installed');
    self.skipWaiting(); // Langsung aktifkan tanpa menunggu tab ditutup
});

// Event listener untuk aktivasi
self.addEventListener('activate', (event) => {
    console.log('Logo Service Worker: Activated');

    // Hapus cache lama
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        console.log('Logo Service Worker: Menghapus cache lama', name);
                        return caches.delete(name);
                    }
                })
            );
        })
    );

    // Ambil kontrol segera
    return self.clients.claim();
});

// Event listener untuk fetch requests
self.addEventListener('fetch', (event) => {
    // Hanya tangani request untuk logo (path mengandung kata storage dan force_refresh parameter)
    if (event.request.url.includes('/storage/') &&
        (event.request.url.includes('force_refresh=1') ||
            event.request.url.match(/\.(png|jpg|jpeg|gif|svg|webp)(\?|$)/i))) {

        event.respondWith(
            fetch(event.request, {
                // Paksa reload dari server
                cache: 'no-store',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            }).then(response => {
                // Clone response untuk disimpan di cache
                const clonedResponse = response.clone();

                // Simpan response baru ke cache, ganti yang lama
                caches.open(CACHE_NAME).then(cache => {
                    // Buat cache key baru dengan timestamp untuk menghindari konflik
                    const cacheKey = new Request(event.request.url.split('?')[0] + '?t=' + Date.now());
                    cache.put(cacheKey, clonedResponse);

                    // Batasi jumlah entri di cache (maksimal 5 logo)
                    limitCacheSize(CACHE_NAME, 5);
                });

                return response;
            }).catch(error => {
                console.log('Logo Service Worker: Fetch gagal, mencoba cache', error);

                // Jika fetch gagal, coba cari dari cache
                return caches.match(event.request).then(cachedResponse => {
                    if (cachedResponse) {
                        console.log('Logo Service Worker: Mengembalikan cache');
                        return cachedResponse;
                    }

                    // Jika tidak ada di cache, cari item dengan URL dasar yang sama tanpa query params
                    const baseUrl = event.request.url.split('?')[0];
                    return caches.open(CACHE_NAME).then(cache => {
                        return cache.keys().then(keys => {
                            // Cari cache dengan URL dasar yang sama
                            const matchingKey = keys.find(key => key.url.split('?')[0] === baseUrl);
                            if (matchingKey) {
                                console.log('Logo Service Worker: Menemukan cache dengan URL dasar yang sama');
                                return cache.match(matchingKey);
                            }

                            // Jika masih tidak ditemukan, kembalikan error
                            throw new Error('Tidak ada cache yang ditemukan untuk logo');
                        });
                    });
                });
            })
        );
    }
});

// Fungsi untuk membatasi ukuran cache
function limitCacheSize(cacheName, maxItems) {
    caches.open(cacheName).then(cache => {
        cache.keys().then(keys => {
            if (keys.length > maxItems) {
                // Hapus item cache tertua
                cache.delete(keys[0]).then(() => {
                    limitCacheSize(cacheName, maxItems);
                });
            }
        });
    });
} 