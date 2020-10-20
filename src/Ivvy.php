<?php

namespace Ivvy;

use GuzzleHttp\Client;
use Ivvy\Model\Company;
use Ivvy\Model\Invoice;
use Ivvy\Model\InvoiceItem;
use Ivvy\Model\Address;
use Ivvy\Model\Contact;
use Ivvy\Model\Booking;

/**
 * Class: Ivvy
 */
class Ivvy
{
    const HOST = 'api.us-west-2.ivvy.com';
    const API_VERSION = '1.0';
    const BASE_URI = "https://" . self::HOST;

    /** @var Signature */
    private $signature;

    /** @var Client */
    private $client;

    /**
     * Creates a new instance
     *
     * @param string $apiKey
     * @param string $apiSecret
     * @param Signature $signature
     * @param Client $client
     */
    public function __construct(
        $apiKey,
        $apiSecret,
        Signature $signature,
        Client $client
    ) {
        $this->apiKey    = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->signature = $signature;
        $this->client    = $client;
    }

    /**
     * Pings the iVvy API service.
     *
     * @return bool - whether the connection was successful.
     */
    public function ping()
    {
        $requestUri = $this->createRequestUri('test', 'ping');

        $body = json_encode([]);

        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        return $response->getStatusCode() === 200;
    }

    /**
     * Run the passed jobs
     *
     * @param array $jobs
     * @return string|null - the async Id
     */
    public function run(array $jobs)
    {
        $requestUri = $this->createRequestUri('batch', 'run');
        $body = json_encode([
            'jobs' => array_map(
                function ($job) {
                    return $job->toArray();
                },
                $jobs
            ),
            'callbackUrl' => 'https://google.com',                  // TODO: remove this geeglo thing LEL
        ]);

        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        if ($response->getStatusCode() === 200) {
            $json = json_decode((string) $response->getBody());

            return $json->asyncId;
        } else {
            return null;
        }
    }

    /**
     * Gets the result of a batch job
     *
     * @param string $async - The asyncId for the batch job to show the results of
     *
     * @return array
     */
    public function result($async)
    {
        $requestUri = $this->createRequestUri('batch', 'results');
        $body = json_encode(compact('async'));
        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        $result = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            return array_merge(['success' => true], $result);
        } elseif ($response->getStatusCode() === 400 && $result['specificCode'] === 24114) {
            return ['success' => false, 'error' => 'not_completed'];
        } else {
            return ['success' => false, 'error' => 'unknown'];
        }
    }

    /**
     * Gets all the companies.
     *
     * @return array<Company>|null
     */
    public function getCompanyList()
    {
        $requestUri = $this->createRequestUri('contact', 'getCompanyList');
        $body = json_encode([]);
        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        $result = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            return array_map(function ($singleResult) {
                $companyData = array_merge($singleResult, ['address' => new Address($singleResult['address'])]);

                return new Company($companyData);
            }, $result['results']);
        } else {
            return null;
        }
    }
    /**
     * Gets all the companies.
     *
     * @param int $perPage - number of Companies per page
     * @param int $start - start page
     *
     * @return array<Company>|null
     */
    public function getCompanyListPage(int $perPage, int $start)
    {
        if (is_null($perPage)) {
            $perPage = 100;
        }
        if (is_null($start)) {
            $start = 0;
        }
        $requestUri = $this->createRequestUri('contact', 'getCompanyList');
        $body = json_encode(compact('perPage', 'start'));
        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        $result = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            return array_map(function ($singleResult) {
                $companyData = array_merge($singleResult, ['address' => new Address($singleResult['address'])]);

                return new Company($companyData);
            }, $result['results']);
        } else {
            return null;
        }
    }
    /**
     * Get the company with the specified Id.
     *
     * @param int $id
     *
     * @return company|null
     */
    public function getCompany(int $id)
    {
        $requestUri = $this->createRequestUri('contact', 'getCompany');
        $body = json_encode(compact('id'));
        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        $result = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            return new Company($result);
        } else {
            return null;
        }
    }

    /**
     * Gets all the contacts. It doesn't support pagination yet.
     *
     * @return array<Contact>|null
     */
    public function getContactList()
    {
        $requestUri = $this->createRequestUri('contact', 'getContactList');
        $body = json_encode([]);
        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        $result = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            return array_map(function ($contactData) {
                return new Contact($contactData);
            }, $result['results']);
        } else {
            return null;
        }
    }
    /**
     * Gets all the contacts.
     *
     * @param int $perPage - number of Contacts per page
     * @param int $start - start page
     *
     * @return array<Company>|null
     */
    public function getContactListPage(int $perPage, int $start)
    {
        if (is_null($perPage)) {
            $perPage = 100;
        }
        if (is_null($start)) {
            $start = 0;
        }
        $requestUri = $this->createRequestUri('contact', 'getContactList');
        $body = json_encode(compact('perPage', 'start'));
        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        $result = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            return array_map(function ($contactData) {
                return new Contact($contactData);
            }, $result['results']);
        } else {
            return null;
        }
    }
    /**
     * Get the contact with the specified Id.
     *
     * @param int $id
     *
     * @return contact|null
     */
    public function getContact(int $id)
    {
        $requestUri = $this->createRequestUri('contact', 'getContact');
        $body = json_encode(compact('id'));
        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        $result = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            return new Contact($result);
        } else {
            return null;
        }
    }

    /**
     * Gets all the invoices. It doesn't support pagination yet.
     *
     * @return array<Invoice>|null
     */
    public function getInvoiceList()
    {
        $requestUri = $this->createRequestUri('invoice', 'getInvoiceList');
        $body = json_encode([]);
        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        $result = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            return array_map(function ($invoiceData) {
                return new Invoice($invoiceData);
            }, $result['results']);
        } else {
            return null;
        }
    }
    /**
     * Gets all the invoices. It doesn't support pagination yet.
     *
     * @param string $fromModifiedDate
     *
     * @return array<Invoice>|null
     */
    public function getInvoiceListFromDate($fromModifiedDate)
    {
        $requestUri = $this->createRequestUri('invoice', 'getInvoiceList');
        if (!is_null($fromModifiedDate)) {
            $filter = compact('fromModifiedDate');
            $body = json_encode(compact('filter'));
        } else {
            $body = json_encode([]);
        }
        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        $result = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            return array_map(function ($invoiceData) {
                return new Invoice($invoiceData);
            }, $result['results']);
        } else {
            return null;
        }
    }
    /**
     * Get the invoice with the specified Id.
     *
     * @param int $id
     *
     * @return Invoice|null
     */
    public function getInvoice(int $id)
    {
        $requestUri = $this->createRequestUri('invoice', 'getInvoice');
        $body = json_encode(compact('id'));
        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        $result = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            $invoiceData = array_merge($result, ['items' => array_map(function ($itemData) {
                return new InvoiceItem($itemData);
            }, $result['items'])]);

            return new Invoice($invoiceData);
        } else {
            return null;
        }
    }

    /**
     * Get the booking with the specified Id.
     *
     * @param int $id
     *
     * @return Booking|null
     */
    public function getBooking(int $id)
    {
        $requestUri = $this->createRequestUri('venue', 'getBooking');
        $body = json_encode(compact('id'));
        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        $result = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            return new Booking($result);
        } else {
            return null;
        }
    }

    /**
     * Get a list of options for the refTypes
     *
     * @return array
     */
    public function getOptions()
    {
        $requestUri = $this->createRequestUri('invoice', 'getOptions');
        $body = json_encode([]);
        $headers = $this->createHeaders($body, $requestUri);

        $response = $this->client->request('POST', $requestUri, compact('body', 'headers'));

        $result = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Creates a request URI string from the passed namespace and action
     *
     * @param string $namespace
     * @param string $action
     *
     * @return string
     */
    protected function createRequestUri($namespace, $action)
    {
        $apiVersion = self::API_VERSION; // It just looks cooler this way

        return "/api/{$apiVersion}/{$namespace}?action={$action}";
    }

    /**
     * Creates the request headers
     *
     * @param string $body
     * @param string $requestUri
     *
     * @return array
     */
    protected function createHeaders($body, $requestUri)
    {
        $contentType = 'application/json';
        $contentMd5 = md5($body);
        $ivvyDate = $this->createIvvyDate();

        $signature = $this->signature->sign(
            $this->apiSecret,
            $contentMd5,
            $requestUri,
            ['IVVY-Date' => $ivvyDate]
        );

        return [
            'Content-Type' => $contentType,
            'Content-MD5' => $contentMd5,
            'IVVY-Date' => $ivvyDate,
            'X-Api-Version' => self::API_VERSION,
            'X-Api-Authorization' => "IWS {$this->apiKey}:{$signature}",
        ];
    }

    /**
     * Creates a date with the format specified by iVvy.
     *
     * @return string
     */
    protected function createIvvyDate()
    {
        return date('Y-m-d hh:mm:ss');
    }
}
