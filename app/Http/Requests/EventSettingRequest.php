<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventSettingRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location_name' => 'required|string|max:255',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'device_type' => 'required|exists:games,device_type',
            'game_type' => 'required|in:FIGHTING,RPG,FPS,TBS,SPORT,ARCADE,RACING,MMORPG,TPS,STRATEGY',
            'game_id' => 'required|exists:games,id',
            'event_image' => 'nullable|image|max:2048',
            'max_participants' => 'required|integer|min:1',
        ];
    }
}