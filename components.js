// FACE TITLES ON SCROLL


// Remove DOMContentLoaded wrapper
const observerOptions = {
  root: null,
  rootMargin: '0px',
  threshold: 0.1
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    }
  });
}, observerOptions);

document.querySelectorAll('.fade-on-scroll').forEach(el => {
  observer.observe(el);
});








// 1. Define loadComponent (Your existing function, kept as is)
function loadComponent(url, placeholderId, lang) {
    var finalUrl = lang ? '/' + lang + url : url;
    
    fetch(finalUrl)
        .then(function (res) { 
            if (!res.ok) throw new Error(`Failed to load ${finalUrl}`);
            return res.text(); 
        })
        .then(function (html) {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var wrapper = doc.body.firstElementChild;
            var target = document.getElementById(placeholderId);
            
            if (target && wrapper) {
                target.parentNode.replaceChild(wrapper, target);
                
                // 2. Initialize Switcher IMMEDIATELY after replacement
                // We pass the langParam to help logic if needed, but mostly just trigger init
                setTimeout(initLanguageSwitcher, 50);
            } else {
                console.error("Could not find placeholder #" + placeholderId);
            }
        })
        .catch(function (err) {
            console.error("Error loading component:", err);
        });
}

function getActiveLanguage(path) {
    var languageMatch = path.match(/^\/(gr|fr)(?=\/|$)/);

    if (languageMatch && languageMatch[1]) {
        return languageMatch[1];
    }

    return 'en';
}

// 3. Auto-load Logic
(function() {
    var path = window.location.pathname;
    var activeLanguage = getActiveLanguage(path);
    var langParam = activeLanguage === 'en' ? null : activeLanguage;

    console.log("Initializing components. Language detected: " + activeLanguage.toUpperCase());
    
    loadComponent('/header.html', 'site-header-placeholder', langParam);
    loadComponent('/footer.html', 'site-footer-placeholder', langParam);
})();

// 4. The Switcher Function with Retry Logic
function initLanguageSwitcher(retriesLeft) {
    retriesLeft = retriesLeft || 5; // Default 5 retries
    
    var switcher = document.querySelector('.lang-switcher');
    
    if (!switcher) {
        if (retriesLeft > 0) {
            console.log("Switcher not found yet. Retrying in 100ms... (" + retriesLeft + " left)");
            setTimeout(function() {
                initLanguageSwitcher(retriesLeft - 1);
            }, 100);
        } else {
            console.error("ERROR: Could not find #lang-switcher after multiple attempts.");
        }
        return;
    }

    console.log("Switcher found! Setting up EN/GR/FR links.");

    var englishLink = switcher.querySelector('a[data-lang="en"]');
    var greekLink = switcher.querySelector('a[data-lang="gr"]');
    var frenchLink = switcher.querySelector('a[data-lang="fr"]');

    if (!englishLink || !greekLink || !frenchLink) {
        console.error("ERROR: Language links are missing in .lang-switcher.");
        return;
    }

    // Detect active language and build equivalent EN, GR, FR paths.
    var path = window.location.pathname;
    var activeLanguage = getActiveLanguage(path);
    var basePath = path.replace(/^\/(gr|fr)(?=\/|$)/, '');

    if (basePath === '') {
        basePath = '/';
    }

    var sharedLocalizedPaths = [
        '/',
        '/index.html',
        '/about.html',
        '/the-house.html',
        '/products.html',
        '/find.html',
        '/nearby.html',
        '/contact.html'
    ];

    var hasLocalizedEquivalent = sharedLocalizedPaths.indexOf(basePath) !== -1;
    var englishPath = hasLocalizedEquivalent ? basePath : '/';

    var greekPath = hasLocalizedEquivalent
        ? (basePath === '/' ? '/gr/' : '/gr' + basePath)
        : '/gr/';

    var frenchPath = hasLocalizedEquivalent
        ? (basePath === '/' ? '/fr/' : '/fr' + basePath)
        : '/fr/';

    // Debug Log
    console.log("Current Path:", path);
    console.log("Active Language:", activeLanguage.toUpperCase());
    console.log("English Path:", englishPath);
    console.log("Greek Path:", greekPath);
    console.log("French Path:", frenchPath);

    englishLink.href = englishPath;
    greekLink.href = greekPath;
    frenchLink.href = frenchPath;

    englishLink.classList.remove('is-active');
    greekLink.classList.remove('is-active');
    frenchLink.classList.remove('is-active');

    if (activeLanguage === 'gr') {
        greekLink.classList.add('is-active');
    } else if (activeLanguage === 'fr') {
        frenchLink.classList.add('is-active');
    } else {
        englishLink.classList.add('is-active');
    }

    switcher.style.cursor = 'default';
}

// Home Hero Slider

function initHeroSlider() {
    var slider = document.querySelector('.hero-slider');

    if (!slider) {
        return;
    }

    var slides = slider.querySelectorAll('.hero-slide');
    var pagerButtons = slider.querySelectorAll('.hero-slider-page');
    var prevButton = slider.querySelector('.hero-slider-arrow-prev');
    var nextButton = slider.querySelector('.hero-slider-arrow-next');

    if (!slides.length) {
        return;
    }

    var activeIndex = 0;

    function setActiveSlide(targetIndex) {
        if (targetIndex < 0) {
            targetIndex = slides.length - 1;
        }

        if (targetIndex >= slides.length) {
            targetIndex = 0;
        }

        activeIndex = targetIndex;

        slides.forEach(function(slide, index) {
            var isActive = index === activeIndex;
            slide.classList.toggle('is-active', isActive);
            slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
        });

        pagerButtons.forEach(function(button, index) {
            var isActive = index === activeIndex;
            button.classList.toggle('is-active', isActive);

            if (isActive) {
                button.setAttribute('aria-current', 'true');
            } else {
                button.removeAttribute('aria-current');
            }
        });
    }

    if (prevButton) {
        prevButton.addEventListener('click', function() {
            setActiveSlide(activeIndex - 1);
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', function() {
            setActiveSlide(activeIndex + 1);
        });
    }

    pagerButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var targetIndex = parseInt(button.getAttribute('data-slide-to'), 10);

            if (!Number.isNaN(targetIndex)) {
                setActiveSlide(targetIndex);
            }
        });
    });

    slider.setAttribute('tabindex', '0');
    slider.addEventListener('keydown', function(event) {
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            setActiveSlide(activeIndex - 1);
        }

        if (event.key === 'ArrowRight') {
            event.preventDefault();
            setActiveSlide(activeIndex + 1);
        }
    });

    setActiveSlide(0);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeroSlider);
} else {
    initHeroSlider();
}

// Hero Zoom Observer

function observeHeroZoom(heroId) {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    var hero = document.getElementById(heroId);

    if (!hero || !hero.classList.contains('hero-section') || hero.classList.contains('hero-slider')) {
        return;
    }

    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting && entry.intersectionRatio >= 0.35) {
                hero.classList.add('hero-zoom-active');
            } else {
                hero.classList.remove('hero-zoom-active');
            }
        });
    }, {
        threshold: [0.2, 0.35, 0.6]
    });

    observer.observe(hero);
}

function initHeroZoomObserver() {
    observeHeroZoom('home');
    observeHeroZoom('location-hero');
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeroZoomObserver);
} else {
    initHeroZoomObserver();
}

// Sounds Spin Observer

function initSoundsSpinObserver() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    var soundDisc = document.querySelector('.sounds-spin-on-view');

    if (!soundDisc) {
        return;
    }

    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting && entry.intersectionRatio >= 0.45) {
                soundDisc.classList.add('sounds-spin-active');
            } else {
                soundDisc.classList.remove('sounds-spin-active');
            }
        });
    }, {
        threshold: [0.2, 0.45, 0.7]
    });

    observer.observe(soundDisc);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSoundsSpinObserver);
} else {
    initSoundsSpinObserver();
}