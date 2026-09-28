@extends('admin.layouts.classDetailManage')

@section('_script')
@endsection

@section('_content')
    <div class="contents__wrap card-body">
        <div class="card-body">
            <table class="table table-hover text-center">
                <colgroup>
                    <col width="15%" />
                    <col width="30%" />
                    <col width="20%" />
                    <col width="20%" />
                    <col width="15%" />
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
                @foreach($reflections as $reflection)
                    <tr>
                        <td>{{ $index++ }}</td>
{{--                        @if( $reflection->withReflectionLog->item()->card())--}}
{{--                            <td>{{ $reflection->withReflectionLog->item()->card() }}</td>--}}
{{--                        @endif--}}
                        @if($reflection->card()->team())
                            <td>{{ $reflection->card()->team()->name }}</td>
                        @else
                        <td></td>
                        @endif
                        <td>{{ $reflection->withReflectionLog->name }}</td>
                        <td>{{ $reflection->withReflectionLog->created_at->format('Y-m-d') }}</td>
                        <td><button class="btn btn-sm btn-primary btn-line"
                                    onclick="location.href='{{ route('admin.classDetail7DetailView', ['classApplyId' => $classApply->id, 'reflectionId' => $reflection]) }}'">확인</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $reflections->withQueryString()->links(('vendor.pagination.tailwind')) }}
        </div>
    </div>
@endsection
