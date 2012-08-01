<?php
   if (!defined("_ECRIRE_INC_VERSION")) return;
   include_spip('inc/meta');
   include_spip('base/create');
   include_spip('base/abstract_sql');

   function vacarme_numeros_upgrade($nom_meta_version_base, $version_cible) {
      $version_actuelle = '0.0';
      if ((!isset($GLOBALS['meta'][$nom_meta_version_base]))|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)) {
         if (version_compare($version_actuelle,'0.0','=')){
            // Création des tables
            creer_base();
         }
         ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
      }
   }

   // Désinstallation
   function vacarme_numeros_vider_tables($nom_meta_version_base) {
      sql_drop_table('spip_vacarme_numeros');
      sql_drop_table('spip_vacarme_numeros_details');
      sql_drop_table('spip_vacarme_numeros_sommaires');
      effacer_meta($nom_meta_version_base);
   }

?>