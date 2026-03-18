<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditalPergunta extends Model
{
    use HasFactory;
    
    protected $table = 'edital_perguntas';
    protected $guarded = ['id'];

    public function secao()
    {
        return $this->belongsTo(EditalSecao::class, 'secao_id');
    }
}
