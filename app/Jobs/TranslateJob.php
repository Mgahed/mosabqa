<?php

namespace App\Jobs;

use App\Models\Translation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $langCode = 'ar';
    public function __construct($langCode)
    {
        $this->langCode = $langCode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = Translation::whereNotNull('translations')
            ->get();

        foreach ($data as $item) {
            // check if translations is not empty and value of ar has english characters
            if (!empty($item->translations) && preg_match('/[a-zA-Z]/', json_decode($item->translations, true)[$this->langCode])) {
                $data = [
                    'en' => $item->translation_key,
                    'ar' => $this->translate($item->translation_key, $this->langCode),
                ];
                $item->translations = json_encode($data);
                $item->save();

                // Prevent rate-limiting from Google
                sleep(2);
            }
        }
    }

    private function translate(mixed $translation_key, string $lang = 'ar')
    {
        $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=$lang&dt=t&q=" . urlencode($translation_key);
        $response = file_get_contents($url);
        $response = json_decode($response, true);
        return $response[0][0][0];
    }
}
