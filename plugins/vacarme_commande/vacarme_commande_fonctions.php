<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

   function filtre_capitale($texte){
      return $texte = ucfirst($texte);
   }

   // Une fonction qui retourne les différents statuts possibles pour une commande ou le nom d'un statut précis
   function filtre_lister_statuts($statut=false){
      $statuts =  array(
      'encours' => _T('vacarme_commande:statut_encours'),
      'erreur' => _T('vacarme_commande:statut_erreur'),
      'attente' => _T('vacarme_commande:statut_attente'),
      'partiel' => _T('vacarme_commande:statut_partiel'),
      'paye' => _T('vacarme_commande:statut_paye'),
      'envoye' => _T('vacarme_commande:statut_envoye'),
      'retour' => _T('vacarme_commande:statut_retour'),
      'retour_partiel' => _T('vacarme_commande:statut_retour_partiel'),
      );

      if ($statut and $nom = $statuts[$statut]) return $nom;
      else return $statuts;
   }

   // on garde deux chiffres après la virgule. filtre appliqué pour le formulaire paypal. Ce qui permet de retrouver le prix envoyé par la fonction |prix_formater (qui n'est pas un arrondi).
   function filtre_decimale($texte) {
      $texte = sprintf("%.2f",$texte);
      return $texte;
   }
?>