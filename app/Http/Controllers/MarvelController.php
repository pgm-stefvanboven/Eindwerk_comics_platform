<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

function getMarvelComics()
{
    // Voer het verzoek uit
    $response = Http::get('http://gateway.marvel.com/v1/public/comics?ts=1&apikey=9446f8eb6e1702835dbb961d763f4401&hash=b1f7a0387b6770554c0768bb48ac02c1');

    return $response->json();
}