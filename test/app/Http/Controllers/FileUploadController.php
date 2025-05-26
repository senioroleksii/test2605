<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function uploadChunk(Request $request)
    {
        $file = $request->file('file');
        $fileName = $request->input('fileName');
        $chunkIndex = (int)$request->input('chunkIndex');
        $totalChunks = (int)$request->input('totalChunks');

        if (!$file || !$fileName || $chunkIndex < 0 || $totalChunks < 1) {
            return response()->json(['success' => false, 'error' => 'Invalid data'], 400);
        }

        $tempDir = "uploads/chunks_$fileName";
        Storage::makeDirectory($tempDir);

        $chunkPath = "$tempDir/part_$chunkIndex";

        // Сохраняем текущий чанк
        $file->storeAs($tempDir, "part_$chunkIndex");

        // Проверяем, все ли чанки загружены
        $allChunks = Storage::files($tempDir);
        if (count($allChunks) == $totalChunks) {
            $finalPath = "uploads/$fileName";
            $output = Storage::disk('local')->put($finalPath, '');

            $outputStream = Storage::disk('local')->path($finalPath);
            $outputFile = fopen($outputStream, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkContent = Storage::disk('local')->get("$tempDir/part_$i");
                fwrite($outputFile, $chunkContent);
            }

            fclose($outputFile);

            // Удаляем чанки
            foreach ($allChunks as $chunkFile) {
                Storage::delete($chunkFile);
            }
            Storage::deleteDirectory($tempDir);
        }

        return response()->json(['success' => true]);
    }
}
