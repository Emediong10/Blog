<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected $guarded=[];

public function scopePublished($query)
{
$query->where('published_at', '<=', Carbon::now());
}

public function likes()
    {
        return $this->belongsToMany(User::class, 'post_like')->withTimestamps();
    }

public function scopeFeatured($query)
{
$query->where('active',1);
}

public function author(){
    return $this->belongsTo(User::class, 'user_id');
}

public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
public function comments()
    {
        return $this->hasMany(Comment::class);
    }

public function scopeWithCategory($query, string $category){
    $query->whereHas('categories', function ($query) use ($category){
        $query->where('slug', $category);
    });
}

public function getReadingTime()
{

   $mins = round(str_word_count($this->content)/250);
   return ($mins < 1) ? 1 : $mins;

}

public function getThumbnailUrl()
{
    $isUrl = str_contains($this->image,'http');

    return ($isUrl) ? $this->image : Storage::disk('public')->url($this->image);
}




}
