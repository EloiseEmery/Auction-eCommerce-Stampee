export default class Timer {
	constructor(elTimerContainer) {
        this._parent = elTimerContainer;
        this._elEnchereDateFin = this._parent.querySelector('[data-js-date-fin]').innerHTML
		this._elEnchereId = this._parent.querySelector('[data-js-enchere-id]').innerHTML
        this._elTimerDay = this._parent.querySelector('[data-js-enchere-day]')
        this._elTimerHour = this._parent.querySelector('[data-js-enchere-hour]')
        this._elTimerMin = this._parent.querySelector('[data-js-enchere-min]')
		this._elTimerSec = this._parent.querySelector('[data-js-enchere-sec]')
		this._elButtonMise = document.querySelector('[data-js-mise]')
		this.init();
    }

	init() {
		// initialiser le temps limite de la durée de l'enchère
		let enchereDeadLine = this._elEnchereDateFin;
		var deadline = new Date(enchereDeadLine).getTime();
	
		// Référence timer function : https://fr.acervolima.com/comment-creer-un-compte-a-rebours-en-utilisant-javascript/
		var x = setInterval(function() {
			var now = new Date().getTime();
	
			var t = deadline - now;
			var days = Math.floor(t / (1000 * 60 * 60 * 24));
			var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
			var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((t % (1000 * 60)) / 1000);
			this._elTimerDay.innerHTML =days ;
			this._elTimerHour.innerHTML =hours;
			this._elTimerMin.innerHTML = minutes; 
			this._elTimerSec.innerHTML =seconds; 

			// Gestion fin de l'enchère
			if (t < 0) {
				clearInterval(x);
				this._parent.innerHTML = "<p><strong class='tile__lot tile__lot--red'>Terminée</strong></p>";
				if (this._elButtonMise != null) {
					this._elButtonMise.disabled = true;
				}
				
				let xhr;
				xhr = new XMLHttpRequest();
			
				if (xhr) {
					// Récupérer les données
					let encodedEnchereid = encodeURIComponent(this._elEnchereId)
					
					// Ouvrir la requête
					xhr.open('POST', '.?entite=enchere&action=e_archive');
					xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
					
					// Envoi de la requête
					xhr.send('enchere_id=' + encodedEnchereid);
					
					// Écouter la requête 
					xhr.addEventListener('readystatechange', function() {
						if (xhr.readyState === 4) {
							if (xhr.status === 404) {
								console.log('Le fichier appelé dans la méthode open() est introuvable.');
							}
						}
					});
				}
			}
		}.bind(this));
	}
}