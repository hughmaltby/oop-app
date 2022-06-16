<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    /**
     * Returns orders for the specified customer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke() : JsonResponse
    {
        /**
         * 
         * This is a very simple app but there are 3 sections here that can be refactored to be more SOLID - pick the one you want and have a crack.
         * Feel free to move onto another section if/when you're ready.
         * There's a bonus section of random todos if you get that far and still have the will to live.
         * 
         * This is just to play around with some of the concepts from the session - don't use jobs/schedules, write tests, bother validating payloads etc. - these are all SUPER important just not today unless it's SOLID related
         * If you find yourself spending time on random logic instead of SOLID related concepts just hardcode stuff or something - if there's an easy way take it, concentrate on classes/contract organization/architecture etc.
         * 
         * We may (Halden/James approving) do other sessions on testing & docker - the aim would be to re-use the same app each time - so implementing some of the SOLID concepts may make it easier if we get to testing.
         * Or implementing tests may force you to rethink what you do this session!
         * 
         */


        /**
         * SECTION 1 - THE API CLIENTS
         */

        // TODO: This class should be responsible for receiving and returning a response - maybe we should handle these validation checks elsewhere?
        $customer = request('customer');

        if (! $customer) {
            return response()->json("You must specify a customer.");
        }

        $allowedCustomers = collect(['W', 'X', 'Y', 'Z']);
        if (! $allowedCustomers->contains($customer)) {
            return response()->json("Customer must be in array ['W', 'X', 'Y', 'Z'].");
        }

        // TODO: This class should be responsible for receiving and returning a response - is there a more OOP way to do this?
        if ($customer == 'W' || $customer == 'X' || $customer == 'Y' || $customer == 'Z') {
            $http = Http::acceptJson();
        } else {
            // else nothing - I just put this here to demonstrate using dedicated API classes could support eg. xml or anything
        }

        // TODO: We are switching auth based on the customer/api which means adding more if statements - is there a more OOP way to do this?
        if ($customer == 'Y') {
            $http->withToken('secret');
        } else {
            $http->withHeaders([
                'x-api-key' => 'secret',
            ]);
        }

        // TODO: We are switching url based on the customer/api which means adding more if statements - is there a more OOP way to do this?
        $base = env('API_URL'); // yes i know this should be in config() - I don't have time - pretend the base changes for every API ie. when you OOP set it individually on each client not from API_URL
        if($customer == 'W' || $customer == 'Z') {
            $url = "{$base}/dropify";
            // Have nested this here for a reason - maybe Z should extend W?
            if($customer == 'Z') {
                $url = "{$url}/limited";
            }
        } elseif($customer == 'X') {
            $url = "{$base}/wamazon";
        } elseif($customer == 'Y') {
            $url = "{$base}/freebay";
        }

        $response = $http->get($url);

        if ($customer == 'W' || $customer == 'X' || $customer == 'Y' || $customer == 'Z') {
            $response = $response->json();
        } else {
            // else nothing - but there could have been...
        }
        
        // TODO - BONUS: If I get around to it (unlikely), CustomerZ (limited dropify) might need pagination - maybe a contract wrapper around dropify might help? One implementation without pagination, one with.

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
