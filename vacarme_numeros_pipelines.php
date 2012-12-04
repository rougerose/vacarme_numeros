<?php

   function vacarme_numeros_taches_generales_cron($taches){
       $taches['vacarme_numeros_import'] = 1*3600; // 1 fois par heure
       // spip_log('le cron est passé par là', 'vacarme_numeros_cron');
       return $taches;
   }

?>