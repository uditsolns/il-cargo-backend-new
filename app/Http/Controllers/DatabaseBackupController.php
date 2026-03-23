<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class DatabaseBackupController extends Controller
{
    public function __invoke(Request $request)
    {
        $filename = 'db_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/' . $filename);

        $process = new Process([
            'mysqldump',
            '--user=' . config("database.connections.mysql.username"),
            '--password=' . config('database.connections.mysql.password'),
            '--host=' . config('database.connections.mysql.host'),
            config('database.connections.mysql.database'),
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception($process->getErrorOutput());
        }

        file_put_contents($path, $process->getOutput());

        return response()->download($path)->deleteFileAfterSend();
    }
}
