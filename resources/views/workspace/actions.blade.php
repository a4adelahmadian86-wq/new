@extends('layouts.app')
@section('content')
<div class="workspace-page" dir="rtl">
  <header class="workspace-page__head"><div><span class="workspace-page__eyebrow"><i class="fa-solid fa-bell"></i> میز کار</span><h1>موارد نیازمند اقدام</h1></div></header>
  <div class="workspace-list">
    @forelse($needsAttention as $item)
      <article class="workspace-card"><strong><i class="fa-solid {{ $item['icon'] }}"></i> {{ $item['title'] }}</strong><p>{{ $item['body'] }}</p></article>
    @empty
      <div class="workspace-empty">مورد فوری نیست.</div>
    @endforelse
  </div>
</div>
@endsection
