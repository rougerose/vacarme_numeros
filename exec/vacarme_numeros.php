<?php
/*
*     page de test depuis l'espace privé
*/


// if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip("inc/presentation");

function exec_vacarme_numeros() {
   if (!autoriser('editer', 'article')) {
      include_spip('inc/minipres');
      echo minipres();
      exit;
   }

   include_spip("genie/vacarme_numeros_import");

   // page
   $commencer_page = charger_fonction('commencer_page', 'inc');
   echo $commencer_page(_T('rssarticle:activer_recopie_intro'), 'editer', 'editer');
   echo gros_titre(_T('rssarticle:activer_recopie_intro'),'', false);
   // colonne gauche
   echo debut_gauche('', true);
   echo debut_droite('', true);

   genie_vacarme_numeros_import_dist('manuel');

   //include_spip("vacarme_numeros_fonctions");
   //vacarme_numeros_import_test();

   echo '<div style="margin:2em 0;"><a href="?exec=vacarme_numeros" style="border:1px solid;padding:0.5em;background:#fff;">'._T('rssarticle:maj_recharge').'</a></div>';

   // pied
   echo fin_gauche() . fin_page();

}

?>
