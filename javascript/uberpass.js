import Bubbles from "./Bubbles.js";
import Form    from "./Form.js";

document.addEventListener("DOMContentLoaded", () => {
	new Bubbles("#background", {
		bubble_count : 15
	});
	new Form();
});