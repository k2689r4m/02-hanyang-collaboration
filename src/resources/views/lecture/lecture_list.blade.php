@extends('layouts.layout')


<script>
    window.onload = () => {
        $("select").niceSelect();
    }
</script>

@section('content')

{{--    modal start--}}
@error('content_length')
<div class="popup confirm" id="errorModal">
    <div class="popup__dim" onclick="document.getElementById('errorModal').remove()"></div>
    <div class="popup-wrap">
        <div class="confirm-txt">
            {{ $message }}
        </div>
        <div class="confirm-btn">
            <button id="btnCommitPeed" class="btn w-100" onclick="document.getElementById('errorModal').remove()">확인</button>
        </div>
    </div>
</div>
@enderror
{{--    modal end--}}
    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>수업관리</span></h3>
            <div class="container-top">
                <form name="frm" method="GET" action="{{ route('lectureList') }}">
                <div class="left">
                        <select class="custom-select" name="year" onchange="document.frm.submit();">
                            <option value="">선택</option>
                            @foreach(range($year + 3, $year - 3) as $y)
                                <option value="{{ $y }}" @if($y == $year) selected @endif>{{ $y }}년</option>
                            @endforeach
                        </select>
                        <select class="custom-select" name="semester" onchange="document.frm.submit();">
                            <option value="">선택</option>
                            @foreach([10, 15, 20, 25] as $m)
                                <option value="{{ $m }}" @if($m == $semester) selected @endif>
                                    @switch($m)
                                        @case(10)
                                            1
                                        @break
                                        @case(15)
                                            여름계절
                                        @break
                                        @case(20)
                                            2
                                        @break
                                        @case(25)
                                            겨울계절
                                        @break
                                        @endswitch
                                    학기
                                </option>
                            @endforeach
                        </select>
                        <div class="search__wrap">
                            <input name="content" class="search-input" type="text" placeholder="검색어를 두자 이상 입력하세요" value="{{ $content ?? '' }}"/>
                            <button class="search-btn"></button>
                        </div>
                </div>
                </form>
                <div class="left">
{{--                    <button class="btn btn-md btn-primary" onclick="">검색</button>--}}
                </div>
                <div class="right">
{{--                    <button class="btn btn-md btn-primary" onclick="btnOnClickedDownload()">엑셀 다운</button>--}}
                    <form method="get" action="{{ route('lectureExport') }}">
                        <input type="hidden" name="search" value="{{ $content ?? '' }}" />
                        <button class="btn btn-md btn-primary">엑셀 다운</button>
                    </form>

{{--                    <button class="btn btn-md btn-primary" onclick="location.href='{{ route('lectureExport') }}'">엑셀 다운</button>--}}

                </div>
                <div class="right" style="margin-right: 10px;">
                    {{--                    <button class="btn btn-md btn-primary" onclick="btnOnClickedDownload()">엑셀 다운</button>--}}
                    <button class="btn btn-md btn-primary" onclick="location.href='{{ route('testClassAdd') }}'">임시 과목 추가</button>                </div>
            </div>
            <div class="contents__wrap">
                <table class="list-table">
                    <colgroup>
                        <col width="10%" />
                        <col width="10%" />
                        <col width="25%" />
                        <col width="10%" />
                        <col width="12%" />
                        <col width="13%" />
                        <col width="9%" />
                        <col width="11%" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>번호</th>
                            <th>수업번호</th>
                            <th class="left">수업명</th>
                            <th>단대</th>
                            <th>학과</th>
                            <th>학기</th>
                            <th>인원</th>
                            <th>상태</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($applies as $key=>$apply)
{{--                        @if($item->withClassApply)--}}
                        <tr>
                            <td>{{ ($applies->currentPage() - 1) * $applies->perPage() + ($key + 1) }}</td>
{{--                            <td>{{ ($key + 1) }}</td>--}}
{{--                            <td>{{ $applies->count() }}</td>--}}
                            <td>@if($apply->state != 'wait'){{ $apply->code }}@endif</td>
                            <td class="left">
                                <a href="{{ route('lectureDetailInfo', ['classApplyId' => $apply->id]) }}">
                                    {{ $apply->korName }}
                                </a>
                            </td>
                            <td>{{ $apply->gnjDaehakNm }}</td>
                            <td>{{ $apply->department }}</td>
                            <td>{{ $apply->year }} {{ $apply->semester }}</td>
                            <td>
                                @if($apply->state != 'wait')
                                    {{ $apply->classListCount2() }}명
                                @endif
                            </td>
                            <td>
                                @if($apply->state == 'wait')
                                    대기중
                                @elseif($apply->state == 'complete' )
                                    승인완료
                                @elseif($apply->state == 'ing')
{{--                                    진행중--}}
                                    @if(($apply->semester === '1학기' && date('m') >= 7) || ($apply->semester === '2학기' && date('Y') > $apply->year))
                                        완료
                                    @else
                                        진행중
                                    @endif
                                @elseif($apply->state == 'end')
                                    수업완료
                                @endif
                            </td>
                        </tr>
{{--                        @endif--}}
                    @endforeach
                    </tbody>
                </table>
{{--                {{ $items->links() }}--}}
                {{ $applies->withQueryString()->links(('vendor.pagination.tailwind')) }}

            </div>
        </div>
    </div>

@endsection
