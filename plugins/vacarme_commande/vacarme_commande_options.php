<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

define('_RENOUVELLE_ALEA',3600); // utile de modifier l'aléa pour les sessions ?

// TVA des abonnements
define('_TVA_ABONNEMENT',0.07);

// remplacer la fonction prix_formater du plugin prix qui ne marche pas comme attendu
function prix_format($prix){
   // sur joyent : le point est converti en virgule,
   // mais le symbole euro n'est pas affiché (EUR uniquement)
   setlocale(LC_ALL,'fr_FR.UTF-8');
   return $prix;
}

?>