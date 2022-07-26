<?php 

namespace App\Http\Clients;

use Illuminate\Support\Facades\Http;
use App\Http\Clients\OrderRetrievableContract;

class FreebayClient implements OrderRetrievableContract
{
    public function __construct()
    {
        $this->base = env('API_URL');
    }

    /**
     * Retrieves orders
     *
     * @return string
     */
    public function index()
    {
        return Http::acceptJson()->withToken('secret')->get("{$this->base}/freebay");
    }
}
