<?php

namespace App\Resources\Project;

use App\Resources\PluralResource;

class DaftarProjectResource extends PluralResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return  $this->collection->map(function ($data) {
            return [
                'no_urut' => $data->no_urut,
                'project_id' => $data->project_id,
                'judul' => $data->judul,
                'deskripsi' => $data->deskripsi,
                'thumbnail' => $data->thumbnail ?? null,
                'thumbnail_url' => $data->thumbnail_url ?? null,
                'thumbnail_link' => $data->thumbnail_link ?? null,
            ];
        });
    }
}
