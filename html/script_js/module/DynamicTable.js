export class DynamicTable {
    /**
     * 
     * @param {HTMLTableElement} table 
     * @param {HTMLTemplateElement} template_tr 
     * @param {()} validate_add
     * @param {(tr: HTMLTableRowElement, values: any[]) => void} fillRow
     */
    constructor(table, template_tr, validate) {
        this.table = table;
        this.template_tr = template_tr;
        this.validate = validate;
        this.fillRow = this.fillRow;
        /** @var {HTMLTableSectionElement} */
        this.tfoot = this.table.querySelector('tfoot');
        /** @var {HTMLTableSectionElement} */
        this.tbody = this.table.querySelector('tbody');
        /** @var {HTMLInputElement[]} */
        this.inputs = Array.from(this.tfoot.querySelectorAll('input'));

        /** @var {HTMLButtonElement} */
        this.add_button = this.tfoot.appendChild(document.createElement('button'));
        this.add_button.className = 'button-add-row';
        this.add_button.type = 'button';
        this.add_button.innerText = '+';
        this.add_button.addEventListener('click', () => this.add_row(this.inputs.map(i => i.value)));
    }

    /**
     * @param {any[]} values 
     */
    add_row(values) {
        // clone the template and append <tr>
        /** @var {HTMLTableRowElement} */
        const tr = this.tbody.appendChild(this.template_tr.children[0].cloneNode(true));

        this.fillRow(tr, values);

        const remove_button = tr.appendChild(document.createElement('button'));
        remove_button.className = 'button-remove-row';
        remove_button.type = 'button';
        remove_button.innerText = '-';

        remove_button.addEventListener('click', () => {
            tr_tarif.remove();
            this.#decide_can_add();
            this.#decide_can_remove();
        });

        this.#decide_can_add();
        this.#decide_can_remove();
    }

    #decide_can_add() {
        this.add_button.disabled = 
    }

    #decide_can_remove() {
        for (const row of this.tbody.rows) {
            return row.querySelector('.button-remove-row').disabled = this.tbody.rows.length < 2;
        }
    }

}