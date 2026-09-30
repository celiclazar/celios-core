<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Form extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'fields',
        'recipient_emails',
        'recipient_user_ids',
        'send_confirmation_email',
        'confirmation_email_subject',
        'confirmation_email_body',
        'redirect_type',
        'redirect_page_id',
        'redirect_url',
        'success_message',
        'submit_button_text',
        'authorized_roles',
        'is_active',
    ];

    public $translatable = [
        'title',
        'description',
        'success_message',
        'submit_button_text',
        'confirmation_email_subject',
        'confirmation_email_body',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'fields' => 'array',
        'recipient_emails' => 'array',
        'recipient_user_ids' => 'array',
        'send_confirmation_email' => 'boolean',
        'confirmation_email_subject' => 'array',
        'confirmation_email_body' => 'array',
        'success_message' => 'array',
        'submit_button_text' => 'array',
        'authorized_roles' => 'array',
        'is_active' => 'boolean',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class)->latest('id');
    }

    protected static function booted(): void
    {
        static::saving(function (Form $form) {
            $fields = $form->fields ?? [];
            if (is_array($fields)) {
                $usedKeys = [];
                $updatedFields = [];
                foreach ($fields as $field) {
                    $key = $field['key'] ?? null;
                    if (blank($key)) {
                        $label = is_array($field['label'] ?? null)
                            ? ($field['label']['sr'] ?? $field['label']['en'] ?? $field['label']['it'] ?? reset($field['label']) ?: 'field')
                            : ($field['label'] ?? 'field');

                        $slug = (string) str($label)->slug('_');
                        $key = filled($slug) ? $slug : 'field';
                    }

                    $originalKey = $key;
                    $counter = 1;
                    while (in_array($key, $usedKeys)) {
                        $key = "{$originalKey}_{$counter}";
                        $counter++;
                    }
                    $usedKeys[] = $key;
                    $field['key'] = $key;
                    $updatedFields[] = $field;
                }
                $form->fields = $updatedFields;
            }
        });
    }

    public function redirectPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'redirect_page_id');
    }

    public function getResolvedRedirectUrl(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();

        if ($this->redirect_type === 'page' && $this->redirect_page_id) {
            $page = $this->redirectPage ?? Page::find($this->redirect_page_id);
            return $page ? $page->getUrl($locale) : null;
        }

        if ($this->redirect_type === 'custom' && filled($this->redirect_url)) {
            $url = trim($this->redirect_url);
            if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                return $url;
            }
            return url('/' . ltrim($url, '/'));
        }

        return null;
    }

    /**
     * Check if a given user has permission to view submissions of this form.
     */
    public function canUserViewSubmissions(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        $roles = $this->authorized_roles ?? [];

        if (empty($roles)) {
            return true;
        }

        return $user->hasAnyRole($roles);
    }
}
