const VMS_CACHE = 'vms-mobile-v1';
const APP_SHELL = [
  '/m',
  '/css/mobile-ui.css',
  '/manifest.webmanifest',
  '/icons/vms-pwa.svg'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(VMS_CACHE)
      .then((cache) => cache.addAll(APP_SHELL))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(keys.filter((key) => key !== VMS_CACHE).map((key) => caches.delete(key))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') {
    return;
  }

  const requestUrl = new URL(event.request.url);
  if (requestUrl.origin !== location.origin) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        const copy = response.clone();
        if (response.ok && (requestUrl.pathname.startsWith('/m') || requestUrl.pathname.startsWith('/css/') || requestUrl.pathname.startsWith('/icons/'))) {
          caches.open(VMS_CACHE).then((cache) => cache.put(event.request, copy));
        }
        return response;
      })
      .catch(() => caches.match(event.request).then((cached) => cached || caches.match('/m')))
  );
});
