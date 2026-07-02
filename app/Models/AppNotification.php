<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id', 'type', 'message', 'document_interne_id', 'lu',
    ];

    protected $casts = [
        'lu' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document()
    {
        return $this->belongsTo(DocumentInterne::class, 'document_interne_id');
    }

    public static function notifier(int $userId, string $message, string $type = 'info', ?int $documentId = null): void
    {
        if (! $userId) {
            return;
        }
        static::create([
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'document_interne_id' => $documentId,
            'lu' => false,
        ]);
    }
}
