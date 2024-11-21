import DynamicTable from './DynamicTable.js';

const type_offre = new URLSearchParams(window.location.search).get('type_offre');

// Grille tarifaire
{
    const i_nom = 0, i_montant = 1;

    const tarifs = new DynamicTable(
        document.getElementById('table-tarifs'),
        document.getElementById('template-tarif-tr'),
        function (tr) {
            return !this.has_row(row => row[i_nom] === nom(tr).value);
        },
        (tr, row) => {
            nom(tr).value = row[i_nom];
            montant(tr).value = row[i_montant];
        },
        1,
    );
    tarifs.add_row(['Adulte', 10]);
    tarifs.setup();

    /**
     * @param {HTMLTableRowElement} tr 
     * @returns {HTMLInputElement}
     */
    function nom(tr) {
        return tr.cells[i_nom].children[0];
    }
    /**
     * @param {HTMLTableRowElement} tr 
     * @returns {HTMLInputElement}
     */
    function montant(tr) {
        return tr.cells[i_montant].children[0];
    }
}

// Périodes
{
    const i_debut = 0, i_fin = 1;

    const periodes = new DynamicTable(
        document.getElementById('table-periodes'),
        document.getElementById('template-periode-tr'),
        tr => {
            fin(tr).min = debut(tr).value;
            return true;
        },
        (tr, row) => {
            debut(tr).value = row[i_debut];
            fin(tr).value = row[i_fin];
        }
    );
    periodes.setup();

    /**
     * @param {HTMLTableRowElement} tr 
     * @returns {HTMLInputElement}
     */
    function debut(tr) {
        return tr.cells[i_debut].children[0];
    }
    /**
     * @param {HTMLTableRowElement} tr 
     * @returns {HTMLInputElement}
     */
    function fin(tr) {
        return tr.cells[i_fin].children[0];
    }
}

// Horaires
{
    for (let dow = 0; dow < 7; ++dow) {
        const button_add_horaire = document.getElementById('button-add-horaire-' + dow);
        const tbody_horaires = document.getElementById('table-horaires-' + dow).querySelector('tbody');
        button_add_horaire.addEventListener('click', () => tbody_horaires.appendChild(create_horaire_tr(dow)));
    }

    /**
     * @param {number} dow 
     * @return {HTMLTableRowElement}
     */
    function create_horaire_tr(dow) {
        /**@type {HTMLTableRowElement}*/ const tr_horaire = document.getElementById('template-horaire-tr-' + dow).content.children[0].cloneNode(true);
        /**@type {HTMLInputElement}*/ const debut = tr_horaire.children[0];
        /**@type {HTMLInputElement}*/ const fin = tr_horaire.children[1];
        /**@type {HTMLButtonElement}*/ const btn_remove = tr_horaire.children[2];

        debut.addEventListener('input', () => fin.min = debut.value);

        btn_remove.addEventListener('click', () => tr_horaire.remove());

        return tr_horaire;
    }
}

// Image previews
{
    setup_preview('image_principale');
    setup_preview('gallerie');
    if (type_offre === 'parc-attractions') {
        setup_preview('image_plan');
    }
    function setup_preview(input_image_id) {
        const e_input_image = document.getElementById(input_image_id);
        const e_preview = document.getElementById(input_image_id + '-preview');
        addEventListener('change', () => preview_image(e_input_image, e_preview));
    }

    /**
     * @param {HTMLInputElement} e_input_image 
     * @param {HTMLElement} e_preview 
    */
    function preview_image(e_input_image, e_preview) {
        e_preview.textContent = '';

        for (const file of e_input_image.files) {
            if (!file.type.match('image.*')) {
                continue;
            }

            const reader = new FileReader();

            reader.addEventListener('load', function (event) {
                const imageUrl = event.target.result;
                const image = new Image();

                image.addEventListener('load', function () {
                    e_preview.appendChild(image);
                });

                image.src = imageUrl;
                image.style.width = '200px'; // Indiquez les dimensions souhaitées ici.
                image.style.height = 'auto'; // Vous pouvez également utiliser "px" si vous voulez spécifier une hauteur.
            });

            reader.readAsDataURL(file);
        }
    }
}