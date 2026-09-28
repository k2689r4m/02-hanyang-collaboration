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
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.consulting1View') }}">신청</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.consulting2View') }}">승인</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.consulting3View') }}">컨설팅관리</a></li>
    </ul>
    <div class="card-body">
        <div class="card-body float-left">
            <form method="get" action="{{ route('admin.consulting1View') }}">
                <button class="btn btn-light btn-sm" name="action" value="allTarget">전체</button>
                <button class="btn btn-light btn-sm" name="action" value="targetPerson">대상자</button>
                <button class="btn btn-light btn-sm" name="action" value="nonTargetPerson">비대상자</button>
            </form>
            <form method="get" action="{{ route('admin.applicantExport') }}">
                <input type="hidden" name="state" value="consulting1" />
                <input type="hidden" name="targetAction" value="{{ request()->query('action') }}" />
                <button class="btn btn-sm btn-primary text-right" onclick="location.href='{{ route('admin.applicantExport') }}'">엑셀 다운</button>
            </form>
        </div>
        <form id="allowForm" method="POST" action="{{ route('admin.consulting1') }}">
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
                    <th>대상자여부</th>
                    <th>신청자아이디</th>
                    <th>신청일</th>
{{--                    <th>신청한 컨설팅명</th>--}}
                    <th>일시</th>
{{--                    <th>컨설팅 강사명</th>--}}
                </tr>
                @foreach($consultingApplies as $consultingApply)
                <tr>
                    <td>
                        <label class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" name="allow[]" value = "{{ $consultingApply->id }}" />
                            <span class="custom-control-label"></span>
                        </label>
                    </td>
                    <td>{{ $consultingApply->id }}</td>
                    <td>{{ $consultingApply->user->name }}</td>
                    @if($consultingApply->user->consultingTarget == 0)
                        <td>비대상자</td>
                    @elseif($consultingApply->user->consultingTarget == 1)
                        <td>대상자</td>
                    @endif
                    <td>{{ $consultingApply->user->email }}</td>
                    <td>{{ date('Y.m.d', strtotime($consultingApply->created_at)) }}</td>
{{--                    <td>{{ $consultingApply->consulting->title }}</td>--}}
                    <td>{{ date('h:m', strtotime($consultingApply->consulting->startDateTime)) }} ~ {{ date('h:m', strtotime($consultingApply->consulting->endDateTime)) }}</td>
{{--                    <td>{{ $consultingApply->consulting->consultant()->first()->name }}</td>--}}
                </tr>
                @endforeach
            </table>
            {{ $consultingApplies->withQueryString()->links('vendor.pagination.tailWind3') }}
        </div>
        </form>
    </div>
@endsection

<script type="text/javascript">
    window.onload = () => {
        document.getElementById('allAllow').addEventListener('click', () => {
            document.getElementsByName('allow[]').forEach((checkBox) => {
                checkBox.checked = true;
            });

            document.getElementById('allowForm').submit();
        });
    }
    const confirmPopupOff = () => {
        document.getElementById('confirmPopup').remove();
    }
</script>