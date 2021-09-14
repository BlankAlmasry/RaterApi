<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Laravel\Passport\Token;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * @SWG\Swagger(
     *     basePath="",
     *     schemes={"http", "https"},
     *     host=L5_SWAGGER_CONST_HOST,
     *     @SWG\Info(
     *         version="1.0.0",
     *         title="L5 Swagger API",
     *         description="L5 Swagger API description",
     *         @SWG\Contact(
     *             email="your-email@domain.com"
     *         ),
     *     )
     * )
     */
    public function __construct()
    {
        if (!request()->bearerToken()) {
            return response("Authenticate with a token please", 401);
        }
        // Get the Access_Token from the request
        $Token = request()->bearerToken();
        // Parse the Access_Token to get the claims from them the jti(Json Token Id)
        $TokenId = (new Parser(new JoseEncoder()))->parse($Token)->claims()
            ->all()['jti'];
        try {
            $this->client = Token::findOrFail($TokenId)->client;
        } catch (\Exception $e) {
            return response("Invalid Access Token", 401);
        }

    }
}
