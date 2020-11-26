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
 * Move a given filepath from one disk to another
 */
class MoveFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Our filepath string
     *
     * @var string
     */
    public $file;

    /**
     * Our disk we wish to get the file from
     *
     * @var string
     */
    public $from_disk;

    /**
     * Our disk we wish to move the file to
     *
     * @var [type]
     */
    public $to_disk;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $file, string $from_disk, string $to_disk)
    {
        $this->file      = $file;
        $this->from_disk = $from_disk;
        $this->to_disk   = $to_disk;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Move file
        $from_disk_storage = Storage::disk($this->from_disk);
        $to_disk_storage   = Storage::disk($this->to_disk);

        $to_disk_storage->put(
            $this->file,
            $from_disk_storage->readStream($this->file)
        );

        // Check file is now on the new disk
        if (!Storage::disk($this->to_disk)->exists($this->file)) {
            throw new Exception("File '{$this->file}' hasn't been moved to disk '{$this->to}'");
        }
    }
}
