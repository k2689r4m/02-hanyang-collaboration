@extends('layouts.layout')

@section('script')

@endsection

@section('content')

    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit" >
                <a><span >마이페이지</span></a>
            </h3>

            <ul class="container-tab">
                <li class="active">기초교육</li>
                <li onclick="location.href='{{ route('consultantMyPageConsultingView') }}'">컨설팅</li>
            </ul>

            <div class="contents__wrap">
                <table class="list-table">
                    <thead>
                    <tr>
                        <th>번호</th>
                        <th>기초교육명</th>
                        <th>신청자명</th>
                        <th>신청자 아이디</th>
                        <th>신청일</th>
                        <th>시간</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($basicApplies as $basicApply)
                            <tr>
                                <td>{{ $basicApply['id'] }}</td>
                                <td>{{ $basicApply['basic']['title'] }}</td>
                                <td>{{ $basicApply['user']['name'] }}</td>
                                <td>{{ $basicApply['user']['email'] }}</td>
                                <td>{{ date('Y.m.d', strtotime($basicApply['basic']['startDateTime'])) }}</td>
                                <td>{{ date('H:i', strtotime($basicApply['basic']['startDateTime'])).'~'.date('H:i', strtotime($basicApply['basic']['endDateTime'])) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $basicApplies->withQueryString()->links(('vendor.pagination.tailwind')) }}
            </div>
        </div>
    </div>

@endsection
