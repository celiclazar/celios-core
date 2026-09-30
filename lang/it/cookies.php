<?php

return [
    // Admin Settings Page
    'title' => 'Gestione Cookie',
    'subtitle' => 'Configura il banner di consenso ai cookie conforme al GDPR, l\'informativa sulla privacy e gli script di tracciamento.',
    'settings_saved' => 'Impostazioni dei cookie salvate con successo!',
    'settings_saved_body' => 'La configurazione del banner di consenso e le regole degli script sono state aggiornate.',

    // General Section
    'section_general' => 'Configurazione Banner di Consenso',
    'section_general_desc' => 'Controlla la visibilità, il layout e i parametri legali del banner di consenso ai cookie.',
    'enabled' => 'Abilita Banner di Consenso Cookie',
    'enabled_help' => 'Se abilitato, ai visitatori per la prima volta verrà mostrato un banner di consenso ai cookie e un blocco script.',
    'position' => 'Posizione del Banner',
    'position_bottom_banner' => 'Barra Inferiore (Larghezza Intera)',
    'position_bottom_right' => 'Scheda Fluttuante (In Basso a Destra)',
    'position_bottom_left' => 'Scheda Fluttuante (In Basso a Sinistra)',
    'position_center_modal' => 'Finestra Modale Centrale',
    'expiry_days' => 'Scadenza del Consenso (Giorni)',
    'expiry_days_help' => 'Numero di giorni prima che il cookie di consenso scada e venga richiesto nuovamente al visitatore (Predefinito: 365 giorni).',
    'privacy_url' => 'URL Informativa sulla Privacy',
    'privacy_url_help' => 'URL relativo o assoluto della tua pagina di privacy policy (es. /it/informativa-privacy).',

    // Content Section
    'section_content' => 'Testi del Banner e della Finestra Modale',
    'section_content_desc' => 'Personalizza il titolo e le descrizioni visualizzate ai visitatori del sito.',
    'banner_title' => 'Titolo del Banner',
    'banner_title_default' => 'Rispettiamo la tua privacy',
    'banner_description' => 'Descrizione del Banner',
    'banner_description_default' => 'Utilizziamo i cookie per migliorare la tua esperienza di navigazione, offrire contenuti personalizzati e analizzare il nostro traffico. Cliccando su "Accetta tutti", acconsenti all\'uso dei cookie.',

    // Categories Section
    'section_categories' => 'Categorie di Cookie e Gestione Script',
    'section_categories_desc' => 'Configura le singole categorie di cookie e i relativi script di tracciamento/marketing.',

    // Necessary
    'cat_necessary' => 'Cookie Essenziali / Necessari',
    'cat_necessary_desc' => 'Questi cookie sono essenziali per il corretto funzionamento del sito e non possono essere disabilitati (es. sessioni, sicurezza CSRF, autenticazione).',
    'cat_necessary_always_active' => 'Sempre Attivo',

    // Analytics
    'cat_analytics' => 'Analisi e Prestazioni',
    'cat_analytics_desc' => 'Ci consentono di contare le visite e le fonti di traffico per misurare e migliorare le prestazioni del sito (es. Google Analytics, Plausible, Matomo).',
    'cat_analytics_enabled' => 'Abilita Categoria Analisi',
    'analytics_head_scripts' => 'Script di Analisi (Eseguiti con Consenso)',
    'analytics_head_scripts_help' => 'Snippet JavaScript o HTML (come Google Analytics / GTAG / Matomo) che verranno inseriti SOLO dopo il consenso ai cookie di analisi.',

    // Marketing
    'cat_marketing' => 'Marketing e Pubblicità',
    'cat_marketing_desc' => 'Utilizzati dai partner pubblicitari per profilare i tuoi interessi e mostrarti annunci pertinenti su altri siti (es. Meta Pixel, Google Ads, LinkedIn Insight).',
    'cat_marketing_enabled' => 'Abilita Categoria Marketing',
    'marketing_head_scripts' => 'Script di Marketing / Pixel (Eseguiti con Consenso)',
    'marketing_head_scripts_help' => 'Tag di marketing (come Meta Pixel / Facebook Pixel, Google Ads) che verranno inseriti SOLO dopo il consenso ai cookie di marketing.',

    // Functional
    'cat_functional' => 'Funzionalità e Preferenze',
    'cat_functional_desc' => 'Abilitano funzionalità avanzate e personalizzazione, come la memorizzazione delle preferenze di lingua e dei lettori video.',
    'cat_functional_enabled' => 'Abilita Categoria Funzionale',
    'functional_scripts' => 'Script Funzionali (Eseguiti con Consenso)',
    'functional_scripts_help' => 'Script funzionali (es. widget chat) che verranno inseriti SOLO dopo il consenso ai cookie funzionali.',

    // Frontend UI Buttons & Labels
    'ui_accept_all' => 'Accetta Tutti',
    'ui_reject_non_essential' => 'Rifiuta Non Essenziali',
    'ui_customize' => 'Personalizza Cookie',
    'ui_save_preferences' => 'Salva Preferenze',
    'ui_preferences_title' => 'Preferenze Cookie',
    'ui_preferences_subtitle' => 'Gestisci le impostazioni dei cookie. Puoi modificare le tue preferenze in qualsiasi momento.',
    'ui_privacy_link' => 'Informativa Privacy',
    'ui_cookie_settings_link' => 'Impostazioni Cookie',
    'ui_cookies_saved_alert' => 'Le tue preferenze sui cookie sono state salvate.',
    'ui_close' => 'Chiudi',
    'status_enabled' => 'Consentito',
    'status_disabled' => 'Bloccato',
];
