export default class DynamicTable {
    /**@type {HTMLTemplateElement}*/ #template_tr;
    /**@type {(tr: HTMLTableRowElement) => boolean}*/ #validate_add;
    /**@type {(tr: HTMLTableRowElement, row: string[]) => void}*/ #fill_row;
    /**@type {HTMLTableSectionElement}*/ #tbody;
    /**@type {HTMLTableElement}*/ #new_row;
    /**@type {HTMLInputElement[]}*/ #new_inputs;
    /**@type {HTMLButtonElement}*/ #add_button = document.createElement('button');
    /**@type {number} */ #min_rows;
    /**
     * @param {HTMLTableElement} table
     * @param {HTMLTemplateElement} template_tr
     * @param {(tr: HTMLTableRowElement) => boolean} validate_add
     * @param {(tr: HTMLTableRowElement, row: string[]) => void} fill_row
     * @param {number=} [min_rows=0]
     */
    constructor(table, template_tr, validate_add, fill_row, min_rows = 0) {
        this.#min_rows = min_rows;
        this.#template_tr = template_tr;
        this.#validate_add = validate_add;
        this.#fill_row = fill_row;
        this.#tbody = table.querySelector('tbody');
        this.#new_row = table.querySelector('tfoot').rows[0];
        this.#new_inputs = Array.from(this.#new_row.querySelectorAll('input'));
    }

    /**
     * Setups DOM interactivity.
     */
    setup() {
        this.#new_row.insertCell().appendChild(this.#add_button);
        this.#add_button.className = 'button-add-row';
        this.#add_button.type = 'button';
        this.#add_button.textContent = '+';
        this.#add_button.addEventListener('click', () => {
            if (this.#new_inputs.every(i => i.reportValidity())) {
                this.add_row(this.#new_inputs.map(i => i.value));
            }
        });

        for (const input of this.#new_inputs) {
            input.addEventListener('input', this.#decide_can_add.bind(this));
        }

        for (const tr of this.#tbody.rows) {
            this.#setup_row(tr);
        }

        this.#decide_can_add();
    }

    /**
     * @param {(row: string[]) => boolean} predicate 
     * @returns {boolean}
     */
    has_row(predicate) {
        for (const tr of this.#tbody.rows) {
            if (predicate(Array.from(tr.querySelectorAll('input')).map(i => i.value)))
                return true;
        }
        return false;
    }

    /**
     * @param {any[]} values 
     */
    add_row(values) {
        // clone the template and append <tr>
        const tr = this.#tbody.appendChild(this.#template_tr.content.children[0].cloneNode(true));
        this.#fill_row(tr, values);
        this.#setup_row(tr);
    }

    /**
     * 
     * @param {HTMLTableRowElement} tr 
     */
    #setup_row(tr) {
        const remove_button = tr.insertCell().appendChild(document.createElement('button'));
        remove_button.className = 'button-remove-row';
        remove_button.type = 'button';
        remove_button.textContent = '-';

        remove_button.addEventListener('click', () => {
            tr.remove();
            this.#decide_can_remove();
            this.#decide_can_add();
        });

        this.#decide_can_remove();
        this.#decide_can_add();
    }

    #decide_can_add() {
        this.#add_button.disabled = !this.#validate_add(this.#new_row);
    }

    #decide_can_remove() {
        for (const tr of this.#tbody.rows) {
            tr.querySelector('.button-remove-row').disabled = this.#tbody.rows.length <= this.#min_rows;
        }
    }

}