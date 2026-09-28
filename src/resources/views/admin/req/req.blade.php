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
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.reqView') }}">1:1문의</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.noticeView') }}">공지</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.faqView') }}">FAQ</a></li>
    </ul>
    <div class="card-body">
        <div class="card-body">
            <table class="table table-hover text-center">
                <colgroup>
                    <col width="5%" />
                    <col width="15%" />
                    <col width="10%" />
                    <col width="50%" />
                    <col width="10%" />
                    <col width="10%" />
                </colgroup>
                <thead>
                <th>번호</th>
                <th>이메일</th>
                <th>이름</th>
                <th>제목</th>
                <th>작성일</th>
                <th>답변 상태</th>
                </thead>
                <tbody>
                @foreach($reqs as $req)
                    <tr>
                        <td>{{ $req->id }}</td>
                        <td>{{ $req->user()->email }}</td>
                        <td>{{ $req->user()->name }}</td>
                        <td><a href="{{ route('admin.reqDetailView', ['reqId' => $req->id]) }}">{{ $req->title }}</a></td>
                        <td>{{ date('Y.m.d', strtotime($req->created_at)) }}</td>
                        <td>{{ $req->adminAnswer ? '답변완료' : '미답변' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $reqs->links('vendor.pagination.tailWind3') }}
        </div>
    </div>
@endsection