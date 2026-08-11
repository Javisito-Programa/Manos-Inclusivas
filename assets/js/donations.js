document.addEventListener('DOMContentLoaded', () => {
    // Tab switching logic
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    if (tabBtns.length > 0 && tabPanes.length > 0) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all
                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));

                // Add active class to clicked
                btn.classList.add('active');
                const targetId = btn.getAttribute('data-target');
                document.getElementById(targetId).classList.add('active');
            });
        });
    }

    // Amount Selection Logic for Online Donations
    const amountBtns = document.querySelectorAll('.amount-btn');
    const customAmountContainer = document.getElementById('custom-amount-container');
    const customAmountInput = document.getElementById('custom-amount');

    if (amountBtns.length > 0) {
        amountBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class
                amountBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Handle 'Otra' option
                if (btn.getAttribute('data-amount') === 'other') {
                    customAmountContainer.style.display = 'block';
                    customAmountInput.required = true;
                } else {
                    customAmountContainer.style.display = 'none';
                    customAmountInput.required = false;
                }
            });
        });
    }

    // Copy to Clipboard (Bank Transfer)
    const copyBtns = document.querySelectorAll('.copy-btn');
    if (copyBtns.length > 0) {
        copyBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const targetId = btn.getAttribute('data-copy-target');
                const textToCopy = document.getElementById(targetId).innerText;

                navigator.clipboard.writeText(textToCopy).then(() => {
                    const originalText = btn.textContent;
                    btn.textContent = '¡Copiado!';
                    btn.classList.add('copied');
                    
                    setTimeout(() => {
                        btn.textContent = originalText;
                        btn.classList.remove('copied');
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            });
        });
    }
});
