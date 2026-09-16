@extends('layouts.app')
@section('content')
<div class="workspace-page" dir="rtl">
  <header class="workspace-page__head"><div><span class="workspace-page__eyebrow"><i class="fa-solid fa-bolt"></i> میز کار</span><h1>میانبرها</h1></div></header>
  <div class="workspace-list">
    @foreach($items as $item)
      <article><a href="{{ route($item['route']) }}"><i class="fa-solid {{ $item['icon'] }}"></i> {{ $item['label'] }}</a></article>
    @endforeach
  </div>
</div>
@endsection
