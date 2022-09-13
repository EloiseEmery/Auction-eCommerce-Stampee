<?php

/**
 * Classe Contrôleur des requêtes de l'application admin
 */
class Frontend extends Routeur {

	protected $entite;
	protected $action;

	protected $utilisateur_id;

	protected static $oRequetesSQL;

	/**
	 * Gérer l'interface d'administration 
	 */  
	public function gerer() {
		self::$oRequetesSQL = new RequetesSQL;

		$entite = $_GET['entite']  ?? '';
		$entite = ucwords($entite);
		$classe = "Frontend$entite";

		if (class_exists($classe)) {
			if($entite == '') self::accueil();
			else (new $classe())->gerer();
		}
		else {
			throw new Exception("L'entité $entite n'existe pas.");
		}
	}

	/**
	 * Redirection page d'accueil
	 * 
	 */  
	static function accueil() {
		if(isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		}
		else {
			$oUtilisateurConnecter = null;
		}

		$oRequetesSQL = new RequetesSQL;
		$encheres = [];
		$enchere_archive_int = $_GET['enchere_archive'] ?? 0;
		$enchere_archive = intval($enchere_archive_int);
		$order = 'Enchere.enchere_id';
		$sens = 'DESC';

		// récupération des données à afficher
		$encheres = $oRequetesSQL->listerEncheres($enchere_archive, $order, $sens);
		$nb_offres = $oRequetesSQL->compterMisesEncheres();
		$dernieresOffres = $oRequetesSQL->afficherDernieresMises();
		$images = $oRequetesSQL->listerImagesEnchere();

		(new Vue)->generer("vHome",
				array(
					'titre' => "Bienvenue sur Stampee",
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'encheres'				=> $encheres,
					'enchereArchive'		=> $enchere_archive,
					'nbOffresEncheres'		=> $nb_offres,
					'dernieresOffres' 	 	=> $dernieresOffres,
					'images'				=> $images
				),
				"gabarit-frontend");
	}
}