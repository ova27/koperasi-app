{{-- layouts/sidebar.blade.php --}}

@if(auth()->user()?->hasRole(['admin','superadmin']))
    @include('layouts.sidebar.admin')
@else
    @include('layouts.sidebar.anggota')
@endif
