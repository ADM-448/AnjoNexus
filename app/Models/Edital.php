<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edital extends Model
{
    use HasFactory;
    
    protected $table = 'editais';
    protected $guarded = ['id'];

    protected $casts = [
        'payload_origem' => 'array',
        'last_scanned_at' => 'datetime',
        'ia_enriquecido' => 'boolean',
    ];

    public function secoes()
    {
        return $this->hasMany(EditalSecao::class);
    }

    public function projetos()
    {
        return $this->hasMany(Projeto::class);
    }
}
