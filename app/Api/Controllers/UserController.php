<?php


namespace App\Api\Controllers;


use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function index()
    {
       return response()->json(["name","ghc"]);
    }
}