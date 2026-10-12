<?php

return [
    // Public block & subscription
    'block_title' => 'Iscrizione alla newsletter',
    'block_heading' => 'Titolo',
    'block_subtitle' => 'Sottotitolo',
    'block_description' => 'Descrizione',
    'block_button_text' => 'Testo pulsante',
    'block_disclaimer' => 'Informativa sulla privacy',
    'block_default_title' => 'Rimani aggiornato con le nostre ultime novità',
    'block_default_subtitle' => 'Iscriviti alla nostra newsletter per ricevere novità, approfondimenti e offerte speciali direttamente nella tua casella di posta.',
    'block_default_description' => 'Iscriviti alla nostra newsletter per ricevere novità, approfondimenti e offerte speciali direttamente nella tua casella di posta.',
    'block_default_button' => 'Iscriviti',
    'block_default_disclaimer' => 'Rispettiamo la tua privacy. Puoi annullare l\'iscrizione in qualsiasi momento con un clic.',
    'email_placeholder' => 'Inserisci il tuo indirizzo email...',
    'subscribed_success' => 'Grazie per esserti iscritto!',
    'already_subscribed' => 'Sei già iscritto alla nostra newsletter.',
    'confirm_email_sent' => 'Controlla la tua casella di posta e conferma la tua iscrizione.',
    'submission_failed' => 'Si è verificato un errore. Riprova più tardi.',

    // Verification & Unsubscribe public pages
    'confirm_subject' => 'Conferma la tua iscrizione a :app',
    'confirm_title' => 'Conferma la tua iscrizione',
    'confirm_greeting' => 'Ciao :name,',
    'confirm_instruction' => 'Grazie per esserti iscritto alla nostra newsletter. Fai clic sul pulsante sottostante per verificare il tuo indirizzo email e completare l\'iscrizione.',
    'confirm_button' => 'Conferma la mia iscrizione',
    'confirm_trouble' => 'Se hai problemi a fare clic sul pulsante, copia e incolla questo URL nel tuo browser:',
    'confirm_ignore' => 'Se non hai richiesto questa iscrizione, ignora semplicemente questa email.',
    'sent_to' => 'Questa email è stata inviata a :email',

    'verified_page_title' => 'Iscrizione confermata',
    'verify_success_title' => 'Iscrizione confermata!',
    'verify_success_message' => 'Grazie! Il tuo indirizzo email è stato verificato e la tua iscrizione alla newsletter è ora attiva.',
    'verify_invalid_or_expired' => 'Questo link di verifica non è valido, è scaduto o è già stato utilizzato.',
    'notice' => 'Avviso',
    'back_to_home' => 'Torna alla Home',

    'unsubscribed_page_title' => 'Disiscritto',
    'unsubscribed_title' => 'Sei stato disiscritto con successo',
    'unsubscribed_desc' => ':email è stato rimosso dalla nostra mailing list. Non riceverai più email promozionali da noi.',
    'unsubscribed_success' => 'Sei stato disiscritto dalla nostra newsletter.',

    // Email layout & campaign
    'footer_reason' => 'Ricevi questa email perché ti sei iscritto agli aggiornamenti sul nostro sito.',
    'unsubscribe' => 'Disiscriviti',

    // Test connection
    'test_connection_subject' => 'Email di prova da Celios CMS Newsletter',
    'test_connection_body' => "Questa è un'email di prova inviata da :app per verificare la configurazione della posta.\n\nData e ora: :date\nSe hai ricevuto questa email, la configurazione funziona correttamente!",
    'test_connection_success' => 'Email di prova inviata con successo!',
    'test_connection_failed' => 'Invio dell\'email di prova non riuscito: :error',

    // Statuses
    'status_pending' => 'In attesa di conferma',
    'status_active' => 'Attivo',
    'status_unsubscribed' => 'Disiscritto',
    'status_bounced' => 'Rimbalzato (Bounced)',

    'campaign_status_draft' => 'Bozza',
    'campaign_status_scheduled' => 'Pianificata',
    'campaign_status_sending' => 'In invio',
    'campaign_status_sent' => 'Inviata',
    'campaign_status_failed' => 'Fallita',
    'campaign_status_cancelled' => 'Annullata',

    // Settings Page
    'settings_section_transport' => 'Trasporto email e flessibilità hosting',
    'settings_section_transport_desc' => 'Scegli se inviare le newsletter tramite il tuo server di hosting / SMTP personalizzato o tramite un servizio cloud dedicato (Amazon SES, Resend, ecc.).',
    'settings_mailer_type' => 'Provider di trasporto email',
    'settings_mailer_app_default' => 'Predefinito applicazione (utilizza configurazione .env Laravel)',
    'settings_mailer_custom_smtp' => 'SMTP personalizzato (Server hosting / cPanel / Plesk)',
    'settings_mailer_ses' => 'Amazon SES (AWS)',
    'settings_mailer_resend' => 'Resend (API/SMTP)',
    'settings_mailer_mailgun' => 'Mailgun',
    'settings_smtp_host' => 'Host SMTP',
    'settings_smtp_port' => 'Porta SMTP',
    'settings_smtp_username' => 'Nome utente SMTP',
    'settings_smtp_password' => 'Password SMTP',
    'settings_smtp_encryption' => 'Crittografia',
    'settings_encryption_tls' => 'TLS (STARTTLS)',
    'settings_encryption_ssl' => 'SSL',
    'settings_encryption_none' => 'Nessuna',

    'settings_section_rate_limit' => 'Limitazione della velocità di invio e throttling',
    'settings_section_rate_limit_desc' => 'Proteggi il tuo server hosting o provider email da blocchi o inserimenti in blacklist.',
    'settings_rate_limit' => 'Email massime al minuto',
    'settings_rate_limit_helper' => 'Consigliato per hosting condiviso / cPanel: 20-30 email/min. Imposta su 0 per invio illimitato / veloce (Amazon SES, Resend, VPS).',

    'settings_section_sender' => 'Identità del mittente',
    'settings_section_sender_desc' => 'Indirizzo mittente e nome visualizzato predefiniti per tutte le newsletter in uscita.',
    'settings_from_email' => 'Indirizzo email mittente',
    'settings_from_name' => 'Nome mittente visualizzato',

    'settings_section_compliance' => 'Conformità e note legali (GDPR e CAN-SPAM)',
    'settings_double_opt_in' => 'Abilita doppio consenso (Email di verifica)',
    'settings_double_opt_in_helper' => 'Se abilitato, gli iscritti devono fare clic su un link di verifica prima di ricevere le campagne (fortemente consigliato per il GDPR).',
    'settings_company_address' => 'Indirizzo postale fisico',
    'settings_company_address_helper' => 'Indirizzo postale fisico visualizzato nel piè di pagina di tutte le email di marketing (legalmente richiesto da CAN-SPAM e dagli standard internazionali).',

    'settings_test_connection' => 'Verifica connessione mailer',
    'settings_recipient_email' => 'Email del destinatario',
    'settings_test_delivered' => 'Un\'email di prova è stata consegnata a :email',
    'settings_test_failed' => 'Connessione al mailer non riuscita',
    'settings_save_button' => 'Salva impostazioni',
    'settings_saved_success' => 'Impostazioni salvate con successo!',
];
