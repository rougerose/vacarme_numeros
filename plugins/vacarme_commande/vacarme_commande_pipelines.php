<?php
   if (!defined("_ECRIRE_INC_VERSION")) return;

   // =================
   // = Insertion css =
   // =================
   function vacarme_commande_insert_head_css($flux){
      $inuit       = find_in_path('css/inuit.css');
      $grille12col = find_in_path('css/grid.inuit.css');
      $dd = find_in_path('css/dropdown.inuit.css');
      $css         = find_in_path('css/screen.css');
      $flux       .= "\n<link rel='stylesheet' type='text/css' media='screen' href='$inuit' />\n";
//      $flux .= "<link rel='stylesheet' type='text/css' media='screen' href='$dd' />\n";
      $flux       .= "<link rel='stylesheet' type='text/css' media='screen' href='$grille12col' />\n";
      $flux       .= "<link rel='stylesheet' type='text/css' media='screen' href='$css' />\n";
      return $flux;
   }

   // ================
   // = Insertion js =
   // ================
   function vacarme_commande_insert_head($flux){
      $js_public = find_in_path('javascript/vacarme_commande-public.js');
      if ($js_public) {
         $flux     .= "\n<script src='$js_public' type='text/javascript'></script>\n";
      }
      return $flux;
   }

   // =========================
   // = pipeline post_edition =
   // =========================
   // ajout des infos relatives au numero_debut et numero_fin d'abonnement

   function vacarme_commande_post_edition($flux){
      if($flux['args']['table'] == 'spip_commandes' and ($statut = $flux['data']['statut']) == 'paye'){
         $id_commande = $flux['args']['id_objet'];

         // on récupère le détail des commandes, si abonnement on récupère le numéro et id_ojbet (id abonnement)
         if($row = sql_allfetsel('id_objet,numero,id_commandes_detail', 'spip_commandes_details', 'id_commande = '.$id_commande.' and objet = "abonnement"')){
            foreach ($row as $r) {
               // on récupère la durée de l'abonnement à partir de son id
               $duree = sql_fetsel('duree,periode','spip_abonnements','id_abonnement = '.$r['id_objet']);
               //spip_log($duree,'vacarme_pipeline');
               $numero_debut = $r['numero'];
               $id_commandes_detail = $r['id_commandes_detail'];
               //spip_log('numero debut '.$numero_debut.' | id_commandes_detail '.$id_commandes_detail,"vacarme_pipeline");
               if($duree['periode'] == 'mois'){
                  //spip_log('condition duree','vacarme_pipeline');
                  $numero_fin = (intval($duree['duree'])/3) + (intval($numero_debut)-1);
                  //spip_log('numero_fin '.$numero_fin,"vacarme_pipeline");
                  // dans contacts_abonnement, à partir de l'id commandes détails, on insère numéro début et fin
                  sql_updateq(
                     'spip_contacts_abonnements',
                     array('numero_debut'=>$numero_debut,'numero_fin'=>$numero_fin),
                     'id_commandes_detail = '.$id_commandes_detail
                  );
               }
            }
         }
      }
      return $flux;
   }

?>