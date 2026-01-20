<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use NumberFormatter;
use DateTimeZone;

class NodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $language = $request->header('Accept-Language', 'en');
        $timezone = $request->header('Time-Zone', 'UTC');

        if (!in_array($timezone, DateTimeZone::listIdentifiers())) {
            throw ValidationException::withMessages([
                'Time-Zone' => 'Invalid timezone',
            ]);
        }

        $formatter = new NumberFormatter($language, NumberFormatter::SPELLOUT);

        return [
            'id' => $this->id,
            'parent' => $this->parent_id,
            'title' => $formatter->format($this->id),
            'created_at' => $this->created_at
                ->copy()
                ->timezone($timezone)
                ->toDateTimeString(),
        ];
    }
}
