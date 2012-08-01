<?php
   if (!defined("_ECRIRE_INC_VERSION")) return;

   function vacarme_numeros_import_test() {

   }

   function vacarme_numeros_copie_locale($id_document) {
      if (intval($id_document)) {
         // charger fonction copier_local du plugin medias
         $copier_local = charger_fonction('copier_local','action');
         $traiter = $copier_local($id_document);
         return $traiter;
      }
   }



?>
