@extends('layouts.layout')
<script type="module">
    import Rest from '{{ asset('js/restful/Rest.js') }}'
    window.Rest = new Rest('{{ route('home') }}', '{{ csrf_token() }}')
</script>

@section('content')

    <my_page_main
            :user="{{auth()->user()}}"
            :my-page-id="{{ $applyId }}"
    ></my_page_main>
@endsection
