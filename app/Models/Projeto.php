<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projeto extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function edital()
    {
        return $this->belongsTo(Edital::class);
    }

    public function respostas()
    {
        return $this->hasMany(ProjetoResposta::class);
    }

    public function documentosGerados()
    {
        return $this->hasMany(DocumentoGerado::class);
    }
}
