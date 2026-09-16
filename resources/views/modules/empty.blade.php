@extends('layouts.app')
@section('content')
<div class="workspace-page" dir="rtl">
  <header class="workspace-page__head">
    <div>
      <span class="workspace-page__eyebrow"><i class="fa-solid fa-puzzle-piece"></i> ماژول</span>
      <h1>{{ $title ?? 'ماژول' }}</h1>
      <p>این بخش در منوی ناوبری ثبت شده و مسیر آن فعال است. محتوای تخصصی به‌تدریج تکمیل می‌شود.</p>
    </div>
  </header>
  <div class="workspace-card workspace-empty">
    <p>slug: <code>{{ $slug }}</code></p>
    <a href="{{ route('dashboard') }}">بازگشت به داشبورد</a>
  </div>
</div>
@endsection
