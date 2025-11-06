<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title')</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

  <!-- Axios Library & CSRF Setup -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
    // Set up Axios defaults for CSRF protection
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (csrfToken) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    }
    
    // Log for debugging
    console.log('Axios CSRF setup complete');
  </script>

  
  <style>
    body {
      background-color: #f8f9fc;
      color: #222;
      font-family: 'Poppins', sans-serif;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* ===== NAVBAR ===== */
    .navbar-custom {
      background: rgba(20, 40, 75, 0.9);
      backdrop-filter: blur(8px);
      transition: background 0.4s ease;
    }

    .navbar-brand {
      font-weight: 700;
      letter-spacing: 1px;
      color: #fff !important;
      text-transform: uppercase;
    }

    .navbar-nav .nav-link {
      color: #f1f1f1 !important;
      font-weight: 500;
      transition: 0.3s;
    }

    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
      color: #61a0ff !important;
    }

    .navbar .dropdown-menu {
      background: #1c2b4b;
      border: none;
    }

    .navbar .dropdown-item {
      color: #e0e0e0;
      font-weight: 500;
    }

    .navbar .dropdown-item:hover {
      background: #2a3e66;
      color: #61a0ff;
    }

    /* ===== BUTTONS ===== */
    .btn-modern {
      border-radius: 50px;
      transition: all 0.3s ease;
    }

    .btn-modern:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.25);
    }

    /* ===== FOOTER ===== */
    footer {
      background: linear-gradient(90deg, #1e3c72, #2a5298);
      color: #fff;
      text-align: center;
      padding: 1.5rem 0;
      margin-top: auto;
    }

    footer a {
      color: #cfd8ff;
      transition: 0.3s;
    }

    footer a:hover {
      color: #fff;
    }

    /* ===== LOGIN MODAL STYLES ===== */
    .login-modal .modal-content {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(30, 60, 114, 0.3);
    }

    .login-modal .modal-header {
        background: linear-gradient(90deg, #1e3c72, #2a5298);
        border-bottom: none;
        padding: 1.5rem 2rem;
    }

    .login-modal .modal-body {
        padding: 2rem;
    }

    .login-modal .form-control {
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
        /* FIX: Add text color for visibility */
        color: #333 !important;
        background-color: #fff !important;
    }

    .login-modal .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
        border-color: #1e3c72;
        /* FIX: Ensure text remains visible on focus */
        color: #333 !important;
        background-color: #fff !important;
    }

    .login-modal .form-control::placeholder {
        color: #6c757d !important;
        opacity: 1;
    }

    .login-modal .btn-login {
        background: linear-gradient(90deg, #1e3c72, #2a5298);
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .login-modal .btn-login:hover {
        background: linear-gradient(90deg, #2a5298, #1e3c72);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(30, 60, 114, 0.4);
    }

    .login-modal .input-group-text {
        background: transparent;
        border: none;
        border-radius: 50px 0 0 50px;
        padding-left: 1.5rem;
        /* FIX: Ensure icon color is visible */
        color: #6c757d !important;
    }

    .login-modal .input-group .form-control {
        border-radius: 0 50px 50px 0;
        padding-left: 0.5rem;
    }

    /* FIX: Ensure form labels are visible */
    .login-modal .form-label {
        color: #333 !important;
        font-weight: 600;
    }

    /* FIX: Ensure form check text is visible */
    .login-modal .form-check-label {
        color: #333 !important;
    }

  </style>
</head>

<body>
  <!-- ===== NAVBAR ===== -->
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top py-3">
    <div class="container">
      <a class="navbar-brand fs-4" href="{{ url('/') }}">RSoft</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
          <li class="nav-item"><a class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
          <li class="nav-item"><a class="nav-link {{ Request::routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a></li>
          <li class="nav-item"><a class="nav-link {{ Request::routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Pages
            </a>
            <ul class="dropdown-menu shadow-lg rounded-3">
              <li><a class="dropdown-item {{ Request::routeIs('testimonials') ? 'active' : '' }}" href="{{ route('testimonials') }}">Testimonials</a></li>
              <li><a class="dropdown-item {{ Request::routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">Gallery</a></li>
            </ul>
          </li>
          <li class="nav-item ms-3">
            <button class="btn btn-light text-dark fw-semibold px-4 btn-modern" data-bs-toggle="modal" data-bs-target="#loginModal">
              Sign In
            </button>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- ===== MAIN CONTENT ===== -->
  <main class="flex-grow-1 mt-5 pt-4">
    @yield('content')
  </main>

  <!-- ===== FOOTER ===== -->
  <footer>
    <p class="mb-1 fw-semibold fs-5">RSoft Technologies</p>
    <p class="mb-0 small opacity-75">© 2025 All Rights Reserved | Designed by RSoft</p>
    <div class="mt-3">
      <a href="https://www.facebook.com/rsoftai"><i class="bi bi-facebook mx-2"></i></a>
      <a href="https://x.com/rsoft_ai"><i class="bi bi-twitter mx-2"></i></a>
      <a href="https://www.linkedin.com/company/rsoftai/"><i class="bi bi-linkedin mx-2"></i></a>
      <a href="https://www.instagram.com/rsoftai"><i class="bi bi-instagram mx-2"></i></a>
    </div>
  </footer>

  <!-- ===== LOGIN MODAL ===== -->
  <div class="modal fade login-modal" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header text-white py-4">
          <h4 class="modal-title w-80 text-center mb-0" id="loginModalLabel">
            <i class="bi bi-person-circle me-2"></i>Sign In
          </h4>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <!-- Modal Body -->
        <div class="modal-body p-4">
          <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf
            
            <div class="mb-4">
              <label for="loginEmail" class="form-label fw-semibold">Email Address</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-envelope text-muted"></i>
                </span>
                <input type="email" name="email" id="loginEmail" class="form-control" 
                       placeholder="Enter your email" required autocomplete="email">
              </div>
            </div>

            <div class="mb-4">
              <label for="loginPassword" class="form-label fw-semibold">Password</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password" name="password" id="loginPassword" class="form-control" 
                       placeholder="Enter your password" required autocomplete="current-password">
              </div>
            </div>

            <button type="submit" class="btn btn-login w-100 py-2 fw-semibold" id="loginSubmitBtn">
              Sign In
              <i class="bi bi-arrow-right ms-2"></i>
            </button>
          </form>

          <!-- Display Messages -->
          @if(session('error'))
            <div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle me-2"></i>
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif
          
          @if(session('success'))
            <div class="alert alert-success mt-3 alert-dismissible fade show" role="alert">
              <i class="bi bi-check-circle me-2"></i>
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <!-- Forgot Password Link -->
          <div class="text-center mt-4">
              <a href="#" class="text-decoration-none" style="color: #1e3c72;" 
                data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal">
                  <i class="bi bi-key me-1"></i>Forgot your password?
              </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== SIMPLE FORGOT PASSWORD MODAL ===== -->
  <div class="modal fade login-modal" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <!-- Modal Header -->
              <div class="modal-header text-white py-4">
                  <h4 class="modal-title w-80 text-center mb-0" id="forgotPasswordModalLabel">
                      <i class="bi bi-key me-2"></i>Reset Your Password
                  </h4>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              
              <!-- Modal Body -->
              <div class="modal-body p-4">
                  <form id="forgotPasswordForm">
                      @csrf
                      
                      <div class="mb-4">
                          <p class="text-muted mb-3">Enter your email and new password to reset.</p>
                          
                          <!-- Email Field -->
                          <div class="mb-3">
                              <label for="forgotEmail" class="form-label fw-semibold">Email Address</label>
                              <div class="input-group">
                                  <span class="input-group-text">
                                      <i class="bi bi-envelope text-muted"></i>
                                  </span>
                                  <input type="email" name="email" id="forgotEmail" class="form-control" 
                                        placeholder="Enter your registered email" required>
                              </div>
                          </div>

                          <!-- New Password Field -->
                          <div class="mb-3">
                              <label for="newPassword" class="form-label fw-semibold">New Password</label>
                              <div class="input-group">
                                  <span class="input-group-text">
                                      <i class="bi bi-lock text-muted"></i>
                                  </span>
                                  <input type="password" name="password" id="newPassword" class="form-control" 
                                        placeholder="Enter new password" required minlength="6">
                              </div>
                              <div class="form-text">Password must be at least 6 characters long.</div>
                          </div>

                          <!-- Confirm Password Field -->
                          <div class="mb-4">
                              <label for="confirmPassword" class="form-label fw-semibold">Confirm Password</label>
                              <div class="input-group">
                                  <span class="input-group-text">
                                      <i class="bi bi-lock-fill text-muted"></i>
                                  </span>
                                  <input type="password" name="password_confirmation" id="confirmPassword" class="form-control" 
                                        placeholder="Confirm new password" required minlength="6">
                              </div>
                          </div>
                      </div>

                      <button type="submit" class="btn btn-login w-100 py-2 fw-semibold" id="resetPasswordBtn">
                          <i class="bi bi-check-lg me-2"></i>Update Password
                      </button>
                  </form>

                  <div class="text-center mt-3">
                      <a href="#" class="text-decoration-none" style="color: #1e3c72;" 
                        data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">
                          <i class="bi bi-arrow-left me-1"></i>Back to Login
                      </a>
                  </div>
              </div>
          </div>
      </div>
  </div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
      const loginModal = document.getElementById('loginModal');
      const loginForm = document.getElementById('loginForm');
      const loginSubmitBtn = document.getElementById('loginSubmitBtn');
      
      // Modal event handlers
      if (loginModal) {
          loginModal.addEventListener('shown.bs.modal', function() {
              const emailField = document.getElementById('loginEmail');
              if (emailField) {
                  emailField.focus();
              }
              
              // Clear any existing alerts
              const alerts = this.querySelectorAll('.alert');
              alerts.forEach(alert => {
                  alert.remove();
              });
          });
          
          loginModal.addEventListener('hidden.bs.modal', function() {
              if (loginForm) {
                  loginForm.reset();
              }
              if (loginSubmitBtn) {
                  loginSubmitBtn.innerHTML = 'Sign In <i class="bi bi-arrow-right ms-2"></i>';
                  loginSubmitBtn.disabled = false;
              }
          });
      }
      
      // Login form submission with AJAX
      if (loginForm) {
          loginForm.addEventListener('submit', function(e) {
              e.preventDefault();
              
              const formData = new FormData(this);
              const submitBtn = document.getElementById('loginSubmitBtn');
              const modalElement = document.getElementById('loginModal');
              const modal = bootstrap.Modal.getInstance(modalElement);
              
              // Show loading state
              submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm me-2"></i> Signing In...';
              submitBtn.disabled = true;
              
              // Use Axios for AJAX submission
              axios.post(this.action, formData)
                  .then(response => {
                      console.log('Login success:', response.data);
                      
                      if (response.data.success) {
                          // Show success message with SweetAlert2
                          Swal.fire({
                              icon: 'success',
                              title: 'Success!',
                              text: response.data.message,
                              timer: 1500,
                              showConfirmButton: false
                          });
                          
                          // Close modal
                          if (modal) {
                              modal.hide();
                          }
                          
                          // Redirect after short delay
                          setTimeout(() => {
                              window.location.href = response.data.redirect;
                          }, 1600);
                      }
                  })
                  .catch(error => {
                      console.error('Login error:', error);
                      
                      // Reset button
                      submitBtn.innerHTML = 'Sign In <i class="bi bi-arrow-right ms-2"></i>';
                      submitBtn.disabled = false;
                      
                      let errorMessage = 'An error occurred during login. Please try again.';
                      
                      if (error.response && error.response.data && error.response.data.message) {
                          errorMessage = error.response.data.message;
                      } else if (error.response && error.response.data && error.response.data.errors) {
                          // Handle validation errors
                          const errors = error.response.data.errors;
                          errorMessage = Object.values(errors)[0][0];
                      }
                      
                      // Show error with SweetAlert2
                      Swal.fire({
                          icon: 'error',
                          title: 'Login Failed',
                          text: errorMessage,
                          timer: 3000,
                          showConfirmButton: false
                      });
                  });
          });
      }
  });

  // script for forgot password 
  document.addEventListener('DOMContentLoaded', function() {
      const forgotPasswordForm = document.getElementById('forgotPasswordForm');
      const forgotPasswordModal = document.getElementById('forgotPasswordModal');

      if (forgotPasswordForm) {
          forgotPasswordForm.addEventListener('submit', function(e) {
              e.preventDefault();
              resetPassword();
          });
      }

      function resetPassword() {
          const formData = new FormData(forgotPasswordForm);
          const submitBtn = document.getElementById('resetPasswordBtn');
          const modal = bootstrap.Modal.getInstance(forgotPasswordModal);

          // Basic client-side validation
          const password = document.getElementById('newPassword').value;
          const confirmPassword = document.getElementById('confirmPassword').value;

          if (password !== confirmPassword) {
              Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Passwords do not match!',
                  timer: 3000,
                  showConfirmButton: false
              });
              return;
          }

          if (password.length < 6) {
              Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Password must be at least 6 characters long!',
                  timer: 3000,
                  showConfirmButton: false
              });
              return;
          }

          // Show loading state
          submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm me-2"></i> Updating...';
          submitBtn.disabled = true;

          axios.post('{{ route("password.reset.direct") }}', formData)
              .then(response => {
                  console.log('Password reset success:', response.data);

                  if (response.data.success) {
                      // Show success message
                      Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: response.data.message,
                          timer: 3000,
                          showConfirmButton: false
                      });

                      // Close modal
                      if (modal) {
                          modal.hide();
                      }

                      // Clear form
                      forgotPasswordForm.reset();

                      // Show login modal after delay
                      setTimeout(() => {
                          const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                          loginModal.show();
                      }, 1500);
                  }
              })
              .catch(error => {
                  console.error('Password reset error:', error);

                  // Reset button
                  submitBtn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Update Password';
                  submitBtn.disabled = false;

                  let errorMessage = 'Failed to update password. Please try again.';

                  if (error.response && error.response.data && error.response.data.message) {
                      errorMessage = error.response.data.message;
                  } else if (error.response && error.response.data && error.response.data.errors) {
                      const errors = error.response.data.errors;
                      errorMessage = Object.values(errors)[0][0];
                  }

                  Swal.fire({
                      icon: 'error',
                      title: 'Update Failed',
                      text: errorMessage,
                      timer: 4000,
                      showConfirmButton: true
                  });
              });
      }

      // Modal event handlers
      if (forgotPasswordModal) {
          forgotPasswordModal.addEventListener('shown.bs.modal', function() {
              const emailField = document.getElementById('forgotEmail');
              if (emailField) {
                  emailField.focus();
              }
          });

          forgotPasswordModal.addEventListener('hidden.bs.modal', function() {
              // Reset form when modal is closed
              if (forgotPasswordForm) {
                  forgotPasswordForm.reset();
              }
              const submitBtn = document.getElementById('resetPasswordBtn');
              if (submitBtn) {
                  submitBtn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Update Password';
                  submitBtn.disabled = false;
              }
          });
      }

      // Password strength indicator (optional enhancement)
      const newPasswordField = document.getElementById('newPassword');
      if (newPasswordField) {
          newPasswordField.addEventListener('input', function() {
              const password = this.value;
              const strengthText = document.getElementById('passwordStrength');
              
              if (!strengthText) {
                  // Create strength indicator if it doesn't exist
                  const strengthDiv = document.createElement('div');
                  strengthDiv.id = 'passwordStrength';
                  strengthDiv.className = 'form-text';
                  this.parentNode.parentNode.appendChild(strengthDiv);
              }
              
              const strengthElement = document.getElementById('passwordStrength');
              let strength = '';
              let color = 'text-danger';
              
              if (password.length === 0) {
                  strength = '';
              } else if (password.length < 6) {
                  strength = 'Weak - at least 6 characters required';
                  color = 'text-danger';
              } else if (password.length < 8) {
                  strength = 'Fair';
                  color = 'text-warning';
              } else {
                  strength = 'Strong';
                  color = 'text-success';
              }
              
              strengthElement.textContent = strength;
              strengthElement.className = `form-text ${color}`;
          });
      }
  });
</script>

{{-- script for login modal validation --}}
<script>
    $(document).ready(function() {
        // Remove any existing error messages on page load
        $('.field-error').remove();

        // ===== LOGIN FORM VALIDATION =====
        
        // Real-time email validation while typing
        $('#loginEmail').on('input', function() {
            const email = $(this).val();
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            
            // Remove existing error message
            $('#loginEmailError').remove();
            
            if (email && !emailRegex.test(email)) {
                // Show error message below the field
                $(this).after('<small class="text-danger field-error" id="loginEmailError">Please enter a valid email address</small>');
                $(this).addClass('is-invalid');
            } else {
                // Remove error message and styling
                $(this).removeClass('is-invalid');
            }
        });

        // Real-time password validation while typing
        $('#loginPassword').on('input', function() {
            const password = $(this).val();
            
            // Remove existing error message
            $('#loginPasswordError').remove();
            
            if (password && password.length < 6) {
                // Show error message below the field
                $(this).after('<small class="text-danger field-error" id="loginPasswordError">Password must be at least 6 characters</small>');
                $(this).addClass('is-invalid');
            } else {
                // Remove error message and styling
                $(this).removeClass('is-invalid');
            }
        });

        // ===== FORGOT PASSWORD FORM VALIDATION =====
        
        // Real-time email validation for forgot password
        $('#forgotEmail').on('input', function() {
            const email = $(this).val();
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            
            // Remove existing error message
            $('#forgotEmailError').remove();
            
            if (email && !emailRegex.test(email)) {
                // Show error message below the field
                $(this).after('<small class="text-danger field-error" id="forgotEmailError">Please enter a valid email address</small>');
                $(this).addClass('is-invalid');
            } else {
                // Remove error message and styling
                $(this).removeClass('is-invalid');
            }
        });

        // Real-time new password validation
        $('#newPassword').on('input', function() {
            const password = $(this).val();
            
            // Remove existing error message
            $('#newPasswordError').remove();
            
            if (password && password.length < 6) {
                // Show error message below the field
                $(this).after('<small class="text-danger field-error" id="newPasswordError">Password must be at least 6 characters</small>');
                $(this).addClass('is-invalid');
            } else {
                // Remove error message and styling
                $(this).removeClass('is-invalid');
            }

            // Also validate confirm password when new password changes
            validatePasswordMatch();
        });

        // Real-time confirm password validation
        $('#confirmPassword').on('input', function() {
            validatePasswordMatch();
        });

        function validatePasswordMatch() {
            const password = $('#newPassword').val();
            const confirmPassword = $('#confirmPassword').val();
            
            // Remove existing error message
            $('#confirmPasswordError').remove();
            
            if (confirmPassword && password !== confirmPassword) {
                // Show error message below the field
                $('#confirmPassword').after('<small class="text-danger field-error" id="confirmPasswordError">Passwords do not match</small>');
                $('#confirmPassword').addClass('is-invalid');
            } else {
                // Remove error message and styling
                $('#confirmPassword').removeClass('is-invalid');
            }
        }

        // Clear errors when user starts typing in a field
        $('input').on('focus', function() {
            $(this).removeClass('is-invalid');
            const fieldId = $(this).attr('id') + 'Error';
            $('#' + fieldId).remove();
        });

        // ===== LOGIN FORM SUBMISSION VALIDATION =====
        $('#loginForm').on('submit', function(e) {
            let hasErrors = false;

            // Email validation
            const email = $('#loginEmail').val();
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!email || !emailRegex.test(email)) {
                hasErrors = true;
                $('#loginEmail').addClass('is-invalid');
                if (!$('#loginEmailError').length) {
                    $('#loginEmail').after('<small class="text-danger field-error" id="loginEmailError">Please enter a valid email address</small>');
                }
            }

            // Password validation
            const password = $('#loginPassword').val();
            if (!password || password.length < 6) {
                hasErrors = true;
                $('#loginPassword').addClass('is-invalid');
                if (!$('#loginPasswordError').length) {
                    $('#loginPassword').after('<small class="text-danger field-error" id="loginPasswordError">Password must be at least 6 characters</small>');
                }
            }

            if (hasErrors) {
                e.preventDefault();
                return false;
            }

            // Show loading state
            $('#loginSubmitBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Signing In...');
        });

        // ===== FORGOT PASSWORD FORM SUBMISSION VALIDATION =====
        $('#forgotPasswordForm').on('submit', function(e) {
            let hasErrors = false;

            // Email validation
            const email = $('#forgotEmail').val();
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!email || !emailRegex.test(email)) {
                hasErrors = true;
                $('#forgotEmail').addClass('is-invalid');
                if (!$('#forgotEmailError').length) {
                    $('#forgotEmail').after('<small class="text-danger field-error" id="forgotEmailError">Please enter a valid email address</small>');
                }
            }

            // New password validation
            const newPassword = $('#newPassword').val();
            if (!newPassword || newPassword.length < 6) {
                hasErrors = true;
                $('#newPassword').addClass('is-invalid');
                if (!$('#newPasswordError').length) {
                    $('#newPassword').after('<small class="text-danger field-error" id="newPasswordError">Password must be at least 6 characters</small>');
                }
            }

            // Confirm password validation
            const confirmPassword = $('#confirmPassword').val();
            if (!confirmPassword || newPassword !== confirmPassword) {
                hasErrors = true;
                $('#confirmPassword').addClass('is-invalid');
                if (!$('#confirmPasswordError').length) {
                    $('#confirmPassword').after('<small class="text-danger field-error" id="confirmPasswordError">Passwords do not match</small>');
                }
            }

            if (hasErrors) {
                e.preventDefault();
                return false;
            }

            // Show loading state
            $('#resetPasswordBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Updating...');
        });

        // Reset form states when modal is shown/hidden
        $('#loginModal').on('show.bs.modal', function() {
            resetLoginForm();
        });

        $('#forgotPasswordModal').on('show.bs.modal', function() {
            resetForgotPasswordForm();
        });

        function resetLoginForm() {
            $('#loginEmail, #loginPassword').removeClass('is-invalid');
            $('#loginEmailError, #loginPasswordError').remove();
            $('#loginSubmitBtn').prop('disabled', false).html('Sign In <i class="bi bi-arrow-right ms-2"></i>');
        }

        function resetForgotPasswordForm() {
            $('#forgotEmail, #newPassword, #confirmPassword').removeClass('is-invalid');
            $('#forgotEmailError, #newPasswordError, #confirmPasswordError').remove();
            $('#resetPasswordBtn').prop('disabled', false).html('<i class="bi bi-check-lg me-2"></i>Update Password');
        }
    });
</script>

<style>
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    .field-error {
        display: block;
        margin-top: 5px;
        font-size: 0.875em;
        color: #dc3545;
    }
    
    /* Style for disabled buttons */
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
 
</body>
</html>