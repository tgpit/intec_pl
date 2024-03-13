// @flow

import {Transport} from "./transport";
import {Item} from "./item";
import {compileTemplate, htmlToElement} from "./utils";

export class Suggest {

	static defaults = {
		transport: null,
		state: null,
		attribute: null,
		selectElement: 'select',
		selectTemplate: '<select>#OPTIONS#</select>',
		inputElement: 'input',
		inputTemplate: '<input type="text" autocomplete="off" />',
		values: {},
	};

	constructor(item: Item, element: HTMLElement, options: Object = {}) {
		this.item = item;
		this.el = element;
		this.options = Object.assign({}, this.constructor.defaults, options);
		this._stored = null;
		this._variants = null;
		this._state = this.options.state;

		this.bind();
	}

	bind() : void {
		this.handleInput(true);
	}

	unbind() : void {
		this.handleInput(false);
	}

	handleInput(dir: boolean, input: HTMLInputElement = null) : void {
		this.handleFocus(dir, input);
		this.handleChange(dir, input);
	}

	handleFocus(dir: boolean, input: HTMLInputElement = null) : void {
		if (input == null) { input = this.input(); }

		input?.[dir ? 'addEventListener' : 'removeEventListener']('focus', this.onFocus);
	}

	handleChange(dir: boolean, input: HTMLInputElement = null) : void {
		if (input == null) { input = this.input(); }

		input?.[dir ? 'addEventListener' : 'removeEventListener']('change', this.onChange);
	}

	onFocus = () => {
		// noinspection JSIgnoredPromiseFromCall
		this.load();
	}

	onChange = () => {
		if (!this.hasVariants()) { return; }

		this.normalize();
		this.item.refresh();
	}

	load() : Promise {
		const values = this.values();

		if (!this.isChanged(values)) { return Promise.resolve(); }

		this.store(values);
		this.clear();
		this._state?.loading();

		return this.query(values)
			.then((variants) => {
				this.render(variants);
				this._state?.waiting();
			})
			.catch((error) => {
				this._state?.error(error)
				this.release();
			});
	}

	isChanged(values: Object) : boolean {
		if (this._stored == null) { return true; }

		let result = false;

		for (const key of Object.keys(values)) {
			const value = values[key];
			const stored = this._stored[key];

			if (!this.compare(value, stored)) {
				result = true;
				break;
			}
		}

		return result;
	}

	store(values: Object) : void {
		this._stored = values;
	}

	release() : void {
		this._stored = null;
	}

	query(values) : Promise {
		return this.transport().fetch('variants', {
			attribute: this.options.attribute,
			values: values,
		});
	}

	transport() : Transport {
		const option = this.options.transport;

		if (!(option instanceof Transport)) {
			throw new Error('transport must be instance of Transport');
		}

		return option;
	}

	clear() : void {
		const input = this.input();
		const tagName = input?.tagName?.toLowerCase();

		if (tagName !== 'select') { return; }

		input
			.querySelectorAll('option')
			.forEach((option) => {
				if (!option.selected) { option.remove(); }
			});
	}

	redraw(variants: Array) : void {
		this.store(this.values());
		this.render(variants);
	}

	render(variants: Array) : void {
		const input = this.input();
		const type = variants.length > 0 ? 'select' : 'input';
		const html = (type === 'select' ? this.selectHtml(variants) : this.inputHtml());
		const selector = this.options[type + 'Element'];
		const selected = input?.value;
		let newInput;

		if (input == null) {
			newInput = htmlToElement(html);
			this.el.insertAdjacentElement('beforeend', newInput);
		} else if (input.matches(selector)) {
			newInput = this.redrawControl(input, html);
		} else {
			newInput = this.replaceControl(input, html);
		}

		this.setInputValue(newInput, selected);
		this._variants = variants;
	}

	selectHtml(variants: Array) : string {
		const template = this.options.selectTemplate;
		const options = variants
			.map((variant: string) => `<option>${BX.util.htmlspecialchars(variant)}</option>`)
			.join('');

		return compileTemplate(template, {
			OPTIONS: options,
		});
	}

	inputHtml() : string {
		return this.options.inputTemplate;
	}

	input() : ?HTMLInputElement {
		return this.el.querySelector(this.options.selectElement) ?? this.el.querySelector(this.options.inputElement);
	}

	selected() : string {
		return this.input()?.value;
	}

	redrawControl(input: HTMLInputElement, html: string) : HTMLInputElement {
		const newInput = htmlToElement(html);
		const contents = newInput.innerHTML.trim();

		if (contents === '') { return input; }

		input.innerHTML = contents;

		return input;
	}

	setInputValue(input: HTMLElement, selected: string) : void {
		if (input.tagName.toLowerCase() === 'select') {
			input
				.querySelectorAll('option')
				.forEach((option) => {
					option.selected = (option.value === selected);
				});
		} else {
			input.value = selected ?? '';
		}
 	}

	replaceControl(input: HTMLInputElement, html: string) : HTMLInputElement {
		const newInput = htmlToElement(html);
		newInput.name = input.name;
		newInput.className = input.className;

		this.handleInput(false, input);
		input.after(newInput);
		input.remove();
		this.handleInput(true, newInput);

		return newInput;
	}

	normalize() : void {
		const current = this.el.value;
		const matched = this.matched(current);

		this.el.value = matched ?? '';
	}

	hasVariants() : boolean {
		return this._variants != null && this._variants.length > 0;
	}

	matched(value: string) : ?string {
		if (this._variants == null) { return; }

		let result;

		for (const variant of this._variants) {
			if (this.compare(value, variant)) {
				result = variant;
				break;
			}
		}

		return result;
	}

	compare(first: string, second: string) {
		return (first ?? '').trim().toLowerCase() === (second ?? '').trim().toLowerCase();
	}

	values() : Object {
		const option = this.options.values;

		return typeof option === 'function' ? option() : option;
	}
}