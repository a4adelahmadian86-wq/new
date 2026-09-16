@extends('layouts.app')
@section('content')
<div class="workspace-page" dir="rtl">
  <header class="workspace-page__head"><div><span class="workspace-page__eyebrow"><i class="fa-solid fa-gauge-high"></i> AI</span><h1>سهمیه AI</h1></div></header>
  <div class="workspace-card"><p>سقف روزانه: {{ ($capabilities['unlimited'] ?? false) ? 'نامحدود' : number_format($capabilities['daily_ai_requests'] ?? 0) }}</p></div>
</div>
@endsection
