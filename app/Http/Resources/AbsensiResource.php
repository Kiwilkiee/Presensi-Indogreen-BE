<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AbsensiResource extends JsonResource
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nama' => $this->user->nama, // Nama pengguna dari relasi user
            'jabatan' => $this->user->jabatan, // Jabatan pengguna dari relasi user
            'jam_masuk' => $this->jam_masuk,
            'jam_pulang' => $this->jam_pulang,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
