<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetOrdersRequest;
use Illuminate\Support\Facades\Request;
use App\Http\Clients\OrderRetrievableContract;

class OrderController extends Controller
{
    /**
     * Returns orders for the specified customer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(GetOrdersRequest $request, OrderRetrievableContract $client) : JsonResponse
    {
        $customer = request('customer');

        $response = $client->index();

        /**
         * SECTION 2 - THE MAPPERS
         */

        // TODO: This class should be responsible for receiving and returning a response - is there a better place to put response mappings?
        // TODO: We are switching response mappings because there is a different response structure for each customer. This means adding more if statements - is there an OOP way to do this?
        // TODO: Bonus - Add the currency as iso-code to the response. Currency comes back differently for Dropify - assume 1=GBP,2=EUR,3=USD - try and convert this using what we've covered
        if($customer === 'W') {
            $mappedResponse = collect(Arr::get($response, 'orders'))->map(function ($order) {
                return [
                    'order_id' => Arr::get($order, 'id'),
                    'origin' => Arr::get($order, 'origin'),
                    'street' => Arr::get($order, 'address.street'),
                    'city' => Arr::get($order, 'address.city'),
                    'county' => Arr::get($order, 'address.state'),
                    'country' => Arr::get($order, 'address.country'),
                ];
            });
        } else if($customer === 'X') {
            $mappedResponse = collect(Arr::get($response, 'orders'))->map(function ($order) {
                return [
                    'order_id' => Arr::get($order, 'id'),
                    'origin' => Arr::get($order, 'origin'),
                    'street' => Arr::get($order, 'line1'),
                    'city' => Arr::get($order, 'city'),
                    'county' => Arr::get($order, 'state'),
                    'country' => Arr::get($order, 'country'),
                ];
            });
        } else if($customer === 'Y') {
            $mappedResponse = collect(Arr::get($response, 'orders'))->map(function ($order) {
                return [
                    'order_id' => Arr::get($order, 'order_id'),
                    'origin' => Arr::get($order, 'source'),
                    'street' => Arr::get($order, 'delivery_address.line1'),
                    'city' => Arr::get($order, 'delivery_address.line2'),
                    'county' => Arr::get($order, 'delivery_address.line3'),
                    'country' => Arr::get($order, 'delivery_address.line4'),
                ];
            });
        } else if($customer === 'Z') {
            $mappedResponse = collect(Arr::get($response, 'orders'))->map(function ($order) {
                return [
                    'order_id' => Arr::get($order, 'id'),
                    'origin' => Arr::get($order, 'origin'),
                    'street' => Arr::get($order, 'address.street'),
                    'city' => Arr::get($order, 'address.city'),
                    'county' => Arr::get($order, 'address.state'),
                    'country' => Arr::get($order, 'address.country'),
                ];
            });
        }

        /**
         * SECTION 3 - THE RECORD STORAGES
         */
        // TODO: This class should be responsible for receiving and returning a response - is there a better place to 'save' a model?
        // TODO: I couldn't be bothered to use a DB for this so it'll be a file save (just copy the code wherever) - maybe a FileOrderRepository + OrderRepositoryContract could save these records?
        // TODO: Bonus - (if i get a db span up into this sometime - or do it yourself) add an EloquentOrderRepository 
        $filePath = database_path("orders/customer{$customer}.json");
        $file = fopen($filePath, 'w');
        fwrite($file, $mappedResponse);
        fclose($file);

        return response()->json($mappedResponse);

        /**
         * BONUS SECTION
         */
        // TODO: where my try/catch, custom exceptions and transactions at?
        // TODO: retry policies are good.
        // TODO: is there a way to return defined API responses? Maybe returning an APIResource/Fractal class from the mapper?
        // TODO: fix all my bugs
        // TODO: have a great weekend y'all! :)
    }
}
