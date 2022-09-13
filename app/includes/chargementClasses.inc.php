<?php 

function chargerClasse($classe) {
  if (!stristr($classe, "mocks")) { 
    $dossiers = array('modeles/sql/', 'modeles/entites/', 'vues/', 'controleurs/'); 
    foreach ($dossiers as $dossier) {
      if (file_exists('./app/'.$dossier.$classe.'.class.php')) {
        require_once('./app/'.$dossier.$classe.'.class.php');
      }
    }
  } else {
    $classe = str_replace('\\', '/', $classe);
    require_once($classe . '.class.php');
  }
}

spl_autoload_register('chargerClasse');