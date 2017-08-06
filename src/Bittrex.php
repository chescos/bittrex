<?php

namespace chescos\Bittrex;

use GuzzleHttp\Client;

class Bittrex
{
    /**
     * The API key used for authentication.
     *
     * @var string
     */
    private $key;

    /**
     * The API secret used for authentication.
     *
     * @var string
     */
    private $secret;

    /**
     * The Guzzle client instance.
     * @var object
     */
    private $client;

    /**
     * The API base uri
     * @var string
     */
    private $baseUri;

    /**
     * Create a new Bittrex instance.
     *
     * @param string $key
     * @param string $secret
     */
    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;

        $this->baseUri = 'https://bittrex.com/api/v1.1/';

        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'timeout'  => 10.0,
        ]);
    }

    /**
     * Used to get the open and available trading markets at Bittrex along with other meta data.
     *
     * @return array
     */
    public function getMarkets()
    {
        return $this->request('public/getmarkets');
    }

    /**
     * Used to get all supported currencies at Bittrex along with other meta data.
     *
     * @return array
     */
    public function getCurrencies()
    {
        return $this->request('public/getcurrencies');
    }

    /**
     * Used to get the current tick values for a market.
     *
     * @param string $market
     *
     * @return array
     */
    public function getTicker($market)
    {
        return $this->request('public/getticker', compact('market'));
    }

    /**
     * Used to get the last 24 hour summary of all active exchanges.
     *
     * @return array
     */
    public function getMarketSummaries()
    {
        return $this->request('public/getmarketsummaries');
    }

    /**
     * Used to get the last 24 hour summary of all active exchanges.
     *
     * @param string $market
     *
     * @return array
     */
    public function getMarketSummary($market)
    {
        return $this->request('public/getmarketsummary', compact('market'));
    }

    /**
     * Used to get retrieve the orderbook for a given market.
     *
     * @param string  $market
     * @param string  $type
     * @param int|int $depth
     *
     * @return array
     */
    public function getOrderBook($market, $type, $depth = 20)
    {
        return $this->request('public/getorderbook', compact('market', 'type', 'depth'));
    }

    /**
     * Used to retrieve the latest trades that have occured for a specific market.
     *
     * @param string $market
     *
     * @return array
     */
    public function getMarketHistory($market)
    {
        return $this->request('public/getmarkethistory', compact('market'));
    }

    /**
     * Used to place a buy order in a specific market.
     *
     * @param string $market
     * @param int    $quantity
     * @param float  $rate
     *
     * @return array
     */
    public function buyLimit($market, $quantity, $rate)
    {
        return $this->request('market/buylimit', compact('market', 'quantity', 'rate'));
    }

    /**
     * Used to place an sell order in a specific market.
     *
     * @param string $market
     * @param int    $quantity
     * @param float  $rate
     *
     * @return array
     */
    public function sellLimit($market, $quantity, $rate)
    {
        return $this->request('market/selllimit', compact('market', 'quantity', 'rate'));
    }

    /**
     * Used to cancel a buy or sell order.
     *
     * @param string $uuid
     *
     * @return array
     */
    public function cancel($uuid)
    {
        return $this->request('market/cancel', compact('uuid'));
    }

    /**
     * Get all orders that you currently have opened.
     *
     * @param string|null $market
     *
     * @return array
     */
    public function getOpenOrders($market = null)
    {
        return $this->request('market/getopenorders', compact('market'));
    }

    /**
     * Used to retrieve all balances from your account.
     *
     * @return array
     */
    public function getBalances()
    {
        return $this->request('account/getbalances');
    }

    /**
     * Used to retrieve the balance from your account for a specific currency.
     *
     * @param string $currency
     *
     * @return array
     */
    public function getBalance($currency)
    {
        return $this->request('account/getbalance', compact('currency'));
    }

    /**
     * Used to retrieve or generate an address for a specific currency.
     *
     * @param string $currency
     *
     * @return array
     */
    public function getDepositAddress($currency)
    {
        return $this->request('account/getdepositaddress', compact('currency'));
    }

    /**
     * Used to withdraw funds from your account.
     *
     * @param string      $currency
     * @param float       $quantity
     * @param string      $address
     * @param string|null $paymentid
     *
     * @return array
     */
    public function withdraw($currency, $quantity, $address, $paymentid = null)
    {
        return $this->request('account/withdraw', compact('currency', 'quantity', 'address', 'paymentid'));
    }

    /**
     * Used to retrieve a single order by uuid.
     *
     * @param string $uuid
     *
     * @return array
     */
    public function getOrder($uuid)
    {
        return $this->request('account/getorder', compact('uuid'));
    }

    /**
     * Used to retrieve your order history.
     *
     * @param string|null $market
     *
     * @return array
     */
    public function getOrderHistory($market = null)
    {
        return $this->request('account/getorderhistory', compact('market'));
    }

    /**
     * Used to retrieve your withdrawal history.
     *
     * @param string $currency
     *
     * @return array
     */
    public function getWithdrawalHistory($currency)
    {
        return $this->request('account/getwithdrawalhistory');
    }

    /**
     * Create and send an HTTP request.
     *
     * @param string $path
     * @param array  $arguments
     *
     * @return array
     */
    private function request($path, $arguments = [])
    {
        $nonce = time();

        $options = [
            'query'     => [
                'apikey'    => $this->key,
                'nonce'     => $nonce
            ]
        ];

        $options['query'] = array_merge($options['query'], $arguments);

        $uri = $this->baseUri . $path . '?' . http_build_query($options['query']);

        $sign = hash_hmac('sha512', $uri, $this->secret);

        $options['headers'] = [
            'apisign' => $sign
        ];

        $response = $this->client->request('GET', $path, $options);

        $result = json_decode($response->getBody(), true);

        return $result;
    }
}
