<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FavoriteController extends Controller
{
    protected $favorite;

    public function __construct(Favorite $favorite)
    {
        $this->favorite = $favorite;
    }

    public function index() 
    {
        $api_key   = config('services.tmdb.api_key');
        $user      = Auth::user();
        $favorites = $user->favorites;
        $details   = [];

        foreach($favorites as $favorite) {
            $tmdb_api_key = "https://api.themoviedb.org/3/".$favorite->media_type."/".$favorite->media_id."?api_key=".$api_key;
            $response     = Http::get($tmdb_api_key);
            if($response->successful()) {
                $details[] = array_merge($response->json(), ['media_type' => $favorite->media_type]);
            }
        }

        return response()->json($details);
    }

    public function toggleFavorite(Request $request) 
    {
        $validatedData = $request->validate([
            'media_type' => 'required|string',
            'media_id'   => 'required|integer'
        ]);

        $existingFavorite =  $this->favorite->where('user_id', Auth::user()->id)
                                            ->where('media_type', $validatedData['media_type'])
                                            ->where('media_id', $validatedData['media_id'])
                                            ->first();

        // Favorite is already exist
        if($existingFavorite) {
            $existingFavorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            // Favorite hasn't existed yet
            $this->favorite->create([
                'media_type' => $validatedData['media_type'],
                'media_id'   => $validatedData['media_id'],
                'user_id'    => Auth::user()->id
            ]);
        }
        
        return response()->json(['status' => 'added']);
    }

    public function checkFavoriteStatus(Request $request) 
    {
        $validatedData = $request->validate([
            'media_type' => 'required|string',
            'media_id'   => 'required|integer'
        ]);

        $isFavorited =  $this->favorite->where('user_id', Auth::user()->id)
                                       ->where('media_type', $validatedData['media_type'])
                                       ->where('media_id', $validatedData['media_id'])
                                       ->exists();
        return response()->json($isFavorited );
    }
}
