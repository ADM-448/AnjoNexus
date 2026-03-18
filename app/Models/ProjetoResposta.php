<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetoResposta extends Model
{
    use HasFactory;
    
    protected $table = 'projeto_respostas';
    protected $guarded = ['id'];

    public function projeto()
    {
        return $this->belongsTo(Projeto::class);
    }

    public function pergunta()
    {
        return $this->belongsTo(EditalPergunta::class, 'pergunta_id');
    }
}
