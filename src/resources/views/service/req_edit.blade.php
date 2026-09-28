@extends('layouts.layout')

@section('script')
@endsection

@section('content')
    {{--    server modal start--}}
    {{--    server modal end--}}

    {{--    js modal start--}}
    {{--    js modal end--}}
    <div class="container">
        <div class="container__wrap">
            <div class="contents__wrap p-40 w-800">
                <h3 class="board-tit">1:1 문의 수정</h3>
            <form name="inquiryForm" method="POST" action="{{ route('reqEdit', ['reqId' => $req->id]) }}">
                @csrf
                <div>
                    <p>제목</p>
                    <input type="text" class="b-gray w-100 mt-10 mb-20" name="title" value="{{ old('title') ?? $req->title }}">
                </div>
                <div>
                    <p>내용</p>
                    <textarea class="b-gray w-100 mt-10 mb-20" rows="6" name="content">{{ old('content') ?? $req->content }}</textarea>
                </div>

                <div class="t-center p-10 board-pagination">
                    <li><button type="button" onclick="location.href='{{ route('reqDetailView', ['reqId' => $req->id]) }}'">취소</button></li>
                    <li><button type="button" onclick="saveModalOn()">수정</button></li>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div id="saveModal" class="popup confirm" style="display:none">
        <div class="popup__dim" onclick="saveModalOff()"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                저장하시겠습니까?
            </div>
            <div class="confirm-btn">
                <button type="button" class="btn w-50 fc-gray" onclick="saveModalOff()">취소</button>
                <button type="button" class="btn w-50" onclick="valCheck()">확인</button>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="popup confirm" style="display:none">
        <div class="popup__dim" onclick="confirmModalOff()"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                제목, 내용을 모두 입력해 주세요.
            </div>
            <div class="confirm-btn">
                <button type="button" class="btn w-100" onclick="confirmModalOff()">확인</button>
            </div>
        </div>
    </div>
@endsection

<script type="text/javascript">
    const saveModalOn = () => {
        document.querySelector('#saveModal').style.display = 'block';
    }
    const saveModalOff = () => {
        document.querySelector('#saveModal').style.display = 'none';
    }
    const confirmModalOn = () => {
        document.querySelector('#confirmModal').style.display = 'block';
    }
    const confirmModalOff = () => {
        document.querySelector('#confirmModal').style.display = 'none';
    }
    const valCheck = () => {
        let inquiryForm = document.inquiryForm;
        let title = inquiryForm.title.value;
        let content = inquiryForm.content.value;
        if(title === '' || content === ''){
            saveModalOff();
            confirmModalOn();
        }else{
            inquiryForm.submit();
        }
    }
</script>
