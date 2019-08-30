@extends('layouts.app')

@section('content')


    <div class="col-12">
        <form action="{{route('admin.products.import')}}" method="post" enctype='multipart/form-data'>
            {{@csrf_field()}}

            <div class="form-group">
                <a href="{{route('admin.products.export')}}" class="btn  btn-primary">Загрузить все продукты XLSX</a>
            </div>

            <hr>

            <div class="form-group">
                <input type="file" name="productsExcelSheet" class="form-control" style="padding-bottom: 50px;padding-top: 20px;" id="productsExcelSheetFile">
                <button type="submit" class="btn btn-primary m-3">Upload</button>
            </div>

        </form>
    </div>
@endsection