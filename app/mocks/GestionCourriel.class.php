<?php

namespace app\mocks;

/**
 * Classe GestionCourriel
 *
 */
class GestionCourriel {

  /**
   * Envoyer un courriel à l'utilisateur pour lui communiquer
   * son identifiant de connexion et son mot de passe
   * @param object $oUtilisateur utilisateur destinataire 
   */
  public function envoyerMdp(\Utilisateur $oUtilisateur) {
    $dateEnvoi     = date("Y-m-d H-i-s");
    $destinataire  = $oUtilisateur->utilisateur_courriel; 
    $message       = (new \Vue)->generer('cMdp',
                                         array(
                                           'titre'        => 'Information',
                                           'oUtilisateur' => $oUtilisateur
                                         ),
                                         'gabarit-admin-min', true);
    
    $nfile = fopen("app/mocks/courriels/$dateEnvoi-$destinataire.html", "w");
    fwrite($nfile, $message);
    fclose($nfile); 
    return true;
  }
}