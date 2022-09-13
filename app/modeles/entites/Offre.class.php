<?php

/**
 * Classe de l'entité Offre
 *
 */
class Offre extends Entite {

	protected $utilisateur_id;
	protected $enchere_id;
	protected $offre_mise;
	protected $erreurs = array();

	// Getters explicites nécessaires au moteur de templates TWIG
	public function getUtilisateur_id()      { return $this->utilisateur_id; }
	public function getEnchere_id()      	 { return $this->enchere_id; }
	public function getOffre_mise()      	 { return $this->offre_mise; }
	public function getErreurs()             { return $this->erreurs; }

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
	 * Mutateur de la propriété offre_mise 
	 * @param string $offre_mise
	 * @return $this
	 */    
	public function setOffre_mise($offre_mise) {
		unset($this->erreurs['offre_mise']);
		$offre_mise = trim($offre_mise);
		$regExp = '/^\d{1,}(?:\.\d{1,2})?$/';
		if (!preg_match($regExp, $offre_mise)) {
			$this->erreurs['offre_mise'] = 'Veuillez entrez une offre valide. ex: 00.00';
		} else if (!$offre_mise || $offre_mise < 1 ) {
			$this->erreurs['offre_mise'] = 'Vous devez miser au moins le minimum.';
		}
		$this->offre_mise = $offre_mise;
		return $this;
	}
}