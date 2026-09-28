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
            <form id="myForm" method="POST" action="{{ route('admin.memberDetailStu',['userId' => $user->id]) }}">
                @csrf
                    <table class="table table-input">
                    <tr>
                        <td><label>이메일</label> <input type="text" class="form-control" value="{{ $user->email }}" disabled/></td>
                        <td><label>휴대폰번호</label> <input type="text" class="form-control" value="{{ $user->contact }}" disabled/></td>
                    </tr>
                    <tr>
                    </tr>
                    <tr>
                        <td><label>이름</label> <input type="text" class="form-control" value="{{ $user->name }}"  /></td>
                        <td><label>신분구분자</label>
                            @if($user->social != '')
                            <input type="text" class="form-control" value="@if($user->authority == 1)학생@elseif($user->authority == 6)조교@else @endif" readonly />
                            @else
                            <select class="form-control" name="authority">
                                <option {{ $user->authority == 0 ? 'selected' : ''}} value="0">일반</option>
                                <option {{ $user->authority == 1 ? 'selected' : ''}} value="1"> 학생</option>
                                <option {{ $user->authority == 4 ? 'selected' : ''}} value="4">컨설턴트</option>
                                <option {{ $user->authority == 5 ? 'selected' : ''}} value="5">공동교수자/외부전문가</option>
                                <option {{ $user->authority == 6 ? 'selected' : ''}} value="6">조교</option>
                                <option {{ $user->authority == 9 ? 'selected' : ''}} value="9">행정</option>
                            </select>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>한양대구분자</label>
                            <input type="text" class="form-control"
                                   @if($user->social=='hanyang')value="hanyang"@else value=""@endif
                                    readonly
                            />
                        </td>
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