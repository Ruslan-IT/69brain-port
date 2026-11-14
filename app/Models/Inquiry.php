<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    public const STATUS_NEW = 'new';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_PROCESSED = 'processed';

    public const STATUS_REJECTED = 'rejected';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'mail_sent_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_NEW => 'Новая',
            self::STATUS_IN_PROGRESS => 'В работе',
            self::STATUS_PROCESSED => 'Обработана',
            self::STATUS_REJECTED => 'Отклонена',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? (string) $this->status;
    }
}
