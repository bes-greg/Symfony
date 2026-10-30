import './stimulus_bootstrap.js';
import './styles/app.css';

document.addEventListener('click', async (e) => {
    const card = e.target.closest('.animeCard');
    if (!card) return;

    const modal = document.getElementById('animeOverlay');
    const modalTitle = document.getElementById('animeModalTitle');
    const modalDescription = document.getElementById('animeModalDesc');
    const modalAuthor = document.getElementById('modal-author');
    const modalDate = document.getElementById('modal-date');
    const modalImage = document.getElementById('animeModalImage');
    const formVu = document.getElementById('modalFormVu');
    const formAVoir = document.getElementById('modalFormAVoir');

    if (!modal || !modalTitle) return;

    const animeId = card.getAttribute('data-id');
    if (formVu) formVu.action = `/anime/${animeId}/add-vu`;
    if (formAVoir) formAVoir.action = `/anime/${animeId}/add-avoir`;

    // 2. Reset et affichage du chargement
    modalTitle.textContent = 'Chargement...';
    modalDescription.textContent = '';
    modalAuthor.textContent = '';
    modalDate.textContent = '';
    modalImage.src = '';

    // 3. Affichage de la modale
    modal.style.display = 'flex';

    try {
        const response = await fetch(`/anime/api/${animeId}`);
        if (!response.ok) throw new Error('Erreur réseau');

        const anime = await response.json();

        // 4. Injection des données fraîches
        modalTitle.textContent = anime.name;
        modalDescription.textContent = anime.description;
        modalAuthor.textContent = anime.author || 'Inconnu';

        if (anime.date) {
            const dateObj = new Date(anime.date);
            modalDate.textContent = dateObj.toLocaleDateString("fr-FR", {
                day: "2-digit",
                month: "long",
                year: "numeric"
            });
        } else {
            modalDate.textContent = 'Inconnue';
        }

        if (anime.image) {
            modalImage.src = anime.image;
        }

    } catch (error) {
        console.error('Erreur attrapée :', error);
        modalTitle.textContent = 'Erreur';
        modalDescription.textContent = 'Impossible de charger les détails.';
    }
});

// 5. Gestion indépendante de la fermeture (Croix et clic en dehors)
document.addEventListener('click', (e) => {
    const modal = document.getElementById('animeOverlay');
    if (!modal) return;

    // Si on clique sur la croix ou en dehors de la boîte blanche
    if (e.target.id === 'animeModalClose' || e.target === modal) {
        modal.style.display = 'none';
    }
});
