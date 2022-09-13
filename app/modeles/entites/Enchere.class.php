<?php

/**
 * Classe de l'entité Enchere
 *
 */
class Enchere extends Entite {
	
	protected $enchere_id = 0;
	protected $enchere_date_debut;
	protected $enchere_date_fin;
	protected $enchere_prix_plancher;
	protected $enchere_archive;
	protected $timbre_id;

	protected $erreurs = array();

	// Getters explicites nécessaires au moteur de templates TWIG
	public function getEnchere_id()       	  	  { return $this->enchere_id; }
	public function getEnchere__date_debut()  	  { return $this->enchere_date_debut; }
	public function getEnchere_date_fin()     	  { return $this->enchere_date_fin; }
	public function getEnchere_prix_plancher()	  { return $this->enchere_prix_plancher; }
	public function getEnchere_enchere_archive()  { return $this->enchere_archive; }
	public function getTimbre_id()  			  { return $this->timbre_id; }
	public function getErreurs()          		  { return $this->erreurs; }

	/**
	 * Mutateur de la propriété enchere_id 
	 * @param int $enchere_id
	 * @return $this
	 */    
	public function setEnchere_id($enchere_id) {
		unset($this->erreurs['enchere_id']);
		$regExp = '/^\d+$/';
		if (!preg_match($regExp, $enchere_id)) {
			$this->erreurs['enchere_id'] = 'Numéro incorrect.';
		}
		$this->enchere_id = $enchere_id;
		return $this;
	}
	
	/**
	 * Mutateur de la propriété enchere_date_debut 
	 * @param string $enchere_date_debut
	 * @return $this
	 */    
	public function setEnchere_date_debut($enchere_date_debut) {
		$now = date('Y-m-d');
		unset($this->erreurs['enchere_date_debut']);
		if ($enchere_date_debut < $now ) {
			$this->erreurs['enchere_date_debut'] = 'L\'enchère doit débuter aujourd\'hui ou plus tard.';
		}
		$this->enchere_date_debut = $enchere_date_debut;
		return $this;
	}

	/**
	 * Mutateur de la propriété enchere_date_fin 
	 * @param string $enchere_date_fin
	 * @return $this
	 */    
	public function setEnchere_date_fin($enchere_date_fin) {
		$enchere_date_debut = $this->enchere_date_debut;
		unset($this->erreurs['enchere_date_fin']);
		if ($enchere_date_fin <= $enchere_date_debut ) {
			$this->erreurs['enchere_date_fin'] = 'L\'enchère ne peut pas terminer à une date ultérieure à celle du début.';
		}
		$this->enchere_date_fin = $enchere_date_fin;
		return $this;
	}

	/**
	 * Mutateur de la propriété enchere_prix_plancher 
	 * @param string $enchere_prix_plancher
	 * @return $this
	 */    
	public function setEnchere_prix_plancher($enchere_prix_plancher) {
		unset($this->erreurs['enchere_prix_plancher']);
		$enchere_prix_plancher = trim($enchere_prix_plancher);
		$regExp = '/^\d+(\.\d{1,2})?$/';
		if (!preg_match($regExp, $enchere_prix_plancher)) {
			$this->erreurs['enchere_prix_plancher'] = 'Veuillez entrez un prix valide (ex: 00.00).';
		}
		$this->enchere_prix_plancher = $enchere_prix_plancher;
		return $this;
	}

	/**
	 * Mutateur de la propriété enchere_archive 
	 * @param int $enchere_archive
	 * @return $this
	 */    
	public function setEnchere_archive($enchere_archive) {
		$this->enchere_archive = $enchere_archive;
		return $this;
	}

	/**
	 * Mutateur de la propriété timbre_id 
	 * @param int $timbre_id
	 * @return $this
	 */    
	public function setTimbre_id($timbre_id) {
		unset($this->erreurs['timbre_id']);
		if($timbre_id == 0) {
			$this->erreurs['timbre_id'] = 'Veuillez choisir un timbre.';
		}
		$this->timbre_id = $timbre_id;
		return $this;
	}
}