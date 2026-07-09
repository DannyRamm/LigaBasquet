document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.video-card');
    const frame = document.getElementById('youtubeFrame');
    const title = document.getElementById('modalTitle');
    const desc = document.getElementById('modalDescription');
    const cat = document.getElementById('modalCategory');
    const modal = document.getElementById('videoModal');

    if (!cards.length || !frame || !title || !desc || !cat || !modal) {
        return;
    }

    cards.forEach(card => {
        card.addEventListener('click', () => {
            title.textContent = card.dataset.title || '';
            desc.textContent = card.dataset.desc || '';
            cat.textContent = card.dataset.cat || '';
            frame.src = `https://www.youtube.com/embed/${card.dataset.video}?autoplay=1&rel=0`;
        });
    });

    modal.addEventListener('hidden.bs.modal', () => {
        frame.src = '';
    });
});
