const originalPageTitle = document.title;

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

    // Check cookie for initial language (robust regex to handle multiple googtrans cookies)
    const getCookie = (name) => {
        const matches = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)', 'g'));
        if (matches) {
            for (let m of matches) {
                const val = m.split('=')[1];
                if (val && decodeURIComponent(val) !== '/es/es' && decodeURIComponent(val) !== '/auto/es') {
                    return val;
                }
            }
            return matches[0].split('=')[1];
        }
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

    // Initialize from cookie (googtrans usually looks like /es/en or %2Fes%2Fen)
    const googtrans = getCookie('googtrans');
    if (googtrans) {
        const decoded = decodeURIComponent(googtrans);
        const lang = decoded.split('/')[2] || 'es';
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
    
    // Restore original title if Google translated it
    if (document.title !== originalPageTitle) {
        document.title = originalPageTitle;
    }
    
    // Fix bad translations for English
    const getCookie = (name) => {
        const matches = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)', 'g'));
        if (matches) {
            for (let m of matches) {
                const val = m.split('=')[1];
                if (val && decodeURIComponent(val) !== '/es/es' && decodeURIComponent(val) !== '/auto/es') {
                    return val;
                }
            }
            return matches[0].split('=')[1];
        }
        return null;
    };
    
    const googtrans = getCookie('googtrans');
    if (googtrans && decodeURIComponent(googtrans).includes('/en')) {
        document.querySelectorAll('a, .nav-link, .dropdown-item').forEach(el => {
            const txt = el.textContent.trim().toLowerCase();
            if (txt === 'start') {
                el.textContent = el.textContent.toUpperCase() === el.textContent.trim() ? 'HOME' : 'Home';
            }
            if (txt === 'we' || txt === 'us') {
                el.textContent = el.textContent.toUpperCase() === el.textContent.trim() ? 'ABOUT US' : 'About Us';
            }
        });
    }
}, 500);
