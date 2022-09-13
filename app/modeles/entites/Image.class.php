<?php

/**
 * Classe de l'entité Image
 *
 */
class Image extends Entite {
	
	protected $image_id = 0;
	protected $image_url = array();
	protected $image_titre;
	protected $image_principale;
	protected $timbre_id;
	protected $erreurs = array();

	// Getters explicites nécessaires au moteur de templates TWIG
	public function getImage_id()       	  { return $this->image_id; }
	public function getImage_url()      	  { return $this->image_url; }
	public function getImage_titre()      	  { return $this->image_titre; }
	public function getImage_principale()     { return $this->image_principale; }
	public function getTimbre_id()      	  { return $this->timbre_id; }
	public function getErreurs()              { return $this->erreurs; }

	/**
	 * Mutateur de la propriété image_id 
	 * @param int $image_id
	 * @return $this
	 */    
	public function setImage_id($image_id) {
		unset($this->erreurs['image_id']);
		$regExp = '/^\d+$/';
		if (!preg_match($regExp, $image_id)) {
			$this->erreurs['image_id'] = 'Numéro incorrect.';
		}
		$this->image_id = $image_id;
		return $this;
	}    

	/**
	 * Mutateur de la propriété image_url 
	 * @param string $image_url
	 * @return $this
	 */    
	public function setImage_url($image_url) {
		$error_types = array(
			1 => 'Le fichier téléchargé dépasse la limite permise par la directive upload_max_filesize dans php.ini.',
			'Le fichier téléchargé dépasse la limite permise.',
			'Le fichier téléchargé a seulement été partiellement téléchargé.',
			'Aucun fichier téléchargé.',
			6 => 'Missing a temporary folder.',
			'Failed to write file to disk.',
			'A PHP extension stopped the file upload.'
		);

		$extension_image = strtolower(  substr(  strrchr($_FILES['image_url']['name'], '.')  ,1)  );
		$extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );

		if ($_FILES['image_url']['error'] == 0) {
			if ( in_array($extension_image,$extensions_valides) ) {
				// Changer le nom de l'image
				$id_image_url = md5(uniqid(rand(), true));
				$img_url = $id_image_url ."-". strtolower($_FILES['image_url']['name']);
				$this->image_url  = (str_replace(' ', '-', $img_url));

				// Déplacer l'image dans le dossier 
				$nom = "assets/img/timbres/{$this->image_url}";
				$resultat = move_uploaded_file($_FILES['image_url']['tmp_name'],$nom);
				if (!$resultat) $this->erreurs['image_url'] = 'Le transfert de votre image a échoué. Veuillez essayer de nouveau.';
			} else {
				$this->erreurs['image_url'] = 'Extension du fichier invalide.';
			}
		} else {
			$this->erreurs['image_url'] = $error_types[$_FILES['image_url']['error']];
		}
		return $this;
	}

  	/**
	 * Mutateur de la propriété image_titre 
	 * @param string $image_titre
	 * @return $this
	 */    
	public function setImage_titre($image_titre) {
		unset($this->erreurs['image_titre']);
		$image_titre = trim($image_titre);
		$regExp = '/^[a-zÀ-ÖØ-öø-ÿ0-9]{2,}( [a-zÀ-ÖØ-öø-ÿ0-9]{2,})*$/i';
		if (!preg_match($regExp, $image_titre)) {
			$this->erreurs['image_titre'] = 'Au moins 2 caractères alphabétiques pour chaque mot.';
		}
		$this->image_titre = $image_titre;
		return $this;
	} 

	/**
	 * Mutateur de la propriété image_principale 
	 * @param int $image_principale
	 * @return $this
	 */    
	public function setImage_principale($image_principale) {
		unset($this->erreurs['image_principale']);
		$regExp = '/^\d+$/';
		if (!preg_match($regExp, $image_principale)) {
			$this->erreurs['image_principale'] = 'Numéro incorrect.';
		}
		$this->image_principale = $image_principale;
		return $this;
	}   

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
}