
const subcategories = {
    restaurant: ['Française', 'Fruits de mer', 'Asiatique', 'Indienne', 'Italienne', 'Gastronomique', 'Restauration rapide', 'Crêperie'],
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
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = subcategory;
            checkbox.name = 'subcategory';
            checkbox.value = subcategory;

            const label = document.createElement('label');
            label.htmlFor = subcategory;
            label.innerText = subcategory;

            const wrapper = document.createElement('div');
            wrapper.appendChild(checkbox);
            wrapper.appendChild(label);

            subcategoryContainer.appendChild(wrapper);
        });
        document.getElementById('subcategories').classList.remove('hidden');
    } else {
        document.getElementById('subcategories').classList.add('hidden');
    }
}

const sortButtons = document.querySelectorAll('.btn-sort');
sortButtons.forEach(button => {
    button.addEventListener('click', () => {
        sortButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
    });
});
