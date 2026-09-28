@extends('admin.layouts.admin')

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
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.competition1View') }}">신청</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.competition2View') }}">승인</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.competition3View') }}">공모전관리</a></li>
    </ul>
    <div class="card-body">
        <div class="text-right card-body">
            <form method="get" action="{{ route('admin.applicantExport') }}">
                <input type="hidden" name="state" value="program1" />
                <button class="btn btn-sm btn-primary text-right" onclick="location.href='{{ route('admin.applicantExport') }}'">엑셀 다운</button>
            </form>
        </div>
        <form id="allowForm" method="POST" action="{{ route('admin.competition1') }}">
            @csrf
            @if(auth()->user()->authority == 3)
                <div class="text-right card-body">
                    <button class="btn btn-outline-primary btn-sm" id="allAllow" type="button" >전체승인</button>
                    <button class="btn btn-outline-primary btn-sm">선택승인</button>
                </div>

            @endif
            <div class="card-body">
                <table class="table table-hover text-center">
                    <tr>
                        <th>check</th>
                        <th>번호</th>
                        <th>신청자명</th>
                        <th>신청자 아이디</th>
                        <th>한양대 아이디</th>
                        <th>신청한 공모전명</th>
                        <th>공모전날짜</th>
                    </tr>
                    @foreach($competitions as $competition)
                    <tr>
                        <td>
                            <label class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="admitIds[]" value="{{ $competition->id }}" />
                                <span class="custom-control-label"></span>
                            </label>
                        </td>
                        <td>ID {{ $competition->id }}</td>
                        <td>{{ $competition->user->name }}</td>
                        <td>{{ $competition->user->email }}</td>
                        <td></td>
                        <td>{{ $competition->competition->title }}</td>
                        <td>{{ $competition->competition->year }}.{{ $competition->competition->month }}.{{ $competition->competition->day }}</td>
                    </tr>
                    @endforeach
                </table>
                {{ $competitions->links('vendor.pagination.tailWind3') }}
            </div>
        </form>
    </div>
@endsection

<script type="text/javascript">
    window.onload = () => {
        document.getElementById('allAllow').addEventListener('click', () => {
            document.getElementsByName('admitIds[]').forEach((checkBox) => {
                checkBox.checked = true;
            });
            document.getElementById('allowForm').submit();
        });
    }

    const confirmPopupOff = () => {
        document.getElementById('confirmPopup').remove();
    };
</script>
