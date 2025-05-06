<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = ['user_id', 'title', 'content'];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function createForUser($user, array $data)
    {
        return $user->journals()->create([
            'title' => $data['title'],
            'content' => $data['content'],
        ]);
    }

    public function updateWithData(array $data)
    {
        $this->update([
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        return $this;
    }
}
