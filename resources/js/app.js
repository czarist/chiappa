import './bootstrap';
import AOS from 'aos';
import 'aos/dist/aos.css';

const animate = (selector, effect = 'fade-up') => {
    document.querySelectorAll(selector).forEach((element, index) => {
        element.dataset.aos ??= effect;
        if (!element.dataset.aosDelay && index > 0) element.dataset.aosDelay = String(index * 100);
    });
};

animate('.section-heading');
animate('.info-card');
animate('.service-card');
animate('.product-card', 'zoom-in');
animate('.stat-card');
animate('.contact-info article');
animate('.contact-lower > :first-child', 'fade-right');
animate('.contact-lower > :last-child', 'fade-left');
animate('.instagram-block > .button', 'zoom-in');
animate('.cta-section .shell', 'fade-up');

AOS.init({
    duration: 700,
    easing: 'ease-out-cubic',
    once: true,
    offset: 80,
});
window.addEventListener('load', () => AOS.refresh());

const menu=document.querySelector('[data-mobile-menu]'),overlay=document.querySelector('[data-menu-overlay]'),toggle=document.querySelector('[data-menu-toggle]'),close=document.querySelector('[data-menu-close]');
const closeMenu=()=>{menu?.classList.remove('is-open');overlay?.classList.remove('is-open');toggle?.setAttribute('aria-expanded','false');document.body.classList.remove('menu-open')};
toggle?.addEventListener('click',()=>{menu?.classList.add('is-open');overlay?.classList.add('is-open');toggle.setAttribute('aria-expanded','true');document.body.classList.add('menu-open')});close?.addEventListener('click',closeMenu);overlay?.addEventListener('click',closeMenu);document.querySelectorAll('[data-mobile-menu] a').forEach(a=>a.addEventListener('click',closeMenu));document.addEventListener('keydown',e=>{if(e.key==='Escape')closeMenu()});
function easeInOutCubic(t) {
    return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
}

function smoothScrollTo(targetY, duration = 800) {
    const startY = window.scrollY;
    const distance = targetY - startY;
    const startTime = performance.now();

    function step(now) {
        const progress = Math.min((now - startTime) / duration, 1);
        window.scrollTo(0, startY + distance * easeInOutCubic(progress));
        if (progress < 1) requestAnimationFrame(step);
    }

    requestAnimationFrame(step);
}

const header = document.querySelector('header');
document.querySelectorAll('.nav-link').forEach((link) => {
    link.addEventListener('click', (event) => {
        const target = link.hash ? document.getElementById(decodeURIComponent(link.hash.slice(1))) : null;

        if (target) {
            event.preventDefault();
            const offset = target.getBoundingClientRect().top + window.scrollY - (header?.offsetHeight ?? 0);
            smoothScrollTo(offset);
        }

        closeMenu();
    });
});

function formatCounter(value, decimals) {
    if (decimals > 0) return value.toFixed(decimals);
    return Math.round(value).toLocaleString('pt-BR');
}

function animateCounter(element) {
    const target = parseFloat(element.dataset.target);
    const decimals = parseInt(element.dataset.decimals || '0', 10);
    const prefix = element.dataset.prefix || '';
    const valueElement = element.querySelector('.counter-value');

    if (!valueElement || Number.isNaN(target)) return;

    const duration = 1800;
    const startTime = performance.now();

    function step(now) {
        const progress = Math.min((now - startTime) / duration, 1);
        const current = target * easeInOutCubic(progress);
        valueElement.textContent = prefix + formatCounter(current, decimals);
        if (progress < 1) requestAnimationFrame(step);
    }

    requestAnimationFrame(step);
}

if ('IntersectionObserver' in window) {
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.4 });

    document.querySelectorAll('.counter-number').forEach((element) => counterObserver.observe(element));
}
document.querySelectorAll('[data-contact-form]').forEach(form=>form.addEventListener('submit',e=>{e.preventDefault();const d=new FormData(form);const text=encodeURIComponent(`Olá! Meu nome é ${d.get('nome')}.\nTelefone: ${d.get('telefone')}\nMensagem: ${d.get('mensagem')}`);window.open(`https://wa.me/5551984531794?text=${text}`,'_blank','noopener')}));
