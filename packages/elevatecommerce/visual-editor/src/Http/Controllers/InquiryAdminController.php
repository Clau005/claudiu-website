<?php

namespace ElevateCommerce\VisualEditor\Http\Controllers;

use ElevateCommerce\VisualEditor\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InquiryAdminController extends Controller
{
    /**
     * Display a listing of inquiries.
     */
    public function index(Request $request)
    {
        $query = Inquiry::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortableColumns = ['type', 'status', 'priority', 'created_at'];
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortColumn, $sortableColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $inquiries = $query->paginate(20)->withQueryString();

        // Get counts for filters
        $counts = [
            'all' => Inquiry::count(),
            'new' => Inquiry::where('status', 'new')->count(),
            'read' => Inquiry::where('status', 'read')->count(),
            'replied' => Inquiry::where('status', 'replied')->count(),
            'closed' => Inquiry::where('status', 'closed')->count(),
        ];

        return view('visual-editor::admin.inquiries.index', [
            'inquiries' => $inquiries,
            'counts' => $counts,
            'navigation' => app('visual-editor.navigation')->all(),
        ]);
    }

    /**
     * Display the specified inquiry.
     */
    public function show(Inquiry $inquiry)
    {
        // Mark as read when viewed
        $inquiry->markAsRead();

        return view('visual-editor::admin.inquiries.show', [
            'inquiry' => $inquiry,
            'navigation' => app('visual-editor.navigation')->all(),
        ]);
    }

    /**
     * Update the specified inquiry.
     */
    public function update(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,read,replied,closed',
            'priority' => 'required|in:low,normal,high,urgent',
            'admin_notes' => 'nullable|string',
        ]);

        $inquiry->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Inquiry updated successfully.');
    }

    /**
     * Remove the specified inquiry.
     */
    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()
            ->route('admin.inquiries.index')
            ->with('success', 'Inquiry deleted successfully.');
    }

    /**
     * Bulk actions for inquiries.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:mark_read,mark_replied,mark_closed,delete',
            'inquiry_ids' => 'required|array',
            'inquiry_ids.*' => 'exists:inquiries,id',
        ]);

        $inquiries = Inquiry::whereIn('id', $validated['inquiry_ids'])->get();

        switch ($validated['action']) {
            case 'mark_read':
                foreach ($inquiries as $inquiry) {
                    $inquiry->markAsRead();
                }
                $message = count($inquiries) . ' inquiries marked as read.';
                break;

            case 'mark_replied':
                foreach ($inquiries as $inquiry) {
                    $inquiry->markAsReplied();
                }
                $message = count($inquiries) . ' inquiries marked as replied.';
                break;

            case 'mark_closed':
                Inquiry::whereIn('id', $validated['inquiry_ids'])->update(['status' => 'closed']);
                $message = count($inquiries) . ' inquiries marked as closed.';
                break;

            case 'delete':
                Inquiry::whereIn('id', $validated['inquiry_ids'])->delete();
                $message = count($inquiries) . ' inquiries deleted.';
                break;

            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()
            ->route('admin.inquiries.index')
            ->with('success', $message);
    }
}
