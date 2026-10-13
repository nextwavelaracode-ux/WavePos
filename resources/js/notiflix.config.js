import { Notify, Confirm, Loading } from 'notiflix';

Notify.init({
    width: '340px',
    position: 'right-bottom',
    distance: '24px',
    opacity: 1,
    borderRadius: '12px',
    rtl: false,
    timeout: 4500,
    messageMaxLength: 1000,
    backOverlay: false,
    backOverlayColor: 'rgba(0,0,0,0.5)',
    plainText: true, // Set to true to prevent Notiflix "Possible HTML Tags Error"
    showOnlyTheLastOne: false,
    clickToClose: true,
    pauseOnHover: true,
    
    closeButton: true,
    useIcon: true,
    useFontAwesome: false,
    fontFamily: 'inherit',
    fontSize: '14px',
    cssAnimation: true,
    cssAnimationDuration: 400,
    cssAnimationStyle: 'from-bottom', 

    success: {
        background: '#22c55e',
        textColor: '#fff',
        childClassName: 'notiflix-notify-success',
        notiflixIconColor: '#fff',
        fontAwesomeClassName: 'fas fa-check-circle',
        fontAwesomeIconColor: '#fff',
        backOverlayColor: 'rgba(34,197,94,0.2)',
    },
    failure: {
        background: '#ef4444',
        textColor: '#fff',
        childClassName: 'notiflix-notify-failure',
        notiflixIconColor: '#fff',
        fontAwesomeClassName: 'fas fa-times-circle',
        fontAwesomeIconColor: '#fff',
        backOverlayColor: 'rgba(239,68,68,0.2)',
    },
    warning: {
        background: '#f59e0b',
        textColor: '#fff',
        childClassName: 'notiflix-notify-warning',
        notiflixIconColor: '#fff',
        fontAwesomeClassName: 'fas fa-exclamation-circle',
        fontAwesomeIconColor: '#fff',
        backOverlayColor: 'rgba(245,158,11,0.2)',
    },
    info: {
        background: '#3b82f6',
        textColor: '#fff',
        childClassName: 'notiflix-notify-info',
        notiflixIconColor: '#fff',
        fontAwesomeClassName: 'fas fa-info-circle',
        fontAwesomeIconColor: '#fff',
        backOverlayColor: 'rgba(59,130,246,0.2)',
    },
});

Confirm.init({
    className: 'notiflix-confirm',
    width: '400px',
    zindex: 4000,
    position: 'center', 
    distance: '10px',
    backgroundColor: '#ffffff',
    borderRadius: '16px',
    backOverlay: true,
    backOverlayColor: 'rgba(0,0,0,0.6)',
    rtl: false,
    fontFamily: 'inherit',
    cssAnimation: true,
    cssAnimationDuration: 300,
    cssAnimationStyle: 'zoom', 
    
    plainText: true,
    titleColor: '#111827', 
    titleFontSize: '18px',
    titleMaxLength: 100,
    messageColor: '#4b5563', 
    messageFontSize: '15px',
    messageMaxLength: 600,
    
    buttonsFontSize: '14px',
    buttonsMaxLength: 34,
    okButtonColor: '#ffffff',
    okButtonBackground: '#ef4444', 
    cancelButtonColor: '#374151',
    cancelButtonBackground: '#e5e7eb',
});

Loading.init({
    className: 'notiflix-loading',
    zindex: 4000,
    backgroundColor: 'rgba(0,0,0,0.7)', 
    rtl: false,
    fontFamily: 'inherit',
    cssAnimation: true,
    cssAnimationDuration: 400,
    clickToClose: true,
    customSvgUrl: null,
    customSvgCode: null,
    svgSize: '80px',
    svgColor: '#3b82f6', 
    messageID: 'NotiflixLoadingMessage',
    messageFontSize: '16px',
    messageMaxLength: 100,
    messageColor: '#ffffff',
});

// Safe wrapper to strip HTML and truncate length defensively for any future caller
function cleanMessage(msg) {
    if (typeof msg !== 'string') return String(msg ?? '');
    // Strip HTML tags defensively and normalize whitespace
    return msg.replace(/<[^>]*>?/gm, '').replace(/\s+/g, ' ').trim();
}

const safeNotify = {
    success(msg, cb, opt) { return Notify.success(cleanMessage(msg), cb, opt); },
    failure(msg, cb, opt) { return Notify.failure(cleanMessage(msg), cb, opt); },
    warning(msg, cb, opt) { return Notify.warning(cleanMessage(msg), cb, opt); },
    info(msg, cb, opt) { return Notify.info(cleanMessage(msg), cb, opt); },
    init(opt) { return Notify.init(opt); },
    merge(opt) { return Notify.merge(opt); },
};

window.Notify = safeNotify;
window.Confirm = Confirm;
window.Loading = Loading;
