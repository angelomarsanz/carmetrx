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
            TAREA: Redacta una descripción de la propiedad en {$langName}. como un agente inmobiliario, amigable, cálida,  y con acento uruguayo. SIN listas. SIN repeticiones.

            NORMAS:
            1. SIN título ni encabezado.
            2. Comience con un gancho fuerte basado en datos (utilice el nombre del edificio si aparece en el título).
            3. Incluya «{$title}» de forma natural UNA VEZ.
            4. RESALTE TODAS LAS COMODIDADES (Servicios) con negrita.
            5. Incluya TODAS las características.
            6. Distribuya las comodidades en 1-2 párrafos, NUNCA las enumere.
            7. Utilice transiciones: «Además...», «Y para mayor comodidad...».
            8. NO invente detalles.

            DATOS:
            - Título: {$title}
            - Resumen: {$summary}
            - Servicios: {$amenities}
            - Características:
            {$featuresList}

            ESTILO:
            - Como un agente inmobiliario enviando un mensaje de texto a un cliente con estrategia de copywriting
            - Cálido, directo, seguro
            - Frases cortas + largas
            - Terminar con una ventaja para el estilo de vida

            COMIENCE AHORA en {$langName}.
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
