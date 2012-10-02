<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_vacarme(){
	if (!autoriser('administrer','vacarme',0)) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('Gestion des commandes et des abonnements'));

	echo gros_titre(_T('Gestion des commandes et des abonnements'),'',false);
	echo debut_grand_cadre(true);

//	echo formulaire_recherche('portfolio');
	echo recuperer_fond('prive/vacarme',$_GET);

	echo fin_grand_cadre(true),fin_page();
}

?>