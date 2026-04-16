<?php

namespace App\Services;

use App\Models\User\BasicSetting;
use Illuminate\Support\Facades\Auth;

class AiServices
{
    private $apiKey, $model, $token, $temperature;
 public function __construct()
    {
        $userBs = BasicSetting::select('ai_api_key', 'ai_api_model', 'ai_api_token', 'ai_api_temperature')
            ->first();

        $this->apiKey = trim($userBs->ai_api_key);
        $this->model  = trim($userBs->ai_api_model ?? 'gpt-3.5-turbo');
        $this->token = (int)($userBs->ai_api_token ?? 400);
        $this->temperature = (float)($userBs->ai_api_temperature ?? 0.8);
    }

    public function generate_content($prompt, $additionalPrompt = null)
    {
        $client = new \GuzzleHttp\Client();

        $messages = [
            ['role' => 'system', 'content' => 'You are a creative and professional real estate copywriter.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        if (!is_null($additionalPrompt)) {
            $messages[] = ['role' => 'user', 'content' => $additionalPrompt];
        }

        try {
            $response = $client->post('https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => $this->temperature,
                    'max_tokens' => $this->token,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $reply = $data['choices'][0]['message']['content'] ?? 'No response';

            return $reply;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return 'API Error: ' . $e->getResponse()->getBody()->getContents();
        } catch (\Exception $e) {
            return 'Unexpected Error: ' . $e->getMessage();
        }
    }

    //improve content
    public function improve_content($existingContent, $improvementInstructions)
    {
        $client = new \GuzzleHttp\Client();

        $systemPrompt = 'You are a professional real estate content editor. ';
        $systemPrompt .= 'Your task is to improve existing property descriptions. ';
        $systemPrompt .= 'Rules: Keep original details, maintain language, enhance engagement.';

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => "Improve this property description:\n\n" . $existingContent],
            ['role' => 'user', 'content' => "Improvement instructions: " . $improvementInstructions],
        ];

        try {
            $response = $client->post('https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => $this->temperature,
                    'max_tokens' => $this->token,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $reply = $data['choices'][0]['message']['content'] ?? 'No response';

            return $reply;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return 'API Error: ' . $e->getResponse()->getBody()->getContents();
        } catch (\Exception $e) {
            return 'Unexpected Error: ' . $e->getMessage();
        }
    }
}
