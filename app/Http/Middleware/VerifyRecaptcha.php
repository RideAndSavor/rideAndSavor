<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

class VerifyRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $recaptchaToken = $request->input('captchaToken');

        if (!$recaptchaToken) {
            return response()->json(['error' => 'reCAPTCHA token missing'], 400);
        }

        $client = new Client();
        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $recaptchaToken,
            ],
        ]);

        $body = json_decode((string)$response->getBody());

        if (!$body->success) {
            return response()->json(['error' => 'reCAPTCHA verification failed'], 400);
        }

        return $next($request);
    }

}
