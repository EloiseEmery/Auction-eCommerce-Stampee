<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Utilisateur de l'application admin
 */
class FrontendFavoris extends Frontend {

	private $methodes = [
		'f_ajouter'     => 'favorisAjouter',
		'f_supprimer'   => 'favorisSupprimer'
	];

	/**
	 * Gérer l'interface d'administration des utilisateurs 
	 */  
	public function gerer($entite = "utilisateur") {
		
		$this->entite  = $entite;
		$this->action  = $_GET['action']  ?? '';
		$this->enchere_id = $_GET['enchere_id'] ?? null;
		$this->redirect = $_GET['redirect'] ?? null;
		$this->messageRetourAction = $_GET['messageRetourAction'] ?? '';

		if (isset($this->methodes[$this->action])) {
			$methode = $this->methodes[$this->action];
			$this->$methode();
		} else {
			throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
		}
	}

	/**
	 * Ajouter des favoris
	 * 
	 */  
	public function favorisAjouter() {
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		} else {
			$oUtilisateurConnecter = null;
		}

		$enchere_id = $this->enchere_id;
		$utilisateur_id = $oUtilisateurConnecter->utilisateur_id;
		$favoris = ['enchere_id' => $enchere_id, 'utilisateur_id' => $utilisateur_id];
		
		// ajout du favoris
		self::$oRequetesSQL->ajouterFavoris($favoris);
		// redirection
		header('Location: .?entite=enchere&action=e_afficher&enchere_id='.$this->enchere_id);
		exit;
	}

	/**
	 * Supprimer un favoris
	* 
	*/  
	public function favorisSupprimer() {
		if(isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		}
		else {
			$oUtilisateurConnecter = null;
		}

		$utilisateur_id = $oUtilisateurConnecter->utilisateur_id;
		$enchere_id =  $this->enchere_id;

		// suppression du favoris
		$retour = self::$oRequetesSQL->supprimerFavoris($utilisateur_id, $enchere_id);
		// redirection
		if (preg_match('/^[1-9]\d*$/', $retour)) {
			if ($this->redirect == 'u_favoris') {
				header('Location: .?entite=utilisateur&action=u_favoris&messageRetourAction=Favoris retiré.');
				exit;
			} else {
				header('Location: .?entite=enchere&action=e_afficher&enchere_id='.$this->enchere_id);
				exit;
			}
		} else {
			header('Location: .?entite=utilisateur&action=u_favoris&messageRetourAction=Suppression non effectuée.');
			exit;
		}
	}
}