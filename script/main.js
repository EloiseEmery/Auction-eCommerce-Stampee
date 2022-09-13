import Timer from './Timer.js';
import Clock from './Clock.js';

window.addEventListener('DOMContentLoaded', function() {
	let enchereTimer = document.querySelectorAll('[data-js-timer]');
	let clock = document.querySelectorAll('[data-js-clock]');

	for (let i = 0, l = clock.length; i < l; i++) { 
		new Clock(clock[i])
	}

	for (let i = 0, l = enchereTimer.length; i < l; i++) { 
		new Timer(enchereTimer[i])
	}
});