<?php

/**
 * Classe Contrôleur des requêtes de l'application admin
 */
class Admin extends Routeur {

	protected $entite;
	protected $action;

	protected $utilisateur_id;

	protected static $oRequetesSQL;

	/**
	 * Gérer l'interface d'administration 
	 */  
	public function gerer() {
		self::$oRequetesSQL = new RequetesSQL;
		$entite = $_GET['entite']  ?? 'film';
		$entite = ucwords($entite);
		$classe = "Admin$entite";

		if (class_exists($classe)) {
			(new $classe())->gerer();
		} else {
			throw new Exception("L'entité $entite n'existe pas.");
		}
	}
}