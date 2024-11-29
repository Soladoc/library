'use strict';

async function getDataJson(url) {
    return await (await fetch(url)).json();
}

let offers; // Will be populated with data from PHP
let images;

async function initializeOffers() {
    [offers, images] = await Promise.all([
        getDataJson(`/json/offres.php`),
        getDataJson(`/json/images.php`),
    ]);
    filterOffers();
}
initializeOffers();

const subcategories = {
    restaurant: ['Française', 'Fruits de mer', 'Asiatique', 'Indienne', 'Italienne', 'Gastronomique', 'Restauration rapide', 'Crêperie'],
    activité: ['Atelier','Cinéma','Cirque','Culturel','Famille','Histoire','Humour','Musée','Musique','Nature','Patrimoine','Son et lumière','Urbain',],
    spectacle: ['Atelier','Cinéma','Cirque','Culturel','Famille','Histoire','Humour','Musée','Musique','Nature','Patrimoine','Son et lumière','Urbain',],
    visite: ['Atelier','Cinéma','Cirque','Culturel','Famille','Histoire','Humour','Musée','Musique','Nature','Patrimoine','Son et lumière','Urbain',],
    parc_d_attraction: ['Atelier','Cinéma','Cirque','Culturel','Famille','Histoire','Humour','Musée','Musique','Nature','Patrimoine','Son et lumière','Urbain',]
};

function showSubcategories() {
    const mainCategory = document.getElementById('main-category').value;
    const subcategoryContainer = document.getElementById('subcategory-list');
    subcategoryContainer.innerHTML = ''; // Réinitialise les sous-catégories précédentes

    if (mainCategory && subcategories[mainCategory]) {
        // Crée les sous-catégories pour la catégorie sélectionnée
        subcategories[mainCategory].forEach(subcategory => {
            const wrapper = document.createElement('div');

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = toLowerCase(subcategory);
            checkbox.name = 'subcategory';
            checkbox.value = subcategory;
            checkbox.addEventListener('change', filterOffers);

            const label = document.createElement('label');
            label.htmlFor = subcategory;
            label.innerText = subcategory;

            wrapper.appendChild(checkbox);
            wrapper.appendChild(label);
            subcategoryContainer.appendChild(wrapper);
        });

        // Affiche la section des sous-catégories
        document.getElementById('subcategories').classList.remove('hidden');
    } else {
        // Masque la section des sous-catégories si aucune catégorie n'est sélectionnée
        document.getElementById('subcategories').classList.add('hidden');
    }
    filterOffers();  // Applique immédiatement le filtre après avoir mis à jour les sous-catégories
}

function sortOffers(criteria, ascending = true) {
    offers.sort((a, b) => {
        let valueA = a[criteria];
        let valueB = b[criteria];

        // Si la catégorie est prix_min ou note_moyenne, on les convertit en nombres
        if (criteria === 'prix_min' || criteria === 'note_moyenne') {
            valueA = parseFloat(valueA) || 0;
            valueB = parseFloat(valueB) || 0;
        } else if (criteria === 'creee_le') {
            // Assure-toi que les valeurs de date sont valides
            valueA = new Date(valueA);
            valueB = new Date(valueB);
            
            // Vérifie que les dates sont valides
            if (isNaN(valueA)) valueA = new Date(0);  // Date invalide, valeur par défaut
            if (isNaN(valueB)) valueB = new Date(0);
        }

        // Tri ascendant ou descendant
        if (ascending) {
            return valueA < valueB ? -1 : 1;
        } else {
            return valueA > valueB ? -1 : 1;
        }
    });
    displayOffers();
    filterOffers();
}

function filterOffers() {
    const mainCategory = document.getElementById('main-category').value;
    const subcategoryCheckboxes = document.querySelectorAll('input[name="subcategory"]:checked');
    const selectedSubcategories = Array.from(subcategoryCheckboxes).map(cb => cb.id);
    const filteredOffers = offers.filter(offer => {
        if (mainCategory && offer.categorie.toLowerCase() !== mainCategory.toLowerCase()) {
            return false;
        }
        if (selectedSubcategories.length > 0) {
            if (!offer.tags || offer.tags.length === 0) {
                return false;
            }
            return selectedSubcategories.some(selected => offer.tags.includes(selected));
        }
        return true;
    });
    displayOffers(filteredOffers);
}

function displayOffers(offersToDisplay = offers) {
    const offerList = document.querySelector('.offer-list');
    offerList.innerHTML = ''; // Réinitialisation avant de commencer à ajouter les éléments

    offersToDisplay.forEach(offer => {
        const offerElement = document.createElement('div');
        offerElement.className = 'offer-card';

        // Formate la date
        const date = new Date(offer.creee_le);
        const formattedDate = date instanceof Date && !isNaN(date) 
            ? date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) 
            : 'Date inconnue';

        // Affichage des offres avec ou sans prix minimum
        if (offer.prix_min != null) {
            offerElement.innerHTML = `
                <h3><a href="/autres_pages/detail_offre.php?id=${offer.id}">${offer.titre}</a></h3>
                <img src="${get_image_filename(offer.id_image_principale)}">
                <p>Catégorie : ${offer.categorie}</p>
                <p>Description : ${offer.resume}</p>
                <p>Adresse : ${offer.formatted_address}</p>
                <p>À partir de : ${offer.prix_min}€</p>
                <p>Note : ${offer.note_moyenne}/5</p>
                <p>Date : ${formattedDate}</p>
            `;
        } else {
            offerElement.innerHTML = `
                <h3><a href="/autres_pages/detail_offre.php?id=${offer.id}">${offer.titre}</a></h3>
                <img src="${get_image_filename(offer.id_image_principale)}">
                <p>Catégorie : ${offer.categorie}</p>
                <p>Description : ${offer.resume}</p>
                <p>Adresse : ${offer.formatted_address}</p>
                <p>Gratuit</p>
                <p>Note : ${offer.note_moyenne}/5</p>
                <p>Date : ${formattedDate}</p>
            `;
        }

        offerList.appendChild(offerElement);
    });
}


const sortButtons = document.querySelectorAll('.btn-sort');
sortButtons.forEach(button => {
    button.addEventListener('click', () => {
        sortButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        const criteria = button.dataset.criteria;
        const ascending = button.dataset.order === 'asc';
        sortOffers(criteria, ascending);
    });
});


document.getElementById('sort-price-up').addEventListener('click', () => sortOffers('prix_min', false));
document.getElementById('sort-price-down').addEventListener('click', () => sortOffers('prix_min', true));
document.getElementById('sort-rating-up').addEventListener('click', () => sortOffers('note_moyenne', false));
document.getElementById('sort-rating-down').addEventListener('click', () => sortOffers('note_moyenne', true));
document.getElementById('sort-date-up').addEventListener('click', () => sortOffers('creee_le', false));
document.getElementById('sort-date-down').addEventListener('click', () => sortOffers('creee_le', true));
document.getElementById('main-category').addEventListener('change', showSubcategories);

function get_image_filename(id_image) {
    return `/images_utilisateur/${id_image}.${images[id_image].mime_subtype}`;
}