<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ContactUs;
use Illuminate\Validation\ValidationException;

class ContactUsService
{

    public static function getPaginateContactUs(Request $request)
    {
        try {
            $items = $request->input('items', 10);
            $contactUs = ContactUs::with('grantCollaboratorCategory')->paginate($items);

            return response()->json(
                [
                    'message' => 'contact us retrieved successfully',
                    'status' => true,
                    'data' => $contactUs
                ]
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'message' => 'Failed to retrieve contact us',
                    'status' => false,
                    'error' => $e->getMessage(),
                ],
                200
            );
        }
    }

    public static function getByIdContactUs($id)
{
    try {
        $contactUs = ContactUs::with('grantCollaboratorCategory')->findOrFail($id);

        return response()->json(
            [
                'message' => 'Contact us retrieved successfully',
                'status' => true,
                'data' => $contactUs
            ]
        );
    } catch (\Throwable $e) {
        return response()->json(
            [
                'message' => 'Failed to retrieve contact us record',
                'status' => false,
                'error' => $e->getMessage(),
            ],
            500
        );
    }
}

    public static function createContactUs(Request $request)
    {
        try {
            $validated = $request->validate([
                'grant_collaborator_category_id' => 'required|integer|exists:grant_collaborator_category,id',
                'sender_name' => 'required|string|max:255',
                'sender_institution' => 'required|string|max:255',
                'contact_number' => 'required|string|max:15',
                'type' => 'required|in:challenge,usermessage',
                'subject' => 'required|string|max:255',
                'content' => 'required|string',
                'status' => 'nullable|in:Open,Read,On Process,Close',
            ]);

            $validated['input_date'] = now();
            $validated['status'] = $validated['status'] ?? 'Open';

            ContactUs::create($validated);

            return response()->json([
                'message' => 'Contact us created successfully',
                'status' => true,
                'data' => $validated
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to create contact us',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function updateContactUs(Request $request, $id)
    {
        try {
            $contact = ContactUs::find($id);

            if (!$contact) {
                return response()->json([
                    'message' => 'Contact not found',
                    'status' => false
                ], 404);
            }

            $validated = $request->validate([
                'grant_collaborator_category_id' => 'nullable|integer|exists:grant_collaborator_category,id',
                'sender_name' => 'nullable|string|max:255',
                'sender_institution' => 'nullable|string|max:255',
                'contact_number' => 'nullable|string|max:15',
                'type' => 'nullable|in:challenge,usermessage',
                'subject' => 'nullable|string|max:255',
                'content' => 'nullable|string',
                'status' => 'nullable|in:Open,Read,On Process,Close',
            ]);

            $contact->update($validated);

            return response()->json([
                'message' => 'Contact updated successfully',
                'status' => true,
                'data' => $contact
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to update contact us',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function deleteContactUs($id)
    {
        try {
            $contactus = ContactUs::findOrFail($id);
            $contactus->delete();

            return response()->json([
                'message' => 'Contact us deleted successfully',
                'status' => true
            ], 202);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to delete contact us',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
