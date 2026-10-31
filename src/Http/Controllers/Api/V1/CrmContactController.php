<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\ContactNoteResource;
use Celios\Core\Http\Resources\V1\ContactResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Crm\Models\Contact;

class CrmContactController extends Controller
{
    /**
     * List CRM contacts and leads with filtering and pagination (Admin).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $stage = $request->input('stage');
        $search = $request->input('search');
        $perPage = min(max($request->integer('per_page', 15), 1), 50);

        $contacts = Contact::query()
            ->with('assignedUser')
            ->when($stage, fn ($q, $stage) => $q->where('stage', $stage))
            ->when($search, fn ($q, $search) => $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return ContactResource::collection($contacts);
    }

    /**
     * Create a new CRM contact / lead (Admin).
     */
    public function store(Request $request): ContactResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'stage' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:100'],
            'lead_value' => ['nullable', 'numeric'],
            'assigned_to_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['stage'] = $validated['stage'] ?? 'new';

        $contact = Contact::create($validated);

        return new ContactResource($contact->load('assignedUser'));
    }

    /**
     * Show a CRM contact along with interaction notes (Admin).
     */
    public function show(Contact $contact): ContactResource
    {
        return new ContactResource($contact->load(['assignedUser', 'interactions.user']));
    }

    /**
     * Update a CRM contact (Admin).
     */
    public function update(Request $request, Contact $contact): ContactResource
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'stage' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:100'],
            'lead_value' => ['nullable', 'numeric'],
            'assigned_to_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
            'last_contacted_at' => ['nullable', 'date'],
        ]);

        $contact->update($validated);

        return new ContactResource($contact->load('assignedUser'));
    }

    /**
     * Delete a CRM contact (Admin).
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json([
            'message' => 'Contact deleted successfully.',
        ]);
    }

    /**
     * Add an interaction note to a CRM contact (Admin).
     */
    public function storeNote(Request $request, Contact $contact): ContactNoteResource
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:note,call,meeting,email'],
            'content' => ['required', 'string'],
        ]);

        $note = $contact->interactions()->create([
            'user_id' => $request->user()?->id,
            'type' => $validated['type'],
            'content' => $validated['content'],
        ]);

        $contact->update([
            'last_contacted_at' => now(),
        ]);

        return new ContactNoteResource($note->load('user'));
    }
}
