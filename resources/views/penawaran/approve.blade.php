@extends('adminlte::page')

@section('title', 'Tambah Document Penawaran')

@section('content_header')
    <h1>Upload Document Penawaran</h1>
@stop

@section('content')
<form action="{{ route('penawaran.approve', $item->id) }}"
      method="POST"
      style="display:inline">
    @csrf
    <button class="btn btn-success btn-sm"
            onclick="return confirm('Approve & generate barcode?')">
        Approve
    </button>
</form>
