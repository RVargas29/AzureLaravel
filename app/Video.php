<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['title', 'url', 'thumbnail', 'description'];

    public function tags() {
        return $this->belongsToMany('App\Tag', 'video_tag', 'video_id', 'tag_id')->withTimestamps();
    }
}
