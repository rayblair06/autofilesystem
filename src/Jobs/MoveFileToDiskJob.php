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
 * Check if a file exists, then begin creating the jobs to move and delete that file from the given from and to disks
 */
class MoveFileToDiskJob implements ShouldQueue
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
        // Check if media still exists on disk
        if (!Storage::disk($this->from_disk)->exists($this->file)) {
            throw new Exception("File '{$this->file}' isn't located on disk '{$this->from_disk}'");
        }
        
        // Dispatch job chain to
        // Move File
        // Delete File
        MoveFileJob::withChain([
            new DeleteFileJob($this->file, $this->from_disk)
        ])->dispatch($this->file, $this->from_disk, $this->to_disk);
    }
}
