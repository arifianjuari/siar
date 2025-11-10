<?php

namespace App\Http\Controllers\Modules\ActivityManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\ActivityStatusLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ActivityCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $activityUuid)
    {
        $activity = Activity::where('uuid', $activityUuid)->firstOrFail();

        $comments = $activity->comments()
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $comments,
                'html' => view('modules.activity_management.comments.partials.comment_list', compact('comments', 'activity'))->render()
            ]);
        }

        return view('modules.activity_management.comments.index', compact('activity', 'comments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $activityUuid)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
            'parent_id' => 'nullable|exists:activity_comments,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx,ppt,pptx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get activity by UUID
            $activity = Activity::where('uuid', $activityUuid)->firstOrFail();

            // Proses lampiran jika ada
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('activity_comments/' . $activity->id, 'public');
                    $attachments[] = [
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType()
                    ];
                }
            }

            // Simpan komentar
            $comment = new ActivityComment();
            $comment->activity_id = $activity->id;
            $comment->user_id = Auth::id();
            $comment->comment = $request->comment;
            $comment->parent_id = $request->parent_id;
            $comment->attachments = !empty($attachments) ? $attachments : null;
            $comment->save();

            // Log aktivitas jika ini adalah komentar utama (bukan balasan)
            if (!$request->parent_id) {
                ActivityStatusLog::create([
                    'activity_id' => $activity->id,
                    'changed_by' => Auth::id(),
                    'log_type' => 'comment_added',
                    'from_value' => null,
                    'to_value' => 'Komentar baru ditambahkan',
                    'note' => 'Komentar baru ditambahkan oleh ' . Auth::user()->name,
                    'created_at' => now()
                ]);
            }

            // Load relasi user untuk respon
            $comment->load('user');

            // Jika ini adalah balasan, load komentar induk dan relasinya
            if ($request->parent_id) {
                $parentComment = ActivityComment::with(['user', 'replies.user'])
                    ->findOrFail($request->parent_id);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Balasan berhasil ditambahkan',
                    'data' => $comment,
                    'parent' => $parentComment,
                    'html' => view('modules.activity_management.comments.partials.reply_item', compact('comment'))->render()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Komentar berhasil ditambahkan',
                'data' => $comment,
                'html' => view('modules.activity_management.comments.partials.comment_item', compact('comment'))->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx,ppt,pptx|max:10240',
            'remove_attachments' => 'nullable|array',
            'remove_attachments.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $comment = ActivityComment::findOrFail($id);

            // Verifikasi bahwa pengguna yang saat ini login adalah pemilik komentar atau memiliki peran admin/supervisor
            if ($comment->user_id !== Auth::id() && !(Auth::user()->role && in_array(Auth::user()->role->slug, ['admin', 'tenant-admin', 'supervisor']))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki izin untuk mengubah komentar ini'
                ], 403);
            }

            // Update teks komentar
            $comment->comment = $request->comment;

            // Proses lampiran yang ada (jika ada yang dihapus)
            $existingAttachments = $comment->attachments ?? [];
            $updatedAttachments = [];

            if (!empty($existingAttachments) && $request->has('remove_attachments')) {
                foreach ($existingAttachments as $attachment) {
                    // Jika attachment tidak dalam daftar yang dihapus, simpan
                    if (!in_array($attachment['path'], $request->remove_attachments)) {
                        $updatedAttachments[] = $attachment;
                    } else {
                        // Hapus file dari storage
                        Storage::disk('public')->delete($attachment['path']);
                    }
                }
            } else {
                $updatedAttachments = $existingAttachments;
            }

            // Proses lampiran baru jika ada
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('activity_comments/' . $comment->activity_id, 'public');
                    $updatedAttachments[] = [
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType()
                    ];
                }
            }

            $comment->attachments = !empty($updatedAttachments) ? $updatedAttachments : null;
            $comment->save();

            // Load relasi user untuk respon
            $comment->load('user');

            // Jika ini adalah balasan, berikan data yang berbeda
            if ($comment->parent_id) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Balasan berhasil diperbarui',
                    'data' => $comment,
                    'html' => view('modules.activity_management.comments.partials.reply_item', compact('comment'))->render()
                ]);
            }

            // Load replies jika ini adalah komentar utama
            if (!$comment->parent_id) {
                $comment->load('replies.user');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Komentar berhasil diperbarui',
                'data' => $comment,
                'html' => view('modules.activity_management.comments.partials.comment_item', compact('comment'))->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $comment = ActivityComment::with('replies')->findOrFail($id);

            // Verifikasi bahwa pengguna yang saat ini login adalah pemilik komentar atau memiliki peran admin/supervisor
            if ($comment->user_id !== Auth::id() && !(Auth::user()->role && in_array(Auth::user()->role->slug, ['admin', 'tenant-admin', 'supervisor']))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki izin untuk menghapus komentar ini'
                ], 403);
            }

            // Hapus semua file lampiran dari storage
            if (!empty($comment->attachments)) {
                foreach ($comment->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }

            // Jika ini adalah komentar utama, hapus juga semua balasan
            if (is_null($comment->parent_id)) {
                // Hapus lampiran dari semua balasan
                foreach ($comment->replies as $reply) {
                    if (!empty($reply->attachments)) {
                        foreach ($reply->attachments as $attachment) {
                            Storage::disk('public')->delete($attachment['path']);
                        }
                    }
                }

                // Hapus semua balasan
                $comment->replies()->delete();
            }

            // Hapus komentar
            $comment->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Komentar berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
