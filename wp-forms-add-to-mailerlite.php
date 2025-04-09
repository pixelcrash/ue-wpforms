<?php 

add_action( 'wpforms_process_complete', 'wpforms_to_mailerlite_subscription', 10, 4 );

function wpforms_to_mailerlite_subscription( $fields, $entry, $form_data, $entry_id ) {
    // Change to your form ID
    $form_id = 123; 
    
    // Return if Form ID is different 
    if ( $form_data['id'] != $form_id ) {
        return;
    }
    
    // Set you checkbox id (do you want to be on our newsletter list)
    $checkbox_field_id = '10';
    
    // ID from the E-Mail field
    $email_field_id = '5'; 
    
    // Use for first- and last name
    $first_name_field_id = '3';
    $last_name_field_id = '4';
    
    // check if checkbox is checked (check one - two) 
    if ( 
        isset( $fields[$checkbox_field_id] ) && 
        !empty( $fields[$checkbox_field_id]['value'] ) && 
        $fields[$checkbox_field_id]['value'] == '1' 
    ) {
        // Get E-Mail from field 
        $email = isset( $fields[$email_field_id] ) ? $fields[$email_field_id]['value'] : '';
        
        if ( empty( $email ) || !is_email( $email ) ) {
            // Has to be an email
            return;
        }
        
        // Optional: Get Names
        $first_name = isset( $fields[$first_name_field_id] ) ? $fields[$first_name_field_id]['value'] : '';
        $last_name = isset( $fields[$last_name_field_id] ) ? $fields[$last_name_field_id]['value'] : '';
        
        // Api Key define in wp-config.php with define('API_MAILERLITE', 'api_key')
        $api_key = API_MAILERLITE;
        
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
