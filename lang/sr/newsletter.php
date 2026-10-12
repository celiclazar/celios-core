<?php

return [
    // Public block & subscription
    'block_title' => 'Prijava na newsletter',
    'block_heading' => 'Naslov',
    'block_subtitle' => 'Podnaslov',
    'block_description' => 'Opis',
    'block_button_text' => 'Tekst na dugmetu',
    'block_disclaimer' => 'Napomena o privatnosti',
    'block_default_title' => 'Budite u toku sa najnovijim vestima',
    'block_default_subtitle' => 'Prijavite se na naš newsletter i primajte najnovije vesti, ponude i obaveštenja direktno u Vaše sanduče.',
    'block_default_description' => 'Prijavite se na naš newsletter i primajte najnovije vesti, ponude i obaveštenja direktno u Vaše sanduče.',
    'block_default_button' => 'Prijavi se',
    'block_default_disclaimer' => 'Poštujemo Vašu privatnost. Možete se odjaviti u bilo kom trenutku jednim klikom.',
    'email_placeholder' => 'Unesite Vašu email adresu...',
    'subscribed_success' => 'Hvala Vam na prijavi!',
    'already_subscribed' => 'Već ste prijavljeni na naš newsletter.',
    'confirm_email_sent' => 'Molimo proverite Vaše email sanduče i potvrdite prijavu.',
    'submission_failed' => 'Došlo je do greške. Molimo pokušajte ponovo.',

    // Verification & Unsubscribe public pages
    'confirm_subject' => 'Molimo potvrdite Vašu prijavu na :app',
    'confirm_title' => 'Potvrda prijave na newsletter',
    'confirm_greeting' => 'Pozdrav :name,',
    'confirm_instruction' => 'Hvala Vam na interesovanju za naš newsletter. Molimo kliknite na dugme ispod kako biste potvrdili Vašu email adresu i aktivirali prijavu.',
    'confirm_button' => 'Potvrdi moju prijavu',
    'confirm_trouble' => 'Ukoliko imate problem sa klikom na dugme, prekopirajte sledeći link u Vaš pretraživač:',
    'confirm_ignore' => 'Ukoliko niste zatražili ovu prijavu, jednostavno ignorišite ovu poruku.',
    'sent_to' => 'Ova poruka je poslata na :email',

    'verified_page_title' => 'Prijava uspešno potvrđena',
    'verify_success_title' => 'Uspešna potvrda!',
    'verify_success_message' => 'Hvala Vam! Vaša email adresa je verifikovana i Vaša prijava na newsletter je sada aktivna.',
    'verify_invalid_or_expired' => 'Ovaj verifikacioni link je nevažeći, istekao ili je već iskorišćen.',
    'notice' => 'Obaveštenje',
    'back_to_home' => 'Povratak na početnu stranicu',

    'unsubscribed_page_title' => 'Odjava uspešna',
    'unsubscribed_title' => 'Uspešno ste se odjavili',
    'unsubscribed_desc' => 'Adresa :email je uspešno uklonjena sa naše mailing liste. Više nećete primati marketinške poruke od nas.',
    'unsubscribed_success' => 'Uspešno ste se odjavili sa newsletter liste.',

    // Email layout & campaign
    'footer_reason' => 'Ovu poruku primate jer ste se prijavili na obaveštenja na našem sajtu.',
    'unsubscribe' => 'Odjavi se',

    // Test connection
    'test_connection_subject' => 'Test email sa Celios CMS Newsletter modula',
    'test_connection_body' => "Ovo je test poruka poslata sa aplikacije :app radi provere ispravnosti mailing konfiguracije.\n\nVreme slanja: :date\nUkoliko ste primili ovu poruku, slanje newslettera radi ispravno!",
    'test_connection_success' => 'Test email je uspešno poslat!',
    'test_connection_failed' => 'Slanje test emaila nije uspelo: :error',

    // Statuses
    'status_pending' => 'Čeka potvrdu',
    'status_active' => 'Aktivan',
    'status_unsubscribed' => 'Odjavljen',
    'status_bounced' => 'Odbijen (Bounced)',

    'campaign_status_draft' => 'Nacrt',
    'campaign_status_scheduled' => 'Zakazana',
    'campaign_status_sending' => 'Šalje se',
    'campaign_status_sent' => 'Poslata',
    'campaign_status_failed' => 'Neuspešna',
    'campaign_status_cancelled' => 'Otkazana',

    // Settings Page
    'settings_section_transport' => 'Podešavanja slanja i transporta',
    'settings_section_transport_desc' => 'Izaberite način slanja newslettera preko Vašeg servera / prilagođenog SMTP-a ili putem namenske cloud usluge (Amazon SES, Resend, itd.).',
    'settings_mailer_type' => 'Provajder za slanje emailova',
    'settings_mailer_app_default' => 'Podrazumevano za aplikaciju (koristi Laravel .env konfiguraciju)',
    'settings_mailer_custom_smtp' => 'Prilagođeni SMTP (Hosting server / cPanel / Plesk)',
    'settings_mailer_ses' => 'Amazon SES (AWS)',
    'settings_mailer_resend' => 'Resend (API/SMTP)',
    'settings_mailer_mailgun' => 'Mailgun',
    'settings_smtp_host' => 'SMTP Host',
    'settings_smtp_port' => 'SMTP Port',
    'settings_smtp_username' => 'SMTP korisničko ime',
    'settings_smtp_password' => 'SMTP lozinka',
    'settings_smtp_encryption' => 'Enkripcija',
    'settings_encryption_tls' => 'TLS (STARTTLS)',
    'settings_encryption_ssl' => 'SSL',
    'settings_encryption_none' => 'Bez enkripcije',

    'settings_section_rate_limit' => 'Ograničenje brzine slanja (Throttling)',
    'settings_section_rate_limit_desc' => 'Zaštitite svoj hosting server ili email provajdera od preopterećenja i stavljanja na crne liste.',
    'settings_rate_limit' => 'Maksimalan broj emailova po minutu',
    'settings_rate_limit_helper' => 'Preporučeno za deljeni hosting / cPanel: 20-30 emailova/min. Postavite 0 za neograničeno / brzo slanje (Amazon SES, Resend, VPS).',

    'settings_section_sender' => 'Identitet pošiljaoca',
    'settings_section_sender_desc' => 'Podrazumevana email adresa i ime pošiljaoca za sve odlazne newslettere.',
    'settings_from_email' => 'Email adresa pošiljaoca',
    'settings_from_name' => 'Ime pošiljaoca',

    'settings_section_compliance' => 'Usklađenost i pravne norme (GDPR i CAN-SPAM)',
    'settings_double_opt_in' => 'Omogući dvostruku potvrdu prijave (Double Opt-In)',
    'settings_double_opt_in_helper' => 'Kada je uključeno, pretplatnici moraju kliknuti na verifikacioni link u svom sandučetu pre nego što prime kampanje (izričito preporučeno za GDPR).',
    'settings_company_address' => 'Fizička poštanska adresa kompanije',
    'settings_company_address_helper' => 'Poštanska adresa koja se prikazuje u podnožju svih marketinških poruka (zakonski obavezno po CAN-SPAM i međunarodnim standardima).',

    'settings_test_connection' => 'Testiraj vezu sa mailerom',
    'settings_recipient_email' => 'Email adresa primaoca',
    'settings_test_delivered' => 'Test email je uspešno isporučen na :email',
    'settings_test_failed' => 'Povezivanje sa mailerom nije uspelo',
    'settings_save_button' => 'Sačuvaj podešavanja',
    'settings_saved_success' => 'Podešavanja su uspešno sačuvana!',
];
