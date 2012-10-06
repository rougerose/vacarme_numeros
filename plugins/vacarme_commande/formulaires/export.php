<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_export_charger_dist(){
   $valeurs = array();
   $valeurs['commande'] = _request('commande');
	var_dump($valeurs);
   return $valeurs;

}

function formulaires_export_verifier_dist(){
   $erreurs = array();
   $commandes = _request('commande');
   if (!is_array($commandes)) {$erreurs['message_erreur'] = _T('pas de sélection');}
	return $erreurs;
}

function formulaires_export_traiter_dist(){
   $retours = array();

   $commande = _request('commande');


   return $retours;
}

?>
