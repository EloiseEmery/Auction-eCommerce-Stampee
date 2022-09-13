<?php

/**
 * Classe de l'entité Utilisateur
 *
 */
class Utilisateur extends Entite {

	protected $utilisateur_id = 0;
	protected $utilisateur_nom;
	protected $utilisateur_prenom;
	protected $utilisateur_courriel;
	protected $utilisateur_mdp;
	protected $utilisateur_profil;

	const PROFIL_UTILISATEUR    = 1;
	const PROFIL_EDITEUR = 2;
	const PROFIL_ADMINISTRATEUR = 3;

	protected $erreurs = array();
	const ERR_COURRIEL_EXISTANT = "Courriel déjà utilisé.";

	// Getters explicites nécessaires au moteur de templates TWIG
	public function getUtilisateur_id()       { return $this->utilisateur_id; }
	public function getUtilisateur_nom()      { return $this->utilisateur_nom; }
	public function getUtilisateur_prenom()   { return $this->utilisateur_prenom; }
	public function getUtilisateur_courriel() { return $this->utilisateur_courriel; }
	public function getUtilisateur_mdp()      { return $this->utilisateur_mdp; }
	public function getUtilisateur_profil()   { return $this->utilisateur_profil; }
	public function getErreurs()              { return $this->erreurs; }

	/**
	 * Mutateur de la propriété utilisateur_id 
	 * @param int $utilisateur_id
	 * @return $this
	 */    
	public function setUtilisateur_id($utilisateur_id) {
		unset($this->erreurs['utilisateur_id']);
		$regExp = '/^\d+$/';
		if (!preg_match($regExp, $utilisateur_id)) {
			$this->erreurs['utilisateur_id'] = 'Numéro incorrect.';
		}
		$this->utilisateur_id = $utilisateur_id;
		return $this;
	}    

	/**
	 * Mutateur de la propriété utilisateur_nom 
	 * @param string $utilisateur_nom
	 * @return $this
	 */    
	public function setUtilisateur_nom($utilisateur_nom) {
		unset($this->erreurs['utilisateur_nom']);
		$utilisateur_nom = trim($utilisateur_nom);
		$regExp = '/^[a-zÀ-ÖØ-öø-ÿ]{2,}( [a-zÀ-ÖØ-öø-ÿ]{2,})*$/i';
		if (!preg_match($regExp, $utilisateur_nom)) {
			$this->erreurs['utilisateur_nom'] = 'Au moins 2 caractères alphabétiques pour chaque mot.';
		}
		$this->utilisateur_nom = $utilisateur_nom;
		return $this;
	}

	/**
	 * Mutateur de la propriété utilisateur_prenom 
	 * @param string $utilisateur_prenom
	 * @return $this
	 */    
	public function setUtilisateur_prenom($utilisateur_prenom) {
		unset($this->erreurs['utilisateur_prenom']);
		$utilisateur_prenom = trim($utilisateur_prenom);
		$regExp = '/^[a-zÀ-ÖØ-öø-ÿ]{2,}( [a-zÀ-ÖØ-öø-ÿ]{2,})*$/i';
		if (!preg_match($regExp, $utilisateur_prenom)) {
			$this->erreurs['utilisateur_prenom'] = 'Au moins 2 caractères alphabétiques pour chaque mot.';
		}
		$this->utilisateur_prenom = $utilisateur_prenom;
		return $this;
	}

	/**
	 * Mutateur de la propriété utilisateur_courriel
	 * @param string $utilisateur_courriel
	 * @return $this
	 */    
	public function setUtilisateur_courriel($utilisateur_courriel) {
		unset($this->erreurs['utilisateur_courriel']);
		$utilisateur_courriel = trim(strtolower($utilisateur_courriel));
		if (!filter_var($utilisateur_courriel, FILTER_VALIDATE_EMAIL)) {
			$this->erreurs['utilisateur_courriel'] = 'Format incorrect.';
		}
		$this->utilisateur_courriel = $utilisateur_courriel;
		return $this;
	}

	/**
	 * Mutateur de la propriété utilisateur_profil
	 * @param string $utilisateur_profil
	 * @return $this
	 */    
	public function setUtilisateur_profil($utilisateur_profil) {
		unset($this->erreurs['utilisateur_profil']);
		if ($utilisateur_profil != self::PROFIL_ADMINISTRATEUR &&
			$utilisateur_profil != self::PROFIL_UTILISATEUR) {
			$this->erreurs['utilisateur_profil'] = 'Profil incorrect.';
		}
		$this->utilisateur_profil = $utilisateur_profil;
		return $this;
	}

	/**
	 * Mutateur de la propriété utilisateur_profil
	 * @param string $utilisateur_profil
	 * @return $this
	 */    
	public function setUtilisateur_mdp($utilisateur_mdp) {
		unset($this->erreurs['utilisateur_mdp']);
		$utilisateur_mdp = trim($utilisateur_mdp);
		$regExp = '/^[0-9a-zA-Z]{6,}$/i';
		if (!preg_match($regExp, $utilisateur_mdp)) {
			$this->erreurs['utilisateur_mdp'] = 'Votre mot de passe doit contenir au moins 6 caractères.';
		}
		$this->utilisateur_mdp = $utilisateur_mdp;
		return $this;
	}

	/**
	 * Controler l'existence du courriel
	 * 
	 */    
	public function courrielExiste() {
		if (!isset($this->erreurs['utilisateur_courriel'])) {
			$retour = (new RequetesSQL)->controlerCourriel(['utilisateur_courriel' => $this->utilisateur_courriel,
															'utilisateur_id'       => $this->utilisateur_id
															]);
			if ($retour) $this->erreurs['utilisateur_courriel'] = self::ERR_COURRIEL_EXISTANT;
		}
	}

	/**
	 * Génération d'un mot de passe aléatoire dans la propriété utilisateur_mdp
	 * @return $mdp
	 */    
	public function genererMdp() {
		$lettres = 'abcdefghijklmnopqrstuvwxyz';
		$mdp = '';
		for ($i = 0; $i < 5; $i++) {
			$mdp .= $lettres[rand(0, 25)];
			$mdp .= rand(0, 9);
		}
		$this->utilisateur_mdp = $mdp;
		return $mdp;
	}
}