/**
 * ONE PIECE TCG - JAVASCRIPT
 * Rain Particle Engine, Conqueror's Haki FX & Interactive Store Operations
 */

document.addEventListener('DOMContentLoaded', () => {
    initRainEngine();
    initLanguageSwitcher();
    initCartModal();
    initHeroCarousel();
});

/* ========================================================
   CANVAS RAIN & OCEAN MIST ENGINE
   ======================================================== */
function initRainEngine() {
    const canvas = document.getElementById('rainCanvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let width = (canvas.width = window.innerWidth);
    let height = (canvas.height = window.innerHeight);

    window.addEventListener('resize', () => {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    });

    const dropCount = Math.floor(width / 7); // density adjusted to screen width
    const drops = [];

    for (let i = 0; i < dropCount; i++) {
        drops.push({
            x: Math.random() * width,
            y: Math.random() * height,
            length: Math.random() * 22 + 12,
            speed: Math.random() * 12 + 15,
            opacity: Math.random() * 0.35 + 0.15,
            width: Math.random() * 1.5 + 0.8,
            color: Math.random() > 0.88 ? 'rgba(255, 23, 68, 0.5)' : 'rgba(186, 230, 253, 0.4)'
        });
    }

    function animateRain() {
        ctx.clearRect(0, 0, width, height);

        for (let i = 0; i < drops.length; i++) {
            const d = drops[i];

            ctx.beginPath();
            ctx.moveTo(d.x, d.y);
            ctx.lineTo(d.x - 2.5, d.y + d.length); // slight wind slant
            ctx.strokeStyle = d.color;
            ctx.lineWidth = d.width;
            ctx.lineCap = 'round';
            ctx.stroke();

            d.y += d.speed;
            d.x -= 1.2; // wind direction

            // Reset when reaching bottom or side
            if (d.y > height) {
                d.y = -20;
                d.x = Math.random() * width + 50;
            }
            if (d.x < -10) {
                d.x = width + 10;
            }
        }

        requestAnimationFrame(animateRain);
    }

    animateRain();
}

/* ========================================================
   LANGUAGE SWITCHER DROPDOWN TOGGLE
   ======================================================== */
function initLanguageSwitcher() {
    const langBtn = document.getElementById('langToggleBtn');
    const langMenu = document.getElementById('langDropdownMenu');

    if (!langBtn || !langMenu) return;

    langBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        langMenu.classList.toggle('show');
    });

    document.addEventListener('click', () => {
        langMenu.classList.remove('show');
    });
}

/* ========================================================
   QUICK BUY & CART MODAL
   ======================================================== */
function initCartModal() {
    const modal = document.getElementById('quickBuyModal');
    if (!modal) return;

    window.openQuickBuy = function (id, name, price, stock, imageUrl) {
        document.getElementById('modalProductId').value = id;
        document.getElementById('modalProductName').innerText = name;
        document.getElementById('modalProductPrice').innerText = Number(price).toLocaleString('en-US', { minimumFractionDigits: 2 });
        document.getElementById('modalProductStock').innerText = stock;
        document.getElementById('modalProductImg').src = imageUrl;
        document.getElementById('modalQuantity').max = stock;
        document.getElementById('modalQuantity').value = 1;
        updateModalSubtotal();

        modal.classList.add('active');
    };

    window.closeQuickBuy = function () {
        modal.classList.remove('active');
    };

    window.updateModalSubtotal = function () {
        const qtyInput = document.getElementById('modalQuantity');
        const qty = parseInt(qtyInput ? qtyInput.value : 1) || 1;
        const priceEl = document.getElementById('modalProductPrice');
        const priceText = priceEl ? priceEl.innerText.replace(/,/g, '') : '0';
        const price = parseFloat(priceText) || 0;
        const subtotal = qty * price;
        
        const subtotalEl = document.getElementById('modalSubtotal');
        if (subtotalEl) {
            subtotalEl.innerText = subtotal.toLocaleString('en-US', { minimumFractionDigits: 2 });
        }

        const walletEl = document.getElementById('modalUserWallet');
        const warningEl = document.getElementById('modalBalanceWarning');
        const submitBtn = document.getElementById('modalSubmitBtn');

        if (walletEl && warningEl) {
            const balance = parseFloat(walletEl.getAttribute('data-balance')) || 0;
            if (subtotal > balance) {
                warningEl.style.display = 'flex';
                if (submitBtn) {
                    submitBtn.style.opacity = '0.7';
                }
            } else {
                warningEl.style.display = 'none';
                if (submitBtn) {
                    submitBtn.style.opacity = '1';
                }
            }
        }
    };

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeQuickBuy();
        }
    });
}

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.position = 'fixed';
    toast.style.bottom = '25px';
    toast.style.right = '25px';
    toast.style.zIndex = '9999';
    toast.style.boxShadow = '0 0 20px rgba(255, 23, 68, 0.6)';
    toast.innerHTML = `<i class="fa-solid fa-bell"></i> ${message}`;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.5s ease';
        setTimeout(() => toast.remove(), 500);
    }, 3500);
}

/* ========================================================
   HERO CAROUSEL & GAMING DISPLAY CASE SLIDER
   ======================================================== */
function initHeroCarousel() {
    const displayCase = document.getElementById('heroDisplayCase');
    const track = document.getElementById('heroSliderTrack');
    const prevBtn = document.getElementById('heroPrevBtn');
    const nextBtn = document.getElementById('heroNextBtn');
    const dotsContainer = document.getElementById('heroDots');

    if (!track || !displayCase) return;

    const slides = track.querySelectorAll('.hero-slide');
    const dots = dotsContainer ? dotsContainer.querySelectorAll('.hero-dot') : [];
    const totalSlides = slides.length;

    if (totalSlides <= 1) return;

    let currentSlide = 0;
    let autoPlayTimer = null;
    let isTransitioning = false;

    function updateSlide(newIndex) {
        if (isTransitioning) return;
        isTransitioning = true;

        if (newIndex < 0) {
            currentSlide = totalSlides - 1;
        } else if (newIndex >= totalSlides) {
            currentSlide = 0;
        } else {
            currentSlide = newIndex;
        }

        // Apply smooth horizontal transform to track
        track.style.transform = `translateX(-${currentSlide * 100}%)`;

        // Update active pagination dot
        dots.forEach((dot, idx) => {
            if (idx === currentSlide) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });

        setTimeout(() => {
            isTransitioning = false;
        }, 600);
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            updateSlide(currentSlide - 1);
            resetAutoPlay();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            updateSlide(currentSlide + 1);
            resetAutoPlay();
        });
    }

    // Dot navigation clicks
    dots.forEach((dot) => {
        dot.addEventListener('click', (e) => {
            e.preventDefault();
            const targetIndex = parseInt(dot.getAttribute('data-index'), 10);
            if (!isNaN(targetIndex) && targetIndex !== currentSlide) {
                updateSlide(targetIndex);
                resetAutoPlay();
            }
        });
    });

    // Auto Play (every 6.5 seconds)
    function startAutoPlay() {
        stopAutoPlay();
        autoPlayTimer = setInterval(() => {
            updateSlide(currentSlide + 1);
        }, 6500);
    }

    function stopAutoPlay() {
        if (autoPlayTimer) {
            clearInterval(autoPlayTimer);
            autoPlayTimer = null;
        }
    }

    function resetAutoPlay() {
        stopAutoPlay();
        startAutoPlay();
    }

    // Pause on hover over display case
    displayCase.addEventListener('mouseenter', stopAutoPlay);
    displayCase.addEventListener('mouseleave', startAutoPlay);

    // Touch swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    displayCase.addEventListener('touchstart', (e) => {
        stopAutoPlay();
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    displayCase.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
        startAutoPlay();
    }, { passive: true });

    function handleSwipe() {
        const threshold = 45;
        const diff = touchEndX - touchStartX;
        if (Math.abs(diff) > threshold) {
            if (diff < 0) {
                // Swiped left -> next
                updateSlide(currentSlide + 1);
            } else {
                // Swiped right -> prev
                updateSlide(currentSlide - 1);
            }
        }
    }

    // Keyboard navigation (Left / Right arrow keys when in viewport)
    document.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        const rect = displayCase.getBoundingClientRect();
        const isInViewport = rect.top < window.innerHeight && rect.bottom > 0;
        if (isInViewport) {
            if (e.key === 'ArrowLeft') {
                updateSlide(currentSlide - 1);
                resetAutoPlay();
            } else if (e.key === 'ArrowRight') {
                updateSlide(currentSlide + 1);
                resetAutoPlay();
            }
        }
    });

    // Start auto-play initially
    startAutoPlay();
}

