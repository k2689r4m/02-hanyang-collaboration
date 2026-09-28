@extends('admin.layouts.admin')

@section('script')
    <script>
        const confirmDelete = () => {
            const confirmDelete = confirm("삭제하시겠습니까?");
            if(confirmDelete){
                return 1;
            }else{
                return 0;
            }
        }
    </script>
@endsection

@section('content')
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.reqView') }}">1:1문의</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.noticeView') }}">공지</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.faqView') }}">FAQ</a></li>
    </ul>
    <div class="card-body">
        <div class="text-right card-body">
            <button class="btn btn-primary" onclick="location.href='{{ route('admin.noticeNewView') }}'">등록</button>
        </div>
        <div class="card-body">
            <table class="table table-hover text-center">
                <colgroup>
                    <col width="10%" />
                    <col width="70%" />
                    <col width="10%" />
                    <col width="10%" />
                </colgroup>
                <thead>
                    <th>번호</th>
                    <th>제목</th>
                    <th>작성일</th>
                    <th>삭제</th>
                </thead>
                <tbody>
                @foreach($notices as $notice)
                    <tr>
                        <td>{{ $notice->id }}</td>
                        <td><a href="{{ route('admin.noticeDetailView', ['noticeId' => $notice->id]) }}">{{ $notice->title }}</a></td>
                        <td>{{ date('Y.m.d', strtotime($notice->created_at)) }}</td>
                        <td><button class="btn btn-danger btn-xs" type="button" onclick="return confirmDelete() ? location.href='{{ route('admin.noticeDelete', ['noticeId' => $notice->id]) }}' : ''">삭제</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $notices->links('vendor.pagination.tailWind3') }}
        </div>
    </div>
@endsection