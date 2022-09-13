<?php

/**
 * Classe des requêtes SQL
 *
 */
class RequetesSQL extends RequetesPDO {
	
	/* GESTION DES UTILISATEURS 
	======================== */
	/**
	 * Ajouter un utilisateur
	 * @param array $champs tableau des champs de l'utilisateur 
	 * @return int|string clé primaire de la ligne ajoutée, message d'erreur sinon
	 */ 
	public function ajouterUtilisateur($champs) {
		$this->sql = '
			INSERT INTO `Utilisateur` SET
			utilisateur_nom      = :utilisateur_nom,
			utilisateur_prenom   = :utilisateur_prenom,
			utilisateur_courriel = :utilisateur_courriel,
			utilisateur_mdp      = SHA2(:utilisateur_mdp, 512),
			utilisateur_profil   = :utilisateur_profil;';
		return $this->CUDLigne($champs);
	}
	
	/**
	 * Récupération des utilisateurs
	 * @return array tableau d'objets Utilisateur
	 */ 
	public function getUtilisateurs() {
		$this->sql = "
			SELECT utilisateur_id, utilisateur_nom, utilisateur_prenom, utilisateur_courriel, utilisateur_profil
			FROM `Utilisateur`;";
		return $this->getLignes();
	}

	/**
	 * Récupération d'un utilisateur
	 * @param int $utilisateur_id, clé du utilisateur  
	 * @return object Utilisateur
	 */ 
	public function getUtilisateur($utilisateur_id) {
		$this->sql = "
			SELECT utilisateur_id, utilisateur_nom, utilisateur_prenom, utilisateur_courriel, utilisateur_profil
			FROM `Utilisateur`
			WHERE utilisateur_id = :utilisateur_id;";
		return $this->getLignes(['utilisateur_id' => $utilisateur_id], RequetesPDO::UNE_SEULE_LIGNE);
	}

	/**
	 * Connecter un utilisateur
	 * @param array $champs, tableau avec les champs utilisateur_courriel et utilisateur_mdp  
	 * @return object Utilisateur
	 */ 
	public function connecter($champs) {
		$this->sql = "
			SELECT utilisateur_id, utilisateur_nom, utilisateur_prenom, utilisateur_courriel, utilisateur_profil
			FROM `Utilisateur`
			WHERE utilisateur_courriel = :utilisateur_courriel AND utilisateur_mdp = SHA2(:utilisateur_mdp, 512);";
		return $this->getLignes($champs, RequetesPDO::UNE_SEULE_LIGNE);
	}
	
	/**
	 * Contrôler si adresse courriel non déjà utilisée par un autre utilisateur que utilisateur_id
	 * @param array $champs tableau utilisateur_courriel et utilisateur_id (0 si dans toute la table)
	 * @return string|false utilisateur avec ce courriel, false si courriel disponible
	 */ 
	public function controlerCourriel($champs) {
		$this->sql = '
			SELECT utilisateur_id FROM `Utilisateur`
			WHERE utilisateur_courriel = :utilisateur_courriel AND utilisateur_id != :utilisateur_id;';
		return $this->getLignes($champs, RequetesPDO::UNE_SEULE_LIGNE);
	}

	/**
	 * Lister l'historique d'un utilisateur
	 * @param int $utilisateur_id, clé du utilisateur  
	 * @return array tableau d'objets
	 */ 
	public function listerHistoriqueUtilisateur($utilisateur_id) {
		$this->sql = '	
			SELECT Offre.*, Enchere.*, Timbre.timbre_nom 
			FROM `Offre`
			JOIN `Enchere` 
			ON Offre.enchere_id = Enchere.enchere_id
			JOIN `Timbre`
			ON Enchere.timbre_id = Timbre.timbre_id
			WHERE Offre.utilisateur_id = :utilisateur_id
			ORDER BY Offre.offre_date_mise DESC;';
		return $this->getLignes(['utilisateur_id' => $utilisateur_id]);
	}

	/**
	 * Lister les timbre d'un utilisateur
	 * @param int $utilisateur_id, clé du utilisateur 
	 * @return array tableau d'objets
	 */ 
	public function listerTimbresUtilisateur($utilisateur_id) {
		$this->sql = '
			SELECT * FROM `Timbre` 
			WHERE timbre_utilisateur = :utilisateur_id
			ORDER BY Timbre.timbre_id DESC;;';
		return $this->getLignes(['utilisateur_id' => $utilisateur_id]);
	}

	/**
	 * Lister les enchères d'un utilisateur
	 * @param int $utilisateur_id, clé du utilisateur  
	 * @return array tableau d'objets
	 */ 
	public function listerEncheresUtilisateur($utilisateur_id) {
		$this->sql = '
			SELECT * FROM `Enchere` 
			JOIN `Timbre` 
			ON Enchere.timbre_id = Timbre.timbre_id
			WHERE Timbre.timbre_utilisateur = :utilisateur_id
			ORDER BY Enchere.enchere_id DESC;';
		return $this->getLignes(['utilisateur_id' => $utilisateur_id]);		
	}

	/**
	 * Lister tous les favoris d'un membre
	 * @param int $utilisateur_id, clé du utilisateur  
	 * @return array tableau d'objets
	 */ 
	public function listerFavorisUtilisateur($utilisateur_id) {
		$this->sql = '
			SELECT * FROM `Favoris`
			JOIN `Enchere` 
			ON Enchere.enchere_id = Favoris.enchere_id
			JOIN `Timbre`
			ON Timbre.timbre_id = Enchere.timbre_id
			JOIN `Utilisateur` 
			ON Utilisateur.utilisateur_id = Favoris.utilisateur_id
			WHERE Favoris.utilisateur_id = :utilisateur_id
			ORDER BY Favoris.enchere_id DESC;';
		return $this->getLignes(['utilisateur_id' => $utilisateur_id]);
	} 
	

	/* GESTION DES TIMBRES 
	======================== */
	/**
	 * Ajouter un timbre
	 * @param array $champs tableau des champs du timbre 
	 * @return int|string clé primaire de la ligne ajoutée, message d'erreur sinon
	 */ 
	public function ajouterTimbre($champs) {
		$this->sql = '
			INSERT INTO `Timbre` SET
			timbre_nom      	   = :timbre_nom,
			timbre_description     = :timbre_description,
			timbre_format 	 	   = :timbre_format,
			timbre_annee_emission  = :timbre_annee_emission,
			timbre_couleur		   = :timbre_couleur,
			timbre_tirage 		   = :timbre_tirage,
			timbre_certifie 	   = :timbre_certifie,
			timbre_type 		   = :timbre_type,
			timbre_condition 	   = :timbre_condition, 
			timbre_pays 		   = :timbre_pays,
			timbre_utilisateur 	   = :timbre_utilisateur;';
		return $this->CUDLigne($champs);
	}

	/**
	 * Modifier un timbre
	 * @param array $champs tableau des champs du timbre 
	 * @return int|string clé primaire de la ligne modifiée, message d'erreur sinon
	 */ 
	public function modifierTimbre($champs) {
		$this->sql = '
			UPDATE `Timbre` SET
			timbre_nom      	   = :timbre_nom,
			timbre_description     = :timbre_description,
			timbre_format 	 	   = :timbre_format,
			timbre_annee_emission  = :timbre_annee_emission,
			timbre_couleur		   = :timbre_couleur,
			timbre_tirage 		   = :timbre_tirage,
			timbre_certifie 	   = :timbre_certifie,
			timbre_type 		   = :timbre_type,
			timbre_condition 	   = :timbre_condition, 
			timbre_pays 		   = :timbre_pays,
			timbre_utilisateur 	   = :timbre_utilisateur
			WHERE timbre_id = :timbre_id;';
		return $this->CUDLigne($champs);
	}

	/**
	 * Supprimer un timbre
	 * @param int $timbre_id clé primaire
	 * @return boolean|string true si suppression effectuée, message d'erreur sinon
	 */ 
	public function supprimerTimbre($timbre_id) {
	$this->sql = '
		DELETE FROM `Timbre` WHERE timbre_id = :timbre_id;';
	return $this->CUDLigne(['timbre_id' => $timbre_id]);
	}

	/**
	 * Récupération d'un timbre
	 * @param int $timbre_id clé primaire
	 * @return object Timbre
	 */ 
	public function getTimbre($timbre_id) {
		$this->sql = '
			SELECT * FROM `Timbre` WHERE timbre_id = :timbre_id;';
		return $this->getLignes(['timbre_id' => $timbre_id], RequetesPDO::UNE_SEULE_LIGNE);;
	}

	/**
	 * Récupérer l'utilisateur ayant publié le timbre
	 * @param int $timbre_id, clé du timbre  
	 * @return int Utilisateur
	 */ 
	public function getUtilisateurTimbre($timbre_id) {
		$this->sql = '
			SELECT timbre_utilisateur 
			FROM `Timbre` 
			WHERE Timbre.timbre_id = :timbre_id;';
		return $this->getLignes(['timbre_id' => $timbre_id], RequetesPDO::UNE_SEULE_LIGNE);
	}

	/**
	 * Récupération des types de timbre
	 * @return array tableau d'objets Type
	 */ 
	public function getTimbreTypes() {
		$this->sql = "
			SELECT * FROM `TimbreType`;";
		return $this->getLignes();
	}

	/**
	 * Récupération des conditions de timbre
	 * @return array tableau d'objets Condition
	 */ 
	public function getTimbreConditions() {
		$this->sql = "
			SELECT * FROM `TimbreCondition`;";
		return $this->getLignes();
	}

	/**
	 * Récupération des pays d'origine
	 * @return array tableau d'objets Condition
	 */ 
	public function getTimbrePays() {
		$this->sql = "
			SELECT * FROM `TimbrePaysOrigine`;";
		return $this->getLignes();
	}


	/* GESTION DES ENCHÈRES 
	======================== */
	/**
	 * Ajouter un enchère
	 * @param array $champs tableau des champs de l'enchère 
	 * @return int|string clé primaire de la ligne ajoutée, message d'erreur sinon
	 */ 
	public function ajouterEnchere($champs) {
		$this->sql = '
			INSERT INTO `Enchere` SET
			timbre_id   		    = :timbre_id,
			enchere_date_debut      = :enchere_date_debut,
			enchere_date_fin        = :enchere_date_fin,
			enchere_prix_plancher   = :enchere_prix_plancher,
			enchere_archive         = :enchere_archive,
			enchere_id              = :enchere_id;';	
		return $this->CUDLigne($champs);
	}

	/**
	 * Modifier une enchère
	 * @param array $champs tableau des champs de l'enchère
	 * @return boolean|string true si modification effectuée, message d'erreur sinon
	 */ 
	public function modifierEnchere($champs) {
		$this->sql = '
			UPDATE `Enchere` SET
			enchere_date_debut      = :enchere_date_debut,
			enchere_date_fin        = :enchere_date_fin,
			enchere_prix_plancher   = :enchere_prix_plancher,
			enchere_archive         = :enchere_archive,
			timbre_id   		    = :timbre_id
			WHERE enchere_id = :enchere_id;';
		return $this->CUDLigne($champs);
	}

	/**
	 * Supprimer une enchère
	 * @param int $enchere_id clé primaire
	 * @return boolean|string true si suppression effectuée, message d'erreur sinon
	 */ 
	public function supprimerEnchere($enchere_id) {
		$this->sql = '
			DELETE FROM `Enchere` WHERE enchere_id = :enchere_id;';
		return $this->CUDLigne(['enchere_id' => $enchere_id]);
	}

	/**
	 * Récupération d'une enchère
	 * @param int $enchere_id clé primaire
	 * @return object Enchere
	 */ 
	public function getEnchere($enchere_id) {
		$this->sql = '
			SELECT * FROM `Enchere`
			JOIN `Timbre`
			ON Enchere.timbre_id = Timbre.timbre_id
			JOIN `TimbreCondition`
			ON Timbre.timbre_condition = TimbreCondition.condition_id
			JOIN `TimbreType`
			ON Timbre.timbre_type = TimbreType.type_id
			JOIN `TimbrePaysOrigine`
			ON Timbre.timbre_pays = TimbrePaysOrigine.pays_id
			JOIN `Utilisateur`
			ON Timbre.timbre_utilisateur = Utilisateur.utilisateur_id
			WHERE enchere_id = :enchere_id;';
		return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
	}
	
	/**
	 * Récupérer l'id du membre ayant publié l'enchère
	 * @param int $enchere_id, clé de l'enchère 
	 * @return int timbre_utilisateur
	 */ 
	public function getUtilisateurEnchere($enchere_id) {
		$this->sql = '
			SELECT timbre_utilisateur 
			FROM `Timbre` 
			JOIN `Enchere` 
			ON Enchere.timbre_id = Timbre.timbre_id
			WHERE Enchere.enchere_id = :enchere_id;';
		return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
	}
	
	/**
	 * Lister les enchères 
	 * @param int $enchere_archive, 1 si l'enchère est archivé | 0 si elle est en cours
	 * @param int $order, paramètre du tri
	 * @param int $sens, ordre du tri ASC|DESC
	 * @return array Encheres
	 */ 
	public function listerEncheres($enchere_archive, $order, $sens) {
		$this->sql = '
			SELECT * FROM `Enchere`
			JOIN `Timbre`
			ON Enchere.timbre_id = Timbre.timbre_id
			WHERE Enchere.enchere_archive = :enchere_archive
			ORDER BY '.$order.' '.$sens.';';
		return $this->getLignes(['enchere_archive' => $enchere_archive]);
	}
	
	/**
	 * Lister les enchères par offres
	 * @param int $enchere_archive, 1 si l'enchère est archivé | 0 si elle est en cours
	 * @param int $order, null
	 * @param int $sens, ordre du tri ASC|DESC
	 * @return array Encheres
	 */ 
	public function listerEncheresParOffre($enchere_archive, $order, $sens) {
		$this->sql = '
			SELECT Offre.enchere_id, MAX(offre_mise) AS offreMax, Enchere.*, Timbre.*
			FROM `Offre`
			JOIN `Enchere`
			ON Offre.enchere_id = Enchere.enchere_id
			JOIN `Timbre`
			ON Timbre.timbre_id = Enchere.timbre_id
			GROUP BY Offre.enchere_id
			ORDER BY offreMax '.$sens.';';
		return $this->getLignes(['enchere_archive' => $enchere_archive]);
	}

	/**
	 * Lister les enchères par recherche avancée 
	 * @param array $champs tableau des enchères
	 * @return array Encheres
	 */ 
	public function listerEncheresAvancee($champs) {
		$this->sql = '
			SELECT * FROM `Enchere` 
			JOIN `Timbre`
			ON Enchere.timbre_id = Timbre.timbre_id
			WHERE Timbre.timbre_condition = :condition
			AND Timbre.timbre_pays = :pays_origine
			AND Timbre.timbre_certifie = :certifie
			AND Timbre.timbre_type = :type
			AND Timbre.timbre_annee_emission >= :annee_emission_min 
			AND Timbre.timbre_annee_emission <= :annee_emission_max;';
		return $this->getLignes($champs);
	}

	/**
	 * Lister les enchères liées à un timbre
	 * @param int $timbre_id clé primaire
	 * @return array Enchere
	 */ 
	public function listerEncheresTimbre($timbre_id) {
		$this->sql = '
			SELECT Enchere.enchere_id FROM `Enchere`
			JOIN `Timbre`
			ON Enchere.timbre_id = Timbre.timbre_id
            WHERE Timbre.timbre_id = :timbre_id;';
		return $this->getLignes(['timbre_id' => $timbre_id]);		
	}

	/**
	 * Archiver une enchère
	 * @param array $champs tableau des champs de l'enchère
	 * @return boolean|string true si modification effectuée, message d'erreur sinon
	 */ 
	public function archiverEnchere($champs) {
		$this->sql = '
			UPDATE `Enchere` SET enchere_archive = 1 WHERE enchere_id = :enchere_id;';
		return $this->CUDLigne($champs);
	}

	/**
	 * Calculer le nombre d'enchère terminée par utilisateur
	 * @return array
	 */ 
	public function compterArchiverEnchere() {
		$this->sql = '
			SELECT COUNT(*) AS NombreEnchereArchive, Timbre.timbre_utilisateur
			FROM `Enchere`
			JOIN `Timbre`
			ON Timbre.timbre_id = Enchere.timbre_id
			WHERE enchere_archive = 1
			GROUP BY Timbre.timbre_utilisateur;';
		return $this->getLignes();
	}

	
	/* GESTION DES MISES 
	======================== */
	/**
	 * Ajouter une mise
	 * @param array $champs tableau des champs de l'enchère 
	 * @return int|string clé primaire de la ligne ajoutée, message d'erreur sinon
	 */ 
	public function ajouterMise($champs) {
		$this->sql = '
			INSERT INTO `Offre` SET
			enchere_id       = :enchere_id,
			offre_mise       = :offre_mise,
			offre_date_mise  = now(),
			utilisateur_id   = :utilisateur_id;';	
		return $this->CUDLigne($champs);
	}

	/**
	 * Supprimer les mises liées à une enchère
	 * @param int $enchere_id clé primaire
	 * @return boolean|string true si suppression effectuée, message d'erreur sinon
	 */ 
	public function supprimerMisesEnchere($enchere_id) {
		$this->sql = '
			DELETE FROM `Offre` WHERE enchere_id = :enchere_id;';
		return $this->CUDLigne(['enchere_id' => $enchere_id]);
	}

	/**
	 * Lister les mises d'une enchère
	 * @param int $enchere_id clé primaire
	 * @return array Enchere
	 */ 
	public function listerMises($enchere_id) {
		$this->sql = '
			SELECT * FROM `Offre`
			JOIN `Utilisateur`
			ON Offre.utilisateur_id = Utilisateur.utilisateur_id
			WHERE enchere_id = :enchere_id
			ORDER BY Offre.offre_date_mise DESC;';	
		return $this->getLignes(['enchere_id' => $enchere_id]);
	}

	/**
	 * Lister les mises d'une enchère
	 * @param int $enchere_id clé primaire
	 * @return string nombre de mises
	 */ 
	public function compterMises($enchere_id) {
		$this->sql = '
			SELECT COUNT(*) AS NombreOffres
			FROM `Offre`
			WHERE enchere_id = :enchere_id;';	
		return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
	}

	/**
	 * Compter le nombre de mises des enchères
	 * @return array Offres
	 */ 
	public function compterMisesEncheres() {
		$this->sql = '
			SELECT Offre.enchere_id AS EnchereIdOffre, COUNT(Offre.enchere_id) AS NombreOffres, Enchere.enchere_id AS EnchereId
			FROM `Offre` 
			RIGHT OUTER JOIN `Enchere` 
			ON Enchere.enchere_id = Offre.enchere_id 
			GROUP BY Enchere.enchere_id;';	
		return $this->getLignes();
	}

	/**
	 * Sélectionner la mise la plus haute d'une enchère
	 * @param int $enchere_id clé primaire
	 * @return int mise la plus haute
	 */ 
	public function afficherDerniereMise($enchere_id) {
		$this->sql = '
			SELECT Utilisateur.utilisateur_id, CONCAT(Utilisateur.utilisateur_prenom," ", Utilisateur.utilisateur_nom) AS DernierEnchérisseur, Offre.offre_mise AS MiseLaPlusHaute
			FROM `Utilisateur`
			JOIN `Offre` ON Offre.utilisateur_id = Utilisateur.utilisateur_id
			JOIN (
				SELECT Offre.enchere_id, MAX(offre_mise) AS offreMax FROM `Offre` GROUP BY Offre.enchere_id
				) offreMaxParEnchere
			ON Offre.enchere_id = offreMaxParEnchere.enchere_id
			WHERE Offre.offre_mise = offreMaxParEnchere.offreMax 
			AND Offre.enchere_id = :enchere_id;';	
		return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
	}

	/**
	 * Sélectionner les mises les plus haute de chaque enchère
	 * @return int mises les plus hautes
	 */ 
	public function afficherDernieresMises() {
		$this->sql = '
			SELECT CONCAT(Utilisateur.utilisateur_prenom," ", Utilisateur.utilisateur_nom) AS DernierEnchérisseur, 
			Offre.offre_mise AS MiseLaPlusHaute, Offre.enchere_id AS IdEnchereOffre
			FROM `Utilisateur`
			JOIN `Offre` ON Offre.utilisateur_id = Utilisateur.utilisateur_id
			JOIN (
				SELECT Offre.enchere_id, MAX(offre_mise) AS offreMax FROM `Offre` GROUP BY Offre.enchere_id
				) offreMaxParEnchere
			ON Offre.enchere_id = offreMaxParEnchere.enchere_id
			WHERE Offre.offre_mise = offreMaxParEnchere.offreMax;';	
		return $this->getLignes();
	}
	
		
	/* GESTION DES IMAGES 
	======================== */
	/**
	 * Ajouter une image
	 * @param array $champs tableau des champs de l'enchère 
	 * @return int|string clé primaire de la ligne ajoutée, message d'erreur sinon
	 */ 
	public function ajouterImage($champs) {
		$this->sql = '
			INSERT INTO `Images` SET
			image_id 			= :image_id,
			image_url			= :image_url,
			image_titre 		= :image_titre,
			image_principale 	= :image_principale,
			timbre_id 			= :timbre_id;';	
		return $this->CUDLigne($champs);
	}

	/**
	 * Supprimer une image
	 * @param int $image_id clé primaire
	 * @return boolean|string true si suppression effectuée, message d'erreur sinon
	 */ 
	public function supprimerImage($image_id) {
		$this->sql = '
			DELETE FROM `Images` WHERE image_id = :image_id;';
		return $this->CUDLigne(['image_id' => $image_id]);
	}
	
	/**
	 * Supprimer toutes les images d'un timbre 
	 * @param array $champs tableau des enchères
	 * @return int|string clé primaire de la ligne ajoutée, message d'erreur sinon
	 */ 
	public function supprimerImagesTimbre($timbre_id) {
		$this->sql = '
			DELETE FROM `Images` WHERE timbre_id = :timbre_id;';
		return $this->getLignes(['timbre_id' => $timbre_id]);
	}

	/**
	 * Récupérer les images d'un timbre 
	 * @param int $timbre_id clé primaire
	 * @return array Images
	 */ 
	public function listerImagesTimbre($timbre_id) {
		$this->sql = '
			SELECT * FROM `Images` WHERE timbre_id = :timbre_id;';
		return $this->getLignes(['timbre_id' => $timbre_id]);
	}

	/**
	 * Récupérer les images d'un utilisateur 
	 * @param int $utilisateur_id clé primaire
	 * @return array Images
	 */ 
	public function listerImagesUtilisateur($utilisateur_id) {
		$this->sql = '
			SELECT * FROM `Images`
			JOIN `Timbre` 
			ON Images.timbre_id = Timbre.timbre_id
			WHERE Timbre.timbre_utilisateur = :utilisateur_id;';
		return $this->getLignes(['utilisateur_id' => $utilisateur_id]);
	}

	/**
	 * Récupérer les images d'une Enchere 
	 * @return array Images
	 */ 
	public function listerImagesEnchere() {
		$this->sql = '
			SELECT * FROM `Images`
			JOIN `Timbre`
			ON Images.timbre_id = Timbre.timbre_id
			LEFT OUTER JOIN Enchere
			ON Enchere.timbre_id = Timbre.timbre_id;';
		return $this->getLignes();
	}

	/**
	 * Récupérer l'url d'une image
	 * @param int $image_id clé primaire
	 * @return string image_url
	 */ 
	public function getImageUrl($image_id) {
		$this->sql = '
			SELECT image_url FROM `Images` WHERE image_id = :image_id;';	
		return $this->getLignes(['image_id' => $image_id], RequetesPDO::UNE_SEULE_LIGNE);
	}

	/**
	 * Ajouter l'image principale
	 * @param array $champs tableau des champs de l'image
	 * @return boolean|string true si modification effectuée, message d'erreur sinon
	 */ 
	public function updateImagePrincipale($champs) {
		$this->sql = '
			UPDATE Images SET image_principale = 1 WHERE image_id = :image_id;';
		return $this->CUDLigne($champs);
	}

	/**
	 * Sélectionner l'image principale par défaut du timbre
	 * @param int $timbre_id clé primaire
	 * @return object Image
	 */ 
	public function getImageParDefaut($timbre_id) {
		$this->sql = '
			SELECT image_id
			FROM `Images` 
			WHERE image_titre = "image principale par défaut" 
			AND timbre_id = :timbre_id;';
		return $this->getLignes(['timbre_id' => $timbre_id], RequetesPDO::UNE_SEULE_LIGNE);
	}
	
	/**
	 * Retirer l'image principale
	 * @param array $champs tableau des champs de l'image
	 * @return boolean|string true si modification effectuée, message d'erreur sinon
	 */ 
	public function unsetImagePrincipale($champs) {
		$this->sql = '
			UPDATE `Images` SET image_principale = 0 WHERE image_id = :image_id;';
		return $this->CUDLigne($champs);
	}

	/**
	 * Trouver l'image principale d'un timbre
	 * @param int $timbre_id clé primaire
	 * @return int image_id
	 */ 
	public function findImagePrincipale($timbre_id) {
		$this->sql = '
			SELECT image_id FROM `Images`
			WHERE image_principale = 1 AND timbre_id = :timbre_id;';
		return $this->getLignes(['timbre_id' => $timbre_id], RequetesPDO::UNE_SEULE_LIGNE);
	}


	/* GESTION DES FAVORIS 
	======================== */
	/**
	 * Ajouter un favoris
	 * @param array $champs tableau des champs de l'enchère 
	 * @return int|string clé primaire de la ligne ajoutée, message d'erreur sinon
	 */ 
	public function ajouterFavoris($champs) {
		$this->sql = '
			INSERT INTO `Favoris` SET
			enchere_id     = :enchere_id,
			utilisateur_id = :utilisateur_id;';	
		return $this->CUDLigne($champs);
	}

	/**
	 * Supprimer tous les favoris
	 * @param int utilisateur_id, $enchere_id clés primaires
	 * @return boolean|string true si suppression effectuée, message d'erreur sinon
	 */ 
	public function supprimerFavoris($utilisateur_id, $enchere_id) {
		$this->sql = '
			DELETE FROM `Favoris`
			WHERE Favoris.utilisateur_id = :utilisateur_id AND Favoris.enchere_id = :enchere_id;';
		return $this->CUDLigne(['utilisateur_id' => $utilisateur_id, 'enchere_id' => $enchere_id]);
	}

	/**
	 * Retirer une enchère favorite
	 * @param int $enchere_id clé primaire
	 * @return boolean|string true si suppression effectuée, message d'erreur sinon
	 */ 
	public function supprimerFavorisEnchere($enchere_id) {
		$this->sql = '
			DELETE FROM `Favoris`
			WHERE Favoris.enchere_id = :enchere_id;';
		return $this->CUDLigne(['enchere_id' => $enchere_id]);
	}

	/**
	 * Trouver les encheres favorites d'un utilisateur
	 * @param int $utilisateur_id, enchere_id clé primaire
	 * @return array favoris
	 */ 
	public function getFavorisUtilisateur($utilisateur_id, $enchere_id) {
		$this->sql = '
			SELECT * FROM `Favoris` 
			WHERE enchere_id = :enchere_id 
			AND utilisateur_id = :utilisateur_id;';
		return $this->CUDLigne(['utilisateur_id' => $utilisateur_id, 'enchere_id' => $enchere_id]);
	}


	/* GESTION DES COMMENTAIRES 
	======================== */
	/**
	 * Ajouter un commentaire
	 * @param array $champs tableau des champs de l'enchère 
	 * @return int|string clé primaire de la ligne ajoutée, message d'erreur sinon
	 */ 
	public function ajouterCommentaire($champs) {
		$this->sql = '
			INSERT INTO `Commentaire` SET
			commentaire = :commentaire,
			commentaire_date_publication = now(),
			enchere_id     = :enchere_id,
			utilisateur_id = :utilisateur_id;';	
		return $this->CUDLigne($champs);
	}

	/**
	 * Retirer un commentaire
	 * @param int $commentaire_id clé primaire
	 * @return boolean|string true si suppression effectuée, message d'erreur sinon
	 */ 
	public function supprimerCommentaire($commentaire_id) {
		$this->sql = '
			DELETE FROM `Commentaire`
			WHERE Commentaire.commentaire_id = :commentaire_id;';
		return $this->CUDLigne(['commentaire_id' => $commentaire_id]);
	}
	
	/**
	 * Lister tous les commentaires d'une enchère archivée
	  * @param int $enchere_id clé primaire
	 * @return array tableau d'objets
	 */ 
	public function listerCommentaireEnchere($enchere_id) {
		$this->sql = '
			SELECT Commentaire.*, CONCAT(Utilisateur.utilisateur_prenom," ", Utilisateur.utilisateur_nom) AS auteurCommentaire 
			FROM `Commentaire` 
			JOIN `Utilisateur`
			ON Utilisateur.utilisateur_id = Commentaire.utilisateur_id
			WHERE enchere_id = :enchere_id
			ORDER BY Commentaire.commentaire_id DESC;';
		return $this->getLignes(['enchere_id' => $enchere_id]);
	}
}