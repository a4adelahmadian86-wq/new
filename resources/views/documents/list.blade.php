@extends('layouts.app')
@section('content')
<div class="workspace-page" dir="rtl">
  <header class="workspace-page__head"><div><span class="workspace-page__eyebrow"><i class="fa-solid fa-files"></i> اسناد</span><h1>{{ $heading }}</h1><p>{{ $subtitle }}</p></div></header>
  <div class="workspace-list">
    @forelse($documents as $d)
      <article><span>{{ $d->title }}</span><span>{{ $d->status }}</span></article>
    @empty
      <div class="workspace-empty">موردی نیست.</div>
    @endforelse
  </div>
  {{ $documents->links() }}
</div>
@endsection
