<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <title>adminLogin</title>
</head>
<body>
<div class="splash-container">
    <div class="card ">
        <div class="card-header text-center">
            <a href="{{ route('login') }}" class="logo"></a>
            <span class="splash-description">ADMIN LOGIN</span>
            <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <input class="form-control form-control-lg" type="text" name="email" placeholder="아이디를 입력해주세요." class="login-input__id" value="{{ old('email') }}"/>
            </div>
            <div class="form-group">
                <input class="form-control form-control-lg" type="password" name="password" placeholder="비밀번호를 입력해주세요." class="login-input__pwd" />
            </div>
            <button class="btn btn-primary btn-lg btn-block" onclick="location.href='{{ route('admin.dashboardView') }}'">로그인</button>
        </div>

            </form>
        </div>
    </div>
    @foreach($errors->all() as $error)
        <div id="mainConfirmModal" class="popup confirm">
            <div class="popup__dim" onclick="mainConfirmModalOff(this)"></div>
            <div class="popup-wrap">
                <div class="confirm-txt">
                    {{ $error }}
                </div>
                <div class="confirm-btn">
                    <button type="button" class="btn w-100" onclick="mainConfirmModalOff(this)">확인</button>
                </div>
            </div>
        </div>


    @endforeach
</div>
<script type="text/javascript">
    const mainConfirmModalOff = (target) => {
        document.querySelector('#mainConfirmModal').remove();
    }
</script>
</body>
</html>