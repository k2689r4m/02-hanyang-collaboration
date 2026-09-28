@extends('layouts.layout')


<script type="module">
    {{--import Card from '{{ asset('js/restful/Card.js') }}'--}}
    {{--import Item from '{{ asset('js/restful/Item.js') }}'--}}
    {{--import Fetch from '{{ asset('js/restful/Fetch.js') }}'--}}

    {{--window.Card = new Card('{{ route('home') }}', '{{ csrf_token() }}')--}}
    {{--window.Item = new Item('{{ route('home') }}', '{{ csrf_token() }}')--}}
    {{--window.Fetch = new Fetch('{{ route('home') }}', '{{ csrf_token() }}')--}}


    import Rest from '{{ asset('js/restful/Rest.js') }}'
    window.Rest = new Rest('{{ route('home') }}', '{{ csrf_token() }}')
</script>

@section('content')
    <my-class-main
        :user="{{auth()->user()}}"
        :my-page-id="{{ $myPage->id }}"
        :my-class-objects='@json($myClasses)'
        :re-class-id="{{$reClassId}}"
    ></my-class-main>
@endsection

