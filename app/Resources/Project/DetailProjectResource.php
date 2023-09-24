<?php

namespace App\Resources\Project;

use App\Resources\SingularResource;

class DetailProjectResource extends SingularResource
{
    public function toArray($request)
    {
        return [
            'project_id' => $this->project_id,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'repo_link' => $this->repo_link,
            'url' => $this->url,
            'file' => $this->file ?? null,
            'file_url' => $this->file_url ?? null,
            'file_link' => $this->file_link ?? null,
        ];
    }
}
