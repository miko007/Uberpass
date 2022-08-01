"use strict";

import {$}                                          from "./util.js";
import {DisplayError, DisplaySuccess, clearDisplay} from "./Display.js";

class Form {
	constructor() {
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
			for (const [_, value] of Object.entries(fields)) {
				if (value === "")
					return new Error("Es sind nicht alle erforderlichen Felder ausgefüllt.");
			}
			if (data.password !== data.passwordConfirm)
				return new Error("Die angegebeben Passwörter stimmen nicht überein.");

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