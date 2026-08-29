document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('news-modal');
    var closeBtn = document.querySelector('.close-modal');
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
                dots[j].style.background = (j === currentIndex) ? 'var(--accent-purple)' : 'rgba(255,255,255,0.5)';
            }
        }
    }

    // AUTO-ROTATE LOGIC FOR PREVIEW CARDS
    var readMoreBtns = document.querySelectorAll('.news-read-more');
    for (var i = 0; i < readMoreBtns.length; i++) {
        (function(btn) {
            var card = btn.closest('.news-card');
            if (!card) return;
            var imgEl = card.querySelector('.news-image');
            var extrasRaw = btn.getAttribute('data-extras') || '[]';
            var mainImg = btn.getAttribute('data-img') || '';
            var images = [mainImg];
            
            try {
                var extras = JSON.parse(extrasRaw);
                if (Object.prototype.toString.call(extras) === '[object Array]') {
                    for (var k = 0; k < extras.length; k++) {
                        images.push(extras[k]);
                    }
                }
            } catch (e) {}

            if (images.length > 1 && imgEl) {
                var currentCardIndex = 0;
                setInterval(function() {
                    currentCardIndex = (currentCardIndex + 1) % images.length;
                    imgEl.src = images[currentCardIndex];
                }, 3000); // 3 seconds per image
            }
        })(readMoreBtns[i]);
    }

    // AUTO-ROTATE LOGIC FOR MODAL
    var modalInterval = null;

    function startModalAutoRotate() {
        stopModalAutoRotate();
        if (currentImages.length > 1) {
            modalInterval = setInterval(function() {
                currentIndex = (currentIndex + 1) % currentImages.length;
                updateCarousel();
            }, 3000);
        }
    }

    function stopModalAutoRotate() {
        if (modalInterval) clearInterval(modalInterval);
    }

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
                if (prevBtn) prevBtn.style.display = 'block';
                if (nextBtn) nextBtn.style.display = 'block';

                if (dotsContainer) {
                    for (var idx = 0; idx < currentImages.length; idx++) {
                        (function(dotIndex) {
                            var dot = document.createElement('div');
                            dot.style.width = '10px';
                            dot.style.height = '10px';
                            dot.style.borderRadius = '50%';
                            dot.style.cursor = 'pointer';
                            dot.onclick = function() {
                                currentIndex = dotIndex;
                                updateCarousel();
                                startModalAutoRotate(); // reset timer
                            };
                            dotsContainer.appendChild(dot);
                        })(idx);
                    }
                }
            } else {
                if (prevBtn) prevBtn.style.display = 'none';
                if (nextBtn) nextBtn.style.display = 'none';
            }

            updateCarousel();
            modal.style.display = 'block';
            startModalAutoRotate();
        });
    }

    if (prevBtn) {
        prevBtn.onclick = function(e) {
            e.preventDefault();
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : currentImages.length - 1;
            updateCarousel();
            startModalAutoRotate(); // reset timer
        };
    }

    if (nextBtn) {
        nextBtn.onclick = function(e) {
            e.preventDefault();
            currentIndex = (currentIndex < currentImages.length - 1) ? currentIndex + 1 : 0;
            updateCarousel();
            startModalAutoRotate(); // reset timer
        };
    }

    if (closeBtn) {
        closeBtn.onclick = function() {
            modal.style.display = 'none';
            stopModalAutoRotate();
        };
    }

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            stopModalAutoRotate();
        }
    });
});
