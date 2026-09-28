@extends('layouts.layout')

@section('script')

@endsection

@section('content')

    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit" >
                <span>마이페이지</span>
            </h3>

            <ul class="container-tab">
                <li onclick="location.href='{{ route('myPageView') }}'">기초교육</li>
                <li class="active">컨설팅</li>
            </ul>

            <div class="contents__wrap">
                <table class="list-table">
                    <thead>
                    <tr>
                        <th>번호</th>
                        <th>컨설팅명</th>
                        <th>신청자명</th>
                        <th>신청자 아이디</th>
                        <th>신청일</th>
                        <th>시간</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($consultingApplies as $consultingApply)
                            <tr>
                                <td>{{ $consultingApply['id'] }}</td>
                                <td style="cursor: pointer" onclick="location.href='{{ route('consultantMyPageConsultingDetailView', ['applyId' => $consultingApply['id']]) }}'">{{ $consultingApply['consulting']['title'] }}</td>
                                <td>{{ $consultingApply['user']['name'] }}</td>
                                <td>{{ $consultingApply['user']['email'] }}</td>
                                <td>{{ date('Y.m.d', strtotime($consultingApply['consulting']['startDateTime'])) }}</td>
                                <td>{{ date('H:i', strtotime($consultingApply['consulting']['startDateTime'])).'~'.date('H:i', strtotime($consultingApply['consulting']['endDateTime'])) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $consultingApplies->withQueryString()->links(('vendor.pagination.tailwind')) }}
            </div>
        </div>
    </div>
@endsection
