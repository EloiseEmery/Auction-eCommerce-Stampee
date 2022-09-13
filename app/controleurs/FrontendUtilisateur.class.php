<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Utilisateur de l'application admin
 */
class FrontendUtilisateur extends Frontend {

	private $methodes = [
		'u_profil'		=> 'utilisateurProfil',
		'u_timbres'	    => 'utilisateurTimbres',
		'u_encheres'	=> 'utilisateurEncheres',
		'u_historique'	=> 'utilisateurHistorique',
		'u_favoris'		=> 'utilisateurFavoris'
	];

	/**
	 * Gérer l'interface d'administration des utilisateurs 
	 */  
	public function gerer($entite = "utilisateur") {
		
		$this->entite  = $entite;
		$this->action  = $_GET['action']  ?? '';
		$this->utilisateur_id = $_GET['utilisateur_id'] ?? null;
		$this->messageRetourAction = $_GET['messageRetourAction'] ?? '';

		if (isset($this->methodes[$this->action])) {
			$methode = $this->methodes[$this->action];
			$this->$methode();
		} else {
			throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
		}
	}

	/**
	 * Afficher le profil d'un utilisateur
	 * 
	 */
	public function utilisateurProfil() {
		// vérifier que l'usager à le droit d'accès à cette page
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];		
		} else {
			Frontend::accueil();
			die();
		}

		(new Vue)->generer('vUtilisateurProfil',
				array(
					'titre'        			=> 'Mes informations',
					'oUtilisateurConnecter' => $oUtilisateurConnecter
		),
		'gabarit-frontendUtilisateurProfil');
	}

	/**
	 * Afficher les enchères d'un utilisateur
	 * 
	 */
	public function utilisateurTimbres() {
		// vérifier que l'usager à le droit d'accès à cette page
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];		
		} else {
			Frontend::accueil();
			die();
		}

		// récupération des données à afficher
		$timbres = [];
		$images = [];
	
		$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		$utilisateur_id = $oUtilisateurConnecter->utilisateur_id;
		$timbres = self::$oRequetesSQL->listerTimbresUtilisateur($utilisateur_id);
		$images = self::$oRequetesSQL->listerImagesUtilisateur($utilisateur_id);

		(new Vue)->generer('vUtilisateurTimbres',
				array(
					'titre'        			=> 'Mes timbres',
					'timbres'				=> $timbres,
					'images'				=> $images,
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
              		'messageRetourAction' 	=> $this->messageRetourAction
				),
				'gabarit-frontendUtilisateurProfil');
	}

	/**
	 * Afficher les enchères d'un utilisateur
	 * 
	 */
	public function utilisateurEncheres() {
		// vérifier que l'usager à le droit d'accès à cette page
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];		
		} else {
			Frontend::accueil();
			die();
		}

		$encheres = [];
	
		// récupération des données à afficher
		$utilisateur_id = $oUtilisateurConnecter->utilisateur_id;
		$encheres = self::$oRequetesSQL->listerEncheresUtilisateur($utilisateur_id);
		$images = self::$oRequetesSQL->listerImagesEnchere();
		$nb_offres = self::$oRequetesSQL->compterMisesEncheres();
		$dernieresOffres = self::$oRequetesSQL->afficherDernieresMises();

		(new Vue)->generer('vUtilisateurEncheres',
				array(
					'titre'        			=> 'Mes enchères',
					'encheres'				=> $encheres,
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'messageRetourAction' 	=> $this->messageRetourAction,
					'images'				=> $images,
					'nbOffresEncheres'		=> $nb_offres,
					'dernieresOffres' 	 	=> $dernieresOffres
		),
		'gabarit-frontendUtilisateurProfil');
	}

	/**
	 * Afficher l'historique d'un utilisateur
	 * 
	 */
	public function utilisateurHistorique() {
		// vérifier que l'usager à le droit d'accès à cette page
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];		
		} else {
			Frontend::accueil();
			die();
		}

		// récupération des données à afficher
		$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		$utilisateur_id = $oUtilisateurConnecter->utilisateur_id;
		$historique = self::$oRequetesSQL->listerHistoriqueUtilisateur($utilisateur_id);

		(new Vue)->generer('vUtilisateurHistorique',
				array(
					'titre'        			=> 'Mon historique',
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'historique'			=> $historique
		),
		'gabarit-frontendUtilisateurProfil');
	}

	/**
	 * Afficher les favoris d'un utilisateur
	 * 
	 */
	public function utilisateurFavoris() {
		// vérifier que l'usager à le droit d'accès à cette page
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];		
		} else {
			Frontend::accueil();
			die();
		}

		// récupération des données à afficher
		$utilisateur_id = $oUtilisateurConnecter->utilisateur_id;
		$favoris = self::$oRequetesSQL->listerFavorisUtilisateur($utilisateur_id);
		$images = self::$oRequetesSQL->listerImagesEnchere();
		$nb_offres = self::$oRequetesSQL->compterMisesEncheres();
		$dernieresOffres = self::$oRequetesSQL->afficherDernieresMises();

		(new Vue)->generer('vUtilisateurFavoris',
				array(
					'titre'        			=> 'Mes favoris',
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'favoris' 				=> $favoris,
					'nbOffresEncheres'		=> $nb_offres,
					'dernieresOffres' 	 	=> $dernieresOffres,
					'images' 				=> $images,
					'messageRetourAction' 	=> $this->messageRetourAction
		),
		'gabarit-frontendUtilisateurProfil');
	}
}