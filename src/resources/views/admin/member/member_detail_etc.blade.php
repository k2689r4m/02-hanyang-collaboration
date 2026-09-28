@extends('admin.layouts.admin')

@section('script')
    <script>
        const confirmPopupOff = () => {
            document.getElementById('confirmPopup').remove();
        }
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
        <div class="card-body card col-6 m-t-20">
            <h3 class="card-title border-bottom p-10">회원정보</h3>
            <form id="myForm" method="POST" action="{{ route('admin.memberDetailOut',['userId' => $user->id]) }}">
                @csrf
                <table class="table table-input">
                    <tr>
                        <td><label>이메일</label><input class="form-control" type="text" value="{{ $user->email }}" disabled/></td>
                        <td><label>휴대폰번호</label><input class="form-control" type="text" value="{{ $user->contact }}" disabled/></td>
                    </tr>
                    <tr>
                    </tr>
                    <tr>
                        <td><label>이름</label><input class="form-control" type="text" value="{{ $user->name }}" disabled/></td>
                        <td>
                            <label>신분구분자</label>
                            <select class="form-control" name="authority">
                                {{--임시로 일반은 authority 0번 --}}
                                <option @if($user->authority == 0) selected @endif value="일반">일반</option>
                                <option @if($user->authority == 4) selected @endif value="컨설턴트">컨설턴트</option>
                                <option @if($user->authority == 5) selected @endif value="공동교수자/외부전문가">공동교수자/외부전문가</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>한양대구분자</label>
                            <input type="text" class="form-control" readonly
                                   @if($user->social=='hanyang')value="hanyang"@else value=""@endif
                            /></td>
                        <td><label>가입 시 입력한 교수코드</label><input class="form-control" type="text" name="code" value="{{ $user->code }}"/></td>
                        {{--                        @if($user->authority)<td>코드 <br><input type="text" readonly/></td>@endif--}}
                    </tr>
                </table>
                <div class="text-center p-t-20">
                    <button class="btn btn-light" type="button" onclick="confirm('취소하시겠습니까?') ? location.href='{{ route('admin.memberView') }}' : ''">취소</button>
                    <button class="btn btn-primary" type="button" onclick="confirm('저장하시겠습니까?') ? document.getElementById('myForm').submit() : ''">확인</button>
                </div>
            </form>
        </div>
    </div>
@endsection