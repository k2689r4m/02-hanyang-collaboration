@extends('layouts.layout')

@section('title')
        타이틀
@endsection

<script>
    window.onload = () => {
        $("select").niceSelect();
    }
    const handleDisplayModal = (action) => {
        const modal = document.getElementById('teamSelectModal');
        switch (action) {
            case 'close':
                modal.classList.add('d-none');
                break;
            case 'open':
                modal.classList.remove('d-none');
                handleDisplayConfirmModal('close');
                break;
            default:
                if (modal.classList.contains('d-none')) {
                    modal.classList.remove('d-none');
                    handleDisplayConfirmModal('close');
                }
                else {
                    modal.classList.add('d-none');
                }
                break;
        }
    }

    // const handleDisplayModal = (action) => {
    //     const modal = document.getElementById('teamSelectModal');
    //     switch (action) {
    //         case 'close':
    //             modal.classList.add('d-none');
    //             break;
    //         case 'open':
    //             modal.classList.remove('d-none');
    //             handleDisplayConfirmModal('close');
    //             break;
    //         default:
    //             if (modal.classList.contains('d-none')) {
    //                 modal.classList.remove('d-none');
    //                 handleDisplayConfirmModal('close');
    //             }
    //             else {
    //                 modal.classList.add('d-none');
    //             }
    //             break;
    //     }
    // }

    const handleDisplayConfirmModal = (action) => {
        const modal = document.getElementById('confirmModal');

        switch (action) {
            case 'close':
                modal.classList.add('d-none');
                break;
            case 'open':
                modal.classList.remove('d-none');
                break;
            default:
                if (modal.classList.contains('d-none')) {
                    modal.classList.remove('d-none');
                }
                else {
                    modal.classList.add('d-none');
                }
                break;
        }
    }

    let route = '';
    const teamLeaderPopOpen = (_route) =>{
        document.getElementById('teamLeaderModal').classList.remove('d-none');
        route = _route;
    }
    const teamLeaderPopClose = () =>{
        document.getElementById('teamLeaderModal').classList.add('d-none');
    }
    const teamLeaderConfirm = () =>{
        document.location.href = route;
    }
</script>

@section('content')

    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>{{$classApply->korName}} </span></h3>
            {{--탭--}}
            <ul class="container-tab">
                <li onclick="location.href='{{ route('lectureDetailInfo', [  $classApply->id ]) }}'">정보</li>
                <li onclick="location.href='{{ route('lectureDetailMember', [ $classObjectId ]) }}'">참여자</li>
                <li class="active" onclick="location.href='{{ route('lectureDetailTeamSelect', [ $classObjectId ]) }}'">팀배정</li>
                <li onclick="location.href='{{ route('lectureDetailProblem', [ $classObjectId ]) }}'">문제분석</li>
                <li onclick="location.href='{{ route('lectureDetailTeam', [ $classObjectId ]) }}'">팀활동보고서</li>
                <li onclick="location.href='{{ route('lectureDetailEvolutionPaper', [ $classObjectId ]) }}'">평가지</li>
                <li onclick="location.href='{{ route('lectureDetailMind', [ $classObjectId ]) }}'">성찰</li>
{{--                <li onclick="location.href='{{ route('lectureDetailStatistics', [ $classApply->classObject->id ]) }}'">통계</li>--}}
            </ul>
            <form method="POST" action="{{ route('lectureDetailTeamSelect.teamNamesChange') }}">
                @csrf
                <input type="hidden" name="classObjectId" value="{{ $classObjectId }}" />
                <div class="container-top team">
                    <div class="right team">
                        <button class="btn btn-md btn-primary btn-line mr-5">팀명저장</button>
                        <button type="button" class="btn btn-md btn-primary" onclick="location.href='{{ route('addTeam', [$classObjectId]) }}'">팀 추가</button>
                        <button type="button" class="btn btn-md btn-primary" onclick="handleDisplayModal()">팀 배정</button>
{{--                        <button type="button" class="btn btn-md btn-primary" onclick="handleDisplayModal()">팀장 지정</button>--}}
                    </div>
                </div>
                <div class="contents__wrap">
                    <table class="list-table sm">
                        <colgroup>
                            <col width="15%" />
                            <col width="25%" />
                            <col width="15%" />
                            <col width="35%" />
                            <col width="10%" />
                        </colgroup>
                        <thead>
                        <tr>
                            <th>번호</th>
                            <th>팀명</th>
                            <th>인원</th>
                            <th class="left">참여자이름</th>
                            <th>비고</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach (range(1,$teams->count()) as $i)
                            <tr>
                                <td>{{ $i }}</td>
                                <input type="hidden" name="id[]" value="{{ $teams[$i - 1]->id ?? '' }}" />
                                <td><input type="text" name="names[]" value="{{ $teams[$i - 1]->name ?? '팀'.$i }}" /></td>
                                <td>{{ $i - 1 < $teams->count() ? $teams[$i - 1]->teamMembers()->count() : 0 }}</td>
                                <td class="left">
                                    @if ($i - 1 < $teams->count())
                                        @foreach ($teams[$i - 1]->teamMembers() as $key=>$member)
                                            @if ($key++ != 0)
                                                @if($teams[$i - 1]->teamMaster() && $teams[$i - 1]->teamMaster()->userId == $member->user()->id)
                                                    {{ ', [팀장]'.$member->user()->name }}
                                                @else
                                                    <button class="btn fc-black p-0" type="button" onclick="teamLeaderPopOpen(`{{ route('teamMaster', ['userId' => $member->user()->id, 'teamId' => $member->teamId]) }}`)">{{ ', '.$member->user()->name }}</button>
                                                @endif

                                            @else
                                                @if($teams[$i - 1]->teamMaster() && $teams[$i - 1]->teamMaster()->userId == $member->user()->id)
                                                    [팀장]{{ $member->user()->name }}
                                                @else
                                                    <button class="btn fc-black p-0" type="button" onclick="teamLeaderPopOpen(`{{ route('teamMaster', ['userId' => $member->user()->id, 'teamId' => $member->teamId]) }}`)">{{ $member->user()->name }}</button>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-gray" href="{{ route('deleteTeam', ['teamId' => $teams[$i - 1]->id]) }}">
                                        삭제
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
    {{--                <ul class="pagination">--}}
    {{--                    <li class="prev">&nbsp;</li>--}}
    {{--                    <li class="active">1</li>--}}
    {{--                    <li>2</li>--}}
    {{--                    <li>3</li>--}}
    {{--                    <li class="next">&nbsp;</li>--}}
    {{--                </ul>--}}
                </div>
            </form>
        </div>
    </div>
    <!-- 팀배정 popup, confirm popup -->
    <form method="POST" action="{{ route('lectureDetailTeamSelect.teamMemberChange') }}">
        @csrf
        <input type="hidden" name="classObjectId" value="{{ $classObjectId }}" />
        <div class="popup d-none" id="teamSelectModal">
            <div class="popup__dim" onclick="handleDisplayModal()"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm">
                    <h3 class="popup-tit">팀 배정</h3>
                    <div class="team-list__wrap">
                        <table class="team-list">
                            <tr>
                                <th>번호</th>
                                <th>이름</th>
                                <th>아이디</th>
                                <th>배정</th>
                            </tr>
                            @foreach ($members as $key=>$member)
                            <tr>
                                <input type="hidden" name="userId[]" value="{{ $member->user()->id }}" />
                                <td><span class="bg">{{ $key + 1 }}</span></td>
                                <td><span class="bg">{{ $member->user()->name }}</span></td>
                                <td><span class="bg">{{ $member->user()->email }}</span></td>
                                <td class="t-center is-select">
                                    <select name="teamId[]">
                                        <option @if(!$member->team()) selected @endif value="0">none</option>
                                    @foreach ($teams as $team)
                                            <option @if($member->team()) @if($member->team()->id == $team->id) selected @endif @endif value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="confirm-btn">
                    <button type="button" class="btn w-50 fc-gray" onclick="handleDisplayModal()">취소</button>
                    <button type="button" class="btn w-50" onclick="handleDisplayConfirmModal()">확인</button>
                </div>
            </div>
        </div>
        <div class="popup confirm d-none" id="confirmModal">
            <div class="popup__dim" onclick="handleDisplayConfirmModal()"></div>
            <div class="popup-wrap">
                <div class="confirm-txt">
                    저장하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button type="button" class="btn w-50 fc-gray" onclick="handleDisplayConfirmModal()">취소</button>
                    <button class="btn w-50">확인</button>
                </div>
            </div>
        </div>
    </form>


{{--    팀장 지정 팝업--}}
    <form method="POST" action="{{ route('lectureDetailTeamSelect.teamMemberChange') }}">
        @csrf
        <input type="hidden" name="classObjectId" value="{{ $classObjectId }}" />
        <div class="popup d-none" id="teamSelectModal">
            <div class="popup__dim" onclick="handleDisplayModal()"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm">
                    <h3 class="popup-tit">팀장 지정</h3>
                    <div class="team-list__wrap">
                        <table class="team-list">
                            <tr>
                                <th>번호</th>
                                <th>이름</th>
                                <th>아이디</th>
                                <th>배정</th>
                            </tr>
                            @foreach ($members as $key=>$member)
                                <tr>
                                    <input type="hidden" name="userId[]" value="{{ $member->user()->id }}" />
                                    <td><span class="bg">{{ $key + 1 }}</span></td>
                                    <td><span class="bg">{{ $member->user()->name }}</span></td>
                                    <td><span class="bg">{{ $member->user()->email }}</span></td>
                                    <td class="t-center is-select">
                                        <select name="teamId[]">
                                            <option @if(!$member->team()) selected @endif value="0">none</option>
                                            @foreach ($teams as $team)
                                                <option @if($member->team()) @if($member->team()->id == $team->id) selected @endif @endif value="{{ $team->id }}">{{ $team->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="confirm-btn">
                    <button type="button" class="btn w-50 fc-gray" onclick="handleDisplayModal()">취소</button>
                    <button type="button" class="btn w-50" onclick="handleDisplayConfirmModal()">확인</button>
                </div>
            </div>
        </div>
        <div class="popup confirm d-none" id="confirmModal">
            <div class="popup__dim" onclick="handleDisplayConfirmModal()"></div>
            <div class="popup-wrap">
                <div class="confirm-txt">
                    저장하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button type="button" class="btn w-50 fc-gray" onclick="handleDisplayConfirmModal()">취소</button>
                    <button class="btn w-50">확인</button>
                </div>
            </div>
        </div>
    </form>

    <div class="popup confirm d-none" id="teamLeaderModal">
        <div class="popup__dim" onclick="teamLeaderPopClose()"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                해당 학생을 팀장으로 지정하시겠습니까?
            </div>
            <div class="confirm-btn">
                <button type="button" class="btn w-50 fc-gray" onclick="teamLeaderPopClose()">취소</button>
                <button class="btn w-50" onclick="teamLeaderConfirm()">확인</button>
            </div>
        </div>
    </div>

@endsection
