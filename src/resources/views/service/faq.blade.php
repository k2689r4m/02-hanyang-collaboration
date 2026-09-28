@extends('layouts.service')

@section('_content')
    <div class="contents__wrap">
        <table class="list-table">
            <colgroup>
                <col width="10%" />
                <col width="90%" />
            </colgroup>
            <thead>
            <th>번호</th>
            <th class="left">자주묻는질문</th>
            </thead>
            <tbody>
            @foreach($faqs as $faq)
                <tr>
                    <td>{{ $faq->id }}</td>
                    <td class="left"><a href="{{ route('faqDetailView', ['faqId' => $faq->id, 'listPage' => $faqs->currentPage()]) }}">{{ $faq->title }}</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $faqs->links(('vendor.pagination.tailwind')) }}
    </div>
@endsection

