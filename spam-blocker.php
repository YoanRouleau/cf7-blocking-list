<?php

include($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

// Check for plugin using plugin name
if(!in_array( 'wp-contact-form-7-spam-blocker/spam-protect-for-contact-form7.php', apply_filters('active_plugins', get_option('active_plugins') ))){ 
    exit("Plugin spam protect manquant ou non actif.");
}

// Curl request to retrieve rules data
$curl = curl_init();

curl_setopt_array($curl, [
CURLOPT_URL => "https://raw.githubusercontent.com/YoanRouleau/cf7-blocking-list/main/rules.json",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => "",
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 30,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => "GET",
CURLOPT_POSTFIELDS => "",
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $keywords = json_decode($response, true);;
}

// Retrieving cf7 forms
$forms_id = get_posts([
    'post_type' => 'wpcf7_contact_form',
    'fields' => 'ids'
]);

// Updating CF7 blocking meta values
foreach($forms_id as $id){
    update_post_meta( $id, '_wpcf7_block_email_list', sanitize_text_field( $keywords['blocked_emails_list_str'] ) );
    update_post_meta( $id, '_wpcf7_block_email_domain', sanitize_text_field( $keywords['blocked_emails_domain_str'] ) );
    update_post_meta( $id, '_wpcf7_block_words', sanitize_text_field( $keywords['blocked_emails_words_str'] ) );
    echo "Formulaire ".$id." mis à jour.<br>";
}