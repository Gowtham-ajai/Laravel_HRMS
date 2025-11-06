<!-- Modern Multicolor Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg, #007bff, #00c6ff, #0066cc);">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand fw-bold text-uppercase" href="#">RSoft</a>

    <!-- Toggle for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item"><a class="nav-link text-light" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link text-light" href="#">About</a></li>
        <li class="nav-item"><a class="nav-link text-light" href="#">Contact</a></li>

        <!-- Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown"
             aria-expanded="false">Pages</a>
          <ul class="dropdown-menu shadow-lg border-0 rounded-3">
            <li><a class="dropdown-item text-primary" href="#">Testimonials</a></li>
            <li><a class="dropdown-item text-primary" href="#">Gallery</a></li>
          </ul>
        </li>

        <!-- Sign In Button -->
        <li class="nav-item ms-3">
                <button class="btn btn-light text-primary fw-semibold px-4 rounded-pill shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#loginModal">
                Sign In
                </button>
        </li>
      </ul>
    </div>
  </div>
</nav>
