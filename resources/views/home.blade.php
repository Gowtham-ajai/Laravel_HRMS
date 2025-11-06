@extends('layouts.master')

@section('title', 'Home Page')

@section('content')

<!-- Hero Banner -->
<section class="position-relative">
  <img src="{{ asset('images/banner.jpg') }}" class="img-fluid w-100" style="height: 420px; object-fit: cover;" alt="Banner">
  <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
    <h1 class="fw-bold display-5 mb-3">Welcome to RSoft HR Management</h1>
    <p class="fs-5 mb-4">Smart Workforce • Smarter Attendance • Seamless Management</p>
    <a href="#" class="btn btn-gradient btn-lg rounded-pill px-5 py-2 fw-semibold shadow-sm">Get Started</a>
  </div>
</section>

<!-- Services Section -->
<section class="py-5">
  <div class="container">
    <h2 class="text-center fw-bold mb-5 text-uppercase text-secondary">Our Expertise</h2>
    <div class="row g-4">
      @php
        $services = [
          ['title' => 'Advice', 'image' => 'advice.jpg', 'description' => 'Get expert HR guidance tailored to your organization. We help you navigate complex employee matters and make informed decisions that drive growth.'],
          ['title' => 'Growth', 'image' => 'growth.jpg', 'description' => 'Empower your team with effective growth strategies. We provide tools and insights to enhance skills, boost productivity, and achieve organizational goals.'],
          ['title' => 'Management', 'image' => 'management.jpg', 'description' => 'Simplify HR operations with streamlined processes. Our solutions ensure smooth employee management, attendance tracking, and performance oversight.'],
          ['title' => 'Strategy', 'image' => 'strategy.jpg', 'description' => 'Build long-term workforce strategies that deliver results. We help align HR practices with business goals for sustainable success.'],
        ];
      @endphp

      @foreach($services as $service)
      <div class="col-md-3 col-sm-6">
        <div class="service-card card border-0 shadow-sm h-100">
          <div class="card-img-wrapper position-relative">
            <img src="{{ asset('images/' . $service['image']) }}" class="card-img-top" alt="{{ $service['title'] }}">
            <div class="overlay-gradient"></div>
            <h5 class="overlay-title fw-bold text-white">{{ $service['title'] }}</h5>
          </div>
          <div class="card-body text-center">
            <p class="text-muted small mb-0">{{ $service['description'] }}</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Our Services Section -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center fw-bold mb-5 text-uppercase text-secondary">Our Services</h2>

    @php
      $services = [
        [
          'title' => 'Talent Acquisition', 
          'image' => 'talent.jpg',
          'description' => 'Find and hire the right talent efficiently. We streamline recruitment processes to attract skilled professionals for your organization.'
        ],
        [
          'title' => 'Employee Development', 
          'image' => 'development.jpg',
          'description' => 'Enhance employee skills with personalized growth plans and training programs that boost performance and engagement.'
        ],
        [
          'title' => 'HR Management', 
          'image' => 'hr_management.jpg',
          'description' => 'Simplify HR operations with easy attendance tracking, leave management, and employee performance monitoring.'
        ],
        [
          'title' => 'Strategic Planning', 
          'image' => 'strategy_planning.jpg',
          'description' => 'Align HR strategies with business goals for long-term success. We help build actionable plans that drive growth and efficiency.'
        ],
      ];
    @endphp

    <div class="row g-4">
      @foreach($services as $service)
      <div class="col-md-3 col-sm-6">
        <div class="service-card card border-0 shadow-sm h-100 position-relative overflow-hidden">
          <img src="{{ asset('images/' . $service['image']) }}" class="card-img-top" alt="{{ $service['title'] }}">
          <!-- Overlay -->
          <div class="card-overlay d-flex flex-column justify-content-end text-white p-3">
            <h5 class="fw-bold">{{ $service['title'] }}</h5>
            <p class="small mb-0">{{ $service['description'] }}</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<style>
  .service-card {
    position: relative;
    overflow: hidden;
  }

  .service-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .service-card:hover img {
    transform: scale(1.1);
  }

  .card-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    padding: 20px;
    transition: all 0.3s ease;
    transform: translateY(100%);
  }

  .service-card:hover .card-overlay {
    transform: translateY(0);
  }
</style>

<!-- Gallery Section -->
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

<!-- Newsletter Section -->
<section class="py-5" style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
  <div class="container text-center">
    <h2 class="fw-bold mb-3 text-white text-uppercase">Subscribe to Our Newsletter</h2>
    <p class="text-white-50 mb-4">Get the latest HR tips and updates from RSoft HR Management.</p>

    <form action="" method="POST" class="d-flex justify-content-center flex-wrap">
      @csrf
      <div class="position-relative mb-2" style="width: 300px;">
        <!-- Email Icon -->
        <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-white">
          <i class="bi bi-envelope-fill"></i>
        </span>
        <input type="email" name="email" class="form-control rounded-pill ps-5 py-2 border-0" placeholder="Enter your email" required>
      </div>
      <button type="submit" class="btn btn-gradient rounded-pill ms-2 mb-2 px-4 fw-semibold">Subscribe</button>
    </form>

    @if(session('success'))
      <div class="alert alert-light mt-3">{{ session('success') }}</div>
    @endif
  </div>
</section>

<style>
  /* Input field styling matching site colors */
  input.form-control {
    background: rgba(255,255,255,0.1);
    color: #fff;
    transition: all 0.3s ease;
  }

  input.form-control::placeholder {
    color: rgba(255,255,255,0.7);
  }

  input.form-control:focus {
    background: rgba(255,255,255,0.15);
    color: #fff;
    outline: none;
    box-shadow: 0 0 8px rgba(255,255,255,0.3);
  }

  /* Email icon inside input */
  .bi-envelope-fill {
    font-size: 1.1rem;
  }

  /* Button styling already matches site gradient, hover slightly lighter */
  .btn-gradient:hover {
    background: linear-gradient(90deg, #2a5298, #1e3c72);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
  }
</style>

@endsection
