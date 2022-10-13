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

function array2csv($data, $delimiter = ',', $enclosure = '"', $escape_char = "\\")
{
    $f = fopen('php://memory', 'r+');
    foreach ($data as $item) {
        fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
    }
    rewind($f);
    return stream_get_contents($f);
}

$input = fopen("customer_list.csv", "r");
$output = fopen("php://output",'w') or die("Can't open php://output");
header("Content-Type:application/csv"); 
header("Content-Disposition:attachment;filename=output.csv"); 

$ids = [];
while (($email = fgetcsv($input, 1000, ",")) !== FALSE) 
{
    $customer = Customer::search(
        $session,
        [],
        ["fields" => "id",'query' => 'email:' . $email[0]],
    );

    if($customer['customers'])
        array_push($ids, $customer['customers'][0]);
    else{
        print_r("error customer not found: " . $email[0] . "\n");
    }
     sleep(1);
}
// print_r($ids);
var_dump(array2csv($ids));
// fputcsv($output, $ids, ",");
fclose($input) or die("Can't close input");
fclose($output) or die("Can't close php://output");
?>