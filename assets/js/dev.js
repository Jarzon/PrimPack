const urls = [
    window.location.href,
    ...Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(l => l.href),
    ...Array.from(document.querySelectorAll('script[src]')).map(s => s.src)
];

const lastModified = {};
let currentIndex = 0;

async function checkUpdates() {
    const url = urls[currentIndex];

    try {
        const res = await fetch(url, { method: 'HEAD', cache: 'no-store' });
        const mod = res.headers.get('last-modified');
        if (!mod || res.status !== 200) return;

        if (lastModified[url] && lastModified[url] !== mod) {
            console.log(`[dev-reload] Change detected in: ${url}`);

            // make sure PHP config opcache.revalidate_freq is at 0
            location.reload();
            return;
        }

        lastModified[url] = mod;
    } catch (e) {
        console.warn(`[dev-reload] Failed to check ${url}:`, e);
    }

    if(currentIndex >= urls.length - 1) {
        currentIndex = 0;
    } else {
        currentIndex++;
    }
}

setInterval(checkUpdates, 150);