<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'start', 'end'];

    #Esta configuración es opcional .... 
    /* protected $fillable = ['title','start','end'];
    
    protected $hidden = ['id','title','start','end','created_at','update_at'];
    protected $appends =  ['motivo_appt','init_appt','end_appt'];
    public function getMotivoApptAttribute()
    {
        return strtoupper(substr($this->title,0,10)) . '...';
    }
    public function getInitApptAttribute()
    {
        return $this->start;
    }
    public function getEndApptAttribute()
    {
        return $this->end;
    } */
}