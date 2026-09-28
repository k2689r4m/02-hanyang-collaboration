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
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.noticeView') }}">공지</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.faqView') }}">FAQ</a></li>
    </ul>
    <div class="card-body">
        <div class="text-right card-body">
            <button class="btn btn-primary" onclick="location.href='{{ route('admin.faqNewView') }}'">등록</button>
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
                @foreach($faqs as $faq)
                    <tr>
                        <td>{{ $faq->id }}</td>
                        <td><a href="{{ route('admin.faqDetailView', ['faqId' => $faq->id]) }}">{{ $faq->title }}</a></td>
                        <td>{{ date('Y.m.d', strtotime($faq->created_at)) }}</td>
                        <td><button class="btn btn-danger btn-xs" type="button" onclick="return confirmDelete() ? location.href='{{ route('admin.faqDelete', ['faqId' => $faq->id]) }}' : ''">삭제</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $faqs->links('vendor.pagination.tailWind3') }}
        </div>
    </div>

@endsection