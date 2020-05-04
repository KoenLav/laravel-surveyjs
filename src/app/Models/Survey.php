<?php

namespace AidynMakhataev\LaravelSurveyJs\app\Models;

use Cocur\Slugify\Slugify;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use SoftDeletes;

    protected $table = 'surveys';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'slug', 'json',
    ];

    protected $casts = [
        'json'  =>  'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($survey) {
            $slugify = new Slugify();

            $survey->slug = $slugify->slugify($survey->name);;

            $latestSlug = static::whereRaw("slug = '$survey->slug' or slug LIKE '$survey->slug-%'")
                ->latest('id')
                ->value('slug');
            if ($latestSlug) {
                $pieces = explode('-', $latestSlug);

                $number = intval(end($pieces));

                $survey->slug .= '-' . ($number + 1);
            }
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function results()
    {
        return $this->hasMany('AidynMakhataev\LaravelSurveyJs\app\Models\SurveyResult', 'survey_id');
    }
}
