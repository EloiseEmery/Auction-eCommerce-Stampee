<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Utilisateur de l'application admin
 */
class FrontendCommentaire extends Frontend {

	private $methodes = [
		'c_ajouter'     => 'commentaireAjouter',
		'c_supprimer'   => 'commentaireSupprimer',
		'c_lister'      => 'commentaireLister'
	];

	/**
	 * Gérer l'interface d'administration des utilisateurs 
	 */  
	public function gerer($entite = "utilisateur") {
		
		$this->entite  = $entite;
		$this->action  = $_GET['action']  ?? '';
		$this->enchere_id = $_GET['enchere_id'] ?? null;
		$this->commentaire_id = $_GET['commentaire_id'] ?? null;
		$this->messageRetourAction = $_GET['messageRetourAction'] ?? '';

		if (isset($this->methodes[$this->action])) {
			$methode = $this->methodes[$this->action];
			$this->$methode();
		} else {
			throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
		}
	}

	/**
	 * Lister les commentaires tout les commentaires d'une enchère
	 * 
	 */  
	public function commentaireLister() {
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		} else {
			$oUtilisateurConnecter = null;
		}

		// récupération des données à afficher
		$enchere_id = $this->enchere_id;
		$enchere = self::$oRequetesSQL->getEnchere($enchere_id);
		$commentaires = self::$oRequetesSQL->listerCommentaireEnchere($enchere_id);

		// vue d'affichage de tous les commentaires
		(new Vue)->generer("vCommentaireLister",
				array(
					'titre'                 => "Tous les commentaire",
					'oUtilisateurConnecter' => $oUtilisateurConnecter,
					'enchere'               => $enchere,
					'commentaires'          => $commentaires,
					'messageRetourAction'   => $this->messageRetourAction
				),
				"gabarit-frontend");

	}
		
	/**
	 * Ajouter un commentaire
	 * 
	 */  
	public function commentaireAjouter() {
		if (isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		} else {
			$oUtilisateurConnecter = null;
		}

		if (count($_POST) !== 0) {
			$_POST['enchere_id'] = $this->enchere_id;
			$_POST['utilisateur_id'] = $oUtilisateurConnecter->utilisateur_id;
			$enchere_id = $this->enchere_id;

			$oCommentaire = new Commentaire($_POST);
			$erreurs = $oCommentaire->erreurs;
				
			if (count($erreurs) === 0) {
				// insertion du commentaire dans la DB
				$retour = self::$oRequetesSQL->ajouterCommentaire([
					'commentaire'        => $oCommentaire->commentaire,
					'utilisateur_id'     => $oCommentaire->utilisateur_id,
					'enchere_id'         => $oCommentaire->enchere_id
				]);

				// rediriger vers la fiche de l'enchère commenter
				if (preg_match('/^[1-9]\d*$/', $retour)) {
					header('Location: .?entite=enchere&action=e_afficher&enchere_id='.$enchere_id.'&messageRetourAction=Ajout du commentaire effectué.#recent-comment');
					exit;
				} else {
					header('Location: .?entite=enchere&action=e_afficher&enchere_id='.$enchere_id.'&messageRetourAction=L\'ajout du commentaire a échoué.');
					exit;
				}   
			}
		}

		// rediriger vers la fiche de l'enchère commenter
		$Enchere = new FrontendEnchere();
		$Enchere->enchereAfficher($erreurs, $this->enchere_id);
		exit;
	}

	/**
	 * Supprimer un commentaire
	 * 
	 */  
	public function commentaireSupprimer() {
		if(isset($_SESSION['oUtilisateurConnecter'])) {
			$oUtilisateurConnecter = $_SESSION['oUtilisateurConnecter'];
		}
		else {
			$oUtilisateurConnecter = null;
		}
		
		$enchere_id = $this->enchere_id;
		$commentaire_id =  $this->commentaire_id;

		// suppression du commentaire
		$retour = self::$oRequetesSQL->supprimerCommentaire($commentaire_id);
		
		// redirection
		if (preg_match('/^[1-9]\d*$/', $retour)) {
			header('Location: .?entite=commentaire&action=c_lister&enchere_id='.$enchere_id.'&messageRetourAction=Suppression effectuée.');
			exit;
		} else {
			header('Location: .?entite=commentaire&action=c_lister&enchere_id='.$enchere_id.'&messageRetourAction=Suppression non effectuée.');
			exit;
		}
	}
}