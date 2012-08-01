<?php
   if (!defined("_ECRIRE_INC_VERSION")) return;

   function vacarme_commande_declarer_tables_principales($tables_principales) {
      $tables_principales['spip_contacts']['field']['organisation'] = "tinytext DEFAULT '' NOT NULL";
      $tables_principales['spip_contacts']['field']['service'] = "TINYTEXT NOT NULL DEFAULT ''";
      $tables_principales['spip_contacts']['field']['type_client'] = "TINYTEXT NOT NULL DEFAULT ''";

      $tables_principales['spip_contacts_abonnements']['field']['numero_debut'] = "BIGINT(21) NOT NULL";
      $tables_principales['spip_contacts_abonnements']['field']['numero_fin'] = "BIGINT(21) NOT NULL";

      $tables_principales['spip_abonnements']['field']['reference'] = "VARCHAR(255) NOT NULL DEFAULT ''";

      $tables_principales['spip_abonnements']['field']['cadeau'] = "INT(4) NOT NULL";

      $tables_principales['spip_paniers_liens']['field']['numero'] = "BIGINT(21) NOT NULL";

      $tables_principales['spip_commandes_details']['field']['numero'] = "BIGINT(21) NOT NULL";

      return $tables_principales;
   }


?>
