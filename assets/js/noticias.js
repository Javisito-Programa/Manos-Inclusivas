document.addEventListener('DOMContentLoaded', function() {
    // 1. Accordion Toggle Logic
    var readMoreBtns = document.querySelectorAll('.news-read-more');
    for (var i = 0; i < readMoreBtns.length; i++) {
        readMoreBtns[i].addEventListener('click', function(e) {
            var btn = this;
            var span = btn.querySelector('span');
            
            // Buscar la tarjeta padre que contiene la noticia
            var card = btn;
            while (card && !card.classList.contains('news-card')) {
                card = card.parentNode;
            }
            
            if (card) {
                // Intercambiar estado
                if (card.classList.contains('expanded')) {
                    card.classList.remove('expanded');
                    if (span) span.innerText = 'Leer más ↓';
                } else {
                    card.classList.add('expanded');
                    if (span) span.innerText = 'Leer menos ↑';
                }
            }
        });
    }

    // 2. Gallery Image Click Logic (CSP-compliant replacing onclick)
    var galleryImages = document.querySelectorAll('.news-gallery img');
    for (var j = 0; j < galleryImages.length; j++) {
        galleryImages[j].addEventListener('click', function() {
            window.open(this.src, '_blank');
        });
    }
});
