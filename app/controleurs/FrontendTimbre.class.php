<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Utilisateur de l'application admin
 */
class FrontendTimbre extends Frontend {

	private $methodes = [
		't_ajouter'     => 'timbreAjouter',
		't_supprimer'   => 'timbreSupprimer',
		't_modifier'    => 'timbreModifier'
	];

	/**
	 * Gérer l'interface d'administration des utilisateurs 
	 */  
	public function gerer($entite = "utilisateur") {
		
		$this->entite  = $entite;
		$this->action  = $_GET['action']  ?? '';
		$this->utilisateur_id = $_GET['utilisateur_id'] ?? null;
		$this->timbre_id = $_GET['timbre_id'] ?? null;
		$this->messageRetourAction = $_GET['messageRetourAction'] ?? '';

		if (isset($this->methodes[$this->action])) {
			$methode = $this->methodes[$this->action];
			$this->$methode();
		} else {
			throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
		}
	}

	/**
	 * Ajouter un timbre
	 * 
	 */  
	public function timbreAjouter() {
		// vérifier que l'utilisateur à la permission d'accès à cette page
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		} else {
			$oUtilisateurConnecter = null;
			Frontend::accueil();
			die();
		}

		// récupération des données à afficher
		$timbre  = [];
		$erreurs = [];
		
		$timbreTypes = self::$oRequetesSQL->getTimbreTypes();
		$timbreConditions = self::$oRequetesSQL->getTimbreConditions();
		$timbrePaysOrigine = self::$oRequetesSQL->getTimbrePays();

		if (count($_POST) !== 0) {
			// récupération des données reçues
			$timbre = $_POST;

			// création d'un objet Timbre pour contrôler la saisie reçue
			$oTimbre = new Timbre($timbre);
			$erreurs = $oTimbre->erreurs;

			// aucune erreur de saisie -> requête SQL d'ajout
			if (count($erreurs) === 0) {
				// insertion du timbre dans la DB
				$retour = self::$oRequetesSQL->ajouterTimbre([
					'timbre_nom'           => $oTimbre->timbre_nom,
					'timbre_description'   => $oTimbre->timbre_description,
					'timbre_format'        => $oTimbre->timbre_format,
					'timbre_annee_emission'=> $oTimbre->timbre_annee_emission,
					'timbre_couleur'       => $oTimbre->timbre_couleur,
					'timbre_tirage'        => $oTimbre->timbre_tirage,
					'timbre_certifie'      => $oTimbre->timbre_certifie,
					'timbre_type'          => $oTimbre->timbre_type,
					'timbre_condition'     => $oTimbre->timbre_condition,
					'timbre_pays'          => $oTimbre->timbre_pays,
					'timbre_utilisateur'   => $oTimbre->timbre_utilisateur
				]);

				if (preg_match('/^[1-9]\d*$/', $retour)) {
					// Initialiser une image principale par défaut
					$oImage = new Image();
					self::$oRequetesSQL->ajouterImage([
						'image_id'           => $oImage->image_id,
						'image_url'          => "default-timbre-image-principale.jpg",
						'image_titre'        => "image principale par défaut",
						'image_principale'   => 1,
						'timbre_id'          => $retour
					]);
					header('Location: .?entite=utilisateur&action=u_timbres&messageRetourAction=Ajout du timbre effectué.');
					exit;
				} 
				else {
					header('Location: .?entite=utilisateur&action=u_timbres&messageRetourAction=Ajout du timbre non effectuée.');
					exit;
				}   
			}
		}

		// générer la vue initiale du formulaire d'ajout
		(new Vue)->generer("vTimbreAjouter",
				array(
					'titre'                 => "Ajouter un timbre",
					'timbre'                => $timbre,
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'timbreTypes'           => $timbreTypes,
					'timbreConditions'      => $timbreConditions,
					'timbrePaysOrigine'     => $timbrePaysOrigine,
					'erreurs'               => $erreurs
				),
				"gabarit-frontend");
	}

	/**
	 * Modifier un timbre
	 * 
	 */  
	public function timbreModifier() {
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];

			// vérifier que l'utilisateur à la permission d'accès à cette page
			$idMembreConnecte = $oUtilisateurConnecter->utilisateur_id;
			$retour = self::$oRequetesSQL->getUtilisateurTimbre($this->timbre_id);
			$idMembreEnchere = $retour['timbre_utilisateur'];
			
			if ($idMembreConnecte != $idMembreEnchere) {
				Frontend::accueil();
				die();
			}
		} else {
			$oUtilisateurConnecter = null;
			Frontend::accueil();
			die();
		}

		// récupération des données à afficher
		$timbre  = [];
		$erreurs = [];
		
		$timbreTypes = self::$oRequetesSQL->getTimbreTypes();
		$timbreConditions = self::$oRequetesSQL->getTimbreConditions();
		$timbrePaysOrigine = self::$oRequetesSQL->getTimbrePays();
		
		if (count($_POST) !== 0) {
			$timbre = $_POST;
			$oTimbre = new Timbre($timbre);
			$erreurs = $oTimbre->erreurs;

			if (count($erreurs) === 0) {
				// modification du timbre dans la DB
				$retour = self::$oRequetesSQL->modifierTimbre([
					'timbre_id'            => $oTimbre->timbre_id,
					'timbre_nom'           => $oTimbre->timbre_nom,
					'timbre_description'   => $oTimbre->timbre_description,
					'timbre_format'        => $oTimbre->timbre_format,
					'timbre_annee_emission'=> $oTimbre->timbre_annee_emission,
					'timbre_couleur'       => $oTimbre->timbre_couleur,
					'timbre_tirage'        => $oTimbre->timbre_tirage,
					'timbre_certifie'      => $oTimbre->timbre_certifie,
					'timbre_type'          => $oTimbre->timbre_type,
					'timbre_condition'     => $oTimbre->timbre_condition,
					'timbre_pays'          => $oTimbre->timbre_pays,
					'timbre_utilisateur'   => $oTimbre->timbre_utilisateur
				]);

				// redirection
				if (preg_match('/^[1-9]\d*$/', $retour)) {
					header('Location: .?entite=utilisateur&action=u_timbres&messageRetourAction=Modification effectuée.');
					exit;
				} else {
					header('Location: .?entite=utilisateur&action=u_timbres&messageRetourAction=Modification non effectuée.');
					exit;
				}
			}
		} else {
			$timbre = self::$oRequetesSQL->getTimbre($this->timbre_id);
			$erreurs = [];
		}

		// vue initiale du formulaire de modification
		(new Vue)->generer("vTimbreModifier",
				array(
					'titre'                 => "Modifier le timbre",
					'timbre'                => $timbre,
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'timbreTypes'           => $timbreTypes,
					'timbreConditions'      => $timbreConditions,
					'timbrePaysOrigine'     => $timbrePaysOrigine,
					'erreurs'               => $erreurs
				),
				"gabarit-frontend");
	}

	/**
	 * Supprimer un timbre
	 * 
	 */  
	public function timbreSupprimer() {
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];

			// vérifier que l'utilisateur à la permission d'accès à cette page
			$idMembreConnecte = $oUtilisateurConnecter->utilisateur_id;
			$retour = self::$oRequetesSQL->getUtilisateurTimbre($this->timbre_id);
			$idMembreEnchere = $retour['timbre_utilisateur'];
			
			if ($idMembreConnecte != $idMembreEnchere) {
				Frontend::accueil();
				die();
			}
		} else {
			$oUtilisateurConnecter = null;
			Frontend::accueil();
			die();
		}
		
		// supprimer toutes les images et vérifier qu'il n'y a pas d'enchères liées au timbre
		$retourTimbre = self::$oRequetesSQL->listerEncheresTimbre($this->timbre_id);
		if ($retourTimbre) {
			header('Location: .?entite=utilisateur&action=u_timbres&messageRetourAction=Veuillez d\'abord supprimer les enchères liées à ce timbre.');
			exit;
		} else {
			// suppression du timbre
			self::$oRequetesSQL->supprimerImagesTimbre($this->timbre_id);
			$retour = self::$oRequetesSQL->supprimerTimbre($this->timbre_id);
			// redirection vers la liste des timbres
			if (preg_match('/^[1-9]\d*$/', $retour)) {
				header('Location: .?entite=utilisateur&action=u_timbres&messageRetourAction=Suppression réussie.');
				exit;
			} else {
				header('Location: .?entite=utilisateur&action=u_timbres&messageRetourAction=Suppression non effectuée.');
				exit;
			}
		}
	}
}