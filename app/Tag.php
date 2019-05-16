<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function videos()
    {
        return $this->belongsToMany('App\Video', 'video_tag', 'tag_id', 'video_id');
    }
}
