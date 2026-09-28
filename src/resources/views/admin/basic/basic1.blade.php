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
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.basic1View') }}">신청</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.basic2View') }}">승인</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.basic3View') }}">기초교육관리</a></li>

    </ul>

    <div class="card-body">
        <div class="card-body float-left">
            <form method="get">
                <button class="btn btn-sm @if(request()->query('action') != 'allTarget') btn-light @else btn-primary @endif" @if(request()->query('action') == 'allTarget') disabled @endif name="action" value="allTarget">전체</button>
                <button class="btn btn-sm @if(request()->query('action') != 'targetPerson') btn-light @else btn-primary @endif" @if(request()->query('action') == 'targetPerson') disabled @endif name="action" value="targetPerson">대상자</button>
                <button class="btn btn-sm @if(request()->query('action') != 'nonTargetPerson') btn-light @else btn-primary @endif" @if(request()->query('action') == 'nonTargetPerson') disabled @endif name="action" value="nonTargetPerson">비대상자</button>
            </form>
            <form method="get" action="{{ route('admin.applicantExport') }}">
                <input type="hidden" name="state" value="basic1" />
                <input type="hidden" name="targetAction" value="{{ request()->query('action') }}" />
                <button class="btn btn-sm btn-primary text-right" onclick="location.href='{{ route('admin.applicantExport') }}'">엑셀 다운</button>
            </form>
        </div>

        <form id="allowForm" method="POST" action="{{ route('admin.basic1') }}">
            @csrf
            @if(auth()->user()->authority == 3)
                <div class="text-right card-body">
                    <button class="btn btn-outline-primary btn-sm" id="allAllow" type="button" >전체승인</button>
                    <button class="btn btn-outline-primary btn-sm">선택승인</button>
                </div>
            @endif
        <table class="table table-hover text-center">
            <tr>
                <th>check</th>
                <th>번호</th>
                <th>신청자명</th>
                <th>대상자여부</th>
                <th>신청자 아이디</th>
                <th>신청일</th>
{{--                <th>신청한 컨설팅명</th>--}}
{{--                <th>시간</th>--}}
                <th>교육일시</th>
{{--                <th>기초교육 강사명</th>--}}
            </tr>
            @foreach($basicApplies as $basicApply)
            <tr>
                <td>
                    <label class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" name="allow[]" value = "{{ $basicApply->id }}" />
                        <span class="custom-control-label"></span>
                    </label>
                </td>
                <td>{{ $basicApply->id }}</td>
                <td>{{ $basicApply->user->name }}</td>
                @if($basicApply->user->basicTarget == 0)
                    <td>비대상자</td>
                @elseif($basicApply->user->basicTarget == 1)
                    <td>대상자</td>
                @endif
                <td>{{ $basicApply->user->email }}</td>
                <td>{{ date('Y.m.d', strtotime($basicApply->created_at)) }}</td>
{{--                <td>{{ $basicApply->basic->title }}</td>--}}
{{--                <td>{{$basicApply->basic->startDateTime}} <br />~ {{$basicApply->basic->endDateTime}}</td>--}}
                <td>{{ $basicApply->basic->startDateTime }}</td>
{{--                <td>{{ $basicApply->basic->consultant()->first()->name }}</td>--}}
            </tr>
            @endforeach
        </table>
            {{ $basicApplies->withQueryString()->links('vendor.pagination.tailWind3') }}
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




















{{--
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
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.basic1View') }}">신청</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.basic2View') }}">승인</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.basic3View') }}">기초교육관리</a></li>
    </ul>
    <div class="card-body">
        <div class="card-body float-left">
            <form method="get">
                <button class="btn btn-light btn-sm" name="action" value="allTarget">전체 {{ request()->query('action') == '' || 'allTarget' ? '선택' : '' }}</button>
                <button class="btn btn-light btn-sm" name="action" value="targetPerson">대상자 {{ request()->query('action') == 'targetPerson' ? '선택' : '' }}</button>
                <button class="btn btn-light btn-sm" name="action" value="nonTargetPerson">비대상자 {{ request()->query('action') == 'nonTargetPerson'? '선택' : '' }} </button>
            </form>
        </div>
        <form id="allowForm" method="POST" action="{{ route('admin.basic1') }}">
            @csrf
            @if(auth()->user()->authority == 3)
                <div class="text-right card-body">
                    <button class="btn btn-outline-primary btn-sm" id="allAllow" type="button" >전체승인</button>
                    <button class="btn btn-outline-primary btn-sm">선택승인</button>
                </div>
            @endif
        <table class="table table-hover text-center">
            <tr>
                <th>check</th>
                <th>번호</th>
                <th>신청자명</th>
                <th>대상자여부</th>
                <th>신청자아이디</th>
                <th>신청일</th>
                <th>신청한 컨설팅명</th>
                <th>시간</th>
                <th>기초교육 강사명</th>
            </tr>
            @foreach($basicApplies as $basicApply)
            <tr>
                <td>
                    <label class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" name="allow[]" value = "{{ $basicApply->id }}" />
                        <span class="custom-control-label"></span>
                    </label>
                </td>
                <td>{{ $basicApply->id }}</td>
                <td>{{ $basicApply->user->name }}</td>
                @if($basicApply->user->basicTarget == 0)
                    <td>비대상자</td>
                @elseif($basicApply->user->basicTarget == 1)
                    <td>대상자</td>
                @endif
                <td>{{ $basicApply->user->email }}</td>
                <td>{{ date('Y.m.d', strtotime($basicApply->created_at)) }}</td>
                <td>{{ $basicApply->basic->title }}</td>
                <td>{{$basicApply->basic->startDateTime}} <br />~ {{$basicApply->basic->endDateTime}}</td>
                <td>{{ $basicApply->basic->consultant()->first()->name }}</td>
            </tr>
            @endforeach
        </table>
            {{ $basicApplies->withQueryString()->links('vendor.pagination.tailWind3') }}
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



--}}
