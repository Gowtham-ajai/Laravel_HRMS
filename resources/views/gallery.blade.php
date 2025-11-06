@extends('layouts.master')

@section('title', 'Gallery Page')

@section('content')

<section class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center fw-bold mb-5 text-uppercase text-secondary">Workplace Moments</h2>
    
    @php
      $images = [
        'work1.jpg',
        'work2.jpg',
        'work3.jpg',
        'work4.jpg',
        'work5.jpg',
        'work6.jpg',
        'work7.jpg',
        'work8.jpg',
      ];
    @endphp

    <div class="row g-4">
      @foreach($images as $image)
      <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm">
          <img src="{{ asset('images/' . $image) }}" class="card-img-top" alt="Workplace Moment">
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

@endsection