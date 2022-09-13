<?php

/**
 * Classe de l'entité Timbre
 *
 */
class Timbre extends Entite {

	protected $timbre_id = 0;
	protected $timbre_nom;
	protected $timbre_description;
	protected $timbre_format;
	protected $timbre_annee_emission;
	protected $timbre_couleur;
	protected $timbre_tirage;
	protected $timbre_certifie;
	protected $timbre_type;
	protected $timbre_condition;
	protected $timbre_pays;
	protected $timbre_utilisateur;
	protected $erreurs = array();

	// Getters explicites nécessaires au moteur de templates TWIG
	public function getTimbre_id()       		 { return $this->timbre_id; }
	public function getTimbre_nom()       	 { return $this->timbre_nom; }
	public function getTimbre_description()    { return $this->timbre_description; }
	public function getTimbre_format()     	 { return $this->timbre_format; }
	public function getTimbre_annee_emission() { return $this->timbre_annee_emission; }
	public function getTimbre_couleur()        { return $this->timbre_couleur; }
	public function getTimbre_tirage()       	 { return $this->timbre_tirage; }
	public function getTimbre_certifie()       { return $this->timbre_certifie; }
	public function getTimbre_type()       	 { return $this->timbre_type; }
	public function getTimbre_condition()      { return $this->timbre_condition; }
	public function getTimbre_pays()           { return $this->timbre_pays; }
	public function getTimbre_utilisateur()    { return $this->timbre_utilisateur; }
	public function getErreurs()               { return $this->erreurs; }

	/**
	 * Mutateur de la propriété timbre_id 
	 * @param int $timbre_id
	 * @return $this
	 */    
	public function setTimbre_id($timbre_id) {
		unset($this->erreurs['timbre_id']);
		$regExp = '/^\d+$/';
		if (!preg_match($regExp, $timbre_id)) {
			$this->erreurs['timbre_id'] = 'Numéro incorrect.';
		}
		$this->timbre_id = $timbre_id;
		return $this;
	}

	/**
	 * Mutateur de la propriété timbre_nom 
	 * @param string $timbre_nom
	 * @return $this
	 */    
	public function setTimbre_nom($timbre_nom) {
		unset($this->erreurs['timbre_nom']);
		$timbre_nom = trim($timbre_nom);
		$regExp = '/^[a-zÀ-ÖØ-öø-ÿ0-9]{2,}( [a-zÀ-ÖØ-öø-ÿ0-9]{2,})*$/i';
		if (!preg_match($regExp, $timbre_nom)) {
			$this->erreurs['timbre_nom'] = 'Au moins 2 caractères alphabétiques pour chaque mot.';
		}
		$this->timbre_nom = $timbre_nom;
		return $this;
	} 
	
	/**
	 * Mutateur de la propriété timbre_tirage 
	 * @param string $timbre_tirage
	 * @return $this
	 */    
	public function setTimbre_tirage($timbre_tirage) {
		unset($this->erreurs['timbre_tirage']);
		$timbre_tirage = trim($timbre_tirage);
		$regExp = '/^\d+$/';
		if (!preg_match($regExp, $timbre_tirage)) {
			$this->erreurs['timbre_tirage'] = 'Veuillez préciser le nombre d\'exemplaire.';
		}
		$this->timbre_tirage = $timbre_tirage;
		return $this;
	}

	/**
	 * Mutateur de la propriété timbre_couleur 
	 * @param string $timbre_couleur
	 * @return $this
	 */    
	public function setTimbre_couleur($timbre_couleur) {
		unset($this->erreurs['timbre_couleur']);
		$timbre_couleur = trim($timbre_couleur);
		$regExp = '/^[a-zÀ-ÖØ-öø-ÿ]{2,}( [a-zÀ-ÖØ-öø-ÿ]{2,})*$/i';
		if (!preg_match($regExp, $timbre_couleur)) {
			$this->erreurs['timbre_couleur'] = 'Au moins 2 caractères alphabétiques pour chaque mot.';
		}
		$this->timbre_couleur = $timbre_couleur;
		return $this;
	}

	/**
	 * Mutateur de la propriété timbre_format 
	 * @param string $timbre_format
	 * @return $this
	 */    
	public function setTimbre_format($timbre_format) {
		unset($this->erreurs['timbre_format']);
		$timbre_format = trim($timbre_format);
		if ($timbre_format == "") {
			$this->erreurs['timbre_format'] = 'Champs requis. Préciser les dimensions et/ou le format du timbre.';
		}
		$this->timbre_format = $timbre_format;
		return $this;
	}
	
	/**
	 * Mutateur de la propriété timbre_annee_emission 
	 * @param string $timbre_annee_emission
	 * @return $this
	 */    
	public function setTimbre_annee_emission($timbre_annee_emission) {
		unset($this->erreurs['timbre_annee_emission']);
		$timbre_annee_emission = trim($timbre_annee_emission);
		if ($timbre_annee_emission < 1840 || $timbre_annee_emission > 2022) {
			$this->erreurs['timbre_annee_emission'] = 'Entrez une année comprise entre 1840 et l\'année courante.';
		}
		
		$this->timbre_annee_emission = $timbre_annee_emission;
		return $this;
	}

	/**
	 * Mutateur de la propriété timbre_type 
	 * @param string $timbre_type
	 * @return $this
	 */    
	public function setTimbre_type($timbre_type) {
		unset($this->erreurs['timbre_type']);
		if($timbre_type == 0) {
			$this->erreurs['timbre_type'] = 'Veuillez définir le type.';
		}
		$this->timbre_type = $timbre_type;
		return $this;
	}

	/**
	 * Mutateur de la propriété timbre_description 
	 * @param string $timbre_description
	 * @return $this
	 */    
	public function setTimbre_description($timbre_description) {
		unset($this->erreurs['timbre_description']);
		$timbre_description = trim($timbre_description);
		$regExp = '/^.{0,250}$/i';
		if (!preg_match($regExp, $timbre_description)) {
			$this->erreurs['timbre_description'] = 'Maximum 250 caractères.';
		}
		$this->timbre_description = $timbre_description;
		return $this;
	}

	/**
	 * Mutateur de la propriété timbre_condition 
	 * @param string $timbre_condition
	 * @return $this
	 */    
	public function setTimbre_condition($timbre_condition) {
		unset($this->erreurs['timbre_condition']);
		if($timbre_condition == 0) {
			$this->erreurs['timbre_condition'] = 'Veuillez définir la condition.';
		}
		$this->timbre_condition = $timbre_condition;
		return $this;
	}

	/**
	 * Mutateur de la propriété timbre_pays 
	 * @param string $timbre_pays
	 * @return $this
	 */    
	public function setTimbre_pays($timbre_pays) {
		unset($this->erreurs['timbre_pays']);
		if($timbre_pays == 0) {
			$this->erreurs['timbre_pays'] = 'Veuillez définir le pays d\'origine.';
		}
		$this->timbre_pays = $timbre_pays;
		return $this;
	}

	/**
	 * Mutateur de la propriété timbre_certifie 
	 * @param string $timbre_certifie
	 * @return $this
	 */    
	public function setTimbre_certifie($timbre_certifie) {
		$this->timbre_certifie = $timbre_certifie;
		return $this;
	}

	/**
	 * Mutateur de la propriété timbre_utilisateur 
	 * @param string $timbre_utilisateur
	 * @return $this
	 */    
	public function setTimbre_utilisateur($timbre_utilisateur) {
		$this->timbre_utilisateur = $timbre_utilisateur;
		return $this;
	}
}