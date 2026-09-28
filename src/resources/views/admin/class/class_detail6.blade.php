@extends('admin.layouts.classDetailManage')

@section('_script')
@endsection

@section('_content')
    <div class="contents__wrap card-body">
        <div class="card-body">
            <table class="table table-hover text-center">
                <colgroup>
                    <col width="15%" />
                    <col width="25%" />
                    <col width="20%" />
                    <col width="20%" />
                    <col width="20%" />
                </colgroup>
                <thead>

                <tr>
                    <th>번호</th>
                    <th>팀명</th>
                    <th>작성자</th>
                    <th>제출일</th>
                    <th>상세확인</th>
                </tr>

                </thead>
                <tbody>
                @php
                    $index = 1;
                @endphp
                @foreach($teams as $team)
                    <tr>
                        <td>{{ $index++ }}</th>
                        <td>{{ $team->name }}</th>
                        <td>{{ $team->cardNum ?? ''}}</th>
                        <td>2021.02.05</th>
                        <td><button class="btn btn-sm btn-primary btn-line" onclick="location.href=''">확인</th>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection
