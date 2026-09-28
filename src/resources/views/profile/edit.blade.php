@extends('layouts.layout')

@section('script')
    <script>
        window.onload = () => {
            document.getElementById('avatar').addEventListener('change', (e) => {
                if(e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const previewImage = document.getElementById("avatar-preview");
                        previewImage.src = e.target.result;
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            })
        }

        const confirmModalOn = () => {
            document.querySelector('#confirmModal').style.display = 'block';
        }
        const confirmModalOff = () => {
            document.querySelector('#confirmModal').style.display = 'none';
        }
    </script>
@endsection

@section('content')
    <div class="container">
        <div class="container__wrap">
            <div class="contents__wrap p-40 w-800">
            <form method="POST" action="{{ route('profileEdit') }}" enctype="multipart/form-data" style="background-color: white;" id="profileEdit">
                @csrf
                <h3 class="con-tit">내 정보</h3>
                <div class="img-edit">
                    <div class="info-img__wrap">
                        <img id="avatar-preview" src="{{ route('avatar', ['userId' => auth()->id()]) }}">
                    </div>
                    <button type="button" onclick="document.getElementById('avatar').click()">+</button>
                    <input id="avatar" type="file" name="avatar" style="display: none" />
                </div>
                @error('avatar')
                    <p style="color: red; text-align: center;">{{ $message }}</p>
                @enderror
                @if(auth()->user()->social === null)
                <div class="col-wrap">
                    <div class="input-wrap col-6">
                        <label class="input-label">아이디</label>
                        <div class="input guide-exist">
                            <input type="text" value="{{ auth()->user()->email }}" disabled />
                        </div>
                        <label class="input-label">이름*</label>
                        <div class="input guide-exist">
                            <input type="text" name="name" value="{{ old('name') ?? auth()->user()->name }}" disabled />
                            @error('name')
                            <p class="input-guide d-block">{{ $message }}</p>
                            @enderror
                        </div>
                        <label class="input-label">새 비밀번호*</label>
                        <div class="input guide-exist">
                            <input type="password" name="new_password" />
                            @error('new_password')
                            <p class="input-guide d-block">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="input-wrap col-6">
                        <label class="input-label">휴대폰번호*</label>
                        <div class="input guide-exist">
                            <input type="text" name="contact" value="{{ old('contact') ?? auth()->user()->contact }}" disabled />
                            @error('contact')
                            <p class="input-guide d-block">{{ $message }}</p>
                            @enderror
                        </div>
                        <label class="input-label">기존 비밀번호*</label>
                        <div class="input guide-exist">
                            <input type="password" name="password" placeholder="영문/숫자 혼합6~12글자" id="password" />
                            @error('password')
                            <p class="input-guide d-block">{{ $message }}</p>
                            @enderror
                        </div>
                        <label class="input-label">새 비밀번호 확인*</label>
                        <div class="input guide-exist">
                            <input type="password" name="new_password_confirmation" placeholder="영문/숫자 혼합6~12글자" />
                            @error('new_password_confirmation')
                            <p class="input-guide d-block">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @else
                <div class="col-wrap">
                    <div class="input-wrap col-6">
                        <label class="input-label">아이디</label>
                        <div class="input guide-exist">
                            <input type="text" value="{{ auth()->user()->email }}" disabled />
                        </div>
                    </div>
                    <div class="input-wrap col-6">
                        <label class="input-label">이름*</label>
                        <div class="input guide-exist">
                            <input type="text" name="name" value="{{ old('name') ?? auth()->user()->name }}" disabled />
                            @error('name')
                            <p class="input-guide d-block">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif
                <div class="t-center">
                    <button class="btn btn-primary btn-line btn-md2 mr-5" type="button" onclick="location.href='{{ route('profileView') }}'">취소</button>
                    @if(auth()->user()->social === null)
                    <button class="btn btn-primary btn-md2" type="button" onclick="if (document.getElementById('password').value == '') { confirmModalOn() } else { document.getElementById('profileEdit').submit() }">수정</button>
                    @else
                    <button class="btn btn-primary btn-md2" type="button" onclick="document.getElementById('profileEdit').submit()">수정</button>
                    @endif
                </div>
            </form>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="popup confirm" style="display:none">
        <div class="popup__dim" onclick="confirmModalOff()"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                정보를 수정하기 위해서는 기존비밀번호를 입력해야 합니다.
            </div>
            <div class="confirm-btn">
                <button type="button" class="btn w-100" onclick="confirmModalOff()">확인</button>
            </div>
        </div>
    </div>
@endsection

