<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use setasign\Fpdi\Fpdi;
use Exception;

class PdfGenerator
{
    /**
     * @throws Exception
     */
    public function generateFromUrls(array $urls): array
    {
        $pdf = new Fpdi();
        $tempFiles = [];

        foreach ($urls as $url) {
            // Загружаем PDF с внешнего URL
            $response = Http::get($url);

            if ($response->ok()) {
                // Сохраняем временный файл
                $tempFile = tempnam(sys_get_temp_dir(), 'ticket') . '.pdf';
                file_put_contents($tempFile, $response->body());
                $tempFiles[] = $tempFile;

                // Добавляем PDF в общий документ
                $pageCount = $pdf->setSourceFile($tempFile);

                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplId = $pdf->importPage($i);
                    $pdf->AddPage();
                    $pdf->useTemplate($tplId);
                }
            } else {
                throw new Exception('Не удалось загрузить PDF по URL. Возможно ваш билет недействителен');
            }
        }

        return [$pdf, $tempFiles];
    }
}
