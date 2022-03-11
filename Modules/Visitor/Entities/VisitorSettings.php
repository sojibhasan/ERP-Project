<?php

namespace Modules\Visitor\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class VisitorSettings extends Model
{
    use Notifiable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function permitted_locations()
    {
        $user = $this;
        return 'all';
    }


}
