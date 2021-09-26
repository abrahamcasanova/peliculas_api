<?php

namespace App\Http\Controllers;

use App\Movie;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $movies =  Movie::with('schedules')->get();
        $movies->map(function($movie) {
            $movie->imgStorage = Storage::url($movie->img);
            return $movie;
        });
        return response()->json($movies);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
        ]);

        $request->merge(['status' => $request->status ? 1:0]);

        $movie = new Movie();
        $movie->fill($request->all());

        
        if ($request->has('file')) {
            $file = $request->file('file');
            $name_file = Str::uuid() . '.' . $file->extension();
            Storage::disk('movies')->put($name_file, file_get_contents($file));
            $movie->img = Storage::disk('movies')->url($name_file);
            $movie->save();
        }

        return response()->json($movie);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
        ]);

        $movie = Movie::find($request->id);
        $request->merge(['status' => $request->status ? 1:0]);
        $movie->fill($request->all());

        if ($request->has('file')) {
            $file = $request->file('file');
            $name_file = Str::uuid() . '.' . $file->extension();
            Storage::disk('movies')->put($name_file, file_get_contents($file));
            $movie->img = Storage::disk('movies')->url($name_file);
            $movie->save();
        }

        $movie->save();
        return response()->json($movie);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movie $movie)
    {
        return response()->json($movie->delete());
    }
}
