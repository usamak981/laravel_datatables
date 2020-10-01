<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sample_data extends Model
{
    
    protected $fillable = [
     'first_name', 'last_name' , 'company_id'
    ];

    function company(){
    	return $this->belongsTo('App\Company');
    }
}
