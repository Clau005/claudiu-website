<?php

namespace ElevateCommerce\VisualEditor\Http\Controllers;

use ElevateCommerce\VisualEditor\Models\Media;
use ElevateCommerce\VisualEditor\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class MediaController extends Controller
{
    /**
     * Display media library.
     */
    public function index(Request $request)
    {
        $query = Media::query();

        // Filter by type
        if ($request->has('type')) {
            $type = $request->get('type');
            if ($type === 'images') {
                $query->where('mime_type', 'like', 'image/%');
            } elseif ($type === 'videos') {
                $query->where('mime_type', 'like', 'video/%');
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('original_filename', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortableColumns = ['filename', 'original_filename', 'created_at', 'size'];
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortColumn, $sortableColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->latest();
        }

        $media = $query->paginate(50)->withQueryString();

        return view('visual-editor::admin.media.index', [
            'media' => $media,
            'navigation' => app('visual-editor.navigation')->all(),
        ]);
    }

    /**
     * API endpoint for media library (returns JSON).
     */
    public function apiIndex(Request $request)
    {
        $query = Media::query();

        // Filter by type (images only for now)
        $query->where('mime_type', 'like', 'image/%');

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('original_filename', 'like', "%{$search}%")
                  ->orWhere('filename', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        // Get all media with URL
        $media = $query->latest()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'filename' => $item->original_filename,
                'url' => $item->url,
                'alt_text' => $item->alt_text,
                'extension' => $item->extension,
                'size' => $item->size,
                'width' => $item->width,
                'height' => $item->height,
                'mime_type' => $item->mime_type,
            ];
        });

        return response()->json([
            'media' => $media
        ]);
    }

    /**
     * Show single media item.
     */
    public function show(int $id)
    {
        $media = Media::findOrFail($id);

        return view('visual-editor::admin.media.show', [
            'media' => $media,
            'navigation' => app('visual-editor.navigation')->all(),
        ]);
    }

    /**
     * Upload new media.
     */
    public function upload(Request $request)
    {
        try {
            Log::info('Media upload request started', [
                'has_files' => $request->hasFile('files'),
                'all_keys' => array_keys($request->all()),
                'file_keys' => array_keys($request->allFiles()),
            ]);

            $request->validate([
                'files' => 'required|array',
                'files.*' => 'file|max:51200', // 50MB max per file
            ]);

            Log::info('Validation passed', ['files_count' => count($request->file('files'))]);

            $uploadedFiles = [];
            $files = $request->file('files');

        foreach ($files as $file) {
            $originalFilename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();

            // Generate unique filename
            $filename = Str::uuid() . '.' . $extension;
            $path = 'media/' . date('Y/m') . '/' . $filename;

            // Store file
            Storage::disk('public')->put($path, file_get_contents($file));

            // Get image dimensions if it's an image
            $width = null;
            $height = null;
            if (str_starts_with($mimeType, 'image/')) {
                try {
                    $imageSize = getimagesize($file);
                    $width = $imageSize[0] ?? null;
                    $height = $imageSize[1] ?? null;
                } catch (\Exception $e) {
                    // Ignore errors
                }
            }

            // Create media record
            $media = Media::create([
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'path' => $path,
                'disk' => 'public',
                'mime_type' => $mimeType,
                'extension' => $extension,
                'size' => $size,
                'width' => $width,
                'height' => $height,
                'uploaded_by' => auth('admin')->id(),
            ]);

            // Optimize images (create WebP versions and responsive sizes)
            if (str_starts_with($mimeType, 'image/') && class_exists(ImageOptimizer::class)) {
                try {
                    $optimizer = new ImageOptimizer();
                    $optimizer->optimize($path, 'public');
                } catch (\Exception $e) {
                    // Log error but don't fail upload
                    \Log::warning('Image optimization failed: ' . $e->getMessage());
                }
            }

            $uploadedFiles[] = $media;
            
            Log::info('File uploaded successfully', [
                'filename' => $filename,
                'original' => $originalFilename,
                'size' => $size,
            ]);
        }

        Log::info('All files uploaded', ['total' => count($uploadedFiles)]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => count($uploadedFiles),
                'media' => $uploadedFiles,
            ]);
        }

        $count = count($uploadedFiles);
        $message = $count === 1 ? 'File uploaded successfully!' : "{$count} files uploaded successfully!";

        return redirect()
            ->route('admin.media.index')
            ->with('success', $message);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Media upload validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Media upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload files: ' . $e->getMessage(),
                ], 500);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Failed to upload files: ' . $e->getMessage());
        }
    }

    /**
     * Replace existing media file.
     */
    public function replace(Request $request, int $id)
    {
        $media = Media::findOrFail($id);

        $request->validate([
            'file' => 'required|file|max:51200',
        ]);

        $file = $request->file('file');

        // Delete old file
        $media->deleteFile();

        // Upload new file
        $originalFilename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        $filename = Str::uuid() . '.' . $extension;
        $path = 'media/' . date('Y/m') . '/' . $filename;

        Storage::disk('public')->put($path, file_get_contents($file));

        // Get dimensions
        $width = null;
        $height = null;
        if (str_starts_with($mimeType, 'image/')) {
            try {
                $imageSize = getimagesize($file);
                $width = $imageSize[0] ?? null;
                $height = $imageSize[1] ?? null;
            } catch (\Exception $e) {
                // Ignore
            }
        }

        // Update media record
        $media->update([
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'path' => $path,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $size,
            'width' => $width,
            'height' => $height,
        ]);

        return redirect()
            ->route('admin.media.show', $id)
            ->with('success', 'File replaced successfully!');
    }

    /**
     * Update media metadata.
     */
    public function update(Request $request, int $id)
    {
        $media = Media::findOrFail($id);

        $validated = $request->validate([
            'alt_text' => 'nullable|string|max:255',
        ]);

        $media->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'media' => $media,
            ]);
        }

        return redirect()
            ->route('admin.media.show', $id)
            ->with('success', 'Media updated successfully!');
    }

    /**
     * Delete media.
     */
    public function destroy(int $id)
    {
        $media = Media::findOrFail($id);
        
        // Delete file from storage
        $media->deleteFile();
        
        // Delete record
        $media->delete();

        return redirect()
            ->route('admin.media.index')
            ->with('success', 'Media deleted successfully!');
    }

    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,download',
            'ids' => 'required|string',
        ]);

        $ids = array_filter(explode(',', $request->ids));
        $action = $request->action;

        if (empty($ids)) {
            return redirect()
                ->back()
                ->with('error', 'No items selected');
        }

        switch ($action) {
            case 'delete':
                $media = Media::whereIn('id', $ids)->get();
                
                foreach ($media as $item) {
                    $item->deleteFile();
                    $item->delete();
                }

                $count = count($media);
                $message = $count === 1 ? 'File deleted successfully!' : "{$count} files deleted successfully!";
                
                return redirect()
                    ->route('admin.media.index')
                    ->with('success', $message);

            case 'download':
                // For now, just show a message
                // In a real implementation, you'd create a ZIP file
                return redirect()
                    ->back()
                    ->with('success', 'Download feature coming soon!');

            default:
                return redirect()
                    ->back()
                    ->with('error', 'Invalid action');
        }
    }
}
