<?php
   if (!defined('_ECRIRE_INC_VERSION')) return;

   function formulaires_paiements_alternatifs_charger_dist($options=array(),$retour) {
      $valeurs = array();
      $valeurs = array_merge($valeurs,$options);
      return $valeurs;
   }

   function formulaires_paiements_alternatifs_verifier_dist($options=array(),$retour) {
      $erreurs = array();
      foreach(array('commande_numero','id_auteur','id_commande') as $champ) {
         if (!_request($champ)) {
                  $erreurs[$champ] = "";
              }
          }
          if (count($erreurs)) {
              $erreurs['message_erreur'] = "Une erreur s'est produite. <a href='#'>Merci de nous contacter</a>.";
          }
      return $erreurs;
   }

   function formulaires_paiements_alternatifs_traiter_dist($options=array(),$retour) {
      //if ($retour) refuser_traiter_formulaire_ajax();
      $retours = array();
      $id_auteur = $options['id_auteur'];
      $reference = $options['commande_numero'];
      if($id_auteur){
         include_spip('base/abstract_sql');
         // statut et référence de la commande : pour vérification ultérieure
         $row = sql_fetsel("statut","spip_commandes","id_commande=".$options['id_commande']);
         // email
         $email = sql_fetsel("email","spip_auteurs","id_auteur=$id_auteur");
         $row['identite'] = sql_fetsel("civilite,nom,prenom,organisation,service","spip_contacts_liens LEFT JOIN spip_contacts USING(id_contact)",array('objet ='.sql_quote('auteur'),'id_objet = '.intval($id_auteur)));

         /* pour le moment inutile
         --------------------------
         // adresses (facturation et/ou livraison)
         //$row['adresses'] = sql_allfetsel('*','spip_adresses_liens LEFT JOIN spip_adresses USING(id_adresse)',array('objet = '.sql_quote('commande'),'id_objet = '.intval($options['id_commande'])));
         --------------------------
         */

         include_spip('inc/config');
         foreach($options['details'] as $v) {
            $total += $v['prix'];
         }

         if($options['tva_applicable']) {
            $total += $total*lire_config('produits/taxe');
            $total = round($total,2);
         }

         // vérification statut en cours et mail est présent
         if ($row['statut']=='encours' AND $email) {
            $envoyer_mail = charger_fonction('envoyer_mail','inc');
            $msg = mail_paiements_alternatifs($row,$options,$total);
            $sujet = _T('vacarme_commande:mail_sujet_paiement', array('numero_commande'=>$reference));

            $adresse_site = $GLOBALS['meta']["adresse_site"];
            $nom_site = $GLOBALS['meta']["nom_site"];

            if (!$envoyer_mail($email, $sujet, $msg, $from, $head)) {
               $retours['message_erreur'] = _T('form_forum_probleme_mail');
               return $retours;
            } else {
               // le mail est parti, la commande passe du statut "encours" à "attente"
               $statut_commande = sql_updateq("spip_commandes",array("statut"=>"attente"),"id_commande=".intval($options['id_commande']));
               if ($statut_commande) $retours['message_ok'] = _T('vacarme_commande:message_ok_formulaire_paiement');
            }

         } else {
            $retours['message_erreur'] = _T('vacarme_commande:message_erreur_formulaire_paiement',array('numero_commande' => $reference));
            spip_log("auteur" .$id_auteur." n a pas pus obtenir le mail d envoi pour un paiement alternatif. Numéro de commande ".$reference." erreur test vérification cohérence numéro commande","vacarme_commande_paiement_alter");
            if ($retour) {
               $retour = parametre_url($retour,'r','0','&');
               $retours['redirect'] = $retour;
            }
            return $retours;

         }
      } else {
         $retours['message_erreur'] = _T('vacarme_commande:message_erreur_formulaire_paiement',array('numero_commande' => $row['reference']));
         spip_log("l'auteur ".$id_auteur." n'a pas pu obtenir le mail d'envoi pour un paiement alternatif. Numéro de commande ".$row['reference']." erreur pas d'id_auteur","vacarme_commande_paiement_alter");
         if ($retour) {
            $retour = parametre_url($retour,'r','0','&');
            $retours['redirect'] = $retour;
         }
         return $retours;
      }
      if ($retour) {
         $retour = parametre_url($retour,'r','2','&');
         $retours['redirect'] = $retour;
      }
      $retours['message_ok'] = _T('vacarme_commande:message_ok_formulaire_paiement');
      return $retours;
   }


   // construction du mail
   function mail_paiements_alternatifs($row,$options,$total){
      include_spip('base/abstract_sql');

      if ($options['type_paiement'] == 'cheque') {
         $type_paiement = _T('vacarme_commande:paiement_cheque');
         $procedure_paiement = _T('vacarme_commande:paiement_cheque_procedure',array('total' =>$total,'numero_commande'=>$options['commande_numero']));
      }

      if ($options['type_paiement'] == 'virement') {
         $type_paiement = _T('vacarme_commande:paiement_virement');
         $procedure_paiement = _T('vacarme_commande:paiement_virement_procedure',array('total' =>$total,'numero_commande'=>$options['commande_numero']));

      }

      /* Pour le moment, inutile
      --------------------------------
      */// les adresses
      /*
         TODO : Devrait-il y avoir un contact par type d'adresse ? Donc possibilité d'ajouter à chaque adresse livraison/facturation un contact différent ?
      *//*
      if ($row['adresses']) {
         $identite = $row['identite']['civilite'].' '.$row['identite']['prenom'].' '.$row['identite']['nom']."\n";
         if($row['identite']['organisation']) {
            $identite .= $row['identite']['organisation']."\n".$row['identite']['service'];
         }
         foreach ($row['adresses'] as $v) {
            $pays = sql_fetsel(sql_multi("nom","fr"), "spip_pays","code LIKE '".$v['pays']."'");
            $message_adresses .= "\n".'Adresse de '.$v['type']."\n";
            $message_adresses .= str_pad("",30,"-")."\n";
            $message_adresses .= $identite."\n";
            $message_adresses .= $v['voie']."\n"
                                 .$v['complement']."\n"
                                 .$v['boite_postale']."\n"
                                 .$v['code_postal'].' '.$v['ville']."\n"
                                 .$pays['multi']."\n";
         }
      }
      */

      $url_compte_commande = generer_url_public('compte','section=commandes&id_commande='.$options['id_commande'], true);

      $message = _T('vacarme_commande:mail_intro_paiement',array('type_paiement'=>$type_paiement))."\n\n"
               . $procedure_paiement."\n\n"
               ._T('vacarme_commande:mail_lien_commande',array('url_compte_commande'=> $url_compte_commande))."\n\n"
               . _T('vacarme_commande:mail_fin_paiement')."\n\n";
               //. str_pad("",40,"=")."\n"
               //. _L('commande numéro '.$row['reference'])."\n"
               //. str_pad("",40,"=")."\n"
               //.$message_adresses;

      return $message;
   }

?>