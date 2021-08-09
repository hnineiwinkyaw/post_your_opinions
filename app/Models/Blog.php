<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Blog extends Model
{
    use HasApiTokens, HasFactory;
    protected $fillable = [
    	'title',
    	'content',
    	'created_by'
    ];

    public function user()
	{
	  return $this->belongsTo(User::class, 'created_by');
	}
}
