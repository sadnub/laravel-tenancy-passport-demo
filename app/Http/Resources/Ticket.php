<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Ticket extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title' => $request->input('title'),
            'contact' => $request->input('contact'),
            'status' => $request->input('status'),
            'issue' => $request->input('issue')
            ];
    }
}
