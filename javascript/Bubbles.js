"use strict";

class Bubble {
	constructor(x, y, dx, dy, radius, opacity, blur)	{
		this.x        = x;
		this.y        = y;
		this.dx       = dx;
		this.dy       = dy;
		this.radius   = radius;
		this.opacity  = 0.01;
		this.size     = radius;
		this.tOpacity = opacity;

		this.blur = blur < 0.01 ? 0.01 : blur;
	}

	update(canvas, deltaTime) {
		this.checkBounds(canvas);

		// fade in bubbles
		if (this.opacity < this.tOpacity)
			this.opacity += 0.03 * deltaTime * Math.random();
		this.opacity = this.opacity > this.tOpacity ? this.tOpacity : this.opacity;

		this.x += this.dx;
		this.y += this.dy;
	}

	render(context) {
		const radgrad = context.createRadialGradient(
			this.x + this.radius,
			this.y + this.radius,
			this.size * (1 - this.blur),
			this.x + this.radius,
			this.y + this.radius,
			this.size
		);
		radgrad.addColorStop(0, 'rgba(255,255,255,' + this.opacity + ')');
		radgrad.addColorStop(1, 'rgba(255,255,255,0)');

		context.fillStyle = radgrad;
		context.fillRect(this.x, this.y, this.size * 2, this.size * 2);
	};

	// Checks to see if the bubble is out of bounds of the passed in canvas and reverses the direction
	checkBounds(canvas) {
		if (this.x + this.dx > canvas.width || this.x + (this.radius * 2) + this.dx < 0)
			this.dx = -this.dx;
		if (this.y + this.dy > canvas.height || this.y + (this.radius * 2) + this.dy < 0)
			this.dy = -this.dy;
	}
}

class Bubbles {
	constructor(selector, options) {
		this.options = {
			bubble_count : 15,
			max_radius   : 0.18,    // Percentage of canvas width
			min_radius   : 0.05,
			max_opacity  : 0.2,   // 0 = transparent, 1 = opaque
			min_opacity  : 0.05,
			max_speed    : 0.25
		};
		this.lastTime      = new Date().getTime() / 1000;
		this.interval      = null;
		this.context       = null;
		this.canvas        = null;
		this.bubbles       = [];
		this.options       = {...this.options, ...options};
		this.container     = document.querySelector(selector);
		this.canvas        = document.createElement("canvas");
		this.canvas.width  = this.container.offsetWidth;
		this.canvas.height = this.container.offsetHeight;
		this.context       = this.canvas.getContext("2d");

		this.container.appendChild(this.canvas);

		const max_radius = this.canvas.width * this.options.max_radius;
		const min_radius = this.canvas.width * this.options.min_radius;

		const {width, height} = this.canvas;

		for (let i = 0; i < options.bubble_count; i++) {
			const radius  = Bubbles.RandomBetween(min_radius, max_radius);

			// The next two options make bubbles that are smaller appear blurrier and more transparent. Bigger ones are more in focus and opaque.
			const blur    = 0.6 - (radius - width * this.options.min_radius) / (width * this.options.max_radius - width * this.options.min_radius);
			let   opacity = blur * this.options.max_opacity;

			opacity = opacity < this.options.min_opacity ? this.options.min_opacity : opacity;

			this.bubbles.push(new Bubble(
				Bubbles.RandomBetween(0, width),
				Bubbles.RandomBetween(0, height),
				Bubbles.RandomBetween(-this.options.max_speed, this.options.max_speed),
				Bubbles.RandomBetween(-this.options.max_speed, this.options.max_speed),
				radius,
				opacity,
				blur
			));
		}

		this.render();
	}

	render() {
		const now = Date.now() / 1000;
		const deltaTime = now - this.lastTime;

		this.context.clearRect(0,0, this.canvas.width, this.canvas.height);

		for (const bubble of this.bubbles) {
			bubble.update(this.canvas, deltaTime);
			bubble.render(this.context);
		}

		this.lastTime = now;
		window.requestAnimationFrame(() => this.render());
	}
	
	static RandomBetween(min, max) {
		return min + Math.random() * (max - min);
	}
}

export default Bubbles;