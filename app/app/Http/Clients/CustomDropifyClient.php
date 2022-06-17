<?php 

// namespace App\Http\Clients;


// use App\Http\Clients\DropifyClient;
// use Illuminate\Support\Facades\Http;

// class CustomDropifyClient extends DropifyClient
// {
//     public function __construct()
//     {
//         $this->base = env('API_URL');
//     }

//     /**
//      * Retrieves orders
//      *
//      * @return string
//      */
//     public function index()
//     {
//         return Http::acceptJson()->withHeaders([
//             'x-api-key' => 'secret'
//         ])->get("{$this->base}/dropify/limited");
//     }
// }
