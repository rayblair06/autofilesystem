<?php

namespace Rayblair\Filesystem\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * Deletes a given filepath from the specified disk
 */
class DeleteFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Our filepath string
     *
     * @var string
     */
    public $file;

    /**
     * Our disk we wish to delete the file from
     *
     * @var string
     */
    public $disk;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $file, string $disk)
    {
        $this->file      = $file;
        $this->disk      = $disk;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Remove the old file
        Storage::disk($this->disk)->delete($this->file);

        // Check file doesn't exist anymore
        if (Storage::disk($this->disk)->exists($this->file)) {
            throw new Exception("File '{$this->file}' hasn't been deleted on '{$this->disk}'");
        }
    }
}
