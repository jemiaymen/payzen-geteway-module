<?php

/**
 * WHMCS Sample Payment Gateway Module
 *
 * Payment Gateway modules allow you to integrate payment solutions with the
 * WHMCS platform.
 *
 * This sample file demonstrates how a payment gateway module for WHMCS should
 * be structured and all supported functionality it can contain.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "gatewaymodule" and therefore all functions
 * begin "payzengateway_".
 *
 * If your module or third party API does not support a given function, you
 * should not define that function within your module. Only the _config
 * function is required.
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/payment-gateways/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function payzengateway_MetaData()
{
    return array(
        'DisplayName' => 'Payzen Payment Gateway Module',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => false,
    );
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */
function payzengateway_config()
{
    return array(
        // the friendly display name for a payment gateway should be
        // defined here for backwards compatibility
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Payzen Third Party Payment Gateway Module',
        ),
        // a text field type allows for single line text input
        'accountID' => array(
            'FriendlyName' => 'Account ID',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your account ID here',
        ),
        // a password field type allows for masked text input
        'secretKey' => array(
            'FriendlyName' => 'Secret Key',
            'Type' => 'password',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter secret key here',
        ),
        // the yesno field type displays a single checkbox option
        'testMode' => array(
            'FriendlyName' => 'Test Mode',
            'Type' => 'yesno',
            'Description' => 'Tick to enable test mode',
        ),

        'signatureAlgo' => array(
            'FriendlyName' => 'Algorithme de signature',
            'Type' => 'dropdown',
            'Options' => array(
                'SHA-1' => 'SHA-1',
                'HMAC_SHA_256' => 'HMAC_SHA_256',
            ),
            'Description' => 'Choose one',
        ),

        'paymentType' => array(
            'FriendlyName' => 'Type de paiement',
            'Type' => 'dropdown',
            'Options' => array(
                'SINGLE' => '1 fois',
                'MULTI' => 'plusieurs fois',
            ),
            'Description' => 'Choose one',
        ),
    );
}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/third-party-gateway/
 *
 * @return string
 */
function payzengateway_link($params)
{
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secretKey = $params['secretKey'];
    $testMode = $params['testMode'] == True ? 'TEST'  : 'PRODUCTION';
    $signature_Algo = $params['signatureAlgo'];
    $paymentType = $params['paymentType'];


    

    // $currency = $params['currency'];


    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $username = $params['clientdetails']['username'];
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    $url = 'https://secure.payzen.eu/vads-payment/';

    $postfields = array();

    $postfields['vads_action_mode'] = "INTERACTIVE";
    $postfields['vads_ctx_mode'] = $testMode;
    $postfields['vads_amount'] = $amount;
    $postfields['vads_currency'] = $currencyCode;
    $postfields['vads_trans_id'] = $invoiceId;
    $postfields['vads_site_id'] = $accountId;
    $postfields['callback_url'] = $systemUrl . '/modules/gateways/callback/' . $moduleName . '.php';
    $postfields['return_url'] = $returnUrl;
    $postfields['vads_payment_config'] = $paymentType;
    $postfields['vads_version'] = "V2";
    $postfields['vads_page_action'] = "PAYMENT";


    $date = new DateTime();
    $date = $date->format("AAAAMMJJhhmmss");

    $postfields['vads_trans_date'] = $date;

    
    
    
    
    
    
    $postfields['vads_order_info'] = $description;
    $postfields['vads_cust_id'] = $username;
    
    $postfields['vads_cust_first_name'] = $firstname;
    $postfields['vads_cust_last_name'] = $lastname;
    $postfields['vads_cust_email'] = $email;
    $postfields['vads_cust_address'] = $address1;
    
    $postfields['vads_cust_city'] = $city;
    $postfields['vads_cust_state'] = $state;
    $postfields['vads_cust_zip'] = $postcode;
    $postfields['vads_cust_country'] = $country;
    $postfields['vads_cust_phone'] = $phone;


    if($signature_Algo == "SHA-1"){
        $signature = getSignature_SHA1($postfields,$secretKey);
    }else if($signature_Algo == "HMAC-SHA-256"){
        $signature = getSignature_HMAC_SHA_256($postfields,$secretKey);
    }

    $postfields['signature'] = $signature;

    // $postfields['address2'] = $address2;

    $htmlOutput = '<form method="post" action="' . $url . '">';
    foreach ($postfields as $k => $v) {
        $htmlOutput .= '<input type="hidden" name="' . $k . '" value="' . urlencode($v) . '" />';
    }
    $htmlOutput .= '<input type="submit" value="' . $langPayNow . '" />';
    $htmlOutput .= '</form>';

    return $htmlOutput;
}

/**
 * Refund transaction.
 *
 * Called when a refund is requested for a previously successful transaction.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/refunds/
 *
 * @return array Transaction response status
 */
function payzengateway_refund($params)
{
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secretKey = $params['secretKey'];
    $testMode = $params['testMode'];


    // Transaction Parameters
    $transactionIdToRefund = $params['transid'];
    $refundAmount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    // perform API call to initiate refund and interpret result

    return array(
        // 'success' if successful, otherwise 'declined', 'error' for failure
        'status' => 'success',
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $responseData,
        // Unique Transaction ID for the refund transaction
        'transid' => $refundTransactionId,
        // Optional fee amount for the fee value refunded
        'fees' => $feeAmount,
    );
}

/**
 * Cancel subscription.
 *
 * If the payment gateway creates subscriptions and stores the subscription
 * ID in tblhosting.subscriptionid, this function is called upon cancellation
 * or request by an admin user.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/subscription-management/
 *
 * @return array Transaction response status
 */
function payzengateway_cancelSubscription($params)
{
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secretKey = $params['secretKey'];
    $testMode = $params['testMode'];


    // Subscription Parameters
    $subscriptionIdToCancel = $params['subscriptionID'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    // perform API call to cancel subscription and interpret result

    return array(
        // 'success' if successful, any other value for failure
        'status' => 'success',
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $responseData,
    );
}


function getSignature_HMAC_SHA_256($params, $key)
{
    /**
     * Fonction qui calcule la signature.
     * $params : tableau contenant les champs à envoyer dans le formulaire.
     * $key : clé de TEST ou de PRODUCTION
     */
    //Initialisation de la variable qui contiendra la chaine à chiffrer
    $contenu_signature = "";
    //Tri des champs par ordre alphabétique
    ksort($params);
    foreach ($params as $nom => $valeur) {
        //Récupération des champs vads_
        if (substr($nom, 0, 5) == 'vads_') {
            //Concaténation avec le séparateur "+"
            $contenu_signature .= $valeur . "+";
        }
    }
    //Ajout de la clé en fin de chaine
    $contenu_signature .= $key;
    //Encodage base64 de la chaine chiffrée avec l'algorithme HMAC-SHA-256
    $signature = base64_encode(hash_hmac('sha256', $contenu_signature, $key, true));
    return $signature;
}


function getSignature_SHA1($params, $key)
{
    /**
     * Fonction qui calcule la signature.
     * $params : tableau contenant les champs à envoyer dans le formulaire.
     * $key : clé de TEST ou de PRODUCTION
     */
    //Initialisation de la variable qui contiendra la chaine à chiffrer
    $contenu_signature = "";

    // Tri des champs par ordre alphabétique
    ksort($params);
    foreach ($params as $nom => $valeur) {

        // Récupération des champs vads_
        if (substr($nom, 0, 5) == 'vads_') {

            // Concaténation avec le séparateur "+"
            $contenu_signature .= $valeur . "+";
        }
    }
    // Ajout de la clé à la fin
    $contenu_signature .= $key;

    // Application de l’algorythme SHA-1
    $signature = sha1($contenu_signature);
    return $signature;
}
