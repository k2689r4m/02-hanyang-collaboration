@extends('admin.layouts.classManage')

@section('_script')

@endsection

@section('_content')
    <div class="card-body">
        <form class="text-right" method="get" action="">
            <input type="hidden" name="gwamokNm" value="{{ request()->query('gwamokNm') ?? '' }}" />
            <input type="hidden" name="gnjSosokNm" value="{{ request()->query('gnjSosokNm') ?? '' }}" />
            <input type="hidden" name="gnjHakgwaNm" value="{{ request()->query('gnjHakgwaNm') ?? '' }}" />
            <input type="hidden" name="year" value="{{ request()->query('year') ?? '' }}" />
            <input type="hidden" name="term" value="{{ request()->query('term') ?? '' }}" />
            <input type="hidden" name="suupNo" value="{{ request()->query('suupNo') ?? '' }}" />
            <input type="hidden" name="daepyoGangsaNm" value="{{ request()->query('daepyoGangsaNm') ?? '' }}" />
            <input type="hidden" name="meca" value="{{ request()->query('meca') ?? '' }}" />
            <input type="hidden" name="state" value="complete" />
            <button class="btn btn-md btn-primary">엑셀 다운</button>
        </form>
        <form method="GET" action="{{ route('admin.classCertificateView') }}">
            <div class="card-body">
                <table class="table table-bordered">
                    <colgroup>
                        <col width="8%" />
                        <col width="12%" />
                        <col width="8%" />
                        <col width="12%" />
                        <col width="8%" />
                        <col width="22%" />
                        <col width="8%" />
                        <col width="22%" />
                    </colgroup>
                    <tr>
                        <th>수업명</th>
                        <td colspan="3"><input class="form-control" type="text" name="gwamokNm" value="{{ request()->query('gwamokNm') }}" /></td>
                        <th>단대</th>
                        <td><input class="form-control" type="text" name="gnjSosokNm" value="{{ request()->query('gnjSosokNm') }}" /></td>
                        <th>학과</th>
                        <td><input class="form-control" type="text" name="gnjHakgwaNm" value="{{ request()->query('gnjHakgwaNm') }}" /></td>
                    </tr>
                    <tr>
                        <th>학기</th>
                        <td>
                            <select class="form-control w-sm" name="year">
                                <option value="" >선택</option>
                                @foreach(range(2010, 2030) as $y)
                                   <option value="{{ $y }}" >{{ $y }}년</option>
{{--                                   <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}년</option>--}}
                                @endforeach
                            </select>
                            <select class="form-control w-sm" name="term">
                                <option value="">
                                    선택
                                </option>
{{--                                <option value="10" {{ $term == '10' ? 'selected' : '' }}>--}}
{{--                                    1학기--}}
{{--                                </option>--}}
{{--                                <option value="15" {{ $term == '15' ? 'selected' : '' }}>--}}
{{--                                    여름학기--}}
{{--                                </option>--}}
{{--                                <option value="20" {{ $term == '20' ? 'selected' : '' }}>--}}
{{--                                    2학기--}}
{{--                                </option>--}}
{{--                                <option value="25" {{ $term == '25' ? 'selected' : '' }}>--}}
{{--                                    겨울학기--}}
{{--                                </option>--}}
                                <option value="10">
                                    1학기
                                </option>
                                <option value="15">
                                    여름학기
                                </option>
                                <option value="20">
                                    2학기
                                </option>
                                <option value="25">
                                    겨울학기
                                </option>
                            </select>
                        </td>
                        <th>유형</th>
                        <td>
                            <select class="form-control w-100" name="meca">
                                <option @if(request()->query('meca') == '') selected @endif>선택</option>
                                <option @if(request()->query('meca') == 'M') selected @endif>M</option>
                                <option @if(request()->query('meca') == 'E') selected @endif>E</option>
                                <option @if(request()->query('meca') == 'C') selected @endif>C</option>
                                <option @if(request()->query('meca') == 'A') selected @endif>A</option>
                            </select>
                        </td>
                        <th>수업번호</th>
                        <td><input class="form-control" type="text" name="suupNo" value="{{ request()->query('suupNo') }}" /></td>
                        <th>교수명</th>
                        <td><input class="form-control" type="text" name="daepyoGangsaNm" value="{{ request()->query('daepyoGangsaNm') }}" /></td>
                    </tr>
                </table>
            </div>
        </form>
        <div class="text-right card-body">
            <div class="float-left col-1">
                <form method="get" action="{{ route('admin.classCertificateView')}} ">
                    <select class="form-control" name="choice" onchange="submit()" >
                        <option type="submit" value="" selected>선택</option>
                        <option type="submit" value="all">전체</option>
                        <option type="submit" value="gnjSosokNm">단대</option>
                        <option type="submit" value="class">수업</option>
                    </select>
                </form>
            </div>
            <button class="btn btn-primary">검색</button>
            <button type="button" class="btn btn-outline-primary" onclick="location.href='{{ route('admin.classCertificateView') }}'">초기화</button>
        </div>
        <div class="card-body">
            <table class="table table-hover text-center">
                <colgroup>
                    <col width="5%" />
                    <col width="10%" />
                    <col width="35%" />
                    <col width="10%" />
                    <col width="10%" />
                    <col width="10%" />
                    <col width="10%" />
                    <col width="10%" />
                </colgroup>
                <thead>
                    <th>번호</th>
                    <th>수업번호</th>
                    <th>수업명</th>
                    <th>단대</th>
                    <th>학과</th>
                    <th>교수명</th>
                    <th>학생 수</th>
                    <th>발급 건수</th>
                </thead>
                <tbody>

                </tbody>
            </table>
{{--                {{ $classApplies->links('vendor.pagination.tailWind3') }}--}}
        </div>
    </div>
@endsection