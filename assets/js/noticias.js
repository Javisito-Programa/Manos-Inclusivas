document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('news-modal');
    var closeBtn = document.getElementById('close-news-modal');
    var prevBtn = document.getElementById('carousel-prev');
    var nextBtn = document.getElementById('carousel-next');
    var dotsContainer = document.getElementById('carousel-dots');
    var modalImg = document.getElementById('modal-img');
    var modalTitle = document.getElementById('modal-title');
    var modalDate = document.getElementById('modal-date');
    var modalText = document.getElementById('modal-text');

    if (!modal) return;

    var currentImages = [];
    var currentIndex = 0;

    function updateCarousel() {
        if (currentImages.length === 0) return;
        if (modalImg) modalImg.src = currentImages[currentIndex];

        if (dotsContainer) {
            var dots = dotsContainer.children;
            for (var j = 0; j < dots.length; j++) {
                if (j === currentIndex) {
                    dots[j].classList.add('active');
                } else {
                    dots[j].classList.remove('active');
                }
            }
        }
    }

    var readMoreBtns = document.querySelectorAll('.news-read-more');
    for (var i = 0; i < readMoreBtns.length; i++) {
        readMoreBtns[i].addEventListener('click', function(e) {
            e.preventDefault();
            var btn = this;
            
            var title = btn.getAttribute('data-title') || '';
            var date = btn.getAttribute('data-date') || '';
            var img = btn.getAttribute('data-img') || '';
            var content = btn.getAttribute('data-content') || '';
            var extrasRaw = btn.getAttribute('data-extras') || '[]';

            if (modalTitle) modalTitle.textContent = title;
            if (modalDate) modalDate.textContent = date;
            if (modalText) modalText.innerHTML = content;

            currentImages = [img];
            try {
                var extras = JSON.parse(extrasRaw);
                if (Object.prototype.toString.call(extras) === '[object Array]') {
                    for (var k = 0; k < extras.length; k++) {
                        currentImages.push(extras[k]);
                    }
                }
            } catch (err) {}

            currentIndex = 0;
            if (dotsContainer) dotsContainer.innerHTML = '';

            if (currentImages.length > 1) {
                if (prevBtn) prevBtn.style.display = 'flex';
                if (nextBtn) nextBtn.style.display = 'flex';

                if (dotsContainer) {
                    for (var idx = 0; idx < currentImages.length; idx++) {
                        (function(dotIndex) {
                            var dot = document.createElement('div');
                            dot.className = 'carousel-dot';
                            dot.addEventListener('click', function() {
                                currentIndex = dotIndex;
                                updateCarousel();
                            });
                            dotsContainer.appendChild(dot);
                        })(idx);
                    }
                }
            } else {
                if (prevBtn) prevBtn.style.display = 'none';
                if (nextBtn) nextBtn.style.display = 'none';
            }

            updateCarousel();
            
            // Mostrar modal con animacion
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevenir scroll del fondo
            
            // Forzar un reflow para que la transicion de opacidad funcione
            void modal.offsetWidth;
            modal.classList.add('show');
        });
    }

    function closeModal() {
        modal.classList.remove('show');
        setTimeout(function() {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300); // Igual a la transicion CSS
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : currentImages.length - 1;
            updateCarousel();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            currentIndex = (currentIndex < currentImages.length - 1) ? currentIndex + 1 : 0;
            updateCarousel();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
});
