<?php
/*
*     page de test depuis l'espace privé
*/


// if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip("inc/presentation");

function exec_vacarme_commande() {
   if (!autoriser('editer', 'article')) {
      include_spip('inc/minipres');
      echo minipres();
      exit;
   }

   $notifications = charger_fonction('notifications', 'inc', true);
   $id = 68;
   $options = array();
   $options['expediteur'] = 1;
   $id_commande = intval($id);

   $id_commande = intval($id);
   $row = sql_fetsel("statut,reference,paiement,id_auteur","spip_commandes","id_commande=$id_commande");
   $row['identite'] = sql_fetsel("nom,prenom,type_client","spip_contacts_liens LEFT JOIN spip_contacts USING(id_contact)",array("objet =".sql_quote('auteur'),"id_objet =".$row['id_auteur']));
   $row['pays'] = sql_fetsel("pays","spip_adresses_liens LEFT JOIN spip_adresses USING(id_adresse)",array("objet =".sql_quote('auteur'),"id_objet =".$row['id_auteur']));


   // le client paie la TVA ?
   include_spip('inc/vacarme_commande');
   if (function_exists('tva_applicable')) {
      $tva = tva_applicable($row['identite']['type_client'],$row['pays']['pays']);
   }
   // le détail de la commande
   $details = sql_allfetsel("prix_unitaire_ht,taxe,quantite","spip_commandes_details","id_commande=$id_commande");
   foreach($details as $d) {
      $total = $d['prix_unitaire_ht']*$d['quantite'];
      if ($tva) {
         $total = $total * ($d['taxe'] + 1);
      }
      $total +=$total;
      $total = round($total,2);
   }
   $url_commande = generer_url_ecrire('commande_voir',"id_commande=$id_commande");
   $msg = _T('vacarme_commande:mail_paiement_vacarme', array(
      'numero_commande' => $row['reference'],
      'prenom' => $row['identite']['prenom'],
      'nom' => $row['identite']['nom'],
      'total' => $total.' euros',
      'statut' => $row['statut'],
      'paiement' => $row['paiement'],
      'url_commande' => $url_commande
      ));

   $message['texte'] = $msg;
   $message['court'] = _T('vacarme_commande:mail_sujet_paiement_vacarme',array('numero_commande'=>$row['reference']));

   var_dump($total);





   // page
   $commencer_page = charger_fonction('commencer_page', 'inc');
   echo $commencer_page(_T('rssarticle:activer_recopie_intro'), 'editer', 'editer');
   echo gros_titre(_T('rssarticle:activer_recopie_intro'),'', false);
   // colonne gauche
   echo debut_gauche('', true);
   echo debut_droite('', true);

   //genie_vacarme_numeros_import_dist('manuel');

   //include_spip("vacarme_numeros_fonctions");
   //vacarme_numeros_import_test();

   echo '<div style="margin:2em 0;"><a href="?exec=vacarme_numeros" style="border:1px solid;padding:0.5em;background:#fff;">'._T('rssarticle:maj_recharge').'</a></div>';

   // pied
   echo fin_gauche() . fin_page();

}
/*
function tva_applicable ($type_client,$pays) {
   // tva applicable si c'est un particulier qui réside en UE. Particulier hors UE et Organisation (dans et hors UE), pas de tva.
   // la liste des pays de l'UE
   $UE = array('AT','BE','BG','CY','CZ','DK','EE','FI','FR','DE','GR','HU','IE','IT','LV','LT','LU','MT','NL','PL','PT','RO','SK','ES','SE','UK');
   $tva = (($type_client == 'particulier') AND (in_array($pays,$UE)) ) ? true : false;
   return $tva;
}
*/

?>
