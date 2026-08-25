document.addEventListener('DOMContentLoaded', () => {
    // Tab switching logic
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    if (tabBtns.length > 0 && tabPanes.length > 0) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                if(btn.tagName.toLowerCase() === 'a') { e.preventDefault(); }
                // Remove active class from all
                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));

                // Add active class to clicked
                btn.classList.add('active');
                const targetId = btn.getAttribute('data-target');
                document.getElementById(targetId).classList.add('active');
                
                // Scroll to content if it's not fully visible
                const targetEl = document.getElementById(targetId);
                if(targetEl && window.innerWidth < 768) {
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    }

    // Frequency change logic (Pill Switch)
    const freqPills = document.querySelectorAll('.freq-pill');
    const freqHidden = document.getElementById('freq_hidden');
    const monthlyLegend = document.getElementById('monthly-legend');
    
    if (freqPills.length > 0 && freqHidden) {
        freqPills.forEach(pill => {
            pill.addEventListener('click', () => {
                freqPills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                const val = pill.getAttribute('data-freq');
                freqHidden.value = val;
                
                if (monthlyLegend) {
                    monthlyLegend.style.display = val === 'monthly' ? 'block' : 'none';
                }
            });
        });
    }

    // Amount Selection and Validation Logic
    const amountBtns = document.querySelectorAll('.amount-btn');
    const customAmountContainer = document.getElementById('custom-amount-container');
    const customAmountInput = document.getElementById('custom-amount');
    const minAmountWarning = document.getElementById('min-amount-warning');
    const btnSubmit = document.getElementById('btn-submit-donation');
    const btnTotalText = document.getElementById('btn-total-text');
    const coverFeeCheckbox = document.getElementById('cover-fee');

    function getBaseAmount() {
        const activeBtn = document.querySelector('.amount-btn.active');
        if (!activeBtn) return 0;
        
        const amountVal = activeBtn.getAttribute('data-amount');
        if (amountVal === 'other') {
            const parsed = parseFloat(customAmountInput.value);
            return isNaN(parsed) ? 0 : parsed;
        }
        return parseFloat(amountVal);
    }

    function updateDonationUI() {
        if (!btnSubmit) return;
        
        const baseAmount = getBaseAmount();
        
        // Handle Minimum Amount Validation
        if (baseAmount > 0 && baseAmount < 50) {
            if (minAmountWarning) minAmountWarning.style.display = 'block';
            btnSubmit.disabled = true;
            btnSubmit.style.opacity = '0.5';
            btnSubmit.style.cursor = 'not-allowed';
            if (btnTotalText) btnTotalText.textContent = '';
            return;
        } else {
            if (minAmountWarning) minAmountWarning.style.display = 'none';
            btnSubmit.disabled = false;
            btnSubmit.style.opacity = '1';
            btnSubmit.style.cursor = 'pointer';
        }

        // Handle Cover Fee Logic
        if (baseAmount > 0) {
            let total = baseAmount;
            if (coverFeeCheckbox && coverFeeCheckbox.checked) {
                total += 3.50;
            }
            if (btnTotalText) btnTotalText.textContent = `($${total.toFixed(2)} MXN)`;
        } else {
            if (btnTotalText) btnTotalText.textContent = '';
        }
    }

    // Toggle CFDI fields requirement
    const requireCfdiCheckbox = document.getElementById('require_cfdi');
    const cfdiFields = document.getElementById('cfdi-fields');
    if (requireCfdiCheckbox && cfdiFields) {
        requireCfdiCheckbox.addEventListener('change', () => {
            if (requireCfdiCheckbox.checked) {
                cfdiFields.classList.add('show');
            } else {
                cfdiFields.classList.remove('show');
            }
            // Toggle required on cfdi inputs
            const cfdiInputs = cfdiFields.querySelectorAll('input, select');
            cfdiInputs.forEach(input => {
                input.required = requireCfdiCheckbox.checked;
            });
        });
    }

    // Openpay Setup & Form Submission
    let deviceSessionId = null;
    
    // We will initialize Openpay using public keys. We can fetch them or assume they are replaced in prod.
    // For now, we assume they are configured here or injected via a separate script.
    // Using placeholders, but Openpay requires actual ones to generate the token. 
    // In production, BBVA will provide these.
    OpenPay.setId('YOUR_MERCHANT_ID');
    OpenPay.setApiKey('YOUR_PUBLIC_KEY');
    OpenPay.setSandboxMode(true); // Set to false in prod
    
    // Setup device session ID for fraud prevention
    deviceSessionId = OpenPay.deviceData.setup("payment-form", "deviceIdHiddenFieldName");

    if (btnSubmit) {
        btnSubmit.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Basic validation
            const form = btnSubmit.closest('form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Procesando... <span class="spinner"></span>';

            // Tokenize card
            OpenPay.token.extractFormAndCreate('payment-form', function(response) {
                // Success callback
                const token_id = response.data.id;
                
                // Collect payload
                const baseAmount = getBaseAmount();
                let total = baseAmount;
                if (coverFeeCheckbox && coverFeeCheckbox.checked) {
                    total += 3.50;
                }
                
                const isRecurring = document.getElementById('freq_hidden').value === 'monthly';
                const email = document.getElementById('email_donante').value;
                const name = document.getElementById('holder_name').value;
                const reqCfdi = requireCfdiCheckbox && requireCfdiCheckbox.checked;
                
                let billing_data = null;
                if (reqCfdi) {
                    billing_data = {
                        rfc: document.getElementById('cfdi_rfc').value,
                        razon_social: document.getElementById('cfdi_razon').value,
                        cp: document.getElementById('cfdi_cp').value,
                        regimen: document.getElementById('cfdi_regimen').value
                    };
                }

                const payload = {
                    token_id: token_id,
                    device_session_id: deviceSessionId,
                    amount: total,
                    email: email,
                    name: name,
                    is_recurring: isRecurring,
                    require_cfdi: reqCfdi,
                    billing_data: billing_data
                };

                // Send to our backend
                fetch('api/donaciones/procesar.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.redirect_url) {
                        // 3D Secure Redirect
                        window.location.href = data.redirect_url;
                    } else if (data.success) {
                        const queryParams = new URLSearchParams({
                            amount: data.amount || total,
                            freq: isRecurring ? 'mensual' : 'unica',
                            auth: data.auth_code || data.authorization || 'Procesado',
                            name: name,
                            email: email
                        }).toString();
                        window.location.href = 'donacion-exitosa.html?' + queryParams;
                    } else {
                        alert('Error al procesar el pago: ' + (data.message || 'Intente nuevamente.'));
                        btnSubmit.disabled = false;
                        updateDonationUI();
                    }
                })
                .catch(err => {
                    // console.error(err);
                    alert('Error de conexión. Intente nuevamente.');
                    btnSubmit.disabled = false;
                    updateDonationUI();
                });

            }, function(error) {
                // Error callback
                alert('Error en los datos de la tarjeta: ' + error.data.description);
                btnSubmit.disabled = false;
                updateDonationUI();
            });
        });
    }

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
                    customAmountInput.value = ''; // Clear input if returning from other
                }
                
                updateDonationUI();
            });
        });
        
        // Listen to custom input
        if (customAmountInput) {
            customAmountInput.addEventListener('input', updateDonationUI);
        }
        
        // Listen to fee checkbox
        if (coverFeeCheckbox) {
            coverFeeCheckbox.addEventListener('change', updateDonationUI);
        }
        
        // Initial run
        updateDonationUI();
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
                    // console.error('Failed to copy: ', err);
                });
            });
        });
    }
});
