
(() => {
    'use strict';
    const preloader = document.getElementById('orvantaPreloader');
    // Если прелоадера нет на странице, ничего не делаем.
    if (!preloader) {
        return;
    }
    const MIN_DISPLAY_TIME = 900;
    const MAX_DISPLAY_TIME = 8000;
    const REMOVE_FALLBACK_TIME = 1000;
    const startTime = Date.now();
    let isHidden = false;
    let fallbackTimer;
    let finishTimer;
    let removeTimer;

    /**
     * Удаляет прелоадер из DOM.
     */
    function removePreloader() {
        clearTimeout(removeTimer);
        preloader.remove();
    }

    /**
     * Плавно скрывает прелоадер и удаляет его из DOM.
     */
    function hidePreloader() {
        if (isHidden) {
            return;
        }

        isHidden = true;
        clearTimeout(fallbackTimer);
        clearTimeout(finishTimer);
        preloader.classList.add('is-hidden');
        // Удаляем элемент после завершения CSS-перехода.
        preloader.addEventListener('transitionend', (event) => {
            if(event.target === preloader && event.propertyName === 'opacity') {
                removePreloader();
            }
        }, { once: true });
        // Резерв на случай, если transitionend не сработает.
        removeTimer = setTimeout(removePreloader, REMOVE_FALLBACK_TIME);
    }

    /**
     * Скрывает заставку после минимального времени показа.
     */
    function finishLoading() {
        if(isHidden) {
            return;
        }
        const elapsed = Date.now() - startTime;
        const remaining = Math.max(0, MIN_DISPLAY_TIME - elapsed);
        finishTimer = setTimeout(hidePreloader, remaining);
    }
    // Резервная защита от бесконительной загрузки.
    fallbackTimer = setTimeout(hidePreloader, MAX_DISPLAY_TIME);
    // Если страница уже загружена, скрываем заставку.
    if (document.readyState === 'complete') {
        finishLoading();
    } else {
        window.addEventListener('load', finishLoading, {
            once: true
        });
    }
})();