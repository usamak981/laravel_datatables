<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //user amount product paid print

    protected $fillable = [
        'name', 'product_name', 'amount', 'user_id','paid'
       ];

    
    public function user(){
       return $this->belongsTo('App\User');
    }
}
