"use strict";

import {$}                                          from "./util.js";
import {DisplayError, DisplaySuccess, clearDisplay} from "./Display.js";

let _;
class Form {
	constructor(i18n) {
		_ = i18n;
		this.container = $("#form");

		this.container.addEventListener("submit", event => {
			event.preventDefault();
			this.submit();
		});
	}

	submit() {
		const fields = this.container.getElementsByTagName("input");
		const data   = {};

		clearDisplay();
		for (const field of fields)
			data[field.name] = field.value;
		const checkValidity = () => {
			for (const [key, input] of Object.entries(fields)) {
				if (input.value === "")
					return new Error(_("Not all nessecary fields are propagated."));
			}
			if (data.password !== data.passwordConfirm)
				return new Error(_("The supplied passwords do not match."));

			return null;
		};

		const error = checkValidity();

		if (error) {
			DisplayError(error.message);

			return;
		}

		fetch("backend.php", {
			method: "post",
			headers : {
				"Content-Type" : "application/json"
			},
			body : JSON.stringify(data)
		}).then(response => response.json()).then(json => {
			if (json.status !== 200)
				DisplayError(json.data);
			else {
				DisplaySuccess("Ihr Passwort wurde erfolgreich geändert.");
				this.container.reset();
			}
		}).catch(error => {
			console.error(error);
		});
	}
}

export default Form;