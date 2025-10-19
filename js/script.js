// Theme toggle
const btn = document.getElementById('themeToggle');
btn.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    const pressed = document.body.classList.contains('dark-mode');
    btn.setAttribute('aria-pressed', pressed ? 'true' : 'false');
    btn.style.background = pressed ? '#eee' : '#111';
    btn.style.color = pressed ? '#111' : '#fff';
});


// Horizontal drag scroll (UX polish)
const scroller = document.querySelector('.posts-scroll');
let isDown = false, startX, scrollLeft;

scroller.addEventListener('pointerdown', (e) => {
    isDown = true;
    startX = e.pageX - scroller.offsetLeft;
    scrollLeft = scroller.scrollLeft;
    scroller.setPointerCapture(e.pointerId);
});

scroller.addEventListener('pointermove', (e) => {
    if (!isDown) return;
    const x = e.pageX - scroller.offsetLeft;
    const walk = (x - startX) * 1.1;
    scroller.scrollLeft = scrollLeft - walk;
});

scroller.addEventListener('pointerup', () => { isDown = false; });
scroller.addEventListener('pointercancel', () => { isDown = false; });



document.addEventListener("scroll", () => {
    const cards = document.querySelectorAll(".parallax-card");
    cards.forEach(card => {
        let offset = window.scrollY * 0.4; // adjust speed
        card.style.backgroundPositionY = `${offset}px`;
    });
});


// Fade-up animation for overlay card for Features section when it enters viewport
document.addEventListener("DOMContentLoaded", () => {
    const overlay = document.getElementById("feature-overlay");

    const observer = new IntersectionObserver(
        entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    overlay.classList.remove("opacity-0", "translate-y-6");
                    overlay.classList.add("opacity-100", "translate-y-0");
                    observer.unobserve(overlay); // run only once
                }
            });
        },
        { threshold: 0.3 }
    );

    if (overlay) observer.observe(overlay);
});