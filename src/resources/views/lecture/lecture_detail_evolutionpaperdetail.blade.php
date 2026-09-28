@extends('layouts.layout')

@section('content')
    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>팀명 :{{--팀명 색깔 다르게--}} {{ $teamId->name }} / 평가지 </span></h3>
        </div>
        <div class="contents__wrap">
            <table>
                {평가지 양식}
            </table>
            <button onclick="onClickedPeedCancel()" class="btn w-50">취소</button><button onclick="onClickedPeedConfirm()" class="btn w-50">확인</button>
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
                <button id="btnCommitPeed" class="btn w-50">확인</button>
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
                <button id="btnCancelPeed" class="btn w-50" onclick="location.href='{{ route('lectureDetailEvolutionPaper', [ 'classObjectId' => $classObjectId ]) }}'">확인</button>
            </div>
        </div>
    </div>

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

@endsection