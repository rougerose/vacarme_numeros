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
   function vacarme_commande_post_edition($flux){
      // après instituer_commande, on peut récupérer le flux.
      // on a une commande, on verifie si on un abonnement dedans et si c'est le cas, on insère les numéros de début et de fin d'abonnement dans la table contacts_abonnement

      if ($flux['args']['table'] == 'spip_commandes') {
         $statut = $flux['data']['statut'];
         $statut_ancien = $flux['args']['statut_ancien'];
         $id_commande = $flux['args']['id_objet'];
         //spip_log('statut ancien '.$statut_ancien.' statut '.$statut.' id_commande '.$id_commande,'vacarme_commande');

         if ($statut != $statut_ancien) {
            // la commande en cours est payée ou en attente ?
            if ($statut_ancien == 'encours') {
               include_spip('inc/config');
               $config = lire_config('commandes');
               if (in_array($statut,$config['quand']) and $statut != 'encours') {
                  // on récupère le détail des commandes, si abonnement on récupère le numéro et id_ojbet (id abonnement)
                  if ($row = sql_allfetsel('id_objet,numero,id_commandes_detail', 'spip_commandes_details', 'id_commande = '.$id_commande.' and objet = "abonnement"')) {
                     foreach ($row as $r) {
                        // on récupère la durée de l'abonnement à partir de son id
                        $duree = sql_fetsel('duree,periode','spip_abonnements','id_abonnement = '.$r['id_objet']);
                        //spip_log($duree,'vacarme_pipeline');
                        $numero_debut = $r['numero'];
                        $id_commandes_detail = $r['id_commandes_detail'];
                        if ($duree['periode'] == 'mois') {
                           $numero_fin = (intval($duree['duree'])/3) + (intval($numero_debut)-1);
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
            }
         }
      }
      return $flux;
   }

?>