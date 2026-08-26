import glob
import re
import urllib.request
import hashlib
import base64
import os

# 1. Download and hash translate script
url = "https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
try:
    with urllib.request.urlopen(req) as response:
        content = response.read()
        hash_sha384 = hashlib.sha384(content).digest()
        translate_sri = base64.b64encode(hash_sha384).decode('utf-8')
except Exception as e:
    print("Failed to hash translate", e)
    translate_sri = ""

# 2. Extract scripts content
security_js = """
// 1. Bloquear click derecho
document.addEventListener('contextmenu', event => event.preventDefault());

// 2. Bloquear atajos de teclado (F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U)
document.addEventListener('keydown', function (e) {
    if (
        e.key === 'F12' ||
        (e.ctrlKey && e.shiftKey && e.key === 'I') ||
        (e.ctrlKey && e.shiftKey && e.key === 'J') ||
        (e.ctrlKey && e.key === 'U') ||
        (e.metaKey && e.altKey && e.key === 'I') // Mac
    ) {
        e.preventDefault();
        return false;
    }
});

function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'es',
        includedLanguages: 'en,fr,de,zh-CN,it,pt,ja,es',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE
    }, 'google_translate_element');
}
"""

with open("assets/js/security.js", "w", encoding="utf-8") as f:
    f.write(security_js)

success_js = """
document.addEventListener('DOMContentLoaded', () => {
    // Parse URL Parameters
    const urlParams = new URLSearchParams(window.location.search);
    
    const amount = parseFloat(urlParams.get('amount') || 0);
    const freq = urlParams.get('freq') || '';
    const auth = urlParams.get('auth') || '';
    const donorName = urlParams.get('name') || '';
    const email = urlParams.get('email') || '';

    // Fallback: If no parameters, user probably accessed directly. Redirect to donar.html
    if (amount === 0 || !auth) {
        window.location.replace('donar.html');
    }

    // Fill receipt
    document.getElementById('r-name').textContent = donorName || 'Donante Anónimo';
    document.getElementById('r-email').textContent = email || 'No proporcionado';
    document.getElementById('r-freq').textContent = freq === 'mensual' ? 'Aportación Mensual' : 'Aportación Única';
    document.getElementById('r-auth').textContent = auth;
    document.getElementById('r-amount').textContent = `$${amount.toFixed(2)} MXN`;
});
"""

with open("assets/js/success.js", "w", encoding="utf-8") as f:
    f.write(success_js)

nosotros_js = """
// Simple script for sidebar active state
document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.page-section-card');
    const navLinks = document.querySelectorAll('.sidebar-nav-link');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (scrollY >= (sectionTop - 200)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').includes(current)) {
                link.classList.add('active');
            }
        });
    });
});
"""

with open("assets/js/nosotros-scroll.js", "w", encoding="utf-8") as f:
    f.write(nosotros_js)

recursos_js = """
document.addEventListener('DOMContentLoaded', () => {
    const bubble = document.querySelector('.hero-recursos-content');
    if(!bubble) return;
    
    // Mouse Interaction (PC)
    document.addEventListener('mousemove', (e) => {
        const x = (window.innerWidth / 2 - e.pageX) / 30;
        const y = (window.innerHeight / 2 - e.pageY) / 30;
        
        bubble.style.transform = `translate(${x}px, ${y}px) rotate(${x/10}deg)`;
    });

    // Gyroscope Interaction (Mobile)
    if (window.DeviceOrientationEvent) {
        window.addEventListener('deviceorientation', (e) => {
            if (e.gamma !== null && e.beta !== null) {
                // gamma is left-to-right tilt in degrees, where right is positive
                // beta is front-to-back tilt in degrees, where front is positive
                const x = e.gamma / 2; // adjust sensitivity
                const y = (e.beta - 45) / 2; // assuming device is held at 45 degree angle
                
                bubble.style.transform = `translate(${x}px, ${y}px) rotate(${x/10}deg)`;
            }
        });
    }
});
"""

with open("assets/js/recursos-bubble.js", "w", encoding="utf-8") as f:
    f.write(recursos_js)

# 3. Modify HTML files
html_files = glob.glob("*.html")
for f in html_files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Remove security script block
    content = re.sub(r'<script>\s*// 1\. Bloquear click derecho.*?</script>', '<script src="assets/js/security.js"></script>', content, flags=re.DOTALL)
    
    # Remove google translate init script block
    content = re.sub(r'<script type="text/javascript">\s*function googleTranslateElementInit.*?<\/script>', '', content, flags=re.DOTALL)
    
    # Add SRI to translate element
    if translate_sri:
        content = content.replace(
            '<script type="text/javascript"\n        src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>',
            f'<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" integrity="sha384-{translate_sri}" crossorigin="anonymous"></script>'
        )
        content = content.replace(
            '<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>',
            f'<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" integrity="sha384-{translate_sri}" crossorigin="anonymous"></script>'
        )
        content = content.replace(
            '<script type="text/javascript"\r\n        src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>',
            f'<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" integrity="sha384-{translate_sri}" crossorigin="anonymous"></script>'
        )
    
    if f == 'donacion-exitosa.html':
        content = re.sub(r'<script>\s*// Parse URL Parameters.*?</script>', '<script src="assets/js/success.js"></script>', content, flags=re.DOTALL)
    
    if f == 'nosotros.html':
        content = re.sub(r'<script>\s*// Simple script for sidebar.*?</script>', '<script src="assets/js/nosotros-scroll.js"></script>', content, flags=re.DOTALL)
        
    if f == 'recursos.html':
        content = re.sub(r'<script>\s*document\.addEventListener\(\'DOMContentLoaded\', \(\) => {\s*const bubble.*?</script>', '<script src="assets/js/recursos-bubble.js"></script>', content, flags=re.DOTALL)

    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)

# 4. Modify .htaccess
with open('.htaccess', 'r', encoding='utf-8') as file:
    htaccess = file.read()

# Replace 'unsafe-inline' from script-src
htaccess = htaccess.replace("script-src 'self' 'unsafe-inline' 'unsafe-eval'", "script-src 'self' 'unsafe-eval'")
with open('.htaccess', 'w', encoding='utf-8') as file:
    file.write(htaccess)

print("Done")
