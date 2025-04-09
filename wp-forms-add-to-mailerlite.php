<?php 
// Diesen Code in die functions.php Ihres Themes oder in ein eigenes Plugin einfügen
add_action( 'wpforms_process_complete', 'wpforms_to_mailerlite_subscription', 10, 4 );

function wpforms_to_mailerlite_subscription( $fields, $entry, $form_data, $entry_id ) {
    // Ersetzen Sie diese ID mit der ID Ihres Formulars
    $form_id = 123; 
    
    // Nur für das spezifische Formular ausführen
    if ( $form_data['id'] != $form_id ) {
        return;
    }
    
    // Ersetzen Sie '10' mit der ID Ihres Checkbox-Feldes
    $checkbox_field_id = '10';
    
    // Ersetzen Sie '5' mit der ID Ihres E-Mail-Feldes
    $email_field_id = '5'; 
    
    // Ersetzen Sie '3' und '4' mit den IDs Ihrer Vor- und Nachname-Felder (falls vorhanden)
    $first_name_field_id = '3';
    $last_name_field_id = '4';
    
    // Prüfen, ob die Checkbox angekreuzt wurde
    if ( 
        isset( $fields[$checkbox_field_id] ) && 
        !empty( $fields[$checkbox_field_id]['value'] ) && 
        $fields[$checkbox_field_id]['value'] == '1' 
    ) {
        // E-Mail-Adresse aus den Feldern holen
        $email = isset( $fields[$email_field_id] ) ? $fields[$email_field_id]['value'] : '';
        
        if ( empty( $email ) || !is_email( $email ) ) {
            // Wenn keine gültige E-Mail gefunden wurde
            return;
        }
        
        // Optional: Namen aus den Feldern holen
        $first_name = isset( $fields[$first_name_field_id] ) ? $fields[$first_name_field_id]['value'] : '';
        $last_name = isset( $fields[$last_name_field_id] ) ? $fields[$last_name_field_id]['value'] : '';
        
        // MailerLite API-Schlüssel - Ersetzen Sie dies mit Ihrem API-Schlüssel
        $api_key = 'IHRE_API_KEY_HIER';
        
        // MailerLite Gruppen-ID - Ersetzen Sie mit Ihrer Gruppen-ID
        $group_id = 'IHRE_GRUPPEN_ID_HIER';
        
        // Daten für die API vorbereiten
        $data = array(
            'email' => $email,
            'name' => $first_name . ' ' . $last_name,
            'fields' => array(
                'name' => $first_name,
                'last_name' => $last_name
            )
        );
        
        // API-Anfrage an MailerLite senden
        $response = wp_remote_post(
            "https://api.mailerlite.com/api/v2/groups/{$group_id}/subscribers",
            array(
                'headers' => array(
                    'X-MailerLite-ApiKey' => $api_key,
                    'Content-Type' => 'application/json'
                ),
                'body' => json_encode( $data )
            )
        );
        
        // Optional: Fehler protokollieren
        if ( is_wp_error( $response ) ) {
            // Fehler protokollieren
            error_log( 'MailerLite API Fehler: ' . $response->get_error_message() );
        }
    }
}
?>
