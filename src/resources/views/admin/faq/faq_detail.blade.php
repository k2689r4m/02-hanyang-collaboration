@extends('admin.layouts.admin')

@section('script')

@endsection

@section('content')
    <div class="card-body">
        <div class="card-body col-6">
            <form method="POST" action="{{ route('admin.faqDetail', ['faqId' => $faq->id]) }}">
                @csrf
                <div class="form-group">
                    <label class="col-form-label">* 제목</label>
                    <input class="form-control" type="text" name="title" value="{{ old('title') ?? $faq->title }}" />
                    @error('title')
                    <p class="text-danger">제목을 입력해 주세요</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="col-form-label">* 내용</label>
                    <textarea class="form-control" name="content">{{ old('content') ?? $faq->content }}</textarea>
                    @error('content')
                    <p class="text-danger">내용을 입력해 주세요</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="col-form-label">* 답변</label>
                    <textarea class="form-control" name="adminAnswer">{{ old('adminAnswer') ?? $faq->adminAnswer }}</textarea>
                    @error('adminAnswer')
                    <p class="text-danger">답변을 입력해 주세요</p>
                    @enderror
                </div>
                <br>
                <div class="text-center">
                    <button class="btn btn-primary">저장</button>
                </div>
            </form>
        </div>
    </div>
@endsection