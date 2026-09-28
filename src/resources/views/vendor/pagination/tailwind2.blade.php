

@if ($paginator->hasPages())
    <ul class="board-pagination">
        @if ($paginator->onFirstPage())
            <li class="prev disabled"><span class="lt">&lt;</span> 이전글</li>
        @else
            <li class="prev" onclick="location.href='{{ $paginator->previousPageUrl() }}'"><span class="lt">&lt;</span> 이전글</li>
        @endif
            @php
                $routeName = Request::route()->getName();
                $routeTarget = '';

                if(str_contains($routeName, 'notice')) {
                    $routeTarget = route('noticeView', ['page' => app('request')->input('listPage')]);
                }
                else if (str_contains($routeName, 'faq')) {
                    $routeTarget = route('faqView', ['page' => app('request')->input('listPage')]);
                }
            @endphp
            <li onclick="location.href='{{ $routeTarget }}'">목록</li>
        @if ($paginator->hasMorePages())
            <li class="next" onclick="location.href='{{ $paginator->nextPageUrl() }}'">다음글 <span class="gt">&gt;</span></li>
        @else
            <li class="next disabled">다음글 <span class="gt">&gt;</span></li>
        @endif
    </ul>
@else
    <ul class="board-pagination">
{{--        <li onclick="location.href='{{ route('faqView', ['page' => app('request')->input('listPage')]) }}'">목록</li>--}}
{{--        @if(str_contains($routeName, 'noticeDetailView'))--}}
{{--            <li onclick="location.href='{{ route('noticeView', ['page' => app('request')->input('listPage')]) }}'">목록</li>--}}
{{--        @elseif(str_contains($routeName, 'faqDetailView'))--}}
{{--            <li onclick="location.href='{{ route('faqView', ['page' => app('request')->input('listPage')]) }}'">목록</li>--}}
{{--        @endif--}}
        @php
            $routeName = Request::route()->getName();
             $routeTarget = '';

             if(str_contains($routeName, 'notice')) {
                 $routeTarget = route('noticeView', ['page' => app('request')->input('listPage')]);
             }
             else if (str_contains($routeName, 'faq')) {
                 $routeTarget = route('faqView', ['page' => app('request')->input('listPage')]);
             }
        @endphp
        <li onclick="location.href='{{ $routeTarget }}'">목록</li>
    </ul>
@endif