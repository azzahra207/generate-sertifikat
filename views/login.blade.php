@extends('layouts.app')

@section('content')
<style>
    @media only screen and (min-width: 1101px) {
        .d-grid {
            grid-template-columns: repeat(2,1fr);
            height:100%;
        }
        #left {
            width:100%;
        }
    }
    
    #left {
        background-color: #1E90FF;
    }
    
    @media only screen and (max-width:1100px) {
        .d-grid {
            grid-template-columns: repeat(1,1fr);  
            width: 100%;
            height:auto;     
        }
        #left {
            background-color: #1E90FF;
            width:100%;
        }
        #right {
            height:100%
        }
        #gmb {
            width: 100vw;
        }
    }
    
    #id {
        text-decoration: none;
    }
    
    #id:hover {
        text-decoration: underline;
    }
</style>

<div class="d-grid">
    <div id="right">
        <img id="gmb" src="img/login.png" alt="">
    </div>
    
    <div id="left" class="p-5">
        <h2 class="text-center fw-bold mb-5 text-light fs-1">{{ __('Entry Your Account') }}</h2>
        
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row mb-3">
                    <label for="email" class="col-form-label text-md-start text-light fs-4">{{ __('Email or Username') }}</label>

                    <div>
                        <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Masukkan Email atau Username">

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <label for="password" class="text-light fs-4 col-form-label text-md-start">{{ __('Password') }}</label>

                    <div>
                        <div class="position-relative">
                            <input placeholder="*********" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;" onclick="togglePassword()">
                                <i id="eye-icon" class="bi bi-eye text-secondary fs-5"></i>
                            </span>
                        </div>
                    
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="form-check">
                        <input class="form-check-input m-auto" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label text-light fs-6" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>

                                <button type="submit" class="btn btn-primary w-100 mb-3">
                    {{ __('Login') }}
                </button>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    @if (Route::has('password.request'))
                        <a class="text-light" id="id" href="{{ route('password.request') }}">
                            {{ __('Lupa Password?') }}
                        </a>
                    @endif

                    @if (Route::has('register'))
                        <a class="text-light fw-bold" id="id" href="{{ route('register') }}">
                            {{ __('Belum punya akun? Daftar') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div> 
</div>
<script>
    function togglePassword() {
        var passwordInput = document.getElementById("password");
        var eyeIcon = document.getElementById("eye-icon");
        
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("bi-eye");
            eyeIcon.classList.add("bi-eye-slash");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("bi-eye-slash");
            eyeIcon.classList.add("bi-eye");
        }
    }
</script>
@endsection