document.addEventListener('DOMContentLoaded', function() {
    const urls = [
        window.location.href,
        ...Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(l => l.href),
        ...Array.from(document.querySelectorAll('script[src]')).map(s => s.src)
    ];

    const lastModified = {};

    async function checkUpdates(currentIndex, skipUntil) {
        if(currentIndex === -1) return;
        const url = urls[currentIndex];

        try {
            const res = await fetch(url, { method: 'HEAD', cache: 'no-store' });
            const mod = res.headers.get('last-modified');

            if (res.status !== 200) {
                skipUntil = currentIndex;
                throw new Error(`${url} returned status code ${res.status}`);
            }
            if(skipUntil !== null) {
                if(currentIndex !== skipUntil) {
                    throw new Error(`Skipping until current index(${currentIndex}) go back to (${skipUntil})`);
                }
                skipUntil = null;
            }

            if (!mod) throw new Error(`No last-modified was found for ${url}`);

            if (lastModified[url] && lastModified[url] !== mod) {
                console.log(`[dev-reload] Change detected in: ${url}`);
                currentIndex = -1;

                window.location.reload();
                return;
            }

            lastModified[url] = mod;
        } catch (e) {
            console.warn(`[dev-reload] ${e.message}`);
        }

        if(currentIndex >= urls.length - 1) {
            currentIndex = 0;
        } else {
            currentIndex++;
        }

        setTimeout(() => {
            checkUpdates(currentIndex, skipUntil);
        }, 1000 / urls.length);
    }

    checkUpdates(0, null);
})