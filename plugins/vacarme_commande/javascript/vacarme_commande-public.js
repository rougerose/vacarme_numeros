$(document).ready(function() {

// ====================================
// = bouton d'activation de la grille =
// ====================================
   $("#spip-admin").append("<a class='spip-admin-boutons grid_tg' href='#'>Grille</a>");
   $(".grid_tg").click(function(){
   	$("body").toggleClass("grid");
   });

// =================
// = jqueryui tabs =
// =================
   $("#offres_abo,#paiements").tabs();

// =========================================
// = jquery accordion sur compte/commandes =
// =========================================
   $("#liste_commandes").accordion({
      active: false,
      autoHeight: false,
      navigation: true,
      icons: false
   });

// ==========
// = Panier =
// ==========
   $("#panier").each(function(){
      var   input = $(this).find("input[type=text]"),
            bouton = $(this).find(".boutons");
      bouton.hide();
      input.change(function(){
         bouton.show('slow');
      });
   });


// =======================================================
// = Présentation produits : accordéon sur les sommaires =
// =======================================================
   initAccordeon();
   onAjaxLoad(initAccordeon);

});

// ====================================
// = jquery accordion sur les numéros =
// ====================================
   var initAccordeon = function() {
      $("#produits.numeros").each(function(){
         var $sommaire = $(this).find(".sommaire");
         $sommaire.accordion({
            active: false,
            autoHeight: false,
            navigation: true,
            icons: false
         });
      });
   }

