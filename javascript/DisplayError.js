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

const clearDisplayError = () => {
	const container = $("#error");
	container.innerHTML = "";
	error = false;
};

export {clearDisplayError};
export default DisplayError;