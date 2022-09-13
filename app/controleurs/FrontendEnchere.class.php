<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Utilisateur de l'application admin
 */
class FrontendEnchere extends Frontend {

	private $methodes = [
		'e_ajouter'     => 'enchereAjouter',
		'e_modifier'    => 'enchereModifier',
		'e_supprimer'   => 'enchereSupprimer',
		'e_lister'      => 'enchereLister',
		'e_afficher'    => 'enchereAfficher',
		'e_historique'  => 'enchereHistorique',
		'e_vendeur'     => 'enchereVendeur',
		'e_miser'       => 'enchereMiser',
		'e_archive'     => 'enchereArchive'
	];

	/**
	 * Gérer l'interface d'administration des utilisateurs 
	 */  
	public function gerer($entite = "utilisateur") {
		
		$this->entite  = $entite;
		$this->action  = $_GET['action']  ?? '';
		$this->utilisateur_id = $_GET['utilisateur_id'] ?? null;
		$this->enchere_timbre_id = $_GET['enchere_timbre_id'] ?? null;
		$this->enchere_archive = $_GET['enchere_archive'] ?? 0;
		$this->enchere_id = $_GET['enchere_id'] ?? null;
		$this->messageRetourAction = $_GET['messageRetourAction'] ?? '';

		if (isset($this->methodes[$this->action])) {
			$methode = $this->methodes[$this->action];
			$this->$methode();
		} else {
			throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
		}
	}

	/**
	 * Ajouter une enchère
	 * 
	 */  
	public function enchereAjouter() {
		// vérifier que l'utilisateur à la permission d'accès à cette page
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];

			if (isset($_GET['enchere_timbre_id'])) {
				$idMembreConnecte = $oUtilisateurConnecter->utilisateur_id;
				$retour = self::$oRequetesSQL->getUtilisateurTimbre($this->enchere_timbre_id);
				$idMembreEnchere = $retour['timbre_utilisateur'];
					
				if ($idMembreConnecte != $idMembreEnchere) {
					Frontend::accueil();
					die();
				}
			}
		} else {
			Frontend::accueil();
			die();
		}

		$enchere  = [];
		$erreurs = [];
		$timbres = [];
			
		// récupération des données à afficher
		$enchere_timbre_id = $this->enchere_timbre_id;
		$this->oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		$utilisateur_id = $this->oUtilisateurConnecter->utilisateur_id;
		$timbres = self::$oRequetesSQL->listerTimbresUtilisateur($utilisateur_id);
			
		if (count($_POST) !== 0) {
			// récupération des données reçues
			$enchere = $_POST;
			// création d'un objet Enchere pour contrôler la saisie reçue
			$oEnchere = new Enchere($enchere);
			$erreurs = $oEnchere->erreurs;

			if (count($erreurs) === 0) {
				// insertion de l'enchere dans la DB
				$retour = self::$oRequetesSQL->ajouterEnchere([
					'enchere_id'              => $oEnchere->enchere_id,
					'enchere_date_debut'      => $oEnchere->enchere_date_debut,
					'enchere_date_fin'        => $oEnchere->enchere_date_fin,
					'enchere_prix_plancher'   => $oEnchere->enchere_prix_plancher,
					'enchere_archive'         => $oEnchere->enchere_archive,
					'timbre_id'               => $oEnchere->timbre_id
				]);
					
				if (preg_match('/^[1-9]\d*$/', $retour)) {
					header('Location: .?entite=utilisateur&action=u_encheres&messageRetourAction=Publication de l\'enchère effectuée.');
					exit;
				} else {
					header('Location: .?entite=utilisateur&action=u_timbres&messageRetourAction=Publication de l\'enchère non effectuée.');
					exit;
				}   
			}
		}
			
		// générer la vue initiale du formulaire d'ajout
		(new Vue)->generer("vEnchereAjouter",
				array(
					'titre'                 => "Publier une enchère",
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'timbres'               => $timbres,
					'enchere'               => $enchere,
					'enchereTimbreId'       => $enchere_timbre_id,
					'erreurs'               => $erreurs
				),
				"gabarit-frontend");
	}

	/**
	 * Modifier une enchère
	 * 
	 */  
	public function enchereModifier() {
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];

			// vérifier que l'utilisateur à la permission d'accès à cette page
			$idMembreConnecte = $oUtilisateurConnecter->utilisateur_id;
			$retour = self::$oRequetesSQL->getUtilisateurEnchere($this->enchere_id);
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
		$enchere  = [];
		$erreurs = [];
		$timbre = [];
			
		if (count($_POST) !== 0) {
			$enchere = $_POST;
			$oEnchere = new Enchere($enchere);
			$erreurs = $oEnchere->erreurs;
			
			if (count($erreurs) === 0) {
				// modification de l'enchere dans la DB
				$retour = self::$oRequetesSQL->modifierEnchere([
					'enchere_id'              => $oEnchere->enchere_id,
					'enchere_date_debut'      => $oEnchere->enchere_date_debut,
					'enchere_date_fin'        => $oEnchere->enchere_date_fin,
					'enchere_prix_plancher'   => $oEnchere->enchere_prix_plancher,
					'enchere_archive'         => $oEnchere->enchere_archive,
					'timbre_id'               => $oEnchere->timbre_id
				]);
					
				if (preg_match('/^[1-9]\d*$/', $retour)) {
					header('Location: .?entite=utilisateur&action=u_encheres&messageRetourAction=Modification effectuée.');
					exit;
				} else {
					header('Location: .?entite=utilisateur&action=u_encheres&messageRetourAction=Modification non effectué.');
					exit;
				}
			}
		} else {
			$enchere = self::$oRequetesSQL->getEnchere($this->enchere_id);
			$erreurs = [];
		}
			
		(new Vue)->generer("vEnchereModifier",
				array(
					'titre'                 => "Modifier l'enchère",
					'enchere'               => $enchere,
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'enchere'               => $enchere,
					'erreurs'               => $erreurs
				),
				"gabarit-frontend");
	}

	/**
	 * Supprimer une enchère
	 * 
	 */  
	public function enchereSupprimer() {
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];

			// vérifier que l'utilisateur à la permission d'accès à cette page
			$idMembreConnecte = $oUtilisateurConnecter->utilisateur_id;
			$retour = self::$oRequetesSQL->getUtilisateurEnchere($this->enchere_id);
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

		// suppresion des favoris et des offres liés à cette enchère
		self::$oRequetesSQL->supprimerFavorisEnchere($this->enchere_id);
		self::$oRequetesSQL->supprimerMisesEnchere($this->enchere_id);
		// suppression de l'enchère
		$retour = self::$oRequetesSQL->supprimerEnchere($this->enchere_id);
			
		// redirection vers la liste des Enchères
		if (preg_match('/^[1-9]\d*$/', $retour)) {
			header('Location: .?entite=utilisateur&action=u_encheres&messageRetourAction=Suppression réussie.');
			exit;
		} else {
			header('Location: .?entite=utilisateur&action=u_encheres&messageRetourAction=Suppression non effectuée.');
			exit;
		}   
	}

	/**
	 * Afficher le catalogue d'enchère
	 * 
	 */  
	public function enchereLister() {
		if(isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		}
		else {
			$oUtilisateurConnecter = null;
		}

		// récupération des données à afficher
		$encheres = [];
		$enchere_archive = intval( $this->enchere_archive );
		$timbrePaysOrigine = self::$oRequetesSQL->getTimbrePays();
		$nb_offres = self::$oRequetesSQL->compterMisesEncheres();
		$dernieresOffres = self::$oRequetesSQL->afficherDernieresMises();
		$images = self::$oRequetesSQL->listerImagesEnchere();
			
		// gestion recherche avancée
		$order = 'Enchere.enchere_id';
		$sens = 'DESC';
		$requete = 'listerEncheres';
		$order_tri = null;
		$pays_tri = null;

		if(isset($_POST['order_enchere'])) {
			$enchere_order = $_POST['order_enchere'];

			switch ($enchere_order) {
				case 'termine_bientot':
					$order_tri = 'termine_bientot';
					$order = 'Enchere.enchere_date_fin';
					$sens = 'ASC';
					$requete = 'listerEncheres';
					break;
				case 'prix_decroissant':
					$order_tri = 'prix_decroissant';
					$order = null;
					$sens = 'DESC';
					$requete = 'listerEncheresParOffre';
					break;
				case 'prix_croissant':
					$order_tri = 'prix_croissant';
					$order = null;
					$sens = 'ASC';
					$requete = 'listerEncheresParOffre';
					break;
				default:
					$order = 'Enchere.enchere_id';
					$sens = 'DESC';
					$requete = 'listerEncheres';
					break;
			}
			$encheres = self::$oRequetesSQL->$requete($enchere_archive, $order, $sens);

		} elseif(isset($_POST['advanced_search'])) {
			$pays_tri = $_POST['pays_origine'];

			$criteres_tri = [];
			if(isset($_POST['condition'])) {
				$criteres_tri['condition'] = $_POST['condition'];
			} else {
				$criteres_tri['condition'] = null;
			}


			if(isset($_POST['certifie'])) {
				$criteres_tri['certifie'] = $_POST['certifie'];
			} else {
				$criteres_tri['certifie'] = null;
			}

			
			if(isset($_POST['type'])) {
				$criteres_tri['type'] = $_POST['type'];
			} else {
				$criteres_tri['type'] = null;
			}

			$criteres_tri['pays_origine'] = $_POST['pays_origine'];         
			$criteres_tri['annee_emission_min'] = $_POST['annee_emission_min'];
			$criteres_tri['annee_emission_max'] = $_POST['annee_emission_max'];

			$encheres = self::$oRequetesSQL->listerEncheresAvancee($criteres_tri);
				
		} else {
			$encheres = self::$oRequetesSQL->$requete($enchere_archive, $order, $sens);
		}
			
		(new Vue)->generer("vEnchereLister",
				array(
					'titre' => "Parcourez nos enchères",
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'timbrePaysOrigine'     => $timbrePaysOrigine,
					'encheres'              => $encheres,
					'enchereArchive'        => $enchere_archive,
					'nbOffresEncheres'      => $nb_offres,
					'dernieresOffres'       => $dernieresOffres,
					'images'                => $images,
					'orderTri'              => $order_tri,
					'paysTri'               => $pays_tri
				),
				"gabarit-frontend");
	}

	/**
	 * Afficher les détails de l'enchère
	 * 
	 */  
	public function enchereAfficher($erreurs = [], $enchere_id_comment = "") {
		if(isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
			$utilisateur_id = $oUtilisateurConnecter->utilisateur_id;
		}
		else {
			$oUtilisateurConnecter = null;
			$utilisateur_id = null;
		}

		// récupération des données à afficher
		if($enchere_id_comment != "") {
			$enchere_id = $enchere_id_comment;
		} else {
			$enchere_id = $this->enchere_id;
		}
		
		$enchere = [];
		$enchere = self::$oRequetesSQL->getEnchere($enchere_id);
		$nb_offres = self::$oRequetesSQL->compterMises($enchere_id);
		$derniere_offre = self::$oRequetesSQL->afficherDerniereMise($enchere_id);
		$images = self::$oRequetesSQL->listerImagesEnchere();
		$favoris = self::$oRequetesSQL->getFavorisUtilisateur($utilisateur_id, $enchere_id);
		$commentaires = self::$oRequetesSQL->listerCommentaireEnchere($enchere_id);

		if (!$favoris) { 
			$favoris = null;
		}
				
		if ($derniere_offre) {
			$derniere_offre = $derniere_offre;
				
		} else {
			$derniere_offre = 0;
		}

		if (count($erreurs) != 0) {
			$erreurCommentaire = $erreurs['commentaire'];
		} else {
			$erreurCommentaire = "";
		}

		(new Vue)->generer("vEnchere",
				array(
					'titre'                 => "Détail",
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'enchere'               => $enchere,
					'nbOffres'              => $nb_offres,
					'derniereOffre'         => $derniere_offre,
					'images'                => $images,
					'favoris'               => $favoris,
					'erreurCommentaire'     => $erreurCommentaire,
					'commentaires'          => $commentaires
				),
				"gabarit-frontendEnchere");
					
	}

	/**
	 * Afficher l'historique de l'enchère
	 * 
	 */  
	public function enchereHistorique($erreurs = []) {
		if(isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		}
		else {
			$oUtilisateurConnecter = null;
		}

		$enchere = [];
		$offres = [];

		if (count($erreurs) != 0) {
			$erreurMise = $erreurs['offre_mise'];
		} else {
			$erreurMise = "";
		}

		// récupération des données à afficher
		$enchere_id = $this->enchere_id;
		$enchere = self::$oRequetesSQL->getEnchere($enchere_id);
		$offres = self::$oRequetesSQL->listerMises($enchere_id);
		$nb_offres = self::$oRequetesSQL->compterMises($enchere_id);
		$derniere_offre = self::$oRequetesSQL->afficherDerniereMise($enchere_id);
		$images = self::$oRequetesSQL->listerImagesEnchere();

		(new Vue)->generer("vEnchereHistorique",
				array(
					'titre'                 => "historique",
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'enchere'               => $enchere,
					'offres'                => $offres,
					'nbOffres'              => $nb_offres,
					'derniereOffre'         => $derniere_offre,
					'images'                => $images,
					'erreurMise'            => $erreurMise
					
				),
				"gabarit-frontendEnchere");
	}

	/**
	 * Afficher le vendeur de l'enchère
	 * 
	 */  
	public function enchereVendeur() {
		if(isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		}
		else {
			$oUtilisateurConnecter = null;
		}
			
		$enchere = [];

		// récupération des données à afficher
		$enchere_id = $this->enchere_id;
		$enchere = self::$oRequetesSQL->getEnchere($enchere_id);
		$nb_offres = self::$oRequetesSQL->compterMises($enchere_id);
		$derniereOffre = self::$oRequetesSQL->afficherDerniereMise($enchere_id);
		$images = self::$oRequetesSQL->listerImagesEnchere();
		$nbVente = self::$oRequetesSQL->compterArchiverEnchere();

		(new Vue)->generer("vEnchereVendeur",
				array(
					'titre'                 => "vendeur",
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'enchere'               => $enchere,
					'nbOffres'              => $nb_offres,
					'derniereOffre'         => $derniereOffre,
					'images'                => $images,
					'nbVentes'              => $nbVente
				),
				"gabarit-frontendEnchere");
	}

	/**
	 * Ajouter une mise
	 * 
	 */ 
	public function enchereMiser() {
		if(isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		}
		else {
			$oUtilisateurConnecter = null;
		}

		$enchere_id = $this->enchere_id;
		$utilisateur_id = $oUtilisateurConnecter->utilisateur_id;
		$derniere_mise =  self::$oRequetesSQL->afficherDerniereMise($enchere_id);

		// récupération de la dernière mise
		if ($derniere_mise) {
			$mise_la_plus_haute = $derniere_mise['MiseLaPlusHaute'];
			$mise_minimum = $mise_la_plus_haute + 1;
		} else {
			$mise_la_plus_haute = 0;
		}

		// gestion et ajout de la mise
		if (count($_POST) !== 0) {
			$offre_mise = $_POST['offre_mise'];
			$offre = ['enchere_id' => $enchere_id, 'utilisateur_id' => $utilisateur_id, 'offre_mise' => $offre_mise];
			$oMise = new Offre($offre);
			$erreurs = $oMise->erreurs;
			if (count($erreurs) != 0) {
				$this->enchereHistorique($erreurs);
			} else if ($mise_minimum > $offre_mise) {
				$erreurs['offre_mise'] = 'Votre mise est inférieur au minimum';
				$this->enchereHistorique($erreurs);
			}
			else {
				$retour = self::$oRequetesSQL->ajouterMise($offre);
						
				header('Location: .?entite=enchere&action=e_historique&enchere_id='.$enchere_id);
				exit;
			}
		}
	}

	/**
	 * Archiver une enchère
	 * 
	 */ 
	public function enchereArchive() {
		if(isset($_POST['enchere_id'])) {
			self::$oRequetesSQL->archiverEnchere($_POST);
		}
	}
}