<?php

namespace App\Http\Controllers;


class HomeController extends Controller
{
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
