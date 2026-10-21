<?php

namespace App\Models;

use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $company
 * @property string|null $email
 * @property string $phone
 * @property string $message
 * @property Carbon|null $read_at
 */
#[Fillable(['first_name', 'last_name', 'company', 'email', 'phone', 'message'])]
class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * Determine if the message has been read
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
