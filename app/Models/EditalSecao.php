<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditalSecao extends Model
{
    use HasFactory;
    
    protected $table = 'edital_secoes';
    protected $guarded = ['id'];

    public function edital()
    {
        return $this->belongsTo(Edital::class);
    }

    public function perguntas()
    {
        return $this->hasMany(EditalPergunta::class, 'secao_id');
    }
}
