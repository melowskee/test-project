<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deliveries extends Model
{
    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'deliveries';

    public $timestamps = true;

    /**
     * Get the user that owns the phone.
     */
    public function contact()
    {
        return $this->belongsTo('App\Contact', 'contact_id');
    }

}
