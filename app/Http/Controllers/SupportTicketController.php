<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = SupportTicket::with(['raiser:id,name', 'cargoDetail:id,dispatch_id'])
            ->visibleTo(Auth::user())
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))
            ->orderByDesc('id')
            ->paginate($request->input('per_page', 15));

        return response()->json($tickets);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cargo_detail_id' => 'nullable|exists:cargo_details,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = Auth::user();

        $ticket = SupportTicket::create([
            'subject' => $request->input('subject'),
            'description' => $request->input('description'),
            'cargo_detail_id' => $request->input('cargo_detail_id'),
            'group_id' => $user->group_id,
            'user_id' => $user->id,
        ]);

        return response()->json(['support_ticket' => $ticket->fresh()], 201);
    }

    public function show(SupportTicket $supportTicket)
    {
        abort_unless(
            SupportTicket::visibleTo(Auth::user())->whereKey($supportTicket->id)->exists(),
            404,
            'Support ticket not found.',
        );

        return response()->json([
            'support_ticket' => $supportTicket->load(['raiser:id,name', 'cargoDetail:id,dispatch_id']),
        ]);
    }

    public function update(Request $request, SupportTicket $supportTicket)
    {
        abort_unless(
            SupportTicket::visibleTo(Auth::user())->whereKey($supportTicket->id)->exists(),
            404,
            'Support ticket not found.',
        );

        $validator = Validator::make($request->all(), [
            'subject' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:' . implode(',', SupportTicket::STATUSES),
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $supportTicket->update($validator->validated());

        return response()->json(['support_ticket' => $supportTicket->fresh()]);
    }

    public function destroy(SupportTicket $supportTicket)
    {
        abort_unless(
            SupportTicket::visibleTo(Auth::user())->whereKey($supportTicket->id)->exists(),
            404,
            'Support ticket not found.',
        );

        $supportTicket->delete();

        return response()->json(['message' => 'Support ticket deleted.']);
    }

    /**
     * Count of currently raised/pending (not resolved/closed) tickets, for
     * the App Header badge. Same visibility as index() - group-scoped for
     * group users, global for Admin/Ground Surveyor.
     */
    public function pendingCount()
    {
        $count = SupportTicket::visibleTo(Auth::user())
            ->whereIn('status', SupportTicket::PENDING_STATUSES)
            ->count();

        return response()->json(['pending_count' => $count]);
    }
}
