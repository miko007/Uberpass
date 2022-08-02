import Bubbles from "./Bubbles.js";
import Form    from "./Form.js";

class App {
	constructor(_) {
		new Bubbles("#background", {
			bubble_count : 15
		});
		new Form(_);
	}
}

export default App;