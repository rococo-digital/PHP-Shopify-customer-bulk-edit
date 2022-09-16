<?php
require_once('vendor/autoload.php');
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Shopify\Auth\FileSessionStorage;
use Shopify\Auth\Session;
use Shopify\Rest\Admin2022_07\Customer;
use Shopify\Utils;

Shopify\Context::initialize(
    $_ENV['SHOPIFY_API_KEY'],
    $_ENV['SHOPIFY_API_SECRET'],
    ['read_customers'],
   'http://localhost',
    new FileSessionStorage('tmp/sessions'),
    '2022-07',
    false,
    false,
);

$session = new Session(
    id:'NA',
    shop: 'the-clinical-academy.myshopify.com',
    isOnline: false,
    state:'NA'
);
$session->setAccessToken($_ENV['SHOPIFY_ADMIN_API_ACCESS_TOKEN']);


$open = fopen("customer_list.csv", "r");
$data = fgetcsv($open, 1000, ",");

while (($email = fgetcsv($open, 1000, ",")) !== FALSE) 
{
    $customer = Customer::search(
        $session,
        [],
        ['query' => 'email:' . $email[0]],
    );
    var_dump($customer) ;
}

?>