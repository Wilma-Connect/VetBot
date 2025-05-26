<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';
    protected $keyType = 'string';
    public $incrementing = false;

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
