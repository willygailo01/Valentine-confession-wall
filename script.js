(function () {
    const heartsContainer = document.querySelector('.floating-hearts');

    if (heartsContainer) {
        const createHeart = function () {
            const heart = document.createElement('span');
            heart.className = 'floating-heart';
            heart.style.left = Math.floor(Math.random() * 100) + 'vw';
            heart.style.animationDuration = (7 + Math.random() * 6).toFixed(2) + 's';
            heart.style.opacity = (0.2 + Math.random() * 0.45).toFixed(2);
            heart.style.transform = 'scale(' + (0.6 + Math.random() * 1).toFixed(2) + ') rotate(45deg)';
            heartsContainer.appendChild(heart);

            setTimeout(function () {
                heart.remove();
            }, 14000);
        };

        for (let i = 0; i < 12; i += 1) {
            setTimeout(createHeart, i * 180);
        }

        setInterval(createHeart, 800);
    }

    const card = document.getElementById('valentineCard');
    const openCardBtn = document.getElementById('openCardBtn');

    if (card && openCardBtn) {
        openCardBtn.addEventListener('click', function () {
            card.classList.toggle('is-flipped');
        });
    }

    const messageInput = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    const loveMeter = document.getElementById('loveMeter');
    const maxChars = 500;

    if (messageInput && charCount && loveMeter) {
        const refreshMeter = function () {
            const count = messageInput.value.length;
            const percent = Math.min((count / maxChars) * 100, 100);

            charCount.textContent = count + ' / ' + maxChars;
            loveMeter.style.width = percent + '%';
        };

        messageInput.addEventListener('input', refreshMeter);
        refreshMeter();
    }

    const feedbackInput = document.getElementById('feedback_comment');
    const feedbackCharCount = document.getElementById('feedbackCharCount');
    const feedbackMaxChars = 700;

    if (feedbackInput && feedbackCharCount) {
        const refreshFeedbackCount = function () {
            const count = feedbackInput.value.length;
            feedbackCharCount.textContent = count + ' / ' + feedbackMaxChars;
        };

        feedbackInput.addEventListener('input', refreshFeedbackCount);
        refreshFeedbackCount();
    }

    const successMessage = document.getElementById('successMessage');

    if (successMessage) {
        burstHearts(successMessage);
    }

    function burstHearts(anchor) {
        const pieces = 22;

        for (let i = 0; i < pieces; i += 1) {
            const confetti = document.createElement('span');
            confetti.className = 'floating-heart';
            confetti.style.position = 'absolute';
            confetti.style.left = 45 + Math.random() * 10 + '%';
            confetti.style.bottom = '20px';
            confetti.style.opacity = '0.8';
            confetti.style.animationDuration = (1.6 + Math.random() * 1.8).toFixed(2) + 's';
            confetti.style.transform = 'scale(' + (0.45 + Math.random() * 0.8).toFixed(2) + ') rotate(45deg)';
            anchor.appendChild(confetti);

            setTimeout(function () {
                confetti.remove();
            }, 3500);
        }
    }

    const musicToggle = document.getElementById('musicToggle');
    const bgMusic = document.getElementById('bgMusic');

    if (musicToggle && bgMusic) {
        let playing = false;

        musicToggle.addEventListener('click', function () {
            if (!playing) {
                bgMusic.play().then(function () {
                    playing = true;
                    musicToggle.textContent = 'Pause Romantic Music';
                }).catch(function () {
                    musicToggle.textContent = 'Audio file missing';
                });
                return;
            }

            bgMusic.pause();
            playing = false;
            musicToggle.textContent = 'Play Romantic Music';
        });
    }
})();
