@extends('layouts.app')
@section('content')
<div class="workspace-page" dir="rtl">
  <header class="workspace-page__head"><div><span class="workspace-page__eyebrow"><i class="fa-solid fa-clock-rotate-left"></i> میز کار</span><h1>فعالیت‌های اخیر</h1><p>اسناد، AI و سفارش‌های اخیر حساب شما.</p></div></header>
  <section class="workspace-card"><h2>اسناد</h2>
    <div class="workspace-list">
      @forelse($documents as $d)
        <article><span>{{ $d->title }}</span><span>{{ $d->status }}</span><a href="{{ route('editor', ['document' => $d->id]) }}">باز کردن</a></article>
      @empty
        <div class="workspace-empty">سندی نیست.</div>
      @endforelse
    </div>
  </section>
</div>
@endsection
