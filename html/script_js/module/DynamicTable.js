export class DynamicTable {
    /**@type {HTMLTableElement}*/ table;
    /**@type {HTMLTemplateElement}*/ template_tr;
    /**@type {(values: any[]) => boolean}*/ validate_add;
    /**@type {(values: any[], tr: HTMLTableRowElement) => void}*/ fill_row;
    /**@type {HTMLTableSectionElement}*/ tfoot;
    /**@type {HTMLTableSectionElement}*/ tbody;
    /**@type {HTMLInputElement[]}*/ new_inputs;
    /**
     * @param {HTMLTableElement} table
     * @param {HTMLTemplateElement} template_tr
     * @param {(values: string[]) => boolean} validate_add
     * @param {(values: string[], tr: HTMLTableRowElement) => void} fill_row
     */
    constructor(table, template_tr, validate_add, fill_row) {
        this.table = table;
        this.template_tr = template_tr;
        this.validate_add = validate_add;
        this.fill_row = fill_row;
        this.tfoot = this.table.querySelector('tfoot');
        this.tbody = this.table.querySelector('tbody');
        this.new_inputs = Array.from(this.tfoot.querySelectorAll('input'));

        // Setup
        this.add_button = this.tfoot.rows[0].insertCell().appendChild(document.createElement('button'));
        this.add_button.className = 'button-add-row';
        this.add_button.type = 'button';
        this.add_button.innerText = '+';
        this.add_button.addEventListener('click', () => this.add_row(this.new_inputs.map(i => i.value)));

        for (const input of this.new_inputs) {
            input.addEventListener('input', this.#decide_can_add.bind(this));
        }

        this.#decide_can_add();
    }

    /**
     * @param {(row: string[]) => boolean} predicate 
     * @returns {boolean}
     */
    has_row(predicate) {
        for (const row of this.tbody.rows) {
            if (predicate(Array.from(row.querySelectorAll('input')).map(i => i.value)))
                return true;
        }
        return false;
    }

    /**
     * @param {any[]} values 
     */
    add_row(values) {
        // clone the template and append <tr>
        const tr = this.tbody.appendChild(this.template_tr.content.children[0].cloneNode(true));

        this.fill_row(values, tr);
        const remove_button = tr.insertCell().appendChild(document.createElement('button'));
        remove_button.className = 'button-remove-row';
        remove_button.type = 'button';
        remove_button.innerText = '-';

        remove_button.addEventListener('click', () => {
            tr.remove();
            this.#decide_can_remove();
            this.#decide_can_add();
        });

        this.#decide_can_remove();
        this.#decide_can_add();
    }

    #decide_can_add() {
        this.add_button.disabled = this.new_inputs.some(i => !i.reportValidity())
            || !this.validate_add(this.new_inputs.map(i => i.value));
    }

    #decide_can_remove() {
        for (const row of this.tbody.rows) {
            row.querySelector('.button-remove-row').disabled = this.tbody.rows.length < 2;
        }
    }

}