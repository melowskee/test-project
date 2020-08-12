<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'campaigns';

    public $timestamps = true;

    /**
     * Get the templates for the campaign.
     */
    public function templates()
    {
        return $this->hasMany('App\Template');
    }
}
