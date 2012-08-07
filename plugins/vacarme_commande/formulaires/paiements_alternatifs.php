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
      $retours = array();
      $id_auteur = $options['id_auteur'];
      $reference = $options['commande_numero'];
      $id_commande = $options['id_commande'];
      $statut_nouveau = 'attente'; // la commande est mise en attente de réception du règlement

      //spip_log("paiements_alternatifs_traiter envoi vers instituer $id_commande-$statut_nouveau",'vacarme_commande');

      $action = charger_fonction('instituer_commande', 'action');
      if ($action) {
         $action($id_commande."-".$statut_nouveau);
         $retours['message_ok'] = _T('vacarme_commande:message_ok_formulaire_paiement');
         if ($retour) $retour = parametre_url($retour,'r','2','&'); $retours['redirect'] = $retour;
      } else {
         $retours['message_erreur'] = _T('vacarme_commande:message_erreur_formulaire_paiement',array('numero_commande' => $reference));
         if ($retour) $retour = parametre_url($retour,'r','0','&'); $retours['redirect'] = $retour;
      }
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