@extends('admin.layouts.classDetailManage')

@section('_script')
    <script>
        window.onload = () => {

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
        //
        // const handleDisplayConfirmModal = (action) => {
        //     const modal = document.getElementById('confirmModal');
        //
        //     switch (action) {
        //         case 'close':
        //             modal.classList.add('d-none');
        //             break;
        //         case 'open':
        //             modal.classList.remove('d-none');
        //             break;
        //         default:
        //             if (modal.classList.contains('d-none')) {
        //                 modal.classList.remove('d-none');
        //             }
        //             else {
        //                 modal.classList.add('d-none');
        //             }
        //             break;
        //     }
        // }
    </script>

@endsection

@section('_content')
    <div class="card-body">
        <div class="card-body text-right">
{{--            <form method="POST" action="{{ route('admin.classDetail3View.teamNamesChange') }}">--}}
{{--                @csrf--}}
                <input type="hidden" name="classObjectId" value="{{ $classApply->classObjectId }}" />
                <div class="container-top">
{{--                    <div class="right">--}}
{{--                        <button class="btn btn-md btn-primary btn-line mr-5">팀명저장</button>--}}
{{--                        <button type="button" class="btn btn-md btn-primary" onclick="handleDisplayModal()">팀 배정</button>--}}
{{--                    </div>--}}
                </div>
                <div class="contents__wrap p-t-20">
                    <table class="table table-hover text-center">
                        <colgroup>
                            <col width="15%" />
                            <col width="25%" />
                            <col width="15%" />
                            <col width="45%" />
                        </colgroup>
                        <thead>
                        <tr>
                            <th>번호</th>
                            <th>팀명</th>
                            <th>인원</th>
                            <th class="left">참여자이름</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach (range(1,20) as $i)
                            <tr>
                                <td>{{ $i }}</td>
                                <input type="hidden" name="id[]" value="{{ $teams[$i - 1]->id ?? '' }}" />
                                <td><input type="text" name="names[]" value="{{ $teams[$i - 1]->name ?? '팀'.$i }}" /></td>
                                <td>{{ $i - 1 < $teams->count() ? $teams[$i - 1]->teamMembers()->count() : 0 }}</td>
                                <td class="left">
                                    @if ($i - 1 < $teams->count())
                                        @foreach ($teams[$i - 1]->teamMembers() as $key=>$member)
                                            @if ($key++ != 0)
                                                {{ ', '.$member->user()->name }}
                                            @else
                                                {{ $member->user()->name }}
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
{{--            </form>--}}
        </div>
    </div>
    <!-- 팀배정 popup, confirm popup -->
{{--    <form method="POST" action="{{ route('lectureDetailTeamSelect.teamMemberChange') }}">--}}
{{--        @csrf--}}
{{--        <input type="hidden" name="classObjectId" value="{{ $classObjectId }}" />--}}
{{--        <div class="popup d-none" id="teamSelectModal">--}}
{{--            <div class="popup__dim" onclick="handleDisplayModal()"></div>--}}
{{--            <div class="popup-wrap">--}}
{{--                <div class="popup-con scroll-sm">--}}
{{--                    <h3 class="popup-tit">팀 배정</h3>--}}
{{--                    <div class="team-list__wrap">--}}
{{--                        <table class="team-list">--}}
{{--                            <tr>--}}
{{--                                <th>번호</th>--}}
{{--                                <th>이름</th>--}}
{{--                                <th>아이디</th>--}}
{{--                                <th>배정</th>--}}
{{--                            </tr>--}}
{{--                            @foreach ($members as $key=>$member)--}}
{{--                                <tr>--}}
{{--                                    <input type="hidden" name="userId[]" value="{{ $member->user()->id }}" />--}}
{{--                                    <td><span class="bg">{{ $key + 1 }}</span></td>--}}
{{--                                    <td><span class="bg">{{ $member->user()->name }}</span></td>--}}
{{--                                    <td><span class="bg">{{ $member->user()->email }}</span></td>--}}
{{--                                    <td class="t-center is-select">--}}
{{--                                        <select name="teamId[]">--}}
{{--                                            <option @if(!$member->team()) selected @endif value="0">none</option>--}}
{{--                                            @foreach ($teams as $team)--}}
{{--                                                <option @if($member->team()) @if($member->team()->id == $team->id) selected @endif @endif value="{{ $team->id }}">{{ $team->name }}</option>--}}
{{--                                            @endforeach--}}
{{--                                        </select>--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                            @endforeach--}}
{{--                        </table>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="confirm-btn">--}}
{{--                    <button type="button" class="btn w-50 fc-gray" onclick="handleDisplayModal()">취소</button>--}}
{{--                    <button type="button" class="btn w-50" onclick="handleDisplayConfirmModal()">확인</button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="popup confirm d-none" id="confirmModal">--}}
{{--            <div class="popup__dim" onclick="handleDisplayConfirmModal()"></div>--}}
{{--            <div class="popup-wrap">--}}
{{--                <div class="confirm-txt">--}}
{{--                    저장하시겠습니까?--}}
{{--                </div>--}}
{{--                <div class="confirm-btn">--}}
{{--                    <button type="button" class="btn w-50 fc-gray" onclick="handleDisplayConfirmModal()">취소</button>--}}
{{--                    <button class="btn w-50">확인</button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
    </form>
@endsection