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

// 3. Auto-load Logic
(function() {
    var path = window.location.pathname;
    var isGreek = path.indexOf('/gr/') !== -1 || path.indexOf('/gr') === 0;
    var langParam = isGreek ? 'gr' : null;

    console.log("Initializing components. Language detected: " + (isGreek ? "Greek" : "English"));
    
    loadComponent('/header.html', 'site-header-placeholder', langParam);
    loadComponent('/footer.html', 'site-footer-placeholder', langParam);
})();

// 4. The Switcher Function with Retry Logic
function initLanguageSwitcher(retriesLeft) {
    retriesLeft = retriesLeft || 5; // Default 5 retries
    
    var switcher = document.getElementById('lang-switcher');
    
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

    console.log("Switcher found! Setting up redirect logic.");

    // Detect Language
    var path = window.location.pathname;
    var isGreek = path.indexOf('/gr/') !== -1 || path.indexOf('/gr') === 0;
    
    // Calculate Target
    var currentPath = path.replace(/^\/gr\//, '/').replace(/^\/gr$/, '/');
    var targetPath = isGreek ? currentPath : (currentPath === '/' || currentPath === '' ? '/gr/' : '/gr' + currentPath);

    // Debug Log
    console.log("Current Path:", path);
    console.log("Is Greek:", isGreek);
    console.log("Target Path:", targetPath);

    // Set Click Event
    switcher.onclick = function(e) {
        e.preventDefault();
        console.log("Redirecting to: " + targetPath);
        window.location.href = targetPath;
    };
    
    // Optional: Add hover style via JS if CSS isn't working
    switcher.style.cursor = 'pointer';
    switcher.style.color = '#0d5eaf'; // Your brand blue
}