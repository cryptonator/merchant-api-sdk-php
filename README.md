# PHP Cryptonator.com Merchant API SDK

## Requirements

PHP 5.3 or above


## Links

1. Cryptonator MerchantAPI Help Center: [Ru](https://cryptonator.zendesk.com/hc/ru/categories/200829259),
[En](https://cryptonator.zendesk.com/hc/en-us/categories/200829259)

## Getting started

### Installation

1. Add `"cryptonator/merchant-php-sdk": "dev-master"` to `composer.json` of your application or clone repo to your project.
2. If you are using composer use `require_once 'vendor/autoload.php';` otherwise paste the following line
    ```php
    require_once '/path/to/cloned/repo/lib/MerchantAPI.php';
    ```

### Merchant API

Using Cryptonator MerchantAPI SDK requires the following steps

1. Paste the following code.
Note: constants `merchant_id` and `secret` you will find in your account settings once you have [set up a merchant account](https://www.cryptonator.com/auth/signup/) with Cryptonator.

    ```php
    use cryptonator\MerchantAPI;

    $cryptonator = new MerchantAPI(merchant_id, secret);
    ```

2. Now you can use Cryptonator MerchantAPI.

    ```php
    // start payment
    $url = $cryptonator->startPayment(array(
       'item_name'               => 'Item Name',
       //'order_id'              => 'Order ID',
       //'item_description'      => 'Item Description',
       'invoice_amount'          => 'Invoice Amount',
       'invoice_currency'        => 'Invoice Currency',
       //'success_url'           => 'Success URL',
       //'failed_url'            => 'Failed URL',
       //'language'              => 'Language',
    ));

    // create invoice
    $invoice = $cryptonator->createInvoice(array(
        'item_name'               => 'Item Name',
        //'order_id'              => 'Order ID',
        //'item_description'      => 'Item Description',
        'checkout_currency'       => 'Checkout Amount',
        'invoice_amount'          => 'Invoice Amount',
        'invoice_currency'        => 'Invoice Currency',
        //'success_url'           => 'Success URL',
        //'failed_url'            => 'Failed URL',
        //'language'              => 'Language',
     ));

    // get invoice
    $invoice = $cryptonator->getInvoice('InvoiceID');

    // list invoices
    $invoices = $cryptonator->listInvoices(array(
        //'invoice_status'       => 'Invoice Status',
        //'invoice_currency'     => 'Invoice Currency',
        //'checkout_currency'    => 'Checkout Currency',
    ));

    // check annswer
    $check_server = $cryptonator->checkAnswer($_POST);
    ```