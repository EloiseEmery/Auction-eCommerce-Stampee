<?php

/**
 * Classe de l'entité Commentaire
 *
 */
class Commentaire extends Entite {
	
	protected $commentaire_id = 0;
	protected $commentaire;
	protected $enchere_id;
	protected $utilisateur_id;
	protected $erreurs = array();

	// Getters explicites nécessaires au moteur de templates TWIG
	public function getCommentaire_id()      			{ return $this->commentaire_id; }
	public function getCommentaire()      	 			{ return $this->commentaire; }
	public function getEnchere_id()      	 			{ return $this->enchere_id; }
	public function getUtilisateur_id()      			{ return $this->utilisateur_id; }
	public function getErreurs()            			{ return $this->erreurs; }

	/**
	 * Mutateur de la propriété commentaire_id 
	 * @param int $commentaire_id
	 * @return $this
	 */    
	public function setCommentaire_id($commentaire_id) {
		unset($this->erreurs['commentaire_id']);
		$regExp = '/^\d+$/';
		if (!preg_match($regExp, $commentaire_id)) {
			$this->erreurs['commentaire_id'] = 'Numéro incorrect.';
		}
		$this->commentaire_id = $commentaire_id;
		return $this;
	}

	/**
	 * Mutateur de la propriété commentaire 
	 * @param string $commentaire
	 * @return $this
	 */    
	public function setCommentaire($commentaire) {
		unset($this->erreurs['commentaire']);
		$commentaire = trim($commentaire);
		$regExp = '/^.{0,500}$/i';
		if (!preg_match($regExp, $commentaire)) {
			$this->erreurs['commentaire'] = 'Maximum 500 caractères.';
		} else if ($commentaire == "") {
			$this->erreurs['commentaire'] = 'Le champs est obligatoire.';
		}
		$this->commentaire = $commentaire;
		return $this;
	}

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
}