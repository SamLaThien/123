<?php

namespace App\Http\Controllers;

use App\Story;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class StoryController extends Controller
{
    const BASE_CHAPTER_URL = 'http://aios.tutien.net/story/';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stories = Story::all();
        return view('stories.index', ['stories' => $stories]);
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
        $storyId = $request->get('story_id');
        if (!empty($storyId)) {
            $response = Curl::to(self::BASE_CHAPTER_URL)
                ->withData([
                    'page_numer' => 1,
                    'story_id' => $storyId,
                    'page_size' => 1,
                ])->get();

            $result = json_decode($response, true);
            if ($result['success']) {
                $data = $result['data'];
                Story::create([
                    'story_id' => $storyId,
                    'name' => $data['NAME'],
                    'first_chapter' => $data['CHAPTER'][0]['id'],
                    'total_chapter' => $data['TOTALCHAPTER'],
                ]);
            }
        }

        $stories = Story::all();
        return view('stories.index', ['stories' => $stories]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function show(Story $story)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function edit(Story $story)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Story $story)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function destroy(Story $story)
    {
        //
    }
}
