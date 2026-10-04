<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dinas extends Model
{
    use HasFactory;

    protected $table = 'sirapi_md_dinas';
    protected $primaryKey = 'id_dinas';

    protected $fillable = [
        'kode_dinas',
        'singkatan',
        'nama_dinas',
        'alamat',
        'telepon',
        'email',
        'kepala_dinas',
        'gps_lat',
        'gps_long',
    ];

    protected $appends = [
        'nama_lengkap',
    ];

    public function getSingkatanAttribute($value): string
    {
        return $value ?: ($this->kode_dinas ?: $this->nama_dinas);
    }

    public function getNamaLengkapAttribute(): string
    {
        $singkatan = $this->getRawOriginal('singkatan') ?: ($this->kode_dinas ?: null);
        $nama = $this->attributes['nama_dinas'] ?? '';

        if (!empty($singkatan) && !str_contains($nama, '(' . $singkatan . ')')) {
            return "{$nama} ({$singkatan})";
        }

        return $nama;
    }

    public function admins()
    {
        return $this->hasMany(Admin::class, 'id_dinas', 'id_dinas');
    }
}

