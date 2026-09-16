@extends('layouts.app')
@section('content')
<div class="workspace-page" dir="rtl">
  <header class="workspace-page__head"><div><span class="workspace-page__eyebrow"><i class="fa-solid fa-user-gear"></i> حساب</span><h1>تنظیمات حساب</h1></div></header>
  @if(session('status'))<div class="workspace-card">{{ session('status') }}</div>@endif
  <form method="post" action="{{ route('account.update') }}" class="workspace-card">@csrf @method('PUT')
    <label>نام<input name="name" value="{{ old('name', $user->name) }}"></label>
    <label>ایمیل<input type="email" name="email" value="{{ old('email', $user->email) }}"></label>
    <button type="submit">ذخیره</button>
  </form>
  <form method="post" action="{{ route('account.password') }}" class="workspace-card">@csrf @method('PUT')
    <label>رمز فعلی<input type="password" name="current_password" required></label>
    <label>رمز جدید<input type="password" name="password" required></label>
    <label>تکرار رمز<input type="password" name="password_confirmation" required></label>
    <button type="submit">تغییر رمز</button>
  </form>
</div>
@endsection
