document.addEventListener('DOMContentLoaded', function() {
    let pause = false;
    let toolbar = document.querySelector('.primToolbar #tools');

    function setColorState(div) {
        div.style.color = !pause? '#1a9500' : '#770000';
    }

    if(toolbar) {
        let div = document.createElement('div');
        div.innerText = 'Dev.js';
        let pauseConf = localStorage.getItem('_devJS_pause');
        pause = pauseConf !== null? JSON.parse(pauseConf) : false;
        setColorState(div);
        div.addEventListener('click', () => {
            pause = !pause;
            setColorState(div);
            localStorage.setItem('_devJS_pause', pause);
        });
        toolbar.appendChild(div);
    }

    const urls = [
        window.location.href,
        ...Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(l => l.href),
        ...Array.from(document.querySelectorAll('script[src]')).map(s => s.src)
    ];

    const lastModified = {};

    async function checkUpdates(currentIndex, skipUntil) {
        if(!pause) {
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
        }

        setTimeout(() => {
            checkUpdates(currentIndex, skipUntil);
        }, 1000 / urls.length);
    }

    checkUpdates(0, null);
})