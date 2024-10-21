"use strict";

/** @type {HTMLTableSectionElement} */
const tbody_tarifs = document.getElementById('tbody_tarifs');

/** @type {HTMLTemplateElement} */
const template_tarif_row = document.getElementById('template_tarif_row');

/**
 * @param {string} nom 
 * @param {number} montant
 * @returns {HTMLTableRowElement}
 */
function create_tarif_row(nom, montant) {
    // Clone the template
    /** @type {HTMLTableRowElement} */
    const tarif_row = template_tarif_row.content.children[0].cloneNode(true);

    tarif_row.id = `tr_tarif_${nom}`;
    tarif_row.children[0].innerText = nom;

    tarif_row.children[1].children[0].value = montant;

    /** @type {HTMLButtonElement} */
    const remove_button = tarif_row.children[2];
    remove_button.addEventListener('click', () => tarif_row.remove());

    return tarif_row;
}

/** @type {HTMLButtonElement} */
const button_add_tarif = document.getElementById('button_add_tarif');

/** @type {HTMLInputElement} */
const nom_tarif = document.getElementById('nom_tarif');

/** @type {HTMLInputElement} */
const montant_tarif = document.getElementById('montant_tarif');

button_add_tarif.addEventListener('click', () => {
    // Custom validation logic
    if (!nom_tarif.value) {
        alert('Nom du tarif manquant');
        return;
    }
    if (isNaN(montant_tarif.valueAsNumber)) {
        alert('Montant du tarif manquant');
        return;
    }
    if (document.getElementById(`tr_tarif_${nom_tarif.value}`) !== null) {
        alert('Un tarif de même nom exite déjà.');
        return;
    }

    tbody_tarifs.appendChild(create_tarif_row(nom_tarif.value, montant_tarif.valueAsNumber));
});