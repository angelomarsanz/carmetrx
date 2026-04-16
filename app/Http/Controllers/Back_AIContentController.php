<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Services\AiServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AIContentController extends Controller
{
    /**
     * Generate prompt for AI service
     */
    public function generatePrompt(AiServices $aiServices, Request $request)
    {
        $amenities = is_array($request->amenities) ? implode('; ', $request->amenities) : '';
        $featuresArray = is_array($request->features) ? $request->features : [];

        $featuresList = "";
        foreach ($featuresArray as $feature) {
            $featuresList .= "• " . $feature . "\n";
        }

        $summary = is_array($request->summary) ? implode('; ', $request->summary) : $request->summary ?? '';
        $title = $request->title ?? '';
        $langName = $request->langName;

        $messages = <<<EOT
            TASK: Write a property description in {$langName}. 100% human, warm, flowing. NO lists. NO repetition.

            RULES:
            1. NO title or heading
            2. Start with a strong, data-based hook (use building name if in title)
            3. Weave "{$title}" naturally ONCE
            4. **BOLD EVERY AMENITY** with **double asterisks**: **Piscina**, **Wi-Fi / Internet Ready**
            5. Include ALL features (e.g., "Built in 1942")
            6. Spread amenities across 1-2 paragraphs — NEVER list
            7. Use transitions: "Además...", "Y para mayor comodidad..."
            8. NO invented details

            DATA:
            • Title: {$title}
            • Summary: {$summary}
            • Amenities: {$amenities}
            • Features:
            {$featuresList}

            STYLE:
            - Like a real estate agent texting a client
            - Warm, direct, confident
            - Short + long sentences
            - End with lifestyle benefit

            EXAMPLE (FOLLOW THIS FLOW):
            "Viva en el **Danish Gray Building**, un clásico de 1942 renovado con gusto. Disfrute de **Piscina**, **Gym**, y **Aire acondicionado** sin salir de casa..."

            START NOW in {$langName}.
            EOT;

        $content = $aiServices->generate_content($messages);
        $htmlContent = Str::markdown($content);
        return response($htmlContent);
    }
    /**
     * Generate content
     */
    public function generateContent(AiServices $aiServices, Request $request)
    {
        if (isset($request->type) && $request->type == 'make_better') {
            $content = $aiServices->improve_content($request->prev_content, $request->new_prompt);
            $htmlContent = Str::markdown($content);
            return response($htmlContent);
        }
    }
}
