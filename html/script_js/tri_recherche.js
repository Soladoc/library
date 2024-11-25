'use strict';



async function getDataJson(url) {
    return await (await fetch(url)).json();
}

let offers; // This will be populated with data from PHP
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
    restauration: ['Française', 'Fruits de mer', 'Asiatique', 'Indienne', 'Italienne', 'Gastronomique', 'Restauration rapide', 'Crêperie'],
    activite: ['Urbain', 'Nature', 'Plein air', 'Culturel', 'Patrimoine', 'Histoire', 'Sport', 'Nautique', 'Gastronomie', 'Musée', 'Atelier', 'Musique', 'Famille'],
    visite: ['Parc d\'attractions'],
    spectacle: ['Cinéma', 'Cirque', 'Son et lumière', 'Humour']
};

function showSubcategories() {
    const mainCategory = document.getElementById('main-category').value;
    const subcategoryContainer = document.getElementById('subcategory-list');
    subcategoryContainer.innerHTML = ''; // Reset

    if (mainCategory && subcategories[mainCategory]) {
        subcategories[mainCategory].forEach(subcategory => {
            const wrapper = document.createElement('div');

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = subcategory;
            checkbox.name = 'subcategory';
            checkbox.value = subcategory;

            const label = document.createElement('label');
            label.htmlFor = subcategory;
            label.innerText = subcategory;

            wrapper.appendChild(checkbox);
            wrapper.appendChild(label);
            subcategoryContainer.appendChild(wrapper);
        });

        document.getElementById('subcategories').classList.remove('hidden');
    } else {
        document.getElementById('subcategories').classList.add('hidden');
    }
    if (mainCategory && subcategories[mainCategory]) {
        subcategories[mainCategory].forEach(subcategory => {
            // ... (existing code to create checkboxes)
            
            // Add event listener to each checkbox
            checkbox.addEventListener('change', filterOffers);
        });

        document.getElementById('subcategories').classList.remove('hidden');
    } else {
        document.getElementById('subcategories').classList.add('hidden');
    }

    // Call filterOffers when main category changes
    filterOffers();
}

function sortOffers(criteria, ascending = true) {
    offers.sort((a, b) => {
        let valueA = a[criteria];
        let valueB = b[criteria];

        if (criteria === 'prix_min') {
            // Convert to number if it's a string, or use 0 if it's null/undefined
            valueA = parseFloat(valueA) || 0;
            valueB = parseFloat(valueB) || 0;
        } else if (criteria === 'note_moyenne') {
            valueA = parseFloat(valueA) || 0;
            valueB = parseFloat(valueB) || 0;
        } else if (criteria === 'date') {
            valueA = new Date(valueA);
            valueB = new Date(valueB);
        }

        if (ascending) {
            return valueA < valueB ? 1 : -1;
        } else {
            return valueA > valueB ? 1 : -1;
        }
    });

    displayOffers();
}

function filterOffers() {
    const mainCategory = document.getElementById('main-category').value;
    const subcategoryCheckboxes = document.querySelectorAll('input[name="subcategory"]:checked');
    const subcategories = Array.from(subcategoryCheckboxes).map(cb => cb.value);

    const filteredOffers = offers.filter(offer => {
        if (mainCategory && offer.categorie !== mainCategory) {
            return false;
        }
        if (subcategories.length > 0 && !subcategories.includes(offer.sous_categorie)) {
            return false;
        }
        return true;
    });

    displayOffers(filteredOffers);
}

function displayOffers(offersToDisplay = offers) {
    const offerList = document.querySelector('.offer-list');
    offerList.innerHTML = ''; // Clear existing offers
    offersToDisplay.forEach(offer => {
        const offerList = document.querySelector('.offer-list');
    offerList.innerHTML = ''; // Clear existing offers
    offers.forEach(offer => {
        const offerElement = document.createElement('div');
        offerElement.className = 'offer-card';
        // Format the date
        const date = new Date(offer.creee_le);
        const formattedDate = date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        if (offer.prix_min!=null){
        offerElement.innerHTML = `
            <h3>${offer.titre}</h3>
            <img src="${get_image_filename(offer.id_image_principale)}">
            <p>Catégorie : ${offer.categorie}</p>
            <p>Description : ${offer.resume}</p>
            <p>Adresse : ${offer.formatted_address}</p>
            <p>À partir de : ${offer.prix_min}€</p>
            <p>Note : ${offer.note_moyenne}/5</p>
            <p>Date : ${formattedDate}</p>
            <a href="/autres_pages/detail_offre.php?id=${offer.id}&pro=true">
                <button class="btn-more-info">En savoir plus</button>
            </a>`;
        } else {
            offerElement.innerHTML = `
            <h3>${offer.titre}</h3>
            <img src="${get_image_filename(offer.id_image_principale)}">
            <p>Catégorie : ${offer.categorie}</p>
            <p>Description : ${offer.resume}</p>
            <p>Adresse : ${offer.formatted_address}</p>
            <p>Gratuit</p>
            <p>Note : ${offer.note_moyenne}/5</p>
            <p>Date : ${formattedDate}</p>
            <a href="/autres_pages/detail_offre.php?id=${offer.id}&pro=true">
                <button class="btn-more-info">En savoir plus</button>
            </a>`;
        }
        offerList.appendChild(offerElement);
    });
    });
}
function displayOffers() {
    const offerList = document.querySelector('.offer-list');
    offerList.innerHTML = ''; // Clear existing offers
    offers.forEach(offer => {
        const offerElement = document.createElement('div');
        offerElement.className = 'offer-card';
        // Format the date
        const date = new Date(offer.creee_le);
        const formattedDate = date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        if (offer.prix_min!=null){
        offerElement.innerHTML = `
            <h3><a href="/autres_pages/detail_offre.php?id=${offer.id}">${offer.titre}</a></h3>
            <img src="${get_image_filename(offer.id_image_principale)}">
            <p>Catégorie : ${offer.categorie}</p>
            <p>Description : ${offer.resume}</p>
            <p>Adresse : ${offer.formatted_address}</p>
            <p>À partir de : ${offer.prix_min}€</p>
            <p>Note : ${offer.note_moyenne}/5</p>
            <p>Date : ${formattedDate}</p>`;
        } else {
            offerElement.innerHTML = `
            <h3><a href="/autres_pages/detail_offre.php?id=${offer.id}">${offer.titre}</a></h3>
            <img src="${get_image_filename(offer.id_image_principale)}">
            <p>Catégorie : ${offer.categorie}</p>
            <p>Description : ${offer.resume}</p>
            <p>Adresse : ${offer.formatted_address}</p>
            <p>Gratuit</p>
            <p>Note : ${offer.note_moyenne}/5</p>
            <p>Date : ${formattedDate}</p>`;
        }
        offerList.appendChild(offerElement);
    });
}

// Event listeners for sort buttons
document.getElementById('sort-price-up').addEventListener('click', () => sortOffers('prix_min', true));
document.getElementById('sort-price-down').addEventListener('click', () => sortOffers('prix_min', false));
document.getElementById('sort-rating-up').addEventListener('click', () => sortOffers('note_moyenne', true));
document.getElementById('sort-rating-down').addEventListener('click', () => sortOffers('note_moyenne', false));
document.getElementById('sort-date-up').addEventListener('click', () => sortOffers('date', true));
document.getElementById('sort-date-down').addEventListener('click', () => sortOffers('date', false));
document.getElementById('main-category').addEventListener('change', showSubcategories);


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

/**
 * @param {number} id_image
 */
function get_image_filename(id_image) {
    return `/images_utilisateur/${id_image}.${images[id_image].mime_subtype}`;
}
