<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function index()
    {
        $path = public_path('images/home');
        $images = File::exists($path)
            ? collect(File::files($path))->map(fn($file) => $file->getFilename())
            : [];

        return view('home', compact('images'));
    }

    public function testimonials()
{
    // Testimonials data only
    $testimonials = [
        [
            'name' => 'Sarah Johnson',
            'position' => 'CEO, TechInnovate',
            'company' => 'techinnovate.png',
            'rating' => 5,
            'text' => 'The HR management system transformed how we handle employee data. The automation features saved us 20+ hours per week.',
            'image' => 'client1.jpg',
            'highlight' => 'Saved 20+ hours weekly'
        ],
        [
            'name' => 'Michael Chen',
            'position' => 'HR Director, Global Solutions', 
            'company' => 'globalsolutions.png',
            'rating' => 5,
            'text' => 'Outstanding support and intuitive interface. Our team adapted quickly.',
            'image' => 'client2.jpg',
            'highlight' => 'Valuable strategic insights'
        ],
    ];

    // Case studies data
    $caseStudies = [
        [
            'company' => 'Manufacturing Corp',
            'industry' => 'Manufacturing',
            'challenge' => 'Manual payroll processing for 500+ employees',
            'solution' => 'Automated payroll system with biometric integration',
            'results' => ['75% faster processing', '99% accuracy rate', '$50K annual savings'],
            'logo' => 'manufacturing-corp.png'
        ],
    ];

    return view('testimonials', compact('testimonials', 'caseStudies'));
}
}
