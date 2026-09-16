@extends('layouts.app')
@section('content')
<div class="workspace-page" dir="rtl">
  <header class="workspace-page__head"><div><span class="workspace-page__eyebrow"><i class="fa-solid fa-file-lines"></i> اسناد</span><h1>اسناد من</h1></div></header>
  <div class="workspace-list">
    @forelse($documents as $d)
      <article><span>{{ $d->title }}</span><a href="{{ route('editor', ['document' => $d->id]) }}">باز کردن</a></article>
    @empty
      <div class="workspace-empty">سندی نیست.</div>
    @endforelse
  </div>
  {{ $documents->links() }}
</div>
@endsection
