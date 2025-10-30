<?php

namespace FriendsOfRedaxo\Warehouse\Api;

use FriendsOfRedaxo\Warehouse\Session;
use rex;
use rex_api_function;
use rex_request;
use rex_response;

class ShippingAddressApi extends rex_api_function
{
    protected $published = true;

    public function execute(): void
    {
        $action = rex_request('action', 'string');
        $response = ['success' => false, 'message' => ''];

        switch ($action) {
            case 'save':
                $this->saveShippingAddress();
                break;
            case 'get':
                $this->getShippingAddress();
                break;
            default:
                $response['message'] = 'Invalid action';
                break;
        }

        rex_response::cleanOutputBuffers();
        rex_response::sendContent(json_encode($response, JSON_UNESCAPED_UNICODE), 'application/json');
        exit;
    }

    private function saveShippingAddress(): void
    {
        $shippingData = [
            \FriendsOfRedaxo\Warehouse\Customer::FIRSTNAME => rex_request('shipping_address_firstname', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::LASTNAME => rex_request('shipping_address_lastname', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::COMPANY => rex_request('shipping_address_company', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::ADDRESS => rex_request('shipping_address_address', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::ZIP => rex_request('shipping_address_zip', 'string', ''),
            \FriendsOfRedaxo\Warehouse\Customer::CITY => rex_request('shipping_address_city', 'string', ''),
            'country' => rex_request('shipping_address_country', 'string', ''),
        ];

        // Filter empty values
        $shippingData = array_filter($shippingData, function(string $value) {
            return $value !== '';
        });

        if (!empty($shippingData)) {
            Session::setShippingAddress($shippingData);
            $response = ['success' => true, 'message' => 'Shipping address saved successfully'];
        } else {
            $response = ['success' => false, 'message' => 'No shipping address data provided'];
        }

        rex_response::cleanOutputBuffers();
        rex_response::sendContent(json_encode($response, JSON_UNESCAPED_UNICODE), 'application/json');
        exit;
    }

    private function getShippingAddress(): void
    {
        $shippingData = Session::getShippingAddressData();
        $response = ['success' => true, 'data' => $shippingData];

        rex_response::cleanOutputBuffers();
        rex_response::sendContent(json_encode($response, JSON_UNESCAPED_UNICODE), 'application/json');
        exit;
    }
}