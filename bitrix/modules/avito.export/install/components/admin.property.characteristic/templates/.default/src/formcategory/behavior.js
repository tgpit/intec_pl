export class Behavior {

	constructor(element: HTMLElement, options: Object = {}) {
		this.el = element;
		this.options = Object.assign({}, this.constructor.defaults, options);
	}

	// noinspection JSUnusedLocalSymbols
	values(action: string, data: Object) : Object {
		throw new Error('not implemented');
	}

	form() : HTMLElement {
		const form = this.el.closest('form');

		if (form == null) { throw new Error('cant find form'); }

		return form;
	}

	formInput(form: HTMLElement, name: string) {
		return form.querySelector(`[name^="${name}"]`);
	}

	inputValue(form: HTMLElement, name: string) : ?string {
		return this.formInput(form, name)?.value;
	}

	selectValue(form: HTMLElement, name: string) : ?Array {
		const select = this.formInput(form, name);

		if (select == null) { return null; }

		const result = [];

		for (const option of select.querySelectorAll('option')) {
			if (!option.selected) { continue; }

			result.push(option.value);
		}

		return result;
	}
}