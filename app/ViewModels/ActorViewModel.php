<?php

namespace App\ViewModels;

use Carbon\Carbon;
use Spatie\ViewModels\ViewModel;

class ActorViewModel extends ViewModel
{
    public $actor;
    public $social;
    public $credits;

    public function __construct($actor, $social, $credits)
    {
        $this->actor = $actor;
        $this->social = $social;
        $this->credits = $credits;
    }

    public function actor()
    {
        return collect($this->actor)
            ->merge([
                'birthday' => Carbon::parse($this->actor['birthday'])->format('M d, Y'),
                'age' => Carbon::parse($this->actor['birthday'])->age,
                'profile_path' => ($this->actor['profile_path'])
                    ? 'https://image.tmdb.org/t/p/w300/' . $this->actor['profile_path']
                    : 'https://ui-avatars.com/api/?size=300&name=' . $this->actor['name'],
                'place_of_birth' => ($this->actor['place_of_birth'])
                    ? 'in ' . $this->actor['place_of_birth']
                    : ''
            ]);
    }

    public function social()
    {
        return collect($this->social)
            ->merge([
                'twitter' => $this->social['twitter_id'] ? 'https://twitter.com/' . $this->social['twitter_id'] : null,
                'instagram' => $this->social['instagram_id'] ? 'https://instagram.com/' . $this->social['instagram_id'] : null,
                'facebook' => $this->social['facebook_id'] ? 'https://facebook.com/' . $this->social['facebook_id'] : null,
            ]);
    }

    public function knownForMovies()
    {
        $castMovies = collect($this->credits)->get('cast');

        return collect($castMovies)
            ->where('media_type', 'movie')
            ->sortByDesc('popularity')
            ->take(5)
            ->map(function ($movie) {
                return collect($movie)->merge([
                    'poster_path' => ($movie['poster_path'])
                        ? 'https://image.tmdb.org/t/p/w185' . $movie['poster_path']
                        : 'https://via.placeholder.com/185x278',
                    'title' => $movie['title'] ?? 'Untitled',
                ]);
            });
    }

    public function credits()
    {
        $castMovies = collect($this->credits)->get('cast');

        return collect($castMovies)->map(function ($movie) {
            if (isset($movie['release_date'])) {
                $releaseDate = $movie['release_date'];
            } else if (isset($movie['first_air_date'])) {
                $releaseDate = $movie['first_air_date'];
            } else {
                $releaseDate = '';
            }

            if (isset($movie['title'])) {
                $title = $movie['title'];
            } else if (isset($movie['name'])) {
                $title = $movie['name'];
            } else {
                $title = 'Untitled';
            }

            return collect($movie)->merge([
                'release_date' => $releaseDate,
                'release_year' => Carbon::parse($releaseDate)->format('Y') ?? 'Future',
                'title' => $title,
                'character' => ($movie['character']) ? 'as ' . $movie['character'] : '',
            ]);
        })
            ->sortByDesc('release_date');
    }
}
