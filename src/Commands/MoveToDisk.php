<?php

namespace Rayblair\Filesystem\Commands;

use Rayblair\Filesystem\Jobs\MoveFileToDiskJob;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MoveToDisk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move:to-disk {from_disk} {to_disk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move all files from one disk to another then remove them from the old disk afterwards';

    /**
     * Our Excluded File, such as system, special files, etc
     *
     * @var array
     */
    protected $exclude_files;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->exclude_files = config('rb-filesystem.exclude_files');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $from_disk = $this->argument('from_disk');
        $to_disk   = $this->argument('to_disk');

        // Check our Disks exist
        $this->checkDiskExists($from_disk);
        $this->checkDiskExists($to_disk);

        $this->line("Moving files from disk '{$from_disk}' to disk '{$to_disk}'");

        // Get our full disk contents
        $files = Storage::disk($from_disk)->allfiles();

        // Remove our files we want to exclude
        foreach ($files as $key => $file) {
            if (array_filter($this->exclude_files, function ($excluded_file) use ($file) {
                return strpos($file, $excluded_file) !== false;
            })) {
                unset($files[$key]);
            }
        }

        $this->line('Attempting to move ' . count($files) . ' files');

        // Dispatch our Job to move the file
        foreach ($files as $file) {
            dispatch(new MoveFileToDiskJob($file, $from_disk, $to_disk));
        }

        $this->info("All files queued to move to new disk '{$to_disk}'");
    }

    private function checkDiskExists(string $disk_name)
    {
        if (!config("filesystems.disks.{$disk_name}.driver")) {
            throw new Exception("Disk driver for disk {$disk_name} not set");
        }
    }
}
