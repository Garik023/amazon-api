<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    protected $fillable = ['name','zip_file_location','date_period', 'json_file_path'];
    protected $table = 'reports';
    public $timestamps = false;

    public static function getAllReportsData()
    {
        $reports = Reports::all()->toArray();

        if(empty($reports)){
            return [];
        }
        return $reports;
    }
}
