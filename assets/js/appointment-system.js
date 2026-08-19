document.addEventListener('DOMContentLoaded', () => {
    // Mock Data for Clinics and Appointments
    const clinicsData = [
        {
            id: 'merida',
            name: 'Sede Principal Mérida',
            address: 'calle 20-H # 271. Int. 13. Fraccionamiento Las Palmas, Kanasín. Yucatán.',
            phone: '999 112 1609',
            lat: 20.9360,
            lng: -89.5590,
            services: ['psicologia', 'terapia-lenguaje', 'integracion-sensorial']
        },
        {
            id: 'merida-norte',
            name: 'Sede Mérida Norte',
            address: 'Av. Paseo de Montejo, Mérida',
            phone: '999 000 0000',
            lat: 21.0000,
            lng: -89.6200,
            services: ['psicologia', 'psiquiatria', 'terapia-ocupacional']
        }
    ];

    const clinicSelect = document.getElementById('clinic-select');
    const serviceSelect = document.getElementById('service-select');
    const appointmentForm = document.getElementById('appointment-form');
    const geoBtn = document.getElementById('btn-geo');
    const clinicInfoCard = document.getElementById('clinic-info-card');

    if (!clinicSelect || !appointmentForm) return;

    // Populate clinics
    clinicsData.forEach(clinic => {
        const option = document.createElement('option');
        option.value = clinic.id;
        option.textContent = clinic.name;
        clinicSelect.appendChild(option);
    });

    // Handle Clinic Selection Change
    clinicSelect.addEventListener('change', (e) => {
        const selectedId = e.target.value;
        const clinic = clinicsData.find(c => c.id === selectedId);
        
        updateServiceOptions(clinic);
        updateClinicCard(clinic);
    });

    function updateServiceOptions(clinic) {
        // Reset services
        serviceSelect.innerHTML = '<option value="">-- Selecciona un Servicio --</option>';
        if (!clinic) return;

        // Mock translation map
        const serviceNames = {
            'psicologia': 'Psicología',
            'psiquiatria': 'Psiquiatría',
            'terapia-lenguaje': 'Terapia de Lenguaje',
            'terapia-ocupacional': 'Terapia Ocupacional',
            'integracion-sensorial': 'Integración Sensorial'
        };

        clinic.services.forEach(svc => {
            const option = document.createElement('option');
            option.value = svc;
            option.textContent = serviceNames[svc] || svc;
            serviceSelect.appendChild(option);
        });
    }

    function updateClinicCard(clinic) {
        if (!clinic) {
            clinicInfoCard.innerHTML = '<p>Selecciona una clínica para ver sus detalles.</p>';
            return;
        }

        clinicInfoCard.innerHTML = `
            <h4>${clinic.name}</h4>
            <p><strong>Dirección:</strong> ${clinic.address}</p>
            <p><strong>Teléfono:</strong> ${clinic.phone}</p>
        `;
    }

    // Geolocation handling (Haversine formula placeholder)
    if (geoBtn) {
        geoBtn.addEventListener('click', () => {
            geoBtn.textContent = "Buscando...";
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        // Mock finding nearest
                        // In reality, apply Haversine distance from position.coords.latitude/longitude
                        setTimeout(() => {
                            clinicSelect.value = 'merida'; // Force Merida as closest for demo
                            clinicSelect.dispatchEvent(new Event('change'));
                            geoBtn.textContent = "Ubicación Usada";
                            geoBtn.classList.add('btn-primary');
                            geoBtn.classList.remove('btn-outline');
                        }, 500);
                    },
                    (error) => {
                        alert("No se pudo obtener la ubicación. Selecciona manualmente.");
                        geoBtn.textContent = "Usar mi ubicación actual";
                    }
                );
            } else {
                alert("Geolocalización no soportada.");
            }
        });
    }

    // Form Submission
    appointmentForm.addEventListener('submit', (e) => {
        e.preventDefault();
        // Simulate processing
        const btn = appointmentForm.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        btn.textContent = "Procesando...";
        btn.disabled = true;

        setTimeout(() => {
            alert("¡Cita pre-registrada exitosamente! Hemos enviado una notificación al WhatsApp oficial (999 112 1609) para confirmar disponibilidad.");
            appointmentForm.reset();
            btn.textContent = originalText;
            btn.disabled = false;
            updateServiceOptions(null);
            updateClinicCard(null);
        }, 1500);
    });
});
