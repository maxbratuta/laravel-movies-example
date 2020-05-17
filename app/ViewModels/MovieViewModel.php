<?php

namespace App\ViewModels;

use Carbon\Carbon;
use Spatie\ViewModels\ViewModel;

class MovieViewModel extends ViewModel
{
    public $movie;

    /**
     * MovieViewModel constructor.
     *
     * @param $movie
     */
    public function __construct($movie)
    {
        $this->movie = $movie;
    }

    /**
     * Function return cast to a view.
     *
     * @param $movie
     * @return \Illuminate\Support\Collection
     */
    public function cast($movie)
    {
        return collect($movie['credits']['cast'])->map(function ($cast) {
            return collect($cast)->merge([
                'profile_path' => ($cast['profile_path'])
                    ? 'https://image.tmdb.org/t/p/w235_and_h235_face/' . $cast['profile_path']
                    : 'https://ui-avatars.com/api/?size=235&name=' . $cast['name'],
            ]);
        });
    }

    /**
     * Function return images to a view.
     *
     * @param $movie
     * @return \Illuminate\Support\Collection
     */
    public function images($movie)
    {
        return collect($movie['images']['backdrops'])->map(function ($image) {
            return collect($image)->merge([
                'original_file_path' => 'https://image.tmdb.org/t/p/original/' . $image['file_path'],
                'w500_file_path' => 'https://image.tmdb.org/t/p/w500/' . $image['file_path'],
            ]);
        });
    }

    /**
     * Function return a movie to a view.
     *
     * @return mixed
     */
    public function movie()
    {
        return collect($this->movie)->merge([
            'poster_path' => 'https://image.tmdb.org/t/p/w500' . $this->movie['poster_path'],
            'vote_average' => $this->movie['vote_average'] * 10 . '%',
            'release_date' => Carbon::parse($this->movie['release_date'])->format('M d, Y'),
            'genres' => collect($this->movie['genres'])->pluck('name')->flatten()->implode(', '),
            'crew' => collect($this->movie['credits']['crew'])->take(2),
            'cast' => $this->cast($this->movie)->take(5),
            'images' => $this->images($this->movie)->take(9),
        ])->only([
            'id',
            'title',
            'poster_path',
            'vote_average',
            'overview',
            'release_date',
            'genres',
            'credits',
            'videos',
            'crew',
            'cast',
            'images',
        ]);
    }
}
