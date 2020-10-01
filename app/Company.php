<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    
    protected $fillable = [
     'Name', 'Phone_no'
    ];


    public function sample_datas(){
    	return $this->hasMany('App\Sample_data');
    }

    public function user(){
    	return $this->hasMany('App\User');
    }
}
