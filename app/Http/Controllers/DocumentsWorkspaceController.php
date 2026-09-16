<?php

namespace App\Http\Controllers;

use App\Models\TypingDocument;
use Illuminate\Http\Request;

class DocumentsWorkspaceController extends Controller
{
    public function mine(Request $request)
    {
        $documents = TypingDocument::query()
            ->where('user_id', $request->user()->id)
            ->where('status', '!=', 'deleted')
            ->latest()
            ->paginate(20);

        return view('documents.mine', compact('documents'));
    }

    public function recent(Request $request)
    {
        $documents = TypingDocument::query()
            ->where('user_id', $request->user()->id)
            ->where('status', '!=', 'deleted')
            ->latest('updated_at')
            ->paginate(20);

        return view('documents.list', [
            'documents' => $documents,
            'heading' => 'اسناد اخیر',
            'subtitle' => 'بر اساس آخرین به‌روزرسانی',
        ]);
    }

    public function drafts(Request $request)
    {
        $documents = TypingDocument::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['draft', 'pending'])
            ->latest()
            ->paginate(20);

        return view('documents.list', [
            'documents' => $documents,
            'heading' => 'پیش‌نویس‌ها',
            'subtitle' => 'اسناد ناتمام یا در انتظار',
        ]);
    }

    public function deleted(Request $request)
    {
        $documents = TypingDocument::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'deleted')
            ->latest()
            ->paginate(20);

        return view('documents.list', [
            'documents' => $documents,
            'heading' => 'حذف‌شده‌ها',
            'subtitle' => 'اسنادی که وضعیت حذف دارند',
        ]);
    }

    public function all(Request $request)
    {
        $documents = TypingDocument::query()
            ->with('user:id,name,mobile')
            ->latest()
            ->paginate(30);

        return view('documents.all', compact('documents'));
    }
}
