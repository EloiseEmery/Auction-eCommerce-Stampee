<?php

/**
 * Classe Routeur
 * analyse l'uri et exécute la méthode associée  
 *
 */
class Routeur {

	private $routes = [
		// uri, classe, méthode
		// --------------------
		["admin",         "Admin",    "gerer"],
		["",              "Frontend", "gerer"]
	];

	const BASE_URI = "\/STAMPEE_E_EMERY\/";

	const ERROR_FORBIDDEN = "HTTP 403";
	const ERROR_NOT_FOUND = "HTTP 404";

	/**
	 * Valider l'URI
	 * et instancier la méthode du contrôleur correspondante
	 *
	 */
	public function router()
	{
		try {
			// contrôle de l'uri si l'action coïncide 
			$uri = preg_replace("/^" . self::BASE_URI . "([^?]*)\??.*$/", "$1", $_SERVER["REQUEST_URI"]);
			// balayage du tableau des routes
			foreach ($this->routes as $route) {

				$routeUri     = $route[0];
				$routeClasse  = $route[1];
				$routeMethode = $route[2];

				if ($routeUri ===  $uri) {
					// on exécute la méthode associée à l'uri
					$oRouteClasse = new $routeClasse;
					$oRouteClasse->$routeMethode();
					exit;
				}
			}
			// aucune route ne correspond à l'uri
			throw new Exception(self::ERROR_NOT_FOUND);
		} catch (Error | Exception $e) {
			$this->erreur($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}

	/**
	 * Méthode qui envoie un compte-rendu d'erreur
	 *
	 */
	public static function erreur($erreur, $fichier, $ligne)
	{
		$message = '';
		if ($erreur == self::ERROR_FORBIDDEN) {
			header('HTTP/1.1 403 Forbidden');
		} else if ($erreur == self::ERROR_NOT_FOUND) {
			header('HTTP/1.1 404 Not Found');
			(new Vue)->generer('vErreur404', [], 'gabarit-erreur');
		} else {
			header('HTTP/1.1 500 Internal Server Error');
			$message = $erreur;
			(new Vue)->generer(
				"vErreur500",
				array('message' => $message, 'fichier' => $fichier, 'ligne' => $ligne),
				'gabarit-erreur'
			);
		}
		exit;
	}
}