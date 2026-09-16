@extends('layouts.app')
@section('content')
<div class="workspace-page" dir="rtl">
  <header class="workspace-page__head"><div><span class="workspace-page__eyebrow"><i class="fa-solid fa-list-check"></i> میز کار</span><h1>وظایف من</h1><p>تیکت‌های باز و پیش‌نویس‌ها.</p></div></header>
  <section class="workspace-card"><h2>پیش‌نویس‌ها</h2>
    <div class="workspace-list">
      @forelse($draftDocs as $d)
        <article><span>{{ $d->title }}</span><span>{{ $d->status }}</span></article>
      @empty
        <div class="workspace-empty">پیش‌نویسی نیست.</div>
      @endforelse
    </div>
  </section>
</div>
@endsection
