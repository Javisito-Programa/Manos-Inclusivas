document.addEventListener('DOMContentLoaded', () => {
    const langBtn = document.getElementById('lang-btn');
    const langDropdown = document.getElementById('lang-dropdown');
    const currentLangText = document.getElementById('current-lang-text');
    const langItems = document.querySelectorAll('.lang-dropdown li');
    
    // Toggle dropdown
    if(langBtn) {
        langBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            langDropdown.classList.toggle('show');
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', () => {
        if(langDropdown && langDropdown.classList.contains('show')) {
            langDropdown.classList.remove('show');
        }
    });

    // Check cookie for initial language
    const getCookie = (name) => {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    };

    const updateUI = (langCode) => {
        langItems.forEach(item => {
            if(item.getAttribute('data-lang') === langCode) {
                item.classList.add('active');
                if(currentLangText) currentLangText.textContent = item.textContent;
            } else {
                item.classList.remove('active');
            }
        });
    };

    // Initialize from cookie (googtrans usually looks like /es/en)
    const googtrans = getCookie('googtrans');
    if (googtrans) {
        const lang = googtrans.split('/')[2] || 'es';
        updateUI(lang);
    }

    // Handle selection
    langItems.forEach(item => {
        item.addEventListener('click', (e) => {
            const lang = item.getAttribute('data-lang');
            
            // Set cookie for all paths and domains to ensure persistence across pages
            document.cookie = `googtrans=/es/${lang}; path=/`;
            document.cookie = `googtrans=/es/${lang}; domain=${window.location.hostname}; path=/`;
            
            updateUI(lang);
            
            // Reload page to apply translation (Google Translate script will read the cookie on load)
            window.location.reload();
        });
    });
});


// Forcefully remove Google Translate top banner continuously
setInterval(() => {
    const banners = document.querySelectorAll('.goog-te-banner-frame, .VIpgJd-Zvi9od-xl07Ob-OEVmcd');
    banners.forEach(b => {
        if(b.style.display !== 'none') b.style.display = 'none';
    });
    if (document.body.style.top && document.body.style.top !== '0px') {
        document.body.style.top = '0px';
    }
    
    // Fix bad translations for English
    const getCookie = (name) => {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    };
    
    const googtrans = getCookie('googtrans');
    if (googtrans && googtrans.includes('/en')) {
        document.querySelectorAll('a, .nav-link, .dropdown-item').forEach(el => {
            if (el.textContent.trim() === 'Start') el.textContent = 'Home';
            if (el.textContent.trim() === 'We') el.textContent = 'About Us';
        });
    }
}, 500);
