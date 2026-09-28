@extends('layouts.layout')

{{--@section('title')--}}
{{--        타이틀--}}
{{--@endsection--}}


@section('content')

    @error('content_length')
{{--    <div class="popup confirm" id="errorModal">--}}
{{--        <div class="popup__dim" onclick="document.getElementById('errorModal').remove()"></div>--}}
{{--        <div class="popup-wrap">--}}
{{--            <div class="confirm-txt">--}}
{{--                {{ $message }}--}}
{{--            </div>--}}
{{--            <div class="confirm-btn">--}}
{{--                <button id="btnCommitPeed" class="btn w-100" onclick="document.getElementById('errorModal').remove()">확인</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    @enderror

    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>{{ $classApply->korName }}</span></h3>
            {{--탭--}}
            <ul class="container-tab">
                <li onclick="location.href='{{ route('lectureDetailInfo', [ $classApply->id ]) }}'">정보</li>
                <li class="active" onclick="location.href='{{ route('lectureDetailMember', [ $classObjectId ]) }}'">참여자</li>
                <li onclick="location.href='{{ route('lectureDetailTeamSelect', [ $classObjectId ]) }}'">팀배정</li>
                <li onclick="location.href='{{ route('lectureDetailProblem', [ $classObjectId ]) }}'">문제분석</li>
                <li onclick="location.href='{{ route('lectureDetailTeam', [ $classObjectId ]) }}'">팀활동보고서</li>
                <li onclick="location.href='{{ route('lectureDetailEvolutionPaper', [ $classObjectId ]) }}'">평가지</li>
                <li onclick="location.href='{{ route('lectureDetailMind', [ $classObjectId ]) }}'">성찰</li>
{{--                <li onclick="location.href='{{ route('lectureDetailStatistics', [ $classApply->classObject->id ]) }}'">통계</li>--}}
            </ul>

{{--            <div>--}}
{{--                <button class="btn btn-primary btn-md mb-10" onclick="location.href='{{ route('testMemberAdd', ['classObjectId' => $classObjectId]) }}'">멤버 추가 임시 버튼</button>--}}
{{--            </div>--}}


            <div class="container-top">
{{--                검색을 위한 폼--}}
{{--                @error('content')--}}
{{--                    {{ $message }}--}}
{{--                @enderror--}}
                <form method="GET" action="{{ route('lectureDetailMember', ['classObjectId' => $classObjectId]) }}">
{{--                    get방식이라서 @csrf 안써두 댐--}}
                    <div class="left">
                        <select class="custom-select" name="type">
                            <option value="name" selected>이름</option>
                            <option value="email">아이디</option>
                        </select>
                        <div class="search__wrap">
                            <input name="content" class="search-input" type="text" placeholder="검색어를 두자 이상 입력하세요"/>
                            <button class="search-btn"></button>
        {{--                        @if(request()->has('key')) value="{{ request()->key }}"--}}
                        </div>
                    </div>
                </form>
                <button onclick="location.href='{{ route('testMember', [ $classObjectId ]) }}'" class="btn btn-primary btn-md ml-5 float-right">임시 참여자 추가</button>
                <button type="button" class="btn btn-md btn-primary btn-line float-right" onclick="popOpen('externalModal')">조교/외부전문가 추가</button>
            </div>
            <div class="contents__wrap">
                <table class="list-table">
                    <thead>
                    <tr>
                        <th>번호</th>
                        <th>이름</th>
                        <th>아이디</th>
                        <th>팀</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $key = 0;
                    @endphp

                    @foreach($classLists as $classList)
                        <tr>
                            <td>{{ ($classLists->currentPage() - 1) * $classLists->perPage() + $key++ + 1 }}</td>
                            {{-- 기존 = $classList->user()-> 을 통해서 값을 가져왔음                        --}}
                            <td>{{ $classList->name ?? $classList->user()->name }}</td>
                            <td>{{ $classList->email ?? $classList->user()->email }}</td>
                            <td>{{ $classList->team()->name ?? '' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $classLists->withQueryString()->links(('vendor.pagination.tailwind')) }}
            </div>
        </div>
    </div>

    <div class="popup confirm d-none" id="classManagerModal">
        <div class="popup__dim" onclick="popClose('classManagerModal')"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                해당 참여자를 조교로 지정하시겠습니까?
            </div>
            <div class="confirm-btn">
                <button type="button" class="btn w-50 fc-gray" onclick="popClose('classManagerModal')">취소</button>
                <button class="btn w-50" onclick="popConfirm('classManager')">확인</button>
            </div>
        </div>
    </div>

{{--    <div class="popup confirm d-none" id="externalModal">--}}
{{--        <div class="popup__dim" onclick="popClose('externalModal')"></div>--}}
{{--        <div class="popup-wrap">--}}
{{--            <div class="confirm-txt">--}}
{{--                해당 참여자를 외부전문가로 지정하시겠습니까?--}}
{{--            </div>--}}
{{--            <div class="confirm-btn">--}}
{{--                <button type="button" class="btn w-50 fc-gray" onclick="popClose('externalModal')">취소</button>--}}
{{--                <button class="btn w-50" onclick="popConfirm()">확인</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

    <div class="popup confirm d-none" id="externalModal" ref="externalModal">
        <lecture-etc
            :class-object-id="{{$classObjectId}}"
        ></lecture-etc>
    </div>
</div>

@endsection

<script type="text/javascript">
    window.onload = () => {
        $(".custom-select").niceSelect();
    }
    let route = '';
    const popOpen = (popName, _route) =>{
        document.getElementById(popName).classList.remove('d-none');
        route = _route;
    }
    const popClose = (popName) =>{
        document.getElementById(popName).classList.add('d-none');
    }

    const popConfirm = () =>{
        document.location.href = route;
    }

</script>
