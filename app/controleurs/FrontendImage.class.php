<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Utilisateur de l'application admin
 */
class FrontendImage extends Frontend {

	private $methodes = [
		'i_ajouter'                 => 'imageAjouter',
		'i_ajouterPrincipale'       => 'imageAjouterPrincipale',
		'i_supprimer'               => 'imageSupprimer'
	];

	/**
	 * Gérer l'interface d'administration des utilisateurs 
	 */  
	public function gerer($entite = "utilisateur") {
		
		$this->entite  = $entite;
		$this->action  = $_GET['action']  ?? '';
		$this->enchere_id = $_GET['enchere_id'] ?? null;
		$this->timbre_id = $_GET['timbre_id'] ?? null;
		$this->image_id = $_GET['image_id'] ?? null;
		$this->messageRetourAction = $_GET['messageRetourAction'] ?? '';

		if (isset($this->methodes[$this->action])) {
			$methode = $this->methodes[$this->action];
			$this->$methode();
		} else {
			throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
		}
	}

	/**
	 * Ajouter une image
	 * 
	 */  
	public function imageAjouter() {
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
		$erreurs = [];
		$images = [];   
		$image = [];
		$timbre_id = $this->timbre_id;
		$images = self::$oRequetesSQL->listerImagesTimbre($timbre_id);
		$timbre = self::$oRequetesSQL->getTimbre($timbre_id);

		if (count($_POST) !== 0 && count($_FILES) !== 0) {
			// récupération des données reçues
			$image['image_url'] = $_FILES['image_url'];
			$image['image_titre'] = $_POST['image_titre'];
			$image['image_principale'] = $_POST['image_principale'];
			$image['timbre_id'] = $_POST['timbre_id'];
			
			// création d'un objet Image pour contrôler la saisie reçue
			$oImage = new Image($image);
			$erreurs = $oImage->erreurs;
			
			if (count($erreurs) === 0) {
				// insertion de l'image dans la DB
				$retour = self::$oRequetesSQL->ajouterImage([
					'image_id'           => $oImage->image_id,
					'image_url'          => $oImage->image_url,
					'image_titre'        => $oImage->image_titre,
					'image_principale'   => $oImage->image_principale,
					'timbre_id'          => $oImage->timbre_id
				]);

				if (preg_match('/^[1-9]\d*$/', $retour)) {
					header('Location: .?entite=image&action=i_ajouter&timbre_id='.$timbre_id.'&messageRetourAction=Ajout de l\'image effectuée.');
					exit;
				} else {
					header('Location: .?entite=image&action=i_ajouter&timbre_id='.$timbre_id.'&messageRetourAction=Ajout de l\'image à échoué.');
					exit;
				}   
			}
		} else {
			$enchere = self::$oRequetesSQL->getEnchere($this->enchere_id);
			$erreurs = [];
		}

		(new Vue)->generer("vImageAjouter",
				array(
					'titre'                 => "Ajouter des images pour",
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'images'                => $images,
					'timbre'                => $timbre,
					'erreurs'               => $erreurs,
					'messageRetourAction'   => $this->messageRetourAction
				),
				"gabarit-frontend");
	}

	/**
	 * Ajouter une image principale
	 * 
	 */
	public function imageAjouterPrincipale() {
		// vérifier si le timbre à déjà une image principale
		$timbre_id = $this->timbre_id;
		$retour = self::$oRequetesSQL->findImagePrincipale($timbre_id);

		if ($retour) {
			// si oui, retiré celle-ci et updater la nouvelle
			self::$oRequetesSQL->unsetImagePrincipale($retour);
			self::$oRequetesSQL->updateImagePrincipale($_POST);
			header('Location: .?entite=image&action=i_ajouter&timbre_id='.$timbre_id.'&messageRetourAction=Image principale mise à jour.');
			exit;
		} else {
			// si non initialisé l'image principale
			self::$oRequetesSQL->updateImagePrincipale($_POST);
			header('Location: .?entite=image&action=i_ajouter&timbre_id='.$timbre_id.'&messageRetourAction=Image principale sélectionnée.');
			exit;
		}
	}

	/**
	 * Supprimer une image
	 * 
	 */  
	public function imageSupprimer() {
		$timbre_id = $this->timbre_id;
		$image_id = $this->image_id;
		$image_url = self::$oRequetesSQL->getImageUrl($image_id);
		
		// suppression de l'enchère dans le folder
		$file_to_delete = 'assets/img/timbres/'.$image_url['image_url'];
		unlink($file_to_delete);

		// suppression dans la BD
		$retour = self::$oRequetesSQL->supprimerImage($image_id);
		
		// redirection vers la liste des Enchères
		if (preg_match('/^[1-9]\d*$/', $retour)) {
				
			// vérifier si le timbre à déjà une image principale
			$retour = self::$oRequetesSQL->findImagePrincipale($timbre_id);
			if (!$retour) {
				// si pas d'image principale mettre celle par défaut
				$retour = self::$oRequetesSQL->getImageParDefaut($timbre_id);
				self::$oRequetesSQL->updateImagePrincipale($retour);
			}
			// redirection
			header('Location: .?entite=image&action=i_ajouter&timbre_id='.$timbre_id.'&messageRetourAction=Supression de l\'image effectuée.');
			exit;
		} else {
			header('Location: .?entite=image&action=i_ajouter&timbre_id='.$timbre_id.'&messageRetourAction=Supression de l\'image à échoué.');
			exit;
		}       
	}
}