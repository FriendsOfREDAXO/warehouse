<?php

namespace FriendsOfRedaxo\Warehouse\Api;

use FriendsOfRedaxo\Warehouse\Session;
use rex;
use rex_api_function;
use rex_request;
use rex_response;

class BillingAddressApi extends rex_api_function
{
    protected $published = true;

    public function execute(): void
    {
        $action = rex_request('action', 'string');
        $response = ['success' => false, 'message' => ''];

        switch ($action) {
            case 'save':
                $this->saveBillingAddress();
                break;
            case 'get':
                $this->getBillingAddress();
                break;
            default:
                $response['message'] = 'Invalid action';
                break;
        }

        rex_response::cleanOutputBuffers();
        rex_response::sendContent(json_encode($response, JSON_UNESCAPED_UNICODE), 'application/json');
        exit;
    }

    private function saveBillingAddress(): void
    {
        $billingData = [
            'firstname' => rex_request('billing_address_firstname', 'string', ''),
            'lastname' => rex_request('billing_address_lastname', 'string', ''),
            'company' => rex_request('billing_address_company', 'string', ''),
            'department' => rex_request('billing_address_department', 'string', ''),
            'address' => rex_request('billing_address_address', 'string', ''),
            'zip' => rex_request('billing_address_zip', 'string', ''),
            'city' => rex_request('billing_address_city', 'string', ''),
            'country' => rex_request('billing_address_country', 'string', ''),
            'email' => rex_request('billing_address_email', 'string', ''),
            'phone' => rex_request('billing_address_phone', 'string', ''),
        ];

        // Filter empty values
        $billingData = array_filter($billingData, function($value) {
            return $value !== '';
        });

        if (!empty($billingData)) {
            Session::setBillingAddress($billingData);
            $response = ['success' => true, 'message' => 'Billing address saved successfully'];
        } else {
            $response = ['success' => false, 'message' => 'No billing address data provided'];
        }

        rex_response::cleanOutputBuffers();
        rex_response::sendContent(json_encode($response, JSON_UNESCAPED_UNICODE), 'application/json');
        exit;
    }

    private function getBillingAddress(): void
    {
        $billingData = Session::getBillingAddressData();
        $response = ['success' => true, 'data' => $billingData];

        rex_response::cleanOutputBuffers();
        rex_response::sendContent(json_encode($response, JSON_UNESCAPED_UNICODE), 'application/json');
        exit;
    }
}