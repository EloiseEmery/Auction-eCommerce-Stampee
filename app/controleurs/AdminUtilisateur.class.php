<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Utilisateur de l'application admin
 */
class AdminUtilisateur extends Admin {

	private $methodes = [
		'u_connexion'	=> 'connexion',
		'u_deconnexion'	=> 'deconnecter',
		'u_ajouter'     => 'ajouterUtilisateur'
	];

	/**
	 * Gérer l'interface d'administration des utilisateurs 
	 */  
	public function gerer($entite = "utilisateur") {

		$this->entite  = $entite;
		$this->action  = $_GET['action']  ?? '';
		$this->utilisateur_id = $_GET['utilisateur_id'] ?? null;

		if (isset($this->methodes[$this->action])) {
			$methode = $this->methodes[$this->action];
			$this->$methode();
		} else {
			throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
		}
	}

    /**
	 * Afficher la page de connexion
	 * 
	 */  
	public function connexion() {
		$messageErreurConnexion = "";

		// si des données sont reçus du formulaire
		if (count($_POST) !== 0) {
			$utilisateur = self::$oRequetesSQL->connecter($_POST);

			// Si aucun utilisateur n'est retourné
			if (!$utilisateur) {
				$messageErreurConnexion = "Courriel ou mot de passe incorrect.";
			} else {
				// initialiser la variable session de l'utilisateur connecté
				$_SESSION['oUtilisateurConnecter'] = new Utilisateur($utilisateur);
				$this->oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
				// rediriger sur la page d'accueil
				Frontend::accueil();
			}
		}
		$utilisateur = [];
		
		// vue initiale du formulaire de connexion
		(new Vue)->generer("vConnexion",
				array(
					'titre' => "Connexion",
					'messageErreurConnexion' => $messageErreurConnexion
				),
				"gabarit-admin");
	}

	/**
	 * Déconnecter un utilisateur
	 * 
	 */
	public function deconnecter() {
		// destruction de la variable session de l'utilisateur connecté
		unset ($_SESSION['oUtilisateurConnecter']);
		// rediriger sur la page de connexion
		$this->connexion();
	}

	/**
	 * Ajouter un utilisateur
	 * 
	 */
	public function ajouterUtilisateur() {
		$utilisateur  = [];
		$erreurs = [];

		// retour de saisie du formulaire
		if (count($_POST) !== 0) {
			// récupération des données reçues
			$utilisateur = $_POST;

			// création d'un objet Utilisateur pour contrôler la saisie reçue
			$oUtilisateur = new Utilisateur($utilisateur);
			$oUtilisateur->courrielExiste();
			$erreurs = $oUtilisateur->erreurs;

			// aucune erreur de saisie -> requête SQL d'ajout
			if (count($erreurs) === 0) {
				$retour = self::$oRequetesSQL->ajouterUtilisateur([
					'utilisateur_nom'      => $oUtilisateur->utilisateur_nom,
					'utilisateur_prenom'   => $oUtilisateur->utilisateur_prenom,
					'utilisateur_courriel' => $oUtilisateur->utilisateur_courriel,
					'utilisateur_mdp'      => $oUtilisateur->utilisateur_mdp,
					'utilisateur_profil'   => $oUtilisateur->utilisateur_profil
				]);

				// vérifier que le courriel n'est pas déjà utilisé
				if ($retour !== Utilisateur::ERR_COURRIEL_EXISTANT) {
					(new Vue)->generer('vConnexion',
							array(
								'titre'        			=> 'Connexion',
								'utilisateur' 			=> $utilisateur,
								'erreurs'      			=> $erreurs
							),
							'gabarit-frontend');
					exit;
				}
				else {
					$erreurs['utilisateur_courriel'] = $retour;
				}
			}
		} 
		else {
			$utilisateur = [];
			$erreurs     = [];
		}

		// générer la vue initiale du formulaire d'inscription
		(new Vue)->generer('vInscription',
				array(
					'titre'        			=> 'Inscription',
					'utilisateur' 			=> $utilisateur,
					'erreurs'      			=> $erreurs
				),
				'gabarit-admin');
	}
}