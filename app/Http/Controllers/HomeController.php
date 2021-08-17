<?php

namespace App\Http\Controllers;


class HomeController extends Controller
{
    public function home()
    {

        /**
         * @OA\Get(path="/store/inventory",
         *   tags={"store"},
         *   summary="Returns pet inventories by status",
         *   description="Returns a map of status codes to quantities",
         *   operationId="getInventory",
         *   parameters={},
         *   @OA\Response(
         *     response=200,
         *     description="successful operation",
         *     @OA\Schema(
         *       additionalProperties={
         *         "type":"integer",
         *         "format":"int32"
         *       }
         *     )
         *   ),
         *   security={{
         *     "api_key":{}
         *   }}
         * )
         */
        return \response()->json([
            "title" => "RaterApi",
            "description" => "RaterApi is a rating api, you can create games, matches between users in these games and
             get calculated skill Rating after it, the api mainly work on Glicko-2 algorithm for now",
            "links" => [
                [
                "rel" => "login",
                "href" => "/api/login",
                "method" => "POST",
                "values" => ["grant_type","client_id", "client_secret"]
                ],
                 [
                "rel" => "games",
                "href" => "/api/games",
                "method" => "GET|POST",
                ],
                 [
                "rel" => "games",
                "href" => "/api/users",
                "method" => "GET",
                ],
            ]
        ]);
    }

    public function login()
    {
        return response(["message" => "Invalid or no api token, authorize with a valid api please" , "links" => [
            "rel" => "login",
            "href" => "/api/login",
            "method" => "post",
            "values" => [
                "grant_type", "client_id", "client_secret"
            ]
        ]],401);
    }

}
