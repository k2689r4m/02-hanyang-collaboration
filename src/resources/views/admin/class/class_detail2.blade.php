@extends('admin.layouts.classDetailManage')

@section('_script')
@endsection

@section('_content')
    <div class="card-body">
        <div class="card-body text-right">
            <form method="GET" action="{{ route('admin.classDetail2View', ['classApplyId' => $classApply->id] ) }}">
                <select class="form-control w-sm" name="type">
                    <option value="name">이름</option>
                    <option value="email">아이디</option>
                </select>
                <input class="form-control w-200" type="text" name="content" value="{{ old('content') }}">
                <button class="btn btn-primary btn-sm">검색</button>
            </form>
        </div>
        <div class="contents__wrap card-body p-t-0">
            <table class="table table-hover text-center">
                <thead>
                    <colgroup>
                        <col width="25%" />
                        <col width="25%" />
                        <col width="25%" />
                        <col width="25%" />
                    </colgroup>
                    <tr>
                        <th>번호</th>
                        <th>이름</th>
                        <th>아이디</th>
                        <th>팀</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $key = 0;
                @endphp
                @foreach($classLists as $classList)
                    <tr>
                        <td>{{ ($classLists->currentPage() - 1) * $classLists->perPage() + $key++ + 1 }}</td>
                        <td>{{ $classList->name  ?? $classList->user()->name }}</td>
                        <td>{{ $classList->email  ?? $classList->user()->email }}</td>
                        <td>{{ $classList->team()->name ?? 'none' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $classLists->withQueryString()->links('vendor.pagination.tailWind3') }}
        </div>
    </div>
@endsection