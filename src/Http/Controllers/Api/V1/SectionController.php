<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\SectionResource;
use Celios\Core\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    /**
     * List active reusable sections.
     */
    public function index(): AnonymousResourceCollection
    {
        $sections = Section::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return SectionResource::collection($sections);
    }

    /**
     * Show a reusable section by slug or ID.
     */
    public function show(string $slugOrId): SectionResource
    {
        $section = Section::query()
            ->where('is_active', true)
            ->where(function ($query) use ($slugOrId) {
                $query->where('slug', $slugOrId)
                    ->orWhere('id', is_numeric($slugOrId) ? (int) $slugOrId : 0);
            })
            ->firstOrFail();

        return new SectionResource($section);
    }

    /**
     * Create a new reusable section (Admin).
     */
    public function store(Request $request): SectionResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:sections,slug'],
            'category' => ['nullable', 'string', 'max:100'],
            'layout' => ['nullable', 'string', 'max:50'],
            'style' => ['nullable', 'array'],
            'header' => ['nullable', 'array'],
            'columns' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $section = Section::create($validated);

        return new SectionResource($section);
    }

    /**
     * Update an existing reusable section (Admin).
     */
    public function update(Request $request, Section $section): SectionResource
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('sections', 'slug')->ignore($section->id)],
            'category' => ['nullable', 'string', 'max:100'],
            'layout' => ['nullable', 'string', 'max:50'],
            'style' => ['nullable', 'array'],
            'header' => ['nullable', 'array'],
            'columns' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $section->update($validated);

        return new SectionResource($section);
    }

    /**
     * Delete a reusable section (Admin).
     */
    public function destroy(Section $section): JsonResponse
    {
        $section->delete();

        return response()->json([
            'message' => 'Section deleted successfully.',
        ]);
    }
}
