@extends('layouts.layout')

@section('content')
    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>{{$classApply->korName}}</span></h3>
            {{--탭--}}
            <ul class="container-tab">
                <li onclick="location.href='{{ route('lectureDetailInfo', [ $classApply->id ]) }}'">정보</li>
                <li onclick="location.href='{{ route('lectureDetailMember', [ $classObject->id ]) }}'">참여자</li>
                <li onclick="location.href='{{ route('lectureDetailTeamSelect', [ $classObject->id ]) }}'">팀배정</li>
                <li onclick="location.href='{{ route('lectureDetailProblem', [ $classObject->id ]) }}'">문제분석</li>
                <li class="active" onclick="location.href='{{ route('lectureDetailTeam', [ $classObject->id ]) }}'">팀활동보고서</li>
                <li onclick="location.href='{{ route('lectureDetailEvolutionPaper', [ $classObject->id ]) }}'">평가지</li>
                <li onclick="location.href='{{ route('lectureDetailMind', [ $classObject->id ]) }}'">성찰</li>
{{--                <li onclick="location.href='{{ route('lectureDetailStatistics', [ $classApply->classObject->id ]) }}'">통계</li>--}}
            </ul>
            <div class="contents__wrap">
                <div class="input-table__wrap">
                    <h4 class="con-tit">{{ $teamActivity->item()->card()->team()->name ?? '' }} / 팀활동보고서 </h4>
                </div>
                    <table class="table-input__wrap table t-center">
                        <colgroup>
                            <col width="15%" />
                            <col width="15%" />
                            <col width="27.5%" />
                            <col width="15" />
                            <col width="27.5%" />
                        </colgroup>
                        <tr>
                            <th rowspan="3">미팅<br />개요</th>
                            <th>일시</th>
                            <td>{{ $teamActivity->dateTime }}</td>
                            <th>문제해결과정</th>
                            <td>{{ $teamActivity->problemSolvingProcess }}</td>
                        </tr>
                        <tr>
                            <th>참석자</th>
                            <td colspan="3">{{ $teamActivity->attendees }}</td>
                        </tr>
                        <tr>
                            <th>본 미팅의<br />주요활동</th>
                            <td colspan="3">{{ $teamActivity->mainActivities }}</td>
                        </tr>
                    </table>
                    <table class="table-input__wrap table t-center">
                        <colgroup>
                            <col width="15%" />
                            <col width="15%" />
                            <col width="27.5%" />
                            <col width="15" />
                            <col width="27.5%" />
                        </colgroup>
                        <tr>
                            <th rowspan="3">진행<br />사항</th>
                            <th>구분</th>
                            <th colspan="2">황동내용</th>
                            <th>조치사항</th>
                        </tr>
                        <tr>
                            <th>이번<br />미팅에서<br/>한 일</th>
                            <td colspan="2">{{ $teamActivity->task1 }}</td>
                            <td class="b-left">{{ $teamActivity->task2 }}</td>
                        </tr>
                        <tr>
                            <th>기타</th>
                            <td colspan="3">{{ $teamActivity->discuss1 }}</td>
                        </tr>
                    </table>
                    <table class="table-input__wrap table t-center">
                        <colgroup>
                            <col width="15%" />
                            <col width="15%" />
                            <col width="27.5%" />
                            <col width="15" />
                            <col width="27.5%" />
                        </colgroup>
                        <tr>
                            <th rowspan="3">추후<br />계획</th>
                            <th>구분</th>
                            <th>활동 내용</th>
                            <th>역할 분담</th>
                            <th>조치 사항</th>
                        </tr>
                        <tr>
                            <th>다음<br />미팅에서<br />해야 할 일</th>
                            <td>{{ $teamActivity->schedule1 }}</td>
                            <td class="b-left">{{ $teamActivity->schedule2 }}</td>
                            <td class="b-left">{{ $teamActivity->schedule3 }}</td>
                        </tr>
                        <tr>
                            <th>기타</th>
                            <td colspan="3">{{ $teamActivity->schedule4 }}</td>
                        </tr>
                    </table>
                </div>
                <div class="contents__wrap mt-10">
                    <form method="POST" action="{{ route('lectureDetailTeamReportFeedback', ['classObjectId' => $classObject->id, 'teamActivityId' => $teamActivity->id]) }}" id="feedback">
                        @csrf
                        <table class="table-input__wrap table t-center">
                            <colgroup>
                                <col width="15%" />
                                <col width="15%" />
                                <col width="70%" />
                            </colgroup>
                            <tr>
                                <th>피드백</th>
                                <th>교수님의<br>피드백<br>사항</th>
                                    <td>
                                        <textarea rows="3" name="feedback">{{ old('feedback') ?? $teamActivity->feedback }}</textarea>
                                    </td>
                            </tr>
                        </table>
                    </form>
                    <div class="t-center mt-20">
                        <button onclick="onClickedPeedCancel()" class="btn btn-md2 btn-primary btn-line mr-10">취소</button>
                        <button onclick="onClickedPeedConfirm()" class="btn btn-md2 btn-primary">확인</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 피드백을 작성하시겠습니까?--}}
    <div id="confirmPeed" class="popup confirm d-none">
        <div class="popup__dim"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                피드백을 작성하시겠습니까?
            </div>
            <div class="confirm-btn">
                <button id="btnConfirmCancel" type="button" class="btn w-50 fc-gray">취소</button>
                <button id="btnCommitPeed" class="btn w-50" onclick="document.getElementById('feedback').submit()">확인</button>
            </div>
        </div>
    </div>

    {{-- 피드백을 작성을 취소하시겠습니까?--}}
    <div id="cancelPeed" class="popup confirm d-none">
        <div class="popup__dim"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                피드백을 작성을 취소하시겠습니까?
            </div>
            <div class="confirm-btn">
                <button id="btnCancelCancel" type="button" class="btn w-50 fc-gray">취소</button>
                <button id="btnCancelPeed" class="btn w-50" onclick="location.href='{{ route('lectureDetailTeam', [ 'classObjectId' => $classObject->id ]) }}'">확인</button>
            </div>
        </div>
    </div>

@endsection



<script>
    //피드백을 작성하시겠습니까?
    function onClickedPeedConfirm (){
        //모달 최상위 div연결
        var confirmPeed = document.getElementById('confirmPeed');
        confirmPeed.classList.remove('d-none');

        //모달의 취소버튼 연결
        var btnCancel = document.getElementById('btnConfirmCancel');

        //모달 숨기기
        btnCancel.addEventListener('click',function(){
            confirmPeed.classList.add('d-none');
        })

        //모달의 확인버튼 누름
        var btnConfirm = document.getElementById('btnCommitPeed');
        btnConfirm.addEventListener('click',function(){
            console.log("피드백을 작성했습니다.");
        })
    }

    //피드백 작성을 취소하시겠습니까?
    function onClickedPeedCancel (){
        //모달 최상위 div연결
        var cancelPeed = document.getElementById('cancelPeed');
        cancelPeed.classList.remove('d-none');

        //모달 취소버튼 연결
        var btnCancel = document.getElementById('btnCancelCancel')
        //모달 숨기기
        btnCancel.addEventListener('click', function(){
            cancelPeed.classList.add('d-none');
        })

        {{--var btnConfirm = document.getElementById('btnCancelPeed');--}}
        {{--btnConfirm.addEventListener('click', function(){--}}

        {{--    window.location = "'{{ route('lectureDetailTeam', ['classObjectId',  $classObjectId ]) }}'";--}}
        {{--})--}}
    }

</script>