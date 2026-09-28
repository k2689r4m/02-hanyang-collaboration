@extends('admin.layouts.admin')

@section('script')

@endsection

@section('content')
    <div class="card-body dashboard">
        <div class="card-body row">
            <div class="card-body">
                <form method="POST" action="{{ route('auth.hy.dataAPI') }}">
                    @csrf
                    API 요청 FORM <br>
                    년도 <input type="text" name="suupYear" placeholder="2021" ><br>
{{--                    학기 <input type="text" name="suupTerm"><br>--}}
                    학기 <select name="suupTerm">
                        <option value="10">1학기</option>
                        <option value="15">여름학기</option>
                        <option value="20">2학기</option>
                        <option value="25">겨울학기</option>
                    </select>
                    <button>요청</button>
                </form>

{{--                <button onclick="location.href=`{{ route('auth.hy.dataAPI',['asdf'=>'asdasd']) }}`">요청</button>--}}
            </div>
        </div>
    </div>


@endsection
