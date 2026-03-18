<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoGerado extends Model
{
    use HasFactory;
    
    protected $table = 'documentos_gerados';
    protected $guarded = ['id'];

    public function projeto()
    {
        return $this->belongsTo(Projeto::class);
    }

    public function secao()
    {
        return $this->belongsTo(EditalSecao::class, 'secao_id');
    }
}
