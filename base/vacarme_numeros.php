<?php
   if (!defined("_ECRIRE_INC_VERSION")) return;

   function vacarme_numeros_declarer_tables_interfaces($interface){
      // Déclaration des alias de table
      $interface['table_des_tables']['vacarme_numeros'] = 'vacarme_numeros'; // table d'importation des numéros
      $interface['table_des_tables']['vacarme_numeros_details'] = 'vacarme_numeros_details'; // les détails principaux des numéros
      $interface['table_des_tables']['vacarme_numeros_sommaires'] = 'vacarme_numeros_sommaires'; // le sommaire de chaque numero

      // Champs date sur les tables
      //$interface['table_date']['produits'] = 'date';

      // Déclaration du titre
      //$interface['table_titre']['produits'] = 'titre, "" as lang';

      return $interface;
   }

   function vacarme_numeros_declarer_tables_principales($tables_principales) {
      $numeros         = array(
         "id_numero"   => "bigint(21) NOT NULL",
         "id_rubrique_distante" => "bigint(21) NOT NULL",
         "numero"      => "varchar(255) NOT NULL DEFAULT ''",
         "saison"      => "tinytext NOT NULL DEFAULT ''",
         "annee"       => "int(4) NOT NULL DEFAULT 0",
         "prix"        => "float NOT NULL default 0",
         "url"         => "varchar(255) NOT NULL DEFAULT ''",
         "logo"        => "varchar(255) NOT NULL DEFAULT ''",
         "sommaire"    => "TEXT NOT NULL DEFAULT ''",
         "statut"      => "varchar(10) DEFAULT '0' NOT NULL",
         "maj"         => "timestamp"

      );

      $numeros_cles = array(
         'PRIMARY KEY'     => 'id_numero'
      );

      $tables_principales['spip_vacarme_numeros'] = array(
         'field'          => &$numeros,
         'key'            => &$numeros_cles
      );
      return $tables_principales;
   }

   function vacarme_numeros_declarer_tables_auxiliaires($tables_auxiliaires) {
      // détails supplémentaires d'un numéro
      $vacarme_numeros_details = array(
         "id_vacarme_numero_detail" => "bigint(21) not null auto_increment",
         "id_produit" => "bigint(21) not null default 0",
         "saison" => "tinytext not null default ''",
         "annee" => "int(4) not null default 0",
         "url" => "varchar(255) not null default ''"
      );
      $vacarme_numeros_details_cles = array(
         'PRIMARY KEY' => 'id_vacarme_numero_detail',
         'KEY id_produit' => 'id_produit'
      );
      $tables_auxiliaires['spip_vacarme_numeros_details'] = array(
         'field' => &$vacarme_numeros_details,
         'key' => &$vacarme_numeros_details_cles
      );

      // sommaires
      $vacarme_numeros_sommaires = array(
         "id_vacarme_numero_sommaire" => "bigint(21) not null auto_increment",
         "id_produit" => "bigint(21) not null default 0",
         "titre" => "varchar(255) not null default ''",
         "resume" => "text not null default ''",
         "url" => "varchar(255) not null default ''"
      );
      $vacarme_numeros_sommaires_cles = array(
         'PRIMARY KEY' => 'id_vacarme_numero_sommaire',
         'KEY id_produit' => 'id_produit'
      );
      $tables_auxiliaires['spip_vacarme_numeros_sommaires'] = array(
         'field' => &$vacarme_numeros_sommaires,
         'key' => &$vacarme_numeros_sommaires_cles
      );
      return $tables_auxiliaires;
   }
?>
