<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Spatie\DbDumper\Databases\MySql;

class DBDumperController extends Controller
{
    /**
     * Dump DB.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $host = env('RDS_HOSTNAME');
        $username = env('RDS_USERNAME');
        $password = env('RDS_PASSWORD');
        $database = env('RDS_DB_NAME');
        MySql::create()
            ->setHost($host)
            ->setDbName($database)
            ->setUserName($username)
            ->setPassword($password)
            ->dumpToFile(storage_path('app/public/dump.sql'));
            return response()->download(storage_path('app/public/dump.sql'));
    }
}
