@extends('layouts.master')

@section('title', 'About Us')

@section('content')

<!-- About Us Intro Section -->
<section class="py-5">
  <div class="container">
    <div class="row align-items-center">
      <!-- Text Left -->
      <div class="col-md-6">
        <h2 class="fw-bold text-uppercase text-secondary">About Us</h2>
        <h3 class="fw-semibold mb-4">Welcome To RSoft</h3>
        <p class="text-muted mb-4">
          RSoft is a leading technology solutions provider specializing in software development, web applications, and IT training. We are committed to delivering innovative, reliable, and scalable solutions that empower businesses and individuals to achieve digital excellence. Our mission is to bridge the gap between technology and talent through quality-driven services and hands-on learning experiences.
        </p>
        <div class="row g-3">
          <div class="col-6"><i class="bi bi-check-circle-fill text-gradient"></i> Good Skills</div>
          <div class="col-6"><i class="bi bi-check-circle-fill text-gradient"></i> Experts Help</div>
          <div class="col-6"><i class="bi bi-check-circle-fill text-gradient"></i> Innovation</div>
          <div class="col-6"><i class="bi bi-check-circle-fill text-gradient"></i> Creative</div>
        </div>
      </div>
      <!-- Image Right -->
      <div class="col-md-6 text-center">
        <img src="{{ asset('images/about/about1.jpg') }}" class="img-fluid rounded shadow-sm" alt="About Image">
      </div>
    </div>
  </div>
</section>

<!-- Best Workplace Section -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="row align-items-center">
      <!-- Image Left -->
      <div class="col-md-6 text-center">
        <img src="{{ asset('images/about/about2.jpg') }}" class="img-fluid rounded shadow-sm" alt="Workplace Image">
      </div>
      <!-- Text Right -->
      <div class="col-md-6">
        <h3 class="fw-bold mb-3">Best Workplace For Innovation</h3>
        <p class="text-muted mb-3">
          At RSoft, innovation drives everything we do. We foster a collaborative and forward-thinking environment where ideas turn into impactful solutions. Our workplace culture encourages creativity, continuous learning, and the freedom to experiment—empowering every team member to shape the future of technology.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Vision & Mission Section -->
<section class="py-5">
  <div class="container text-center">
    <h2 class="fw-bold text-uppercase text-secondary mb-5">Our Vision & Mission</h2>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="p-4 bg-light border rounded shadow-sm h-100">
          <h4 class="fw-bold mb-3">Our Vision</h4>
          <p class="text-muted">Our vision is to become a global leader in digital transformation by fostering innovation, nurturing talent, and creating technology that inspires progress and connects people worldwide.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="p-4 bg-light border rounded shadow-sm h-100">
          <h4 class="fw-bold mb-3">Our Mission</h4>
          <p class="text-muted">At RSoft, our mission is to empower businesses and individuals through innovative software solutions that enhance efficiency, creativity, and growth. We strive to deliver cutting-edge technology with a strong commitment to quality, integrity, and customer satisfaction.
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Meet Our Experts Section -->
{{-- <section class="py-5 bg-light">
  <div class="container text-center">
    <h2 class="fw-bold text-uppercase text-secondary mb-5">Meet Our Expert Team</h2>
    <div class="row g-4">
      @php
        $experts = [
          ['name' => 'John Doe', 'position' => 'CEO & Founder', 'image' => 'team1.jpg'],
          ['name' => 'Jane Smith', 'position' => 'HR Manager', 'image' => 'team2.jpeg'],
          ['name' => 'Ana de arams', 'position' => 'Operations Manager', 'image' => 'team3.jpg'],
          ['name' => 'Emily Davis', 'position' => 'Sales Officer', 'image' => 'team4.jpg'],
        ];
      @endphp

      @foreach($experts as $expert)
      <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
          <img src="{{ asset('images/team/' . $expert['image']) }}" class="card-img-top rounded-circle mx-auto mt-3" style="width:150px; height:150px; object-fit:cover;" alt="{{ $expert['name'] }}">
          <div class="card-body text-center">
            <h5 class="fw-bold mb-1">{{ $expert['name'] }}</h5>
            <p class="text-muted small mb-0">{{ $expert['position'] }}</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section> --}}

@endsection
