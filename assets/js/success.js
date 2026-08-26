
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
