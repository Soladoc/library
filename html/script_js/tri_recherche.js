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
}

// ... (keep your existing code for subcategories)

let offers = []; // This will be populated with data from PHP

function sortOffers(criteria, ascending = true) {
    offers.sort((a, b) => {
        let valueA = a[criteria];
        let valueB = b[criteria];

        if (criteria === 'date') {
            valueA = new Date(valueA);
            valueB = new Date(valueB);
        }

        if (ascending) {
            return valueA > valueB ? 1 : -1;
        } else {
            return valueA < valueB ? 1 : -1;
        }
    });

    displayOffers();
}

function displayOffers() {
    const offerList = document.querySelector('.offer-list');
    offerList.innerHTML = ''; // Clear existing offers

    offers.forEach(offer => {
        const offerElement = document.createElement('div');
        offerElement.className = 'offer-card';

        // Format the date
        const date = new Date(offer.modifiee_le);
        const formattedDate = date.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });

        offerElement.innerHTML = `
            <h3>${offer.titre}</h3>
            <img src="../images_utilisateur/${offer.id_image_principale}.jpg" 
                onerror="this.onerror=null; 
                  this.src='../images_utilisateur/${offer.id_image_principale}.png';
                  this.onerror=function(){
                        this.onerror=null; 
                        this.src='../images_utilisateur/${offer.id_image_principale}.webp';
                        this.onerror=function(){
                            this.onerror=null;
                            this.src='../images/${offer.id_image_principale}.jpeg';
                        }
                    }
            ">
            <p>Catégorie : ${offer.categorie}</p>
            <p>Description : ${offer.description}</p>
            <p>Adresse : ${offer.adresse}</p>
            <p>Prix : ${offer.prix_min}€</p>
            <p>Note : ${offer.note}/5</p>
            <p>Date : ${formattedDate}</p>`;
        offerList.appendChild(offerElement);
    });
}

// Event listeners for sort buttons
document.getElementById('sort-price-up').addEventListener('click', () => sortOffers('prix', true));
document.getElementById('sort-price-down').addEventListener('click', () => sortOffers('prix', false));
document.getElementById('sort-rating-up').addEventListener('click', () => sortOffers('note', true));
document.getElementById('sort-rating-down').addEventListener('click', () => sortOffers('note', false));
document.getElementById('sort-date-up').addEventListener('click', () => sortOffers('date', true));
document.getElementById('sort-date-down').addEventListener('click', () => sortOffers('date', false));

// Function to initialize offers from PHP data
function initializeOffers(offersData) {
    offers = offersData;
    displayOffers();
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
