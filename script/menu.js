window.addEventListener('DOMContentLoaded', function() {

    /**
     * Récupération des éléments du DOM nécessaires
     */
    let elBurger = document.querySelector('[data-js-burger]'),
        elClose = document.querySelector('[data-js-close]'),
        elMenu = document.querySelector('[data-js-menu]'),
		elSearch = document.querySelector('[data-js-search]'),
		elSearchbar = document.querySelector('[data-js-search-bar]'),
		elCloseSearchBar = document.querySelector('[data-js-close-search-bar]'),
        elHTML = document.documentElement,
        elBody = document.body;
    

	//** MENU MOBILE **/
    /**
     * Ouverture du menu
     * @param {Event} click, évènement de type click.
     */ 
    elBurger.addEventListener('click', function() {
        if (elMenu.classList.contains('menu--close')) {
            elMenu.classList.replace('menu--close', 'menu--open');

            // Ajoute la propriété overflow-y: hidden; sur les éléments html et body afin d'enlever le scroll en Y lorsque le modal est ouvert
            elHTML.classList.add('overflow-y--hidden');
            elBody.classList.add('overflow-y--hidden');	
        }
    });

    /**
     * Fermeture du menu
     * @param {Event} click, évènement de type click
     */
    elClose.addEventListener('click', function() {
         if (elMenu.classList.contains('menu--open')) {
            elMenu.classList.replace('menu--open', 'menu--transition');
            
			/**
			 * @param {Event} transitionend, évènement de type transitionend.
			 */
            elMenu.addEventListener('transitionend', function(e) {
                if (e.propertyName == 'left') { 
                    elMenu.classList.replace('menu--transition', 'menu--close');
                }
            });
            
            // Enlève la propriété overflow-y: hidden; sur les éléments html et body afin de rendre de nouveau possible le scroll en Y lorsque le modal est fermé
            elHTML.classList.remove('overflow-y--hidden');
            elBody.classList.remove('overflow-y--hidden');
        }
    });


	//** SEARCH BAR MOBILE **/
	if (elSearch) {
		/**
		 * Ouverture de la barre de recherche
		 * @param {Event} click, évènement de type click.
		 */ 
		elSearch.addEventListener('click', function() {
			if (elSearchbar.classList.contains('menu--close')) {
				elSearchbar.classList.replace('menu--close', 'menu--open');
				elHTML.classList.add('overflow-y--hidden');
				elBody.classList.add('overflow-y--hidden');	
			}
		});

		/**
		 * Fermeture de la barre de recherche
		 * @param {Event} click, évènement de type click
		 */
		elCloseSearchBar.addEventListener('click', function() {
			if (elSearchbar.classList.contains('menu--open')) {
				elSearchbar.classList.replace('menu--open', 'menu--transition');

				/**
				 * @param {Event} transitionend, évènement de type transitionend.
				 */
				elSearchbar.addEventListener('transitionend', function(e) {
					if (e.propertyName == 'left') { 
					elSearchbar.classList.replace('menu--transition', 'menu--close');
					}
				});

				elHTML.classList.remove('overflow-y--hidden');
				elBody.classList.remove('overflow-y--hidden');
			}
		});
	}
});