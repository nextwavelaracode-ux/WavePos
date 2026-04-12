{{--
    Preloader Ejecutivo — WavePOS
    Anima los 41 frames SVG con gradiente inline real (cyan → navy),
    idéntico a los colores del logo oficial.
--}}
<div
    x-show="loaded"
    x-init="setTimeout(() => { loaded = false }, 3200)"
    x-transition:leave="transition ease-in-out duration-700"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    id="wpos-preloader"
>
    <div id="wpos-pre-bg"></div>

    <div id="wpos-pre-center">
        {{-- Contenedor donde se inyectan los SVG inline (Lottie) --}}
        <div id="wpos-pre-logo-wrap">
            <div id="wpos-pre-svg-container"></div>
        </div>
    </div>
</div>

<style>
    #wpos-preloader {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        font-family: 'Outfit', 'Inter', sans-serif;
    }

    #wpos-pre-bg {
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse at 20% 50%, rgba(0, 194, 255, 0.06) 0%, transparent 60%),
            radial-gradient(ellipse at 80% 20%, rgba(29, 58, 109, 0.06) 0%, transparent 50%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 40%, #f1f5f9 100%);
    }

    #wpos-pre-bg::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(0,0,0,0.02) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0,0,0,0.02) 1px, transparent 1px);
        background-size: 48px 48px;
    }

    #wpos-pre-center {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: wpos-fadein 0.6s ease both;
    }

    @keyframes wpos-fadein {
        from { opacity: 0; transform: scale(0.95); }
        to   { opacity: 1; transform: scale(1); }
    }

    /* Contenedor del logo */
    #wpos-pre-logo-wrap {
        width: 180px;
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #wpos-pre-svg-container {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #wpos-pre-svg-container svg {
        width: 100% !important;
        height: 100% !important;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
<script>
(function () {
    const container = document.getElementById('wpos-pre-svg-container');

    if (!container) return;

    // Cargar la animación JSON de Lottie
    const animation = lottie.loadAnimation({
        container: container,
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: '/images/video/animation.json'
    });

    // Limpiar al remover del DOM
    const observer = new MutationObserver(() => {
        if (!document.getElementById('wpos-preloader')) {
            animation.destroy(); // Detiene Lottie y libera memoria
            observer.disconnect();
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });
})();
</script>
