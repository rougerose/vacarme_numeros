<?php
   if (!defined("_ECRIRE_INC_VERSION")) return;

   // fonction pour le pipeline, n'a rien a effectuer
   function vacarme_commande_autoriser(){}

   // declarations d'autorisations
   function autoriser_commande_voir($faire, $quoi, $id, $qui, $options) {
      $id_auteur = sql_getfetsel('id_auteur','spip_commandes','id_commande='.$id);
      if ($qui['statut'] == '0minirezo' OR $qui['id_auteur'] == $id_auteur) return true;
      else return false;
   }

   function autoriser_commande_supprimer($faire,$quoi,$id,$qui,$options) {
      $id_auteur = sql_getfetsel('id_auteur','spip_commandes','id_commande='.$id);
      if ($qui['statut'] == '0minirezo' OR $qui['id_auteur'] == $id_auteur) return true;
      else return false;
   }


?>