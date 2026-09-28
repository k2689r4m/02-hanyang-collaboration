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
                            @if($user->social != '')
                                <input type="text" class="form-control" value="@if($user->authority == 1)학생@elseif($user->authority == 6)조교@else @endif" readonly />
                            @else
                            <select class="form-control" name="authority">
                                {{--임시로 일반은 authority 0번 관리자도 됨 막아야함 --}}
                                <option @if($user->authority == 0) selected @endif value="0">일반</option>
                                <option @if($user->authority == 1) selected @endif value="0">학생</option>
                                <option @if($user->authority == 2) selected @endif value="2">교수</option>
                                <option @if($user->authority == 4) selected @endif value="4">컨설턴트</option>
                                <option @if($user->authority == 5) selected @endif value="5">공동교수자/외부전문가</option>
                                <option @if($user->authority == 9) selected @endif value="9">행정</option>
                            </select>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>한양대구분자</label>
                            <input type="text" class="form-control" readonly
                                                        @if($user->social=='hanyang')value="hanyang"@else value=""@endif
                            /></td>
                        <td><label>가입 시 입력한 코드</label><input class="form-control" type="text" name="code" value="{{ $user->code }}"/></td>
{{--                        @if($user->authority)<td>코드 <br><input type="text" readonly/></td>@endif--}}
                    </tr>

                    @if( $user->authority == 2 )
                        <tr>
                            <td>
                                <label>기초교육 대상자 여부</label>
                                <select name="basic" class="form-control">
                                    <option value="0" >비대상자</option>
                                    <option value="1" @if($user->basicTarget) selected @endif>대상자</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>컨설팅 대상자 여부</label>
                                <select name="consulting" class="form-control">
                                    <option value="0">비대상자</option>
                                    <option value="1" @if($user->consultingTarget) selected @endif>대상자</option>
                                </select>
                            </td>
                            <td>
                                <label>컨설팅 담당 컨설턴트</label>
                                <select class="form-control" name="consultant">
                                    <option>선택</option>
{{--                                    @foreach($users as $user)--}}
{{--                                        <option value="{{ $user->id }}">{{ $user->name }}</option>--}}
{{--                                    @endforeach--}}
                                </select>
                            </td>
                        </tr>
                    @endif
                </table>
                <div class="text-center p-t-20">
                    <button class="btn btn-light" type="button" onclick="confirm('취소하시겠습니까?') ? location.href='{{ route('admin.memberView') }}' : ''">취소</button>
                    <button class="btn btn-primary" type="button" onclick="confirm('저장하시겠습니까?') ? document.getElementById('myForm').submit() : ''">확인</button>
                </div>
            </form>
        </div>
    </div>
@endsection