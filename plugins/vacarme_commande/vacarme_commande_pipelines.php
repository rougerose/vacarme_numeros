<?php
   if (!defined("_ECRIRE_INC_VERSION")) return;

   // =================
   // = Insertion css =
   // =================
   function vacarme_commande_insert_head_css($flux){
      $inuit        = find_in_path('css/inuit.css');
      $grille12col  = find_in_path('css/grid.inuit.css');
      $css          = find_in_path('css/styles.css');
      $ie8          = find_in_path('css/ie8.css');
      $flux        .= "\n<link rel='stylesheet' type='text/css' href='$inuit' />\n";
      $flux        .= "<link rel='stylesheet' type='text/css' href='$grille12col' />\n";
      $flux        .= "<link rel='stylesheet' type='text/css' href='$css' />\n";
      $flux        .= "<!--[if lte IE 8]><link rel='stylesheet' type='text/css' href='$ie8' /><![endif]-->\n";
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

   //
   function vacarme_commande_header_prive($flux){
   	//$js = find_in_path('javascript/saisies.js');
   	//$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
   	$css = generer_url_public('vacarme_commande_prive.css');
   	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' media='all' />\n";
   	return $flux;
   }


   // ==============================
   // = pipeline traitement_paypal =
   // ==============================
   function vacarme_commande_traitement_paypal($flux) {
      if ( $flux['args']['paypal']['custom'] == 'payer_commande'
         and $reference = $flux['args']['paypal']['invoice']
      and $commande = sql_fetsel('id_commande', 'spip_commandes', 'reference = '.sql_quote($reference)) ) {
         // c'est un paiement par paypal (cqfd)
         sql_updateq('spip_commandes',array('paiement' => 'paypal'),'id_commande='.$commande['id_commande']);
      }
      return $flux;
   }


   // ===========================
   // = pipeline post_insertion =
   // ===========================
   // flux depuis creer_commande_encours : on ajoute l'id de commande dans sa référence
   function vacarme_commande_post_insertion ($flux) {
      if ($flux['args']['table'] == 'spip_commandes' and $flux['args']['id_objet'] and $flux['data']['reference']) {
         $id_commande = intval($flux['args']['id_objet']);
         $reference = $flux['data']['reference']; // de la forme aaaammjj-id_auteur
         $reference = $reference."-".$id_commande; // devient aaaammjj-id_auteur-id_commande
         sql_updateq("spip_commandes", array('reference' => $reference), "id_commande=$id_commande");
         // on renvoie dans le flux la nouvelle référence
         $flux['data']['reference'] = $reference;
         return $flux;
      }
   }


   // =========================
   // = pipeline post_edition =
   // =========================
   function vacarme_commande_post_edition($flux){
      // après instituer_commande, on peut récupérer le flux.
      if ($flux['args']['table'] == 'spip_commandes' AND $flux['args']['action'] == 'instituer') {
         $statut = $flux['data']['statut'];
         $statut_ancien = $flux['args']['statut_ancien'];
         $id_commande = $flux['args']['id_objet'];

         // on a une commande, on verifie si on un abonnement dedans
         // et si c'est le cas, on insère les numéros de début et de fin d'abonnement dans la table contacts_abonnement

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

         // on envoie les notifications
         $notifications = charger_fonction('notifications', 'inc', true);
         // on reprend sur le modèle de la notification dans instituer_commande, c'est-à-dire uniquement l'id du webmestre dans $options,
         // mais on pourrait, du coup, en ajouter d'autres puisqu'on fabrique notre propre notification. A voir plus tard.
         $options = array();
         $options['expediteur'] = 1; // webmestre
         // on envoie
         $notifications('vacarme_commande_vendeur', $id_commande, $options);
         $notifications('vacarme_commande_client', $id_commande, $options);
      }
      return $flux;
   }

?>
