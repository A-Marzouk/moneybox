@extends('layouts.app')

@section('content')


    <div class="col-12">
        <form action="{{route('admin.products.import')}}" method="post" enctype='multipart/form-data'>
            {{@csrf_field()}}

            <div class="form-group">
                <a href="{{route('admin.products.export')}}" class="btn  btn-primary">Скачать все продукты XLSX</a>
            </div>

            <div class="form-group">
                <a href="{{route('admin.clients.export')}}" class="btn  btn-primary">Скачать список менеджеров XLSX</a>
            </div>

            <hr>

            <div class="form-group">
                <input type="file" name="productsExcelSheet" class="form-control" style="padding-bottom: 50px;padding-top: 20px;" id="productsExcelSheetFile" required>
                <button type="submit" class="btn btn-primary mt-3">Загрузить</button>
            </div>

        </form>
    </div>
@endsection
