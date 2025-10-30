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
            \FriendsOfRedaxo\Warehouse\Customer::FIRSTNAME => rex_request('billing_address_firstname', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::LASTNAME => rex_request('billing_address_lastname', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::COMPANY => rex_request('billing_address_company', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::DEPARTMENT => rex_request('billing_address_department', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::ADDRESS => rex_request('billing_address_address', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::ZIP => rex_request('billing_address_zip', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::CITY => rex_request('billing_address_city', 'string', ''),
            'country' => rex_request('billing_address_country', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::EMAIL => rex_request('billing_address_email', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::PHONE => rex_request('billing_address_phone', 'string', ''),
        ];

        // Filter empty values
        $billingData = array_filter($billingData, function(string $value) {
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