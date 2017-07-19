<?php
namespace NYPL\HoldRequestResultConsumer\OAuthClient;


use NYPL\HoldRequestResultConsumer\Model\DataModel\Item;
use NYPL\Starter\APIException;
use NYPL\Starter\APILogger;
use NYPL\Starter\Config;
use NYPL\Starter\Model\Response\ErrorResponse;

class ItemClient extends APIClient
{
    /**
     * @param string $itemId
     * @param $nyplSource
     * @return null|Item
     * @throws APIException
     */
    public static function getItemByIdAndSource($itemId = '', $nyplSource)
    {
        $url = Config::get('API_ITEM_URL') . '/' . $nyplSource . '/' . $itemId;

        APILogger::addDebug('Retrieving item by Id and Source', (array) $url);

        $response = self::get($url);

        $response = json_decode((string) $response->getBody(), true);

        APILogger::addDebug(
            'Retrieve item by id and source',
            $response['data']
        );

        // Check statusCode range
        if ($response['statusCode'] === 200) {
            return new Item($response['data']);
        } elseif ($response['statusCode'] >= 500 && $response['statusCode'] <= 599) {
            throw new APIException(
                'Server Error',
                'getItemByIdAndSource met a server error',
                $response['statusCode'],
                null,
                $response['statusCode'],
                new ErrorResponse(
                    $response['statusCode'],
                    'internal-server-error',
                    'getItemByIdAndSource met a server error'
                )
            );
        } else {
            APILogger::addError(
                'Failed',
                array('Failed to retrieve item ', $itemId, $response['type'], $response['message'])
            );
            return null;
        }
    }
}
