<?php

namespace App\Models;

use App\Models\token;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Log;
class whatsapp extends Model
{
    use HasFactory;

    public static function sendMessage($vetor, $token = null)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v18.0/234377473099729/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($vetor),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$token ?? token::token(),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Obtenha o código HTTP da resposta
        curl_close($curl);
        Log::create(
            [
                "code_http" => $httpCode,
                "response"  => $response,
                "url" => "https://graph.facebook.com/v18.0/234377473099729/messages",
                "request" => json_encode($vetor)
            ]
        );
        return $response;
    }
}
