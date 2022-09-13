export default class Clock { 
	constructor(clock) {
		this._clock = clock;
		this.init();
    }

	init() { 
		setInterval(function() {	
			var date = new Date()
			var h = date.getHours();
			var m = date.getMinutes();
			var s = date.getSeconds();
			if( h < 10 ){ h = '0' + h; }
			if( m < 10 ){ m = '0' + m; }
			if( s < 10 ){ s = '0' + s; }
			var time = h + ':' + m + ':' + s + ' <small>ET</small>'
			
			this._clock.innerHTML = time;
		
		}.bind(this));
	}
}