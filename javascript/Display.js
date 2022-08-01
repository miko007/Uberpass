"use strict";

import {$} from "./util.js";

let error = false;

const DisplayError = message => {
	if (error)
		return;
	const container = $("#error");
	container.innerHTML = `
		<section class="alert">
			${message}
		</section>
	`;
	error = true;
};

const DisplaySuccess = message => {
	const container = $("#error");
	container.innerHTML = `
		<section class="alert success">
			${message}
		</section>
	`;
	error = true;
};

const clearDisplay = () => {
	const container = $("#error");
	container.innerHTML = "";
	error = false;
};

export {clearDisplay, DisplayError, DisplaySuccess};