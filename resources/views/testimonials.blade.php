@extends('layouts.master')

@section('title', 'Testimonials - What Our Clients Say')

@section('content')

<!-- Hero Section -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">What Our Clients Say</h1>
                <p class="lead mb-4">Discover why businesses trust our HR solutions and how we've helped transform their workforce management.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <div class="text-center">
                        <h3 class="fw-bold text-warning">4.9/5</h3>
                        <p class="small">Average Rating</p>
                    </div>
                    <div class="text-center">
                        <h3 class="fw-bold text-warning">500+</h3>
                        <p class="small">Happy Clients</p>
                    </div>
                    <div class="text-center">
                        <h3 class="fw-bold text-warning">98%</h3>
                        <p class="small">Satisfaction Rate</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-secondary mb-3">Client Testimonials</h2>
            <p class="text-muted">Read what our clients have to say about their experience</p>
        </div>
        
        <div class="row g-4">
            @foreach($testimonials as $testimonial)
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100 testimonial-card">
                    <div class="card-body p-4">
                        <!-- Rating -->
                        <div class="mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill {{ $i <= $testimonial['rating'] ? 'text-warning' : 'text-light' }}"></i>
                            @endfor
                        </div>
                        
                        <!-- Testimonial Text -->
                        <p class="lead fst-italic mb-4">"{{ $testimonial['text'] }}"</p>
                        
                        <!-- Highlight -->
                        <div class="bg-primary text-white rounded-pill px-3 py-1 d-inline-block mb-3">
                            <small class="fw-bold">{{ $testimonial['highlight'] }}</small>
                        </div>
                        
                        <!-- Client Info -->
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('images/testimonials/' . $testimonial['image']) }}" 
                                 class="rounded-circle me-3" 
                                 style="width: 60px; height: 60px; object-fit: cover;" 
                                 alt="{{ $testimonial['name'] }}">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $testimonial['name'] }}</h6>
                                <p class="text-muted small mb-1">{{ $testimonial['position'] }}</p>
                                <img src="{{ asset('images/companies/' . $testimonial['company']) }}" 
                                     style="height: 20px;" 
                                     alt="{{ $testimonial['name'] }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3 col-6">
                <h2 class="fw-bold display-4">500+</h2>
                <p class="mb-0">Companies Served</p>
            </div>
            <div class="col-md-3 col-6">
                <h2 class="fw-bold display-4">50K+</h2>
                <p class="mb-0">Employees Managed</p>
            </div>
            <div class="col-md-3 col-6">
                <h2 class="fw-bold display-4">99.9%</h2>
                <p class="mb-0">Uptime Reliability</p>
            </div>
            <div class="col-md-3 col-6">
                <h2 class="fw-bold display-4">24/7</h2>
                <p class="mb-0">Support Available</p>
            </div>
        </div>
    </div>
</section>

<!-- Case Studies Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-secondary mb-3">Success Case Studies</h2>
            <p class="text-muted">Detailed examples of how we've helped businesses succeed</p>
        </div>
        
        <div class="row g-4">
            @foreach($caseStudies as $case)
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ asset('images/companies/' . $case['logo']) }}" 
                                 style="height: 40px;" 
                                 class="me-3" 
                                 alt="{{ $case['company'] }}">
                            <div>
                                <h5 class="fw-bold mb-1">{{ $case['company'] }}</h5>
                                <span class="badge bg-secondary">{{ $case['industry'] }}</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-primary">Challenge:</h6>
                            <p class="small text-muted">{{ $case['challenge'] }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-success">Our Solution:</h6>
                            <p class="small text-muted">{{ $case['solution'] }}</p>
                        </div>
                        
                        <div>
                            <h6 class="text-info">Results Achieved:</h6>
                            <ul class="list-unstyled small">
                                @foreach($case['results'] as $result)
                                <li class="mb-1">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>{{ $result }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    .testimonial-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 4px solid #1e3c72 !important;
    }

    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }

    .bg-primary {
        background: linear-gradient(135deg, #1e3c72, #2a5298) !important;
    }
</style>

@endsection