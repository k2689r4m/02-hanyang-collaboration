@extends('layouts.layout')


<script>
</script>

@section('content')

    {{--    modal start--}}
{{--    @error('content_length')--}}
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
{{--    @enderror--}}
    {{--    modal end--}}
    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>임시 수업 추가</span></h3>

            <div class="contents__wrap" style="padding:100px calc(20vw - 50px)">
                <form method="POST" action="{{ route('testMemberAdd', ['classObjectId' => $classObject->id]) }}">
                    @csrf
                    <table class="table-input__wrap th-center table mb-0">
                        <colgroup>
                            <col width="10%" />
                            <col width="30%" />
                            <col width="45%" />
                            <col width="15%" />
                        </colgroup>
                        <tr>
                            <th></th>
                            <th>이름</th>
                            <th>아이디</th>
                            <th>한양대 여부</th>
                        </tr>
                    </table>
                    <div class="list-table__scroll scroll-sm mb-30">
                        <table class="list-table">
                            <colgroup>
                                <col width="10%" />
                                <col width="30%" />
                                <col width="45%" />
                                <col width="15%" />
                            </colgroup>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $user->id }}">
                                    </td>
                                    <td>
                                        {{ $user->name }}
                                    </td>
                                    <td>
                                        {{ $user->email }}
                                    </td>
                                    <td>
                                        {{ $user->social == 'hanyang' ? 'Y' : 'N' }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    <div class="t-center">
{{--                        <button type="reset" class="btn btn-gray btn-lg mr-5">취소</button>--}}
                        <button type="submit" class="btn btn-primary btn-lg">확인</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

<script>
    // window.onload = () => {
    //     $("select").niceSelect();
    // }
</script>
