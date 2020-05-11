<?php

namespace App\ViewModels;

use Carbon\Carbon;
use Spatie\ViewModels\ViewModel;

class MoviesViewModel extends ViewModel
{
    public $popularMovies;
    public $nowPlayingMovies;
    public $genres;

    /**
     * MoviesViewModel constructor.
     *
     * @param $popularMovies
     * @param $nowPlayingMovies
     * @param $genres
     */
    public function __construct($popularMovies, $nowPlayingMovies, $genres)
    {
        $this->popularMovies = $popularMovies;
        $this->nowPlayingMovies = $nowPlayingMovies;
        $this->genres = $genres;
    }

    /**
     * Function return popular movies to a view.
     *
     * @return mixed
     */
    public function popularMovies()
    {
        return $this->formatMovies($this->popularMovies);
    }

    /**
     * Function return now playing movies to a view.
     *
     * @return mixed
     */
    public function nowPlayingMovies()
    {
        return $this->formatMovies($this->nowPlayingMovies);
    }

    /**
     * Function return genres to a view.
     *
     * @return mixed
     */
    public function genres()
    {
        return collect($this->genres)->mapWithKeys(function ($genre) {
            return [$genre['id'] => $genre['name']];
        });
    }

    /**
     * Function return a movie collection with a custom format of displaying.
     *
     * @param $movies
     * @return \Illuminate\Support\Collection
     */
    private function formatMovies($movies)
    {
        return collect($movies)->map(function ($movie) {
            $genresFormatted = collect($movie['genre_ids'])->mapWithKeys(function($value) {
                return [$value => $this->genres()->get($value)];
            })->implode(', ');

            return collect($movie)->merge([
                'poster_path' => 'https://image.tmdb.org/t/p/w500/' . $movie['poster_path'],
                'vote_average' => $movie['vote_average'] * 10 . '%',
                'release_date' => Carbon::parse($movie['release_date'])->format('M d, Y'),
                'genres' => $genresFormatted,
            ])->only([
                'id',
                'title',
                'poster_path',
                'vote_average',
                'overview',
                'release_date',
                'genres_ids',
                'genres',
            ]);
        });
    }
}
