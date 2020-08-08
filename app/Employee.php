<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //protected $guarded=[];
    protected $fillable = ['name','email','phone_number','age','department','salary'];
}
