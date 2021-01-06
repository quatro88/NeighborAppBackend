<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventUser;
use App\Models\EventImage;
use App\Models\ImageModel;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Intervention\Image\ImageManagerStatic as Image;

use Illuminate\Support\Facades\Storage;

use Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $eventList = DB::table('events')
                    ->select('events.*', 'images.id', 'images.path', 'locations.latitude', 'locations.longitude')
                    ->leftJoin('locations', 'events.location_id', '=', 'locations.id')
                    ->leftJoin('event_images', 'events.id', '=', 'event_images.event_id')
                    ->leftJoin('images', 'event_images.image_id', '=', 'images.id')
                    ->orderByDesc('events.id')
                    ->get();
        return response()->json(["response" => ["code" => "1", "success" => "1", "message" => "Events listed."], 
        "events" => $eventList], 200);
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
        $rules = [
            'title' => 'required|min:3'
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $event = Event::create($request->all());
        $eventUser = new EventUser;
        $eventUser->event_id = $event->id;
        $eventUser->user_id = $request['user_id'];
        $eventUser->attending_id = $request['attending_id'];
        $eventUser->save();

        if($request->file('image1')){
            $path = $request->file('image1')->store('public');

            if($path != ''){
                Image::make(storage_path('app/public/' . substr($path, 7)))->orientate()
                ->resize(1024, 1024)
                ->save(storage_path('app/public/' . substr($path, 7)));

                $filename = $request->file('image1')->hashname();
                $image = new ImageModel;
                $image->path = $filename;

                $image->save();

                $eventImage = new EventImage;
                $eventImage->event_id = $event->id;
                $eventImage->image_id = $image->id;
                $eventImage->save();

                $url = Storage::url($image->path);
                return response()->json(["response" => ["code" => "1", "success" => "1", "message" => "Event created."], 
                    "url" => $url],200);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = DB::table('events')
                    ->select('events.*', 'images.id', 'images.path', 'locations.latitude', 'locations.longitude')
                    ->leftJoin('locations', 'events.location_id', '=', 'locations.id')
                    ->leftJoin('event_images', 'events.id', '=', 'event_images.event_id')
                    ->leftJoin('images', 'event_images.image_id', '=', 'images.id')
                    ->where('events.id', $id)
                    ->get()->first();

        return response()->json(["response" => ["code" => "1", "success" => "1", "message" => "Event displayed."], 
            "event" => $event],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }
}
