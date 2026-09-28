<style>
    .main {
        display: flex;
        justify-content: center;
        align-items: center;

        flex-direction: column;

        height: 100vh;
    }
    h1, p {
        text-align: center;
    }

    form {
        width: 500px;
        display: flex;
        justify-content: center;

        flex-direction: column;
    }

    input {
        width: 100%;
    }

    button {
        width: 30%;
    }

    button:not(:last-child) {
        margin-right: 10px;
    }

    .btn-container {
        display: flex;
        justify-content: flex-end;

        margin-top: 30px;
    }
</style>


<div class="main">
    <h1>
        마이페이지 생성을 위한 임시페이지
    </h1>
    <form method="POST" action="{{ route('createMyPage') }}">
        @csrf
        <div class="btn-container">
            <button type="button" onclick="location.href='{{ route('myPageView') }}'">go to mypage</button>
            <button>submit</button>
        </div>
    </form>
</div>
