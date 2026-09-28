@extends('layouts.layout')

@section('script')
@endsection

@section('content')
    @error('service error')
    <div>
        {{ $message }}
    </div>
    @enderror

    <div class="container">
        <div class="container__wrap">
            <div class="contents__wrap p-40 w-800">
                <h3 class="board-tit">
                    {{ $req->title }}
                </h3>
                <p class="board-date">{{ date('Y.m.d', strtotime($req->created_at)) }}</p>
                <div class="board-con">
                    <div class="txt">{{ $req->content }}</div>
                </div>
                <div class="t-center board-pagination">
                    <li><button type="button" onclick="location.href='{{ route('reqView', ['page' => app('request')->input('listPage')]) }}'">목록</button></li>
                    @auth
                        @if(Auth::user()->id === $req->userId)
                            @if(is_null($req->adminAnswer))
                            <li><button type="button" onclick="location.href='{{ route('reqEditView', ['reqId' => $req->id]) }}'">수정</button></li>
                            @endif
                        @endif
                        @if(Auth::user()->id === $req->userId || Auth::user()->authority === 3)
                        <li><button type="button" onclick="deleteModalOn()">삭제</button></li>
                        @endif
                    @endauth
                </div>
            </div>
            <div class="contents__wrap p-40 w-800 mt-10">
                <h3 class="board-tit type2">관리자의 답변</h3>
                @if($req->adminAnswer)
                    {{ $req->adminAnswer }}
                @else
                    <span class="fc-gray">아직 답변이 달리지 않았습니다.</span>
                @endif
            </div>

        </div>
    </div>

    <div id="deleteModal" class="popup confirm" style="display:none">
        <div class="popup__dim" onclick="deleteModalOff()"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                삭제하시겠습니까?
            </div>
            <div class="confirm-btn">
                <button type="button" class="btn w-50 fc-gray" onclick="deleteModalOff()">취소</button>
                <button type="button" class="btn w-50" onclick="location.href='{{ route('reqDelete', ['reqId' => $req->id]) }}'">확인</button>
            </div>
        </div>
    </div>
@endsection

<script type="text/javascript">
    const deleteModalOn = () => {
        document.querySelector('#deleteModal').style.display = 'block';
    }
    const deleteModalOff = () => {
        document.querySelector('#deleteModal').style.display = 'none';
    }
</script>