<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\ChatMessage;

class CleanTempChatPhotos extends Command
{
    protected $signature = 'chat:clean-photos {--hours=24 : Hours after which orphan photos are removed}';
    protected $description = 'Remove temporary chat photos that are not linked to any pengaduan';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $cutoff = now()->subHours($hours);

        $files = Storage::disk('public')->files('chat_photos');
        $removed = 0;

        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);
            $fileTime = \Carbon\Carbon::createFromTimestamp($lastModified);

            if ($fileTime->lt($cutoff)) {
                // Check if this photo is used in any message
                $inUse = ChatMessage::where('attachment', $file)->exists();

                if (!$inUse) {
                    Storage::disk('public')->delete($file);
                    $removed++;
                    $this->line("  Removed: {$file}");
                }
            }
        }

        $this->info("Done. Removed {$removed} orphan photo(s).");
        return Command::SUCCESS;
    }
}
