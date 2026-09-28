@extends('layouts.layout')

@section('script')
    <script type="text/javascript">
        var selected = null;
        const requestModalOn = (button) => {
            selected = button;
            document.querySelector('#requestModal').style.display = 'block';
        }
        const requestModalOff = () => {
            document.querySelector('#requestModal').style.display = 'none';
        }
        const confirmModalOn = (id) => {
            document.querySelector(`#${id}`).style.display = 'block';
        }
        const confirmModalOff = (id) => {
            document.querySelector(`#${id}`).style.display = 'none';
        }

        const apply = () => {
            requestModalOff();
            axios.post(`{{ route('myStuCom') }}`, { competitionId: selected.id }).then((result) => {
                if (result.data) {
                    window.days[result.data.day].forEach((competition) => {
                        if (competition.id == result.data.competitionApply.competitionId) {
                            competition.competition_applies.push(result.data.competitionApply);
                        }
                    })

                    // console.log(window.days);

                    selected.innerText = '신청완료';
                    selected.disabled = true;
                    confirmModalOn('confirmModal');
                }
                else {
                    confirmModalOn('confirmModal2')
                }
            });

        }
    </script>
@endsection

@section('content')
    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>마이페이지</span></h3>
            {{--탭--}}
            <ul class="container-tab">
                <a href="{{ route('myPageView') }}"><li>수업목록</li></a>
                <a href="{{ route('myStuCom') }}"><li class="active">수강후기 공모전</li></a>
            </ul>

            <div class="container-top">
{{--                <form method="GET" action="{{ route('myPageView') }}" >--}}
                    <div class="col-wrap pt-50">
                        <div class="col-6 h-400 bg-white calendar-type2">
                            <my-calendar
                                    :server="`{{ route('myStuCom') }}`"
                            ></my-calendar>
                        </div>
                        <div class="col-6 h-400 bg-white">
                            <h4 class="popup-sub2 mt-0 mb-20" id="dataDate"></h4>
                            <ul class="request-list type2" id="dataList">
{{--                                <li class="request-list__item">--}}
{{--                                    <div class="con-wrap">--}}
{{--                                        <span class="con">기아 레이 2020 공모전</span>--}}
{{--                                    </div>--}}
{{--                                    <button type="button" class="btn btn-primary btn-line">신청</button>--}}
{{--                                <li class="request-list__item">--}}
{{--                                    <div class="con-wrap">--}}
{{--                                        <span class="con">기아 레이 2020 공모전</span>--}}
{{--                                    </div>--}}
{{--                                    <button disabled="disabled" class="btn btn-primary btn-line">신청완료</button></li>--}}
{{--                                </li>--}}
                            </ul>
                        </div>
                    </div>
{{--                </form>--}}
            </div>
        </div>
    </div>

    <div id="requestModal" class="popup confirm" style="display: none">
        <div onclick="requestModalOff()" class="popup__dim"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                신청하시겠습니까?
            </div>
            <div class="confirm-btn">
                <button type="button" onclick="requestModalOff()" class="btn fc-gray w-50">취소</button>
                <button type="button" onclick="apply()" class="btn w-50">확인</button>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="popup confirm" style="display: none">
        <div onclick="confirmModalOff('confirmModal')" class="popup__dim"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                신청되었습니다.
            </div>
            <div class="confirm-btn">
                <button type="button" onclick="confirmModalOff('confirmModal')" class="btn w-100">확인</button>
            </div>
        </div>
    </div>

    <div id="confirmModal2" class="popup confirm" style="display: none">
        <div onclick="confirmModalOff('confirmModal2')" class="popup__dim"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                신청할 수 없는 공모전입니다.
            </div>
            <div class="confirm-btn">
                <button type="button" onclick="confirmModalOff('confirmModal2')" class="btn w-100">확인</button>
            </div>
        </div>
    </div>

@endsection
