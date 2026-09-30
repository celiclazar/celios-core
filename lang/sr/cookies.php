<?php

return [
    // Admin Settings Page
    'title' => 'Upravljanje kolačićima',
    'subtitle' => 'Podesite GDPR usklađeno obaveštenje o kolačićima, politiku privatnosti i skripte za praćenje.',
    'settings_saved' => 'Podešavanja kolačića su uspešno sačuvana!',
    'settings_saved_body' => 'Konfiguracija banera za saglasnost i pravila skripti su ažurirani.',

    // General Section
    'section_general' => 'Konfiguracija banera saglasnosti',
    'section_general_desc' => 'Kontrolišite vidljivost, raspored i pravne parametre obaveštenja o kolačićima.',
    'enabled' => 'Omogući obaveštenje o kolačićima',
    'enabled_help' => 'Kada je uključeno, posetiocima se prikazuje baner za pristanak na kolačiće i blokator skripti.',
    'position' => 'Pozicija banera',
    'position_bottom_banner' => 'Donja traka (Puna širina)',
    'position_bottom_right' => 'Plutajuća kartica (Dole desno)',
    'position_bottom_left' => 'Plutajuća kartica (Dole levo)',
    'position_center_modal' => 'Centralni modalni dijalog',
    'expiry_days' => 'Istek saglasnosti (Dani)',
    'expiry_days_help' => 'Broj dana nakon kojih saglasnost ističe i posetiocu se ponovo prikazuje upit (Podrazumevano: 365 dana).',
    'privacy_url' => 'URL Politike privatnosti',
    'privacy_url_help' => 'Relativna ili apsolutna adresa stranice o privatnosti (npr. /sr/politika-privatnosti).',

    // Content Section
    'section_content' => 'Tekstovi na baneru i modalu',
    'section_content_desc' => 'Prilagodite naslov i opis koji se prikazuju posetiocima sajta.',
    'banner_title' => 'Naslov banera',
    'banner_title_default' => 'Poštujemo vašu privatnost',
    'banner_description' => 'Opis na baneru',
    'banner_description_default' => 'Koristimo kolačiće kako bismo unapredili vaše korisničko iskustvo, pružili personalizovani sadržaj i analizirali posetu. Klikom na "Prihvati sve" pristajete na našu upotrebu kolačića.',

    // Categories Section
    'section_categories' => 'Kategorije kolačića i upravljanje skriptama',
    'section_categories_desc' => 'Podesite pojedinačne kategorije kolačića i pripadajuće analitičke i marketinške skripte.',

    // Necessary
    'cat_necessary' => 'Neophodni / Funkcionalni kolačići',
    'cat_necessary_desc' => 'Ovi kolačići su neophodni za funkcionisanje sajta i ne mogu se isključiti (sesije, CSRF zaštita, prijava, sistemska podešavanja).',
    'cat_necessary_always_active' => 'Uvek aktivno',

    // Analytics
    'cat_analytics' => 'Analitika i performanse',
    'cat_analytics_desc' => 'Omogućavaju nam merenje broja poseta i izvora saobraćaja kako bismo poboljšali rad sajta (npr. Google Analytics, Plausible, Matomo).',
    'cat_analytics_enabled' => 'Omogući analitičku kategoriju',
    'analytics_head_scripts' => 'Analitičke skripte (Izvršavaju se po pristanku)',
    'analytics_head_scripts_help' => 'JavaScript ili HTML kod (kao što je Google Analytics / GTAG / Matomo) koji će se učitati SAMO ako posetilac odobri analitičke kolačiće.',

    // Marketing
    'cat_marketing' => 'Marketing i oglašavanje',
    'cat_marketing_desc' => 'Koriste ih naši partneri za oglašavanje kako bi kreirali profil vaših interesovanja i prikazali relevantne oglase (npr. Meta Pixel, Google Ads, LinkedIn Insight).',
    'cat_marketing_enabled' => 'Omogući marketinšku kategoriju',
    'marketing_head_scripts' => 'Marketinške / Pixel skripte (Izvršavaju se po pristanku)',
    'marketing_head_scripts_help' => 'Marketinški tagovi (kao što su Meta Pixel / Facebook Pixel, Google Ads remarketing) koji će se učitati SAMO ako posetilac odobri marketinške kolačiće.',

    // Functional
    'cat_functional' => 'Funkcionalnost i podešavanja',
    'cat_functional_desc' => 'Omogućavaju napredne funkcije i personalizaciju, poput pamćenja izabranog jezika, video plejera i korisničkih postavki.',
    'cat_functional_enabled' => 'Omogući funkcionalnu kategoriju',
    'functional_scripts' => 'Funkcionalne skripte (Izvršavaju se po pristanku)',
    'functional_scripts_help' => 'Funkcionalne skripte (npr. vidžet za čet, posebni dodaci) koje se učitavaju SAMO ako posetilac odobri funkcionalne kolačiće.',

    // Frontend UI Buttons & Labels
    'ui_accept_all' => 'Prihvati sve',
    'ui_reject_non_essential' => 'Samo neophodni',
    'ui_customize' => 'Podešavanje kolačića',
    'ui_save_preferences' => 'Sačuvaj izbor',
    'ui_preferences_title' => 'Podešavanje kolačića',
    'ui_preferences_subtitle' => 'Prilagodite koje vrste kolačića želite da dozvolite. Vaš izbor možete promeniti u bilo kom trenutku.',
    'ui_privacy_link' => 'Politika privatnosti',
    'ui_cookie_settings_link' => 'Kolačići',
    'ui_cookies_saved_alert' => 'Vaša podešavanja kolačića su sačuvana.',
    'ui_close' => 'Zatvori',
    'status_enabled' => 'Dozvoljeno',
    'status_disabled' => 'Blokirano',
];
