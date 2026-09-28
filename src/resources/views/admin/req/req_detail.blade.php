@extends('admin.layouts.admin')

@section('script')
    <script>
        const confirmPopupOff = () => {
            document.getElementById('confirmPopup').remove();
        };
    </script>
@endsection

@section('content')
    @error('error')
    <div class="popup" id="confirmPopup">
        <div class="popup__dim"></div>
        <div class="popup-wrap confirm">
            <div class="popup-con text-center">
                {{ $message }}
                <br>
                <button class="m-t-30 btn btn-primary" onclick="confirmPopupOff()">확인</button>
            </div>
        </div>
    </div>
    @enderror
    <div class="card-body">
        <div class="card-body">
        <table class="table table-bordered">
            <colgroup>
                <col width="12%" />
                <col width="21%" />
                <col width="12%" />
                <col width="21%" />
                <col width="12%" />
                <col width="21%" />
            </colgroup>
            <tr>
                <th>문의자 이름</th>
                <td>{{ $req->user()->name }}</td>
                <th>이메일</th>
                <td>{{ $req->user()->email }}</td>
                <th>작성일</th>
                <td>{{ date('Y.m.d', strtotime($req->created_at)) }}</td>
            </tr>
            <tr>
                <th>문의 제목</th>
                <td colspan="5">{{ $req->title }}</td>
            </tr>
            <tr>
                <th>문의 내용</th>
                <td colspan="5">{{ $req->content }}</td>
            </tr>
        </table>
        </div>
        <div class="card-body">
        <form id="myForm" method="POST" action="{{ route('admin.reqDetail', ['reqId' => $req->id]) }}">
            @csrf
            <h4>관리자 답변 등록</h4>
            <table class="table table-bordered">
                <colgroup>
                </colgroup>
                <tr>
                    <th>답변 내용</th>
                    <td>
                        <textarea class="form-control" rows="4" name="adminAnswer">{{ old('adminAnswer') ?? $req->adminAnswer }}</textarea>
                        @error('adminAnswer')
                        <p class="text-danger">관리자 답변을 작성해 주세요.</p>
                        @enderror
                    </td>
                </tr>
            </table>
            <br>
            <div class="text-center">
                <button class="btn btn-light" type="button" onclick="confirm('취소하시겠습니까?') ? location.href='{{ route('admin.reqView') }}' : ''">취소</button>
                <button class="btn btn-primary" type="button" onclick="confirm('저장하시겠습니까?') ? document.getElementById('myForm').submit() : ''">확인</button>
            </div>
        </form>
        </div>
    </div>
@endsection