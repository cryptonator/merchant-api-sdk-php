<?php

namespace cryptonator;

use cryptonator\exceptions\ServerError;

require_once __DIR__ . '/BaseAPI.php';

class MerchantAPI extends BaseAPI
{
    private $merchant_id;
    private $secret;

    function __construct($merchant_id, $secret)
    {
        $this->merchant_id = $merchant_id;
        $this->secret = $secret;
    }

    /**
     * Создание счета с возможностью выбора криптовалюты оплаты.
     * Возвращает ссылку на страницу платежа.
     * @see https://www.cryptonator.com/hc/ru/articles/207346169-Метод-startpayment
     * @param $options array обязательные ключи item_name, invoice_currency, invoice_amount
     * @return string
     * @throws ServerError
     */
    public function startPayment($options)
    {
        if (isset($options['item_name'], $options['invoice_currency'], $options['invoice_amount'])) {
            $options['merchant_id'] = $this->merchant_id;

            return self::API_URL . 'startpayment?' . http_build_query($options);
        }
        else {
            throw new ServerError('Неверные параметры. Ообязательные параметры item_name, invoice_currency, invoice_amount', 0);
        }
    }

    /**
     * Создание счета на оплату, возвращает ID счета.
     * Если ответ не проходит проверку secret_hash возвращает null.
     * @see https://www.cryptonator.com/hc/ru/articles/208018135-Метод-createinvoice
     * @param $options array
     * @return mixed
     */
    public function createInvoice($options)
    {
        $result_array = array(
            'merchant_id'           => $this->merchant_id,
            'item_name'             => '',
            'order_id'              => '',
            'item_description'      => '',
            'checkout_currency'     => '',
            'invoice_amount'        => '',
            'invoice_currency'      => '',
            'success_url'           => '',
            'failed_url'            => '',
            'confirmation_policy'   => '',
            'language'              => '',
        );

        $this->fillResultArray($result_array, $options);
        $result_array['secret_hash'] = $this->generateHash($result_array);

        return $this->hashCheck($this->sendReqest('createinvoice', $result_array));
    }

    /**
     * Получение информации о счете
     * @see https://www.cryptonator.com/hc/ru/articles/208018455-Метод-getinvoice
     * @param $id string
     * @return mixed
     */
    public function getInvoice($id)
    {
        $result_array = array(
            'merchant_id' => $this->merchant_id,
            'invoice_id' => $id
        );


        $result_array['secret_hash'] = $this->generateHash($result_array);

        return $this->sendReqest('getinvoice', $result_array);
    }

    /**
     * Получение списка счетов, удовлетворяющих условиию
     * @see https://www.cryptonator.com/hc/ru/articles/208018895-Метод-listinvoices
     * @param $options array
     * @return mixed
     */
    public function listInvoices($options = array())
    {
        $result_array = array(
            'merchant_id' => $this->merchant_id,
            'invoice_status' => '',
            'invoice_currency' => '',
            'checkout_currency' => '',
        );

        $this->fillResultArray($result_array, $options);
        $result_array['secret_hash'] = $this->generateHash($result_array);

        return $this->sendReqest('listinvoices', $result_array);
    }

    /**
     * Проверка присланных данных о оплате счета на основании secret_hash.
     * true - данные прошли проверку, false - не прошли.
     * @see https://www.cryptonator.com/hc/ru/articles/207291239-HTTP-уведомления
     * @param $array array параметры POST
     * @return bool
     */
    public function checkAnswer($array)
    {
        $hash = $array['secret_hash'];
        unset($array['secret_hash']);

        $generate = $this->generateHash($array);

        if ($generate !== null && $hash == $this->generateHash($array)) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Заполняет массив элементами массива-донора
     * если в массиве-доноре нет соответствующего ключа, то значение в массиве не изменяется
     * @param $result array исходный массив
     * @param $options array массив-донор
     * @return bool
     */
    private function fillResultArray(&$result, $options) {
        if (is_array($result) && is_array($options)) {
            foreach ($result as $key => $r) {
                if (isset($options[$key])) {
                    $result[$key] = $options[$key];
                }

                unset($key, $r);
            }

            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Генерация кэш-строки
     * @see https://www.cryptonator.com/hc/ru/articles/207300279-Проверка-подлинности-HTTP-уведомлений
     * @param $array array массив параметров для отправки на сервер
     * @return null|string
     */
    private function generateHash($array)
    {
        if (is_array($array) && count($array) > 0) {
            $string = implode('&', $array) . '&' . $this->secret;

            return sha1($string);
        }
        else {
            return null;
        }
    }

    /**
     * Проверка присланных данных севером на основании secret_hash
     * @param $body
     * @return null
     */
    private function hashCheck($body)
    {
        $hash = $body['secret_hash'];
        unset($body['secret_hash']);

        if ($hash == sha1(implode('&', $body) . '&' . $this->secret)) {
            return $body;
        }
        else {
            return null;
        }
    }
}